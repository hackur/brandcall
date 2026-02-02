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

const features = [
    {
        icon: (
            <svg className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
        ),
        title: 'STIR/SHAKEN Compliant',
        description: 'Full attestation for legitimate business calls. Meet FCC requirements automatically.',
    },
    {
        icon: (
            <svg className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
            </svg>
        ),
        title: 'Rich Call Display',
        description: 'Show your logo, business name, and call reason. Stand out on every device.',
    },
    {
        icon: (
            <svg className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
            </svg>
        ),
        title: 'Higher Answer Rates',
        description: 'Branded calls see up to 30% higher answer rates compared to unknown numbers.',
    },
    {
        icon: (
            <svg className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
        ),
        title: 'Number Reputation',
        description: 'Monitor and protect your calling numbers from spam flags and blocking.',
    },
];

const steps = [
    { number: '01', title: 'Create Account', description: 'Sign up and verify your business in minutes.' },
    { number: '02', title: 'Register Numbers', description: 'Add your outbound calling numbers to the platform.' },
    { number: '03', title: 'Brand Your Calls', description: 'Upload logo, set business name and call categories.' },
    { number: '04', title: 'Start Calling', description: 'Your branded identity displays on every outbound call.' },
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
                <div className="absolute inset-0 bg-gradient-hero" />
                
                {/* Animated gradient orbs */}
                <div className="absolute inset-0 overflow-hidden">
                    <div className="animate-blob absolute -left-40 -top-40 h-[500px] w-[500px] rounded-full bg-brand-600/20 mix-blend-screen blur-3xl filter" />
                    <div className="animate-blob animation-delay-2000 absolute -right-40 top-20 h-[500px] w-[500px] rounded-full bg-purple-600/15 mix-blend-screen blur-3xl filter" />
                    <div className="animate-blob animation-delay-4000 absolute -bottom-40 left-1/2 h-[500px] w-[500px] rounded-full bg-violet-600/15 mix-blend-screen blur-3xl filter" />
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
                    <nav className="px-6 py-6">
                        <div className="container-wide flex items-center justify-between">
                            {/* Logo */}
                            <div className="flex items-center gap-3">
                                <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-brand shadow-brand">
                                    <svg className="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                </div>
                                <span className="text-xl font-bold text-white font-heading">BrandCall</span>
                            </div>

                            {/* Nav Links */}
                            <div className="flex items-center gap-6">
                                {auth.user ? (
                                    <Link href={route('dashboard')} className="btn-primary btn-sm">
                                        Dashboard
                                    </Link>
                                ) : (
                                    <>
                                        <Link
                                            href={route('login')}
                                            className="text-sm font-medium text-slate-400 transition-colors hover:text-white"
                                        >
                                            Log in
                                        </Link>
                                        <Link href={route('register')} className="btn-primary btn-sm">
                                            Get Started
                                        </Link>
                                    </>
                                )}
                            </div>
                        </div>
                    </nav>

                    {/* Hero Section */}
                    <main className="section-lg">
                        <div className="container-wide">
                            <div className="text-center">
                                {/* Eyebrow */}
                                <p className="text-overline mb-6 animate-fade-in">
                                    Trusted by 500+ Businesses
                                </p>

                                {/* Rotating Headlines */}
                                <div className="relative h-40 sm:h-48 mb-8">
                                    <h1 
                                        className={`text-display text-balance absolute inset-x-0 transition-all duration-500 ${
                                            isTransitioning 
                                                ? 'opacity-0 translate-y-4' 
                                                : 'opacity-100 translate-y-0'
                                        }`}
                                    >
                                        <span className="text-gradient">{headlines[currentHeadline].title}</span>
                                        <br />
                                        <span className="text-white">{headlines[currentHeadline].subtitle}</span>
                                    </h1>
                                </div>

                                {/* Subheadline */}
                                <p className="lead max-w-2xl mx-auto mb-10 animate-fade-in animation-delay-200">
                                    Display your business name, logo, and call reason on every outbound call. 
                                    Build trust before they even answer.
                                </p>

                                {/* CTAs */}
                                <div className="flex flex-col sm:flex-row items-center justify-center gap-4 animate-fade-in animation-delay-400">
                                    <Link href={route('register')} className="btn-primary btn-lg">
                                        Start Free Trial
                                        <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                        </svg>
                                    </Link>
                                    <button className="btn-secondary btn-lg">
                                        Watch Demo
                                        <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </button>
                                </div>

                                {/* Headline indicators */}
                                <div className="flex justify-center gap-2 mt-12">
                                    {headlines.map((_, index) => (
                                        <button
                                            key={index}
                                            onClick={() => setCurrentHeadline(index)}
                                            className={`h-1.5 rounded-full transition-all duration-300 ${
                                                index === currentHeadline 
                                                    ? 'w-8 bg-brand-500' 
                                                    : 'w-1.5 bg-slate-700 hover:bg-slate-600'
                                            }`}
                                        />
                                    ))}
                                </div>
                            </div>
                        </div>
                    </main>

                    {/* Features Section */}
                    <section className="section">
                        <div className="container-wide">
                            {/* Section Header */}
                            <div className="text-center mb-16">
                                <p className="text-overline mb-4">Features</p>
                                <h2 className="text-4xl font-bold text-white mb-4">
                                    Everything You Need to Brand Your Calls
                                </h2>
                                <p className="lead max-w-2xl mx-auto">
                                    Complete caller ID branding platform with compliance built in.
                                </p>
                            </div>

                            {/* Features Grid */}
                            <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                                {features.map((feature, index) => (
                                    <div 
                                        key={index} 
                                        className="card-hover animate-fade-in-up"
                                        style={{ animationDelay: `${index * 100}ms` }}
                                    >
                                        <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-600/10 text-brand-400 mb-4">
                                            {feature.icon}
                                        </div>
                                        <h3 className="text-lg font-semibold text-white mb-2">
                                            {feature.title}
                                        </h3>
                                        <p className="text-slate-400 text-sm leading-relaxed">
                                            {feature.description}
                                        </p>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </section>

                    {/* How It Works Section */}
                    <section className="section">
                        <div className="container-wide">
                            {/* Section Header */}
                            <div className="text-center mb-16">
                                <p className="text-overline mb-4">How It Works</p>
                                <h2 className="text-4xl font-bold text-white mb-4">
                                    Get Started in Minutes
                                </h2>
                                <p className="lead max-w-2xl mx-auto">
                                    Four simple steps to branded caller ID.
                                </p>
                            </div>

                            {/* Steps */}
                            <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                                {steps.map((step, index) => (
                                    <div key={index} className="relative">
                                        {/* Connector line */}
                                        {index < steps.length - 1 && (
                                            <div className="hidden lg:block absolute top-8 left-full w-full h-px bg-gradient-to-r from-slate-700 to-transparent" />
                                        )}
                                        
                                        <div className="text-center lg:text-left">
                                            <span className="text-5xl font-extrabold text-brand-600/20">
                                                {step.number}
                                            </span>
                                            <h3 className="text-xl font-semibold text-white mt-2 mb-2">
                                                {step.title}
                                            </h3>
                                            <p className="text-slate-400">
                                                {step.description}
                                            </p>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </section>

                    {/* CTA Section */}
                    <section className="section">
                        <div className="container-default">
                            <div className="card-glass text-center py-16 px-8">
                                <h2 className="text-4xl font-bold text-white mb-4">
                                    Ready to Brand Your Calls?
                                </h2>
                                <p className="lead max-w-xl mx-auto mb-8">
                                    Join hundreds of businesses already using BrandCall to improve answer rates and build trust.
                                </p>
                                <div className="flex flex-col sm:flex-row items-center justify-center gap-4">
                                    <Link href={route('register')} className="btn-primary btn-lg">
                                        Start Free Trial
                                    </Link>
                                    <Link href={route('login')} className="btn-secondary btn-lg">
                                        Contact Sales
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </section>

                    {/* Footer */}
                    <footer className="py-12 border-t border-slate-800/50">
                        <div className="container-wide">
                            <div className="flex flex-col md:flex-row items-center justify-between gap-6">
                                {/* Logo */}
                                <div className="flex items-center gap-3">
                                    <div className="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-brand">
                                        <svg className="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                    </div>
                                    <span className="font-semibold text-white">BrandCall</span>
                                </div>

                                {/* Links */}
                                <div className="flex items-center gap-8 text-sm">
                                    <a href="#" className="text-slate-400 hover:text-white transition-colors">Privacy</a>
                                    <a href="#" className="text-slate-400 hover:text-white transition-colors">Terms</a>
                                    <a href="#" className="text-slate-400 hover:text-white transition-colors">Support</a>
                                </div>

                                {/* Copyright */}
                                <p className="text-sm text-slate-500">
                                    © 2026 BrandCall. All rights reserved.
                                </p>
                            </div>
                        </div>
                    </footer>
                </div>
            </div>
        </>
    );
}
