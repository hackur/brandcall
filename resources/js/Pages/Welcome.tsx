import { PageProps } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { useEffect, useState } from 'react';

const headlines = [
    { title: 'Branded Caller ID', subtitle: 'That Builds Trust' },
    { title: 'Stop Getting Ignored', subtitle: 'Start Getting Answered' },
    { title: '30% Higher Answer Rates', subtitle: 'With Every Call' },
    { title: 'Your Brand, Your Identity', subtitle: 'On Every Outbound Call' },
    { title: 'STIR/SHAKEN Compliant', subtitle: 'FCC-Ready From Day One' },
    { title: 'Rich Call Data', subtitle: 'Logo, Name & Call Reason' },
];

export default function Welcome({ auth }: PageProps) {
    const [currentHeadline, setCurrentHeadline] = useState(0);
    const [isTransitioning, setIsTransitioning] = useState(false);

    useEffect(() => {
        const interval = setInterval(() => {
            setIsTransitioning(true);
            setTimeout(() => {
                setCurrentHeadline((prev) => (prev + 1) % headlines.length);
                setIsTransitioning(false);
            }, 500);
        }, 4000);
        return () => clearInterval(interval);
    }, []);

    return (
        <>
            <Head title="BrandCall - Branded Caller ID Platform" />
            
            {/* Animated Background */}
            <div className="relative min-h-screen overflow-hidden">
                {/* Base gradient */}
                <div className="absolute inset-0 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950" />
                
                {/* Animated gradient orbs */}
                <div className="absolute inset-0 overflow-hidden">
                    <div className="animate-blob absolute -left-40 -top-40 h-96 w-96 rounded-full bg-purple-600/20 mix-blend-screen blur-3xl filter" />
                    <div className="animate-blob animation-delay-2000 absolute -right-40 top-20 h-96 w-96 rounded-full bg-indigo-600/20 mix-blend-screen blur-3xl filter" />
                    <div className="animate-blob animation-delay-4000 absolute -bottom-40 left-1/2 h-96 w-96 rounded-full bg-violet-600/20 mix-blend-screen blur-3xl filter" />
                </div>
                
                {/* Subtle grid pattern */}
                <div 
                    className="absolute inset-0 opacity-[0.02]"
                    style={{
                        backgroundImage: `url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0 0h60v60H0z' fill='none' stroke='%23fff' stroke-width='1'/%3E%3C/svg%3E")`,
                    }}
                />

                {/* Content */}
                <div className="relative z-10">
                    {/* Navigation */}
                    <nav className="px-6 py-4">
                        <div className="mx-auto flex max-w-7xl items-center justify-between">
                            <div className="flex items-center gap-2">
                                <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-purple-500 to-indigo-600 shadow-lg shadow-purple-500/25">
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
                                        className="rounded-lg bg-gradient-to-r from-purple-500 to-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-lg shadow-purple-500/25 transition hover:shadow-purple-500/40"
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
                                            className="rounded-lg bg-gradient-to-r from-purple-500 to-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-lg shadow-purple-500/25 transition hover:shadow-purple-500/40"
                                        >
                                            Get Started
                                        </Link>
                                    </>
                                )}
                            </div>
                        </div>
                    </nav>

                    {/* Hero Section with Carousel */}
                    <main className="px-6 py-20 lg:py-32">
                        <div className="mx-auto max-w-7xl">
                            <div className="text-center">
                                {/* Rotating Headlines */}
                                <div className="relative h-40 sm:h-48">
                                    <h1 
                                        className={`absolute inset-x-0 text-5xl font-bold tracking-tight text-white transition-all duration-500 sm:text-6xl lg:text-7xl ${
                                            isTransitioning ? 'translate-y-4 opacity-0' : 'translate-y-0 opacity-100'
                                        }`}
                                    >
                                        {headlines[currentHeadline].title}
                                        <span className="mt-2 block bg-gradient-to-r from-purple-400 via-violet-400 to-indigo-400 bg-clip-text text-transparent">
                                            {headlines[currentHeadline].subtitle}
                                        </span>
                                    </h1>
                                </div>

                                {/* Headline indicators */}
                                <div className="mt-4 flex items-center justify-center gap-2">
                                    {headlines.map((_, index) => (
                                        <button
                                            key={index}
                                            onClick={() => {
                                                setIsTransitioning(true);
                                                setTimeout(() => {
                                                    setCurrentHeadline(index);
                                                    setIsTransitioning(false);
                                                }, 300);
                                            }}
                                            className={`h-1.5 rounded-full transition-all duration-300 ${
                                                index === currentHeadline 
                                                    ? 'w-8 bg-purple-500' 
                                                    : 'w-1.5 bg-gray-600 hover:bg-gray-500'
                                            }`}
                                        />
                                    ))}
                                </div>

                                <p className="mx-auto mt-8 max-w-2xl text-lg text-gray-400">
                                    Display your company name, logo, and call reason on outbound calls. 
                                    Increase answer rates by up to 30% with STIR/SHAKEN compliant branded calling.
                                </p>
                                
                                <div className="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
                                    <Link
                                        href={route('register')}
                                        className="group relative inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-purple-500 to-indigo-600 px-8 py-4 text-lg font-semibold text-white shadow-xl shadow-purple-500/25 transition-all hover:shadow-purple-500/40"
                                    >
                                        <span>Start Free Trial</span>
                                        <svg className="h-5 w-5 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                        </svg>
                                    </Link>
                                    <a
                                        href="#pricing"
                                        className="inline-flex items-center gap-2 rounded-xl border border-gray-700 bg-white/5 px-8 py-4 text-lg font-semibold text-gray-300 backdrop-blur transition hover:border-gray-600 hover:bg-white/10 hover:text-white"
                                    >
                                        View Pricing
                                    </a>
                                </div>

                                {/* Trust badges */}
                                <div className="mt-16 flex flex-wrap items-center justify-center gap-8 text-sm text-gray-500">
                                    <div className="flex items-center gap-2">
                                        <svg className="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                                        </svg>
                                        <span>STIR/SHAKEN Compliant</span>
                                    </div>
                                    <div className="flex items-center gap-2">
                                        <svg className="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                                        </svg>
                                        <span>All Major US Carriers</span>
                                    </div>
                                    <div className="flex items-center gap-2">
                                        <svg className="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                                        </svg>
                                        <span>No Setup Fees</span>
                                    </div>
                                </div>
                            </div>

                            {/* Features */}
                            <div className="mt-32 grid gap-6 md:grid-cols-3">
                                {[
                                    {
                                        icon: (
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        ),
                                        title: 'STIR/SHAKEN Compliant',
                                        description: 'FCC-mandated attestation ensures your calls are verified and trusted by carriers.',
                                    },
                                    {
                                        icon: (
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        ),
                                        title: 'Rich Call Data',
                                        description: 'Display your logo, brand name, and call reason directly on the recipient\'s phone.',
                                    },
                                    {
                                        icon: (
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                        ),
                                        title: 'Higher Answer Rates',
                                        description: 'Customers are 30% more likely to answer when they see who\'s calling.',
                                    },
                                ].map((feature, index) => (
                                    <div 
                                        key={index}
                                        className="group relative rounded-2xl border border-gray-800 bg-gradient-to-b from-gray-900/50 to-gray-900/30 p-8 backdrop-blur transition-all hover:border-purple-500/50 hover:shadow-lg hover:shadow-purple-500/10"
                                    >
                                        <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-purple-500/20 to-indigo-500/20 ring-1 ring-purple-500/30">
                                            <svg className="h-6 w-6 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                {feature.icon}
                                            </svg>
                                        </div>
                                        <h3 className="mt-4 text-xl font-semibold text-white">{feature.title}</h3>
                                        <p className="mt-2 text-gray-400">{feature.description}</p>
                                    </div>
                                ))}
                            </div>

                            {/* Pricing */}
                            <div id="pricing" className="mt-32 scroll-mt-20">
                                <div className="text-center">
                                    <h2 className="text-3xl font-bold text-white sm:text-4xl">Simple, Usage-Based Pricing</h2>
                                    <p className="mx-auto mt-4 max-w-2xl text-gray-400">
                                        Pay only for successful branded calls. Volume discounts automatically applied.
                                    </p>
                                </div>

                                <div className="mx-auto mt-12 max-w-3xl">
                                    <div className="overflow-hidden rounded-2xl border border-gray-800 bg-gradient-to-b from-gray-900/80 to-gray-900/60 backdrop-blur">
                                        <table className="w-full">
                                            <thead>
                                                <tr className="border-b border-gray-800">
                                                    <th className="px-8 py-4 text-left text-sm font-medium text-gray-400">Monthly Volume</th>
                                                    <th className="px-8 py-4 text-right text-sm font-medium text-gray-400">Price per Call</th>
                                                </tr>
                                            </thead>
                                            <tbody className="divide-y divide-gray-800/50">
                                                {[
                                                    { volume: '0 - 9,999 calls', price: '$0.075' },
                                                    { volume: '10,000 - 99,999 calls', price: '$0.065' },
                                                    { volume: '100,000 - 999,999 calls', price: '$0.050' },
                                                    { volume: '1M - 9.99M calls', price: '$0.035' },
                                                    { volume: '10M+ calls', price: '$0.025' },
                                                ].map((tier, index) => (
                                                    <tr key={index} className="transition hover:bg-white/5">
                                                        <td className="px-8 py-4 text-white">{tier.volume}</td>
                                                        <td className="px-8 py-4 text-right font-mono text-purple-400">{tier.price}</td>
                                                    </tr>
                                                ))}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            {/* CTA */}
                            <div className="mt-32 text-center">
                                <h2 className="text-3xl font-bold text-white sm:text-4xl">Ready to Build Trust?</h2>
                                <p className="mt-4 text-gray-400">
                                    Start displaying your brand on every outbound call today.
                                </p>
                                <Link
                                    href={route('register')}
                                    className="mt-8 inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-purple-500 to-indigo-600 px-8 py-4 text-lg font-semibold text-white shadow-xl shadow-purple-500/25 transition-all hover:shadow-purple-500/40"
                                >
                                    Create Your Account
                                    <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                    </svg>
                                </Link>
                            </div>
                        </div>
                    </main>

                    {/* Footer */}
                    <footer className="border-t border-gray-800/50 px-6 py-12">
                        <div className="mx-auto max-w-7xl text-center text-sm text-gray-500">
                            <p>© 2026 BrandCall. All rights reserved.</p>
                        </div>
                    </footer>
                </div>
            </div>

            {/* CSS for animations */}
            <style>{`
                @keyframes blob {
                    0%, 100% { transform: translate(0, 0) scale(1); }
                    25% { transform: translate(20px, -30px) scale(1.1); }
                    50% { transform: translate(-20px, 20px) scale(0.9); }
                    75% { transform: translate(30px, 10px) scale(1.05); }
                }
                .animate-blob {
                    animation: blob 15s infinite ease-in-out;
                }
                .animation-delay-2000 {
                    animation-delay: 2s;
                }
                .animation-delay-4000 {
                    animation-delay: 4s;
                }
            `}</style>
        </>
    );
}
