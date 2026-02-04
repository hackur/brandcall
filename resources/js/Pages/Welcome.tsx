import { PageProps } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { useRef } from 'react';
import { Swiper, SwiperSlide } from 'swiper/react';
import { Navigation, Pagination, Autoplay, EffectFade } from 'swiper/modules';
import type { Swiper as SwiperType } from 'swiper';

// Swiper styles
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/effect-fade';

const headlines = [
    { title: 'Branded Caller ID', subtitle: 'That Builds Trust' },
    { title: 'Stop Getting Ignored', subtitle: 'Start Getting Answered' },
    { title: '30% Higher Answer Rates', subtitle: 'With Every Call' },
    { title: 'Your Brand, Your Identity', subtitle: 'On Every Outbound Call' },
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
    const swiperRef = useRef<SwiperType | null>(null);

    return (
        <div className="overflow-x-hidden">
            <Head title="BrandCall - Branded Caller ID Platform" />
            
            <div className="relative min-h-screen bg-slate-950">
                {/* Subtle gradient background */}
                <div className="absolute inset-0 bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950" />
                
                {/* Single subtle accent glow - contained to prevent horizontal scroll */}
                <div className="absolute inset-x-0 top-0 h-[600px] flex justify-center overflow-hidden pointer-events-none">
                    <div className="w-[800px] h-[600px] bg-brand-600/10 rounded-full blur-[120px] flex-shrink-0" />
                </div>

                {/* Content */}
                <div className="relative z-10">
                    {/* Navigation - Sticky header with glass effect */}
                    <nav className="sticky top-0 z-50 px-5 sm:px-6 py-3 sm:py-4 bg-slate-950/80 backdrop-blur-lg border-b border-slate-800/50">
                        <div className="max-w-7xl mx-auto flex items-center justify-between">
                            {/* Logo */}
                            <Link href="/" className="flex items-center gap-2 sm:gap-3">
                                <div className="flex h-8 w-8 sm:h-10 sm:w-10 items-center justify-center rounded-lg sm:rounded-xl bg-gradient-to-br from-brand-500 to-brand-600 shadow-lg shadow-brand-600/25">
                                    <svg className="h-4 w-4 sm:h-5 sm:w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                </div>
                                <span className="text-lg sm:text-xl font-bold text-white">BrandCall</span>
                            </Link>

                            {/* Nav Links */}
                            <div className="flex items-center gap-2 sm:gap-4">
                                {auth.user ? (
                                    <Link 
                                        href={route('dashboard')} 
                                        className="inline-flex items-center px-3 py-1.5 sm:px-5 sm:py-2.5 text-xs sm:text-sm font-semibold text-white bg-brand-600 rounded-lg hover:bg-brand-500 transition-colors"
                                    >
                                        Dashboard
                                    </Link>
                                ) : (
                                    <>
                                        <Link
                                            href={route('login')}
                                            className="hidden sm:block px-4 py-2 text-sm font-medium text-slate-400 hover:text-white transition-colors"
                                        >
                                            Log in
                                        </Link>
                                        <Link 
                                            href={route('register')} 
                                            className="inline-flex items-center px-3 py-1.5 sm:px-5 sm:py-2.5 text-xs sm:text-sm font-semibold text-white bg-brand-600 rounded-lg hover:bg-brand-500 transition-colors"
                                        >
                                            Get Started
                                        </Link>
                                    </>
                                )}
                            </div>
                        </div>
                    </nav>

                    {/* Hero Section */}
                    <section className="py-12 sm:py-16 lg:py-32">
                        <div className="max-w-7xl mx-auto px-6 sm:px-8">
                            <div className="max-w-4xl mx-auto text-center">
                                {/* Eyebrow */}
                                <p className="text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-brand-400 mb-3 sm:mb-6">
                                    Trusted by 500+ Businesses
                                </p>

                                {/* Headlines Carousel with Swiper */}
                                <div className="relative mb-4 sm:mb-8">
                                    {/* Desktop Navigation Arrows */}
                                    <button 
                                        onClick={() => swiperRef.current?.slidePrev()}
                                        className="hidden md:flex absolute left-0 top-1/2 -translate-y-1/2 -translate-x-12 lg:-translate-x-16 z-10 h-10 w-10 items-center justify-center rounded-full border border-slate-700 bg-slate-800/80 text-slate-400 hover:bg-slate-700 hover:text-white hover:border-slate-600 transition-all"
                                        aria-label="Previous headline"
                                    >
                                        <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                                        </svg>
                                    </button>
                                    <button 
                                        onClick={() => swiperRef.current?.slideNext()}
                                        className="hidden md:flex absolute right-0 top-1/2 -translate-y-1/2 translate-x-12 lg:translate-x-16 z-10 h-10 w-10 items-center justify-center rounded-full border border-slate-700 bg-slate-800/80 text-slate-400 hover:bg-slate-700 hover:text-white hover:border-slate-600 transition-all"
                                        aria-label="Next headline"
                                    >
                                        <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>

                                    <Swiper
                                        modules={[Pagination, Autoplay, EffectFade]}
                                        effect="fade"
                                        fadeEffect={{ crossFade: true }}
                                        autoplay={{
                                            delay: 5000,
                                            disableOnInteraction: false,
                                        }}
                                        pagination={{
                                            clickable: true,
                                            bulletClass: 'swiper-pagination-bullet !w-1.5 !h-1.5 !bg-slate-700 !opacity-100 !mx-1 transition-all duration-200',
                                            bulletActiveClass: '!w-6 sm:!w-8 !bg-brand-500 !rounded-full',
                                        }}
                                        loop={true}
                                        onSwiper={(swiper) => { swiperRef.current = swiper; }}
                                        className="!pb-10"
                                    >
                                        {headlines.map((headline, index) => (
                                            <SwiperSlide key={index}>
                                                <h1 className="text-[clamp(1.5rem,5.5vw,3.75rem)] font-extrabold tracking-tight leading-[1.1] min-h-[100px] sm:min-h-[140px] flex flex-col items-center justify-center">
                                                    <span className="bg-gradient-to-r from-brand-400 via-purple-400 to-brand-400 bg-clip-text text-transparent block">
                                                        {headline.title}
                                                    </span>
                                                    <span className="text-white block">
                                                        {headline.subtitle}
                                                    </span>
                                                </h1>
                                            </SwiperSlide>
                                        ))}
                                    </Swiper>
                                </div>

                                {/* Subheadline */}
                                <p className="text-sm sm:text-base lg:text-lg text-slate-400 max-w-xs sm:max-w-xl lg:max-w-2xl mx-auto mb-6 sm:mb-10 leading-relaxed">
                                    Display your business name, logo, and call reason on every outbound call. 
                                    Build trust before they even answer.
                                </p>

                                {/* CTAs */}
                                <div className="flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4">
                                    <Link 
                                        href={route('register')} 
                                        className="inline-flex items-center gap-2 px-5 py-2.5 sm:px-8 sm:py-4 text-sm sm:text-base font-semibold text-white bg-gradient-to-r from-brand-500 to-brand-600 rounded-lg shadow-lg shadow-brand-600/25 hover:shadow-xl hover:shadow-brand-600/30 hover:-translate-y-0.5 transition-all duration-200"
                                    >
                                        Start Free Trial
                                        <svg className="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                        </svg>
                                    </Link>
                                    <button className="inline-flex items-center gap-2 px-5 py-2.5 sm:px-8 sm:py-4 text-sm sm:text-base font-semibold text-slate-300 border border-slate-700 rounded-lg hover:bg-slate-800 hover:border-slate-600 hover:text-white transition-colors">
                                        Watch Demo
                                        <svg className="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </section>

                    {/* Features Section */}
                    <section className="py-12 sm:py-16 lg:py-24 border-t border-slate-800/50">
                        <div className="max-w-7xl mx-auto px-6 sm:px-8">
                            {/* Section Header */}
                            <div className="text-center mb-8 sm:mb-12 lg:mb-16">
                                <p className="text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-brand-400 mb-2 sm:mb-4">Features</p>
                                <h2 className="text-xl sm:text-2xl lg:text-4xl font-bold text-white mb-2 sm:mb-4">
                                    Everything You Need to Brand Your Calls
                                </h2>
                                <p className="text-sm sm:text-base lg:text-lg text-slate-400 max-w-2xl mx-auto">
                                    Complete caller ID branding platform with compliance built in.
                                </p>
                            </div>

                            {/* Features Grid */}
                            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-6">
                                {features.map((feature, index) => (
                                    <div 
                                        key={index} 
                                        className="p-4 sm:p-5 lg:p-6 rounded-xl sm:rounded-2xl bg-slate-800/50 border border-slate-700/50 hover:bg-slate-800/70 hover:border-slate-600/50 transition-colors duration-200"
                                    >
                                        <div className="flex h-10 w-10 sm:h-12 sm:w-12 items-center justify-center rounded-lg sm:rounded-xl bg-brand-600/10 text-brand-400 mb-3 sm:mb-4">
                                            {feature.icon}
                                        </div>
                                        <h3 className="text-base sm:text-lg font-semibold text-white mb-1 sm:mb-2">
                                            {feature.title}
                                        </h3>
                                        <p className="text-slate-400 text-xs sm:text-sm leading-relaxed">
                                            {feature.description}
                                        </p>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </section>

                    {/* How It Works Section */}
                    <section className="py-12 sm:py-16 lg:py-24 border-t border-slate-800/50">
                        <div className="max-w-7xl mx-auto px-6 sm:px-8">
                            {/* Section Header */}
                            <div className="text-center mb-8 sm:mb-12 lg:mb-16">
                                <p className="text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-brand-400 mb-2 sm:mb-4">How It Works</p>
                                <h2 className="text-xl sm:text-2xl lg:text-4xl font-bold text-white mb-2 sm:mb-4">
                                    Get Started in Minutes
                                </h2>
                                <p className="text-sm sm:text-base lg:text-lg text-slate-400 max-w-2xl mx-auto">
                                    Four simple steps to branded caller ID.
                                </p>
                            </div>

                            {/* Steps */}
                            <div className="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 lg:gap-12">
                                {steps.map((step, index) => (
                                    <div key={index} className="relative">
                                        {/* Connector line (desktop only) */}
                                        {index < steps.length - 1 && (
                                            <div className="hidden lg:block absolute top-6 left-full w-full h-px bg-gradient-to-r from-slate-700 to-transparent" />
                                        )}
                                        
                                        <div className="text-center lg:text-left">
                                            <span className="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-brand-600/20">
                                                {step.number}
                                            </span>
                                            <h3 className="text-sm sm:text-base lg:text-xl font-semibold text-white mt-1 sm:mt-2 mb-1 sm:mb-2">
                                                {step.title}
                                            </h3>
                                            <p className="text-xs sm:text-sm text-slate-400">
                                                {step.description}
                                            </p>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </section>

                    {/* CTA Section */}
                    <section className="py-12 sm:py-16 lg:py-24 border-t border-slate-800/50">
                        <div className="max-w-4xl mx-auto px-6 sm:px-8">
                            <div className="text-center p-6 sm:p-10 lg:p-16 rounded-xl sm:rounded-2xl bg-gradient-to-b from-slate-800/50 to-slate-800/30 border border-slate-700/50">
                                <h2 className="text-xl sm:text-2xl lg:text-4xl font-bold text-white mb-2 sm:mb-4">
                                    Ready to Brand Your Calls?
                                </h2>
                                <p className="text-sm sm:text-base lg:text-lg text-slate-400 max-w-xl mx-auto mb-5 sm:mb-8">
                                    Join hundreds of businesses already using BrandCall to improve answer rates and build trust.
                                </p>
                                <div className="flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4">
                                    <Link 
                                        href={route('register')} 
                                        className="inline-flex items-center px-5 py-2.5 sm:px-8 sm:py-4 text-sm sm:text-base font-semibold text-white bg-gradient-to-r from-brand-500 to-brand-600 rounded-lg shadow-lg shadow-brand-600/25 hover:shadow-xl hover:shadow-brand-600/30 hover:-translate-y-0.5 transition-all duration-200"
                                    >
                                        Start Free Trial
                                    </Link>
                                    <Link 
                                        href={route('login')} 
                                        className="inline-flex items-center px-5 py-2.5 sm:px-8 sm:py-4 text-sm sm:text-base font-semibold text-slate-300 border border-slate-700 rounded-lg hover:bg-slate-800 hover:border-slate-600 hover:text-white transition-colors"
                                    >
                                        Contact Sales
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </section>

                    {/* Footer */}
                    <footer className="py-6 sm:py-8 lg:py-12 border-t border-slate-800/50">
                        <div className="max-w-7xl mx-auto px-6 sm:px-8">
                            <div className="flex flex-col sm:flex-row items-center justify-between gap-4 sm:gap-6">
                                {/* Logo */}
                                <Link href="/" className="flex items-center gap-2">
                                    <div className="flex h-6 w-6 sm:h-8 sm:w-8 items-center justify-center rounded-lg bg-gradient-to-br from-brand-500 to-brand-600">
                                        <svg className="h-3 w-3 sm:h-4 sm:w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                    </div>
                                    <span className="text-sm sm:text-base font-semibold text-white">BrandCall</span>
                                </Link>

                                {/* Links */}
                                <div className="flex items-center gap-4 sm:gap-8 text-xs sm:text-sm">
                                    <a href="#" className="text-slate-400 hover:text-white transition-colors">Privacy</a>
                                    <a href="#" className="text-slate-400 hover:text-white transition-colors">Terms</a>
                                    <a href="#" className="text-slate-400 hover:text-white transition-colors">Support</a>
                                </div>

                                {/* Copyright */}
                                <p className="text-xs sm:text-sm text-slate-500">
                                    © 2026 BrandCall. All rights reserved.
                                </p>
                            </div>
                        </div>
                    </footer>
                </div>
            </div>
        </div>
    );
}
