import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { PageProps } from '@/types';

interface AdminDashboardProps extends PageProps {
    stats: {
        totalTenants: number;
        totalUsers: number;
        totalBrands: number;
        totalCalls: number;
        callsToday: number;
        revenueThisMonth: number;
    };
    recentTenants: Array<{
        id: number;
        name: string;
        email: string;
        slug: string;
        created_at: string;
        users: Array<{ id: number; name: string; email: string }>;
    }>;
    recentCalls: Array<{
        id: number;
        call_id: string;
        from_number: string;
        to_number: string;
        status: string;
        cost: number;
        created_at: string;
        brand: { name: string } | null;
        tenant: { name: string } | null;
    }>;
}

export default function AdminDashboard({ auth, stats, recentTenants, recentCalls }: AdminDashboardProps) {
    return (
        <AuthenticatedLayout
            header={
                <div className="flex items-center gap-2">
                    <span className="rounded bg-red-500 px-2 py-1 text-xs font-bold text-white">ADMIN</span>
                    <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                        Admin Dashboard
                    </h2>
                </div>
            }
        >
            <Head title="Admin Dashboard" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    {/* Stats Grid */}
                    <div className="grid gap-6 md:grid-cols-3 lg:grid-cols-6">
                        <div className="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                            <p className="text-sm font-medium text-gray-500 dark:text-gray-400">Tenants</p>
                            <p className="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{stats.totalTenants}</p>
                        </div>
                        <div className="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                            <p className="text-sm font-medium text-gray-500 dark:text-gray-400">Users</p>
                            <p className="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{stats.totalUsers}</p>
                        </div>
                        <div className="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                            <p className="text-sm font-medium text-gray-500 dark:text-gray-400">Brands</p>
                            <p className="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{stats.totalBrands}</p>
                        </div>
                        <div className="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                            <p className="text-sm font-medium text-gray-500 dark:text-gray-400">Total Calls</p>
                            <p className="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{stats.totalCalls.toLocaleString()}</p>
                        </div>
                        <div className="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                            <p className="text-sm font-medium text-gray-500 dark:text-gray-400">Calls Today</p>
                            <p className="mt-2 text-3xl font-bold text-green-600 dark:text-green-400">{stats.callsToday.toLocaleString()}</p>
                        </div>
                        <div className="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                            <p className="text-sm font-medium text-gray-500 dark:text-gray-400">Revenue (Month)</p>
                            <p className="mt-2 text-3xl font-bold text-purple-600 dark:text-purple-400">${stats.revenueThisMonth.toFixed(2)}</p>
                        </div>
                    </div>

                    {/* Admin Navigation */}
                    <div className="mt-8 grid gap-4 md:grid-cols-4">
                        <Link
                            href={route('admin.tenants')}
                            className="rounded-lg bg-white p-6 shadow transition hover:shadow-lg dark:bg-gray-800"
                        >
                            <h3 className="text-lg font-semibold text-gray-900 dark:text-white">Manage Tenants</h3>
                            <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">View and manage customer accounts</p>
                        </Link>
                        <Link
                            href={route('admin.brands')}
                            className="rounded-lg bg-white p-6 shadow transition hover:shadow-lg dark:bg-gray-800"
                        >
                            <h3 className="text-lg font-semibold text-gray-900 dark:text-white">All Brands</h3>
                            <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">View all brands across tenants</p>
                        </Link>
                        <Link
                            href="#"
                            className="rounded-lg bg-white p-6 shadow transition hover:shadow-lg dark:bg-gray-800"
                        >
                            <h3 className="text-lg font-semibold text-gray-900 dark:text-white">Call Logs</h3>
                            <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">View all call activity</p>
                        </Link>
                        <Link
                            href="#"
                            className="rounded-lg bg-white p-6 shadow transition hover:shadow-lg dark:bg-gray-800"
                        >
                            <h3 className="text-lg font-semibold text-gray-900 dark:text-white">Billing</h3>
                            <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">Revenue and invoices</p>
                        </Link>
                    </div>

                    <div className="mt-8 grid gap-6 lg:grid-cols-2">
                        {/* Recent Tenants */}
                        <div className="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                            <h3 className="text-lg font-semibold text-gray-900 dark:text-white">Recent Tenants</h3>
                            <div className="mt-4 space-y-3">
                                {recentTenants.map((tenant) => (
                                    <div key={tenant.id} className="flex items-center justify-between rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                                        <div>
                                            <p className="font-medium text-gray-900 dark:text-white">{tenant.name}</p>
                                            <p className="text-sm text-gray-500 dark:text-gray-400">{tenant.email}</p>
                                        </div>
                                        <div className="text-right">
                                            <p className="text-sm text-gray-500 dark:text-gray-400">{tenant.users?.length || 0} users</p>
                                            <p className="text-xs text-gray-400">{new Date(tenant.created_at).toLocaleDateString()}</p>
                                        </div>
                                    </div>
                                ))}
                                {recentTenants.length === 0 && (
                                    <p className="text-center text-gray-500 dark:text-gray-400">No tenants yet</p>
                                )}
                            </div>
                        </div>

                        {/* Recent Calls */}
                        <div className="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                            <h3 className="text-lg font-semibold text-gray-900 dark:text-white">Recent Calls</h3>
                            <div className="mt-4 space-y-3">
                                {recentCalls.map((call) => (
                                    <div key={call.id} className="flex items-center justify-between rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                                        <div>
                                            <p className="font-mono text-sm text-gray-900 dark:text-white">{call.from_number} → {call.to_number}</p>
                                            <p className="text-xs text-gray-500 dark:text-gray-400">
                                                {call.tenant?.name} / {call.brand?.name}
                                            </p>
                                        </div>
                                        <div className="text-right">
                                            <span className={`rounded-full px-2 py-1 text-xs font-medium ${
                                                call.status === 'completed' 
                                                    ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-400'
                                                    : call.status === 'failed'
                                                    ? 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-400'
                                                    : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-400'
                                            }`}>
                                                {call.status}
                                            </span>
                                            <p className="mt-1 text-xs text-gray-400">${call.cost.toFixed(4)}</p>
                                        </div>
                                    </div>
                                ))}
                                {recentCalls.length === 0 && (
                                    <p className="text-center text-gray-500 dark:text-gray-400">No calls yet</p>
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
