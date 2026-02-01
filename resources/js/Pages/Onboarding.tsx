import { Head, Link } from '@inertiajs/react';
import { PageProps } from '@/types';

export default function Onboarding({ auth }: PageProps) {
    return (
        <>
            <Head title="Get Started - BrandCall" />
            <div className="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 flex items-center justify-center p-6">
                <div className="max-w-lg w-full">
                    <div className="text-center mb-8">
                        <div className="flex items-center justify-center gap-2 mb-4">
                            <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-500">
                                <svg className="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <span className="text-2xl font-bold text-white">BrandCall</span>
                        </div>
                        <h1 className="text-3xl font-bold text-white">Welcome, {auth.user?.name}!</h1>
                        <p className="mt-2 text-gray-400">Let's set up your organization</p>
                    </div>

                    <div className="rounded-2xl bg-white/10 backdrop-blur p-8">
                        <div className="space-y-6">
                            <div>
                                <h2 className="text-xl font-semibold text-white">Create Your Organization</h2>
                                <p className="mt-2 text-gray-300">
                                    To start using BrandCall, you need to create an organization. This will be your workspace for managing brands and making calls.
                                </p>
                            </div>

                            <div className="space-y-4">
                                <div className="flex items-start gap-3">
                                    <div className="flex h-8 w-8 items-center justify-center rounded-full bg-purple-500/20 text-purple-400 text-sm font-bold">1</div>
                                    <div>
                                        <p className="font-medium text-white">Set up your company profile</p>
                                        <p className="text-sm text-gray-400">Name, contact info, and billing details</p>
                                    </div>
                                </div>
                                <div className="flex items-start gap-3">
                                    <div className="flex h-8 w-8 items-center justify-center rounded-full bg-purple-500/20 text-purple-400 text-sm font-bold">2</div>
                                    <div>
                                        <p className="font-medium text-white">Create your first brand</p>
                                        <p className="text-sm text-gray-400">Upload logo and configure caller ID</p>
                                    </div>
                                </div>
                                <div className="flex items-start gap-3">
                                    <div className="flex h-8 w-8 items-center justify-center rounded-full bg-purple-500/20 text-purple-400 text-sm font-bold">3</div>
                                    <div>
                                        <p className="font-medium text-white">Start making branded calls</p>
                                        <p className="text-sm text-gray-400">Via API or dashboard</p>
                                    </div>
                                </div>
                            </div>

                            <div className="pt-4">
                                <p className="text-center text-sm text-gray-400 mb-4">
                                    Coming soon! Contact us to get early access.
                                </p>
                                <div className="flex gap-3">
                                    <a
                                        href="mailto:support@brandcall.com"
                                        className="flex-1 rounded-lg bg-purple-500 px-4 py-3 text-center text-sm font-medium text-white hover:bg-purple-600 transition"
                                    >
                                        Contact Sales
                                    </a>
                                    <Link
                                        href={route('logout')}
                                        method="post"
                                        as="button"
                                        className="rounded-lg border border-gray-600 px-4 py-3 text-sm font-medium text-gray-300 hover:border-gray-500 transition"
                                    >
                                        Log Out
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
