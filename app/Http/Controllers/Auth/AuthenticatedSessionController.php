<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     *
     * Redirects users based on their role:
     * - super-admin: /admin (Filament admin panel) - uses Inertia::location for full page redirect
     * - regular users: /dashboard (which handles onboarding redirect)
     */
    public function store(LoginRequest $request): RedirectResponse|\Symfony\Component\HttpFoundation\Response
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();

        // Super-admins go directly to admin panel
        // Use Inertia::location() for full page redirect to non-Inertia route (Filament/Livewire)
        if ($user->hasRole('super-admin')) {
            return Inertia::location('/admin');
        }

        // Everyone else goes to dashboard (handles onboarding redirect)
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
