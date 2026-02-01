import { PageProps } from '@/types';
import { Head, Link } from '@inertiajs/react';

export default function Welcome({ auth }: PageProps) {
    return (
        <>
            <Head title="BrandCall - Branded Caller ID Platform" />
            <div className="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900">
                {/* Navigation */}
                <nav className="px-6 py-4">
                    <div className="mx-auto flex max-w-7xl items-center justify-between">
                        <div className="flex items-center gap-2">
                            <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-500">
                                <svg className="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <span className="text-xl font-bold text-white">BrandCall</span>
                        </div>
                        <div className="flex items-center gap-4">
                            {auth.user ? (
                                <Link
                                    href={route('dashboard')}
                                    className="rounded-lg bg-purple-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-purple-600"
                                >
                                    Dashboard
                                </Link>
                            ) : (
                                <>
                                    <Link
                                        href={route('login')}
                                        className="text-sm font-medium text-gray-300 transition hover:text-white"
                                    >
                                        Log in
                                    </Link>
                                    <Link
                                        href={route('register')}
                                        className="rounded-lg bg-purple-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-purple-600"
                                    >
                                        Get Started
                                    </Link>
                                </>
                            )}
                        </div>
                    </div>
                </nav>

                {/* Hero Section */}
                <main className="px-6 py-20">
                    <div className="mx-auto max-w-7xl">
                        <div className="text-center">
                            <h1 className="text-5xl font-bold tracking-tight text-white sm:text-6xl">
                                Branded Caller ID
                                <span className="block text-purple-400">That Builds Trust</span>
                            </h1>
                            <p className="mx-auto mt-6 max-w-2xl text-lg text-gray-300">
                                Display your company name, logo, and call reason on outbound calls. 
                                Increase answer rates by up to 30% with STIR/SHAKEN compliant branded calling.
                            </p>
                            <div className="mt-10 flex items-center justify-center gap-4">
                                <Link
                                    href={route('register')}
                                    className="rounded-lg bg-purple-500 px-6 py-3 text-lg font-semibold text-white shadow-lg transition hover:bg-purple-600"
                                >
                                    Start Free Trial
                                </Link>
                                <a
                                    href="#pricing"
                                    className="rounded-lg border border-gray-600 px-6 py-3 text-lg font-semibold text-gray-300 transition hover:border-gray-500 hover:text-white"
                                >
                                    View Pricing
                                </a>
                            </div>
                        </div>

                        {/* Features */}
                        <div className="mt-32 grid gap-8 md:grid-cols-3">
                            <div className="rounded-2xl bg-white/5 p-8 backdrop-blur">
                                <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-500/20">
                                    <svg className="h-6 w-6 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </div>
                                <h3 className="mt-4 text-xl font-semibold text-white">STIR/SHAKEN Compliant</h3>
                                <p className="mt-2 text-gray-400">
                                    FCC-mandated attestation ensures your calls are verified and trusted by carriers.
                                </p>
                            </div>

                            <div className="rounded-2xl bg-white/5 p-8 backdrop-blur">
                                <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-500/20">
                                    <svg className="h-6 w-6 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <h3 className="mt-4 text-xl font-semibold text-white">Rich Call Data</h3>
                                <p className="mt-2 text-gray-400">
                                    Display your logo, brand name, and call reason directly on the recipient's phone.
                                </p>
                            </div>

                            <div className="rounded-2xl bg-white/5 p-8 backdrop-blur">
                                <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-500/20">
                                    <svg className="h-6 w-6 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                    </svg>
                                </div>
                                <h3 className="mt-4 text-xl font-semibold text-white">Higher Answer Rates</h3>
                                <p className="mt-2 text-gray-400">
                                    Customers are 30% more likely to answer when they see who's calling.
                                </p>
                            </div>
                        </div>

                        {/* Pricing */}
                        <div id="pricing" className="mt-32">
                            <h2 className="text-center text-3xl font-bold text-white">Simple, Usage-Based Pricing</h2>
                            <p className="mx-auto mt-4 max-w-2xl text-center text-gray-400">
                                Pay only for successful branded calls. Volume discounts automatically applied.
                            </p>

                            <div className="mx-auto mt-12 max-w-3xl">
                                <div className="rounded-2xl bg-white/5 p-8 backdrop-blur">
                                    <table className="w-full">
                                        <thead>
                                            <tr className="border-b border-gray-700">
                                                <th className="pb-4 text-left text-sm font-medium text-gray-400">Monthly Volume</th>
                                                <th className="pb-4 text-right text-sm font-medium text-gray-400">Price per Call</th>
                                            </tr>
                                        </thead>
                                        <tbody className="divide-y divide-gray-800">
                                            <tr>
                                                <td className="py-4 text-white">0 - 9,999 calls</td>
                                                <td className="py-4 text-right text-purple-400">$0.075</td>
                                            </tr>
                                            <tr>
                                                <td className="py-4 text-white">10,000 - 99,999 calls</td>
                                                <td className="py-4 text-right text-purple-400">$0.065</td>
                                            </tr>
                                            <tr>
                                                <td className="py-4 text-white">100,000 - 999,999 calls</td>
                                                <td className="py-4 text-right text-purple-400">$0.050</td>
                                            </tr>
                                            <tr>
                                                <td className="py-4 text-white">1M - 9.99M calls</td>
                                                <td className="py-4 text-right text-purple-400">$0.035</td>
                                            </tr>
                                            <tr>
                                                <td className="py-4 text-white">10M+ calls</td>
                                                <td className="py-4 text-right text-purple-400">$0.025</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {/* CTA */}
                        <div className="mt-32 text-center">
                            <h2 className="text-3xl font-bold text-white">Ready to Build Trust?</h2>
                            <p className="mt-4 text-gray-400">
                                Start displaying your brand on every outbound call today.
                            </p>
                            <Link
                                href={route('register')}
                                className="mt-8 inline-block rounded-lg bg-purple-500 px-8 py-4 text-lg font-semibold text-white shadow-lg transition hover:bg-purple-600"
                            >
                                Create Your Account
                            </Link>
                        </div>
                    </div>
                </main>

                {/* Footer */}
                <footer className="border-t border-gray-800 px-6 py-12">
                    <div className="mx-auto max-w-7xl text-center text-sm text-gray-500">
                        <p>© 2026 BrandCall. Powered by NumHub BrandControl.</p>
                    </div>
                </footer>
            </div>
        </>
    );
}
