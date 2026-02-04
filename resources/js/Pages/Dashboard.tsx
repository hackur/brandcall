import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { PageProps } from '@/types';

interface DashboardProps extends PageProps {
    stats: {
        totalBrands: number;
        activeBrands: number;
        totalCalls: number;
        monthlySpend: number;
    };
    recentBrands: Array<{
        id: number;
        name: string;
        slug: string;
        status: string;
        logo_path: string | null;
    }>;
}

export default function Dashboard({ auth, stats, recentBrands }: DashboardProps) {
    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-theme-primary">
                    Dashboard
                </h2>
            }
        >
            <Head title="Dashboard" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    {/* Stats Cards */}
                    <div className="grid gap-6 md:grid-cols-4">
                        <div className="card">
                            <div className="flex items-center">
                                <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-brand-600/10">
                                    <svg className="h-6 w-6 text-brand-600 dark:text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <div className="ml-4">
                                    <p className="text-sm font-medium text-theme-muted">Total Brands</p>
                                    <p className="text-2xl font-semibold text-theme-primary">{stats?.totalBrands ?? 0}</p>
                                </div>
                            </div>
                        </div>

                        <div className="card">
                            <div className="flex items-center">
                                <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-green-500/10">
                                    <svg className="h-6 w-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div className="ml-4">
                                    <p className="text-sm font-medium text-theme-muted">Active Brands</p>
                                    <p className="text-2xl font-semibold text-theme-primary">{stats?.activeBrands ?? 0}</p>
                                </div>
                            </div>
                        </div>

                        <div className="card">
                            <div className="flex items-center">
                                <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-500/10">
                                    <svg className="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                </div>
                                <div className="ml-4">
                                    <p className="text-sm font-medium text-theme-muted">Total Calls</p>
                                    <p className="text-2xl font-semibold text-theme-primary">{stats?.totalCalls?.toLocaleString() ?? 0}</p>
                                </div>
                            </div>
                        </div>

                        <div className="card">
                            <div className="flex items-center">
                                <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-amber-500/10">
                                    <svg className="h-6 w-6 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div className="ml-4">
                                    <p className="text-sm font-medium text-theme-muted">This Month</p>
                                    <p className="text-2xl font-semibold text-theme-primary">${stats?.monthlySpend?.toFixed(2) ?? '0.00'}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Quick Actions + Recent Brands */}
                    <div className="mt-8 grid gap-6 lg:grid-cols-2">
                        {/* Quick Actions */}
                        <div className="card">
                            <h3 className="text-lg font-semibold text-theme-primary">Quick Actions</h3>
                            <div className="mt-4 grid gap-4 sm:grid-cols-2">
                                <Link
                                    href={route('brands.create')}
                                    className="flex items-center rounded-lg border border-theme-primary p-4 transition hover:bg-theme-tertiary"
                                >
                                    <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-brand-600/10">
                                        <svg className="h-5 w-5 text-brand-600 dark:text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
                                        </svg>
                                    </div>
                                    <div className="ml-3">
                                        <p className="font-medium text-theme-primary">Create Brand</p>
                                        <p className="text-sm text-theme-muted">Add a new branded caller ID</p>
                                    </div>
                                </Link>

                                <Link
                                    href={route('brands.index')}
                                    className="flex items-center rounded-lg border border-theme-primary p-4 transition hover:bg-theme-tertiary"
                                >
                                    <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-500/10">
                                        <svg className="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                        </svg>
                                    </div>
                                    <div className="ml-3">
                                        <p className="font-medium text-theme-primary">View Brands</p>
                                        <p className="text-sm text-theme-muted">Manage your brands</p>
                                    </div>
                                </Link>

                                <Link
                                    href={route('calls.index')}
                                    className="flex items-center rounded-lg border border-theme-primary p-4 transition hover:bg-theme-tertiary"
                                >
                                    <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-green-500/10">
                                        <svg className="h-5 w-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                    </div>
                                    <div className="ml-3">
                                        <p className="font-medium text-theme-primary">Call Logs</p>
                                        <p className="text-sm text-theme-muted">View call history</p>
                                    </div>
                                </Link>

                                <Link
                                    href={route('profile.edit')}
                                    className="flex items-center rounded-lg border border-theme-primary p-4 transition hover:bg-theme-tertiary"
                                >
                                    <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-theme-tertiary">
                                        <svg className="h-5 w-5 text-theme-tertiary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                    <div className="ml-3">
                                        <p className="font-medium text-theme-primary">Settings</p>
                                        <p className="text-sm text-theme-muted">Account settings</p>
                                    </div>
                                </Link>
                            </div>
                        </div>

                        {/* Recent Brands */}
                        <div className="card">
                            <div className="flex items-center justify-between">
                                <h3 className="text-lg font-semibold text-theme-primary">Recent Brands</h3>
                                <Link href={route('brands.index')} className="text-sm text-brand-600 hover:text-brand-500 dark:text-brand-400">
                                    View all
                                </Link>
                            </div>
                            <div className="mt-4">
                                {recentBrands && recentBrands.length > 0 ? (
                                    <div className="space-y-3">
                                        {recentBrands.map((brand) => (
                                            <Link
                                                key={brand.id}
                                                href={route('brands.show', brand.id)}
                                                className="flex items-center rounded-lg border border-theme-primary p-3 transition hover:bg-theme-tertiary"
                                            >
                                                <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-theme-tertiary">
                                                    {brand.logo_path ? (
                                                        <img src={brand.logo_path} alt={brand.name} className="h-8 w-8 object-contain" />
                                                    ) : (
                                                        <span className="text-lg font-semibold text-theme-muted">
                                                            {brand.name.charAt(0)}
                                                        </span>
                                                    )}
                                                </div>
                                                <div className="ml-3 flex-1">
                                                    <p className="font-medium text-theme-primary">{brand.name}</p>
                                                    <p className="text-sm text-theme-muted">{brand.slug}</p>
                                                </div>
                                                <span className={`rounded-full px-2 py-1 text-xs font-medium ${
                                                    brand.status === 'active' 
                                                        ? 'bg-green-500/10 text-green-600 dark:text-green-400'
                                                        : brand.status === 'pending_vetting'
                                                        ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400'
                                                        : 'bg-theme-tertiary text-theme-muted'
                                                }`}>
                                                    {brand.status}
                                                </span>
                                            </Link>
                                        ))}
                                    </div>
                                ) : (
                                    <div className="rounded-lg border-2 border-dashed border-theme-primary p-8 text-center">
                                        <svg className="mx-auto h-12 w-12 text-theme-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        <p className="mt-2 text-sm text-theme-muted">No brands yet</p>
                                        <Link
                                            href={route('brands.create')}
                                            className="mt-4 inline-block rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-500"
                                        >
                                            Create Your First Brand
                                        </Link>
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>

                    {/* API Documentation */}
                    <div className="card mt-8">
                        <h3 className="text-lg font-semibold text-theme-primary">API Quick Start</h3>
                        <p className="mt-2 text-sm text-theme-muted">
                            Make branded calls using our simple REST API. Each brand has a unique API key.
                        </p>
                        <div className="mt-4 rounded-lg bg-slate-900 dark:bg-slate-950 p-4 border border-slate-800">
                            <pre className="overflow-x-auto text-sm text-green-400">
{`curl -X POST https://api.brandcall.io/v1/brands/{brand_slug}/calls \\
  -H "Authorization: Bearer {api_key}" \\
  -H "Content-Type: application/json" \\
  -d '{
    "from": "+15551234567",
    "to": "+15559876543",
    "call_reason": "Appointment Reminder"
  }'`}
                            </pre>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
