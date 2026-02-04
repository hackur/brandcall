<?php

declare(strict_types=1);

namespace App\Models\NumHub;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * NumHubToken Model - OAuth token cache for NumHub API.
 *
 * Stores and manages NumHub API access tokens with automatic expiry tracking.
 * Tokens can be global (platform-wide) or tenant-scoped.
 *
 * Token Lifecycle:
 * 1. Authenticate with NumHub credentials
 * 2. Store access_token with expires_at
 * 3. Auto-refresh before expiry (5-minute buffer)
 * 4. Delete expired tokens via cleanup job
 *
 * @property int            $id            Primary key
 * @property int|null       $tenant_id     Owning tenant (null = global)
 * @property string         $token_type    Token type (access|refresh)
 * @property string         $access_token  Bearer token (encrypted)
 * @property string|null    $refresh_token Refresh token (encrypted)
 * @property \Carbon\Carbon $expires_at    Expiration timestamp
 * @property array|null     $scopes        Granted API scopes
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read Tenant|null    $tenant        Associated tenant
 */
class NumHubToken extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'numhub_tokens';

    /**
     * Token type constants.
     */
    public const TYPE_ACCESS = 'access';

    public const TYPE_REFRESH = 'refresh';

    /**
     * Buffer time before expiry to trigger refresh (seconds).
     */
    public const REFRESH_BUFFER_SECONDS = 300; // 5 minutes

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'token_type',
        'access_token',
        'refresh_token',
        'expires_at',
        'scopes',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'access_token',
        'refresh_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'access_token' => 'encrypted',
            'refresh_token' => 'encrypted',
            'expires_at' => 'datetime',
            'scopes' => 'array',
        ];
    }

    /**
     * Get the tenant that owns this token.
     *
     * @return BelongsTo<Tenant, NumHubToken>
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Check if the token is expired.
     *
     * @return bool True if expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if the token should be refreshed (within buffer period).
     *
     * @return bool True if should refresh
     */
    public function shouldRefresh(): bool
    {
        return $this->expires_at->subSeconds(self::REFRESH_BUFFER_SECONDS)->isPast();
    }

    /**
     * Check if the token is valid and not near expiry.
     *
     * @return bool True if valid and not near expiry
     */
    public function isValid(): bool
    {
        return ! $this->isExpired() && ! $this->shouldRefresh();
    }

    /**
     * Get a valid token for the given tenant (or global).
     *
     * Returns the most recent valid token. If no valid token exists
     * or the token needs refresh, returns null (caller should authenticate).
     *
     * @param int|null $tenantId Tenant ID (null for global token)
     *
     * @return self|null Valid token or null
     */
    public static function getValidToken(?int $tenantId = null): ?self
    {
        return static::where('tenant_id', $tenantId)
            ->where('token_type', self::TYPE_ACCESS)
            ->where('expires_at', '>', now()->addSeconds(self::REFRESH_BUFFER_SECONDS))
            ->orderByDesc('created_at')
            ->first();
    }

    /**
     * Store a new token from API response.
     *
     * @param array{access_token: string, expires_in?: int, refresh_token?: string, scope?: string|array<string>|null} $response API response
     * @param int|null                                                                                                 $tenantId Tenant ID (null for global)
     *
     * @return self The created token
     */
    public static function storeFromResponse(array $response, ?int $tenantId = null): self
    {
        // Delete existing tokens for this tenant
        static::where('tenant_id', $tenantId)->delete();

        // Calculate expiry (default 24 hours if not specified)
        $expiresIn = $response['expires_in'] ?? 86400;
        $expiresAt = now()->addSeconds($expiresIn);

        // Parse scopes
        $scopes = null;
        if (isset($response['scope']) && $response['scope'] !== null) {
            /** @var string|array<string> $scopeValue */
            $scopeValue = $response['scope'];
            $scopes = is_array($scopeValue) ? $scopeValue : explode(' ', $scopeValue);
        }

        return static::create([
            'tenant_id' => $tenantId,
            'token_type' => self::TYPE_ACCESS,
            'access_token' => $response['access_token'],
            'refresh_token' => $response['refresh_token'] ?? null,
            'expires_at' => $expiresAt,
            'scopes' => $scopes,
        ]);
    }

    /**
     * Delete all expired tokens (cleanup job).
     *
     * @return int Number of deleted records
     */
    public static function deleteExpired(): int
    {
        return static::where('expires_at', '<', now())->delete();
    }

    /**
     * Get the bearer token string for API requests.
     *
     * @return string Bearer token value
     */
    public function getBearerToken(): string
    {
        return $this->access_token;
    }
}
