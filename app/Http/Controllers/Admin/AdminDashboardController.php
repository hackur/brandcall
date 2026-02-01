<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\CallLog;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Inertia\Inertia;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();

        $stats = [
            'totalTenants' => Tenant::count(),
            'totalUsers' => User::count(),
            'totalBrands' => Brand::withoutGlobalScopes()->count(),
            'totalCalls' => CallLog::withoutGlobalScopes()->count(),
            'callsToday' => CallLog::withoutGlobalScopes()
                ->whereDate('created_at', $now->toDateString())
                ->count(),
            'revenueThisMonth' => CallLog::withoutGlobalScopes()
                ->whereYear('created_at', $now->year)
                ->whereMonth('created_at', $now->month)
                ->sum('cost'),
        ];

        $recentTenants = Tenant::with('users')
            ->latest()
            ->limit(10)
            ->get();

        $recentCalls = CallLog::withoutGlobalScopes()
            ->with(['brand', 'tenant'])
            ->latest()
            ->limit(20)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentTenants' => $recentTenants,
            'recentCalls' => $recentCalls,
        ]);
    }

    public function tenants()
    {
        $tenants = Tenant::with(['users', 'brands'])
            ->withCount(['brands', 'callLogs'])
            ->latest()
            ->paginate(20);

        return Inertia::render('Admin/Tenants', [
            'tenants' => $tenants,
        ]);
    }

    public function brands()
    {
        $brands = Brand::withoutGlobalScopes()
            ->with(['tenant', 'phoneNumbers'])
            ->latest()
            ->paginate(20);

        return Inertia::render('Admin/Brands', [
            'brands' => $brands,
        ]);
    }
}
