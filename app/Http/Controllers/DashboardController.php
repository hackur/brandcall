<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\CallLog;
use App\Models\UsageRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $tenant = $user->tenant;

        // Super admins without tenant go to Filament admin
        if (! $tenant && $user->hasRole('super-admin')) {
            return redirect('/admin');
        }

        // Regular users without tenant need onboarding
        if (! $tenant) {
            return Inertia::render('Onboarding');
        }

        // Set tenant context
        app()->instance('current_tenant', $tenant);

        $now = Carbon::now();

        // Get stats
        $stats = [
            'totalBrands' => Brand::count(),
            'activeBrands' => Brand::where('status', 'active')->count(),
            'totalCalls' => CallLog::count(),
            'monthlySpend' => UsageRecord::where('year', $now->year)
                ->where('month', $now->month)
                ->first()?->total_cost ?? 0,
        ];

        // Get recent brands
        $recentBrands = Brand::latest()
            ->limit(5)
            ->get(['id', 'name', 'slug', 'status', 'logo_path']);

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'recentBrands' => $recentBrands,
        ]);
    }
}
