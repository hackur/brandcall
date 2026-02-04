import { PageProps } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { useState, useEffect, useRef } from 'react';
import { Swiper, SwiperSlide } from 'swiper/react';
import { Pagination, Autoplay, EffectFade } from 'swiper/modules';
import type { Swiper as SwiperType } from 'swiper';

// Swiper styles
import 'swiper/css';
import 'swiper/css/pagination';
import 'swiper/css/effect-fade';

const headlines = [
    { title: 'Branded Caller ID', subtitle: 'That Builds Trust' },
    { title: 'Stop Getting Ignored', subtitle: 'Start Getting Answered' },
    { title: '30% Higher Answer Rates', subtitle: 'With Every Call' },
    { title: 'Your Brand, Your Identity', subtitle: 'On Every Outbound Call' },
];

// Custom SVG Icons
const Icons = {
    phone: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
        </svg>
    ),
    shield: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
            <path d="M9 12l2 2 4-4" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
        </svg>
    ),
    brandIdentity: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <rect x="3" y="3" width="18" height="18" rx="3" stroke="currentColor" strokeWidth="1.5"/>
            <circle cx="12" cy="10" r="3" stroke="currentColor" strokeWidth="1.5"/>
            <path d="M7 21v-1a5 5 0 0110 0v1" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round"/>
        </svg>
    ),
    analytics: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <path d="M18 20V10M12 20V4M6 20v-6" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
        </svg>
    ),
    lock: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <rect x="3" y="11" width="18" height="11" rx="2" stroke="currentColor" strokeWidth="1.5"/>
            <path d="M7 11V7a5 5 0 0110 0v4" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round"/>
            <circle cx="12" cy="16" r="1" fill="currentColor"/>
        </svg>
    ),
    api: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <path d="M16 18l2-2-2-2M8 18l-2-2 2-2M14 4l-4 16" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
        </svg>
    ),
    users: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <circle cx="9" cy="7" r="3" stroke="currentColor" strokeWidth="1.5"/>
            <path d="M3 21v-2a4 4 0 014-4h4a4 4 0 014 4v2" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round"/>
            <circle cx="17" cy="8" r="2.5" stroke="currentColor" strokeWidth="1.5"/>
            <path d="M21 21v-1.5a3 3 0 00-3-3h-.5" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round"/>
        </svg>
    ),
    building: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <path d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-4h6v4M9 9h.01M15 9h.01M9 13h.01M15 13h.01" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
        </svg>
    ),
    heart: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
            <path d="M12 8v4M12 16h.01" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round"/>
        </svg>
    ),
    dollar: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <circle cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="1.5"/>
            <path d="M12 6v12M9 9.5a2.5 2.5 0 015 0c0 1.5-2.5 2-2.5 3.5M12 17.5v.5" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round"/>
        </svg>
    ),
    shieldCheck: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" stroke="currentColor" strokeWidth="1.5"/>
        </svg>
    ),
    sun: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <circle cx="12" cy="12" r="5" stroke="currentColor" strokeWidth="1.5"/>
            <path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round"/>
        </svg>
    ),
    moon: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
        </svg>
    ),
    arrow: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <path d="M5 12h14M12 5l7 7-7 7" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
        </svg>
    ),
    check: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <path d="M20 6L9 17l-5-5" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
        </svg>
    ),
};

const stats = [
    { value: '99.9%', label: 'Uptime SLA' },
    { value: '500+', label: 'Enterprise Clients' },
    { value: '50M+', label: 'Calls Branded Monthly' },
    { value: '30%', label: 'Higher Answer Rates' },
];

const features = [
    {
        icon: Icons.shield,
        title: 'STIR/SHAKEN Attestation',
        description: 'Full A-level attestation for legitimate business calls. Automatic FCC compliance for all outbound calls.',
    },
    {
        icon: Icons.brandIdentity,
        title: 'Rich Call Data (RCD)',
        description: 'Display your company logo, verified business name, and call purpose on supported devices.',
    },
    {
        icon: Icons.analytics,
        title: 'Number Reputation Management',
        description: 'Real-time monitoring across carrier databases. Automated remediation for flagged numbers.',
    },
    {
        icon: Icons.lock,
        title: 'Enterprise Security',
        description: 'SOC 2 Type II certified. End-to-end encryption. Role-based access control and audit logging.',
    },
    {
        icon: Icons.api,
        title: 'API-First Platform',
        description: 'RESTful APIs with comprehensive documentation. Webhooks for real-time call status updates.',
    },
    {
        icon: Icons.users,
        title: 'Multi-Tenant Support',
        description: 'Manage multiple brands and campaigns. Hierarchical organization with delegated administration.',
    },
];

const useCases = [
    {
        title: 'Contact Centers',
        description: 'Increase connection rates and reduce call abandonment with verified caller identity.',
        icon: Icons.building,
    },
    {
        title: 'Healthcare',
        description: 'HIPAA-compliant calling with verified provider identification for patient outreach.',
        icon: Icons.heart,
    },
    {
        title: 'Financial Services',
        description: 'Build trust with verified calls for collections, fraud alerts, and account notifications.',
        icon: Icons.dollar,
    },
    {
        title: 'Insurance',
        description: 'Improve policyholder engagement with branded calls for claims and policy updates.',
        icon: Icons.shieldCheck,
    },
];

const complianceBadges = [
    { name: 'SOC 2', description: 'Type II Certified' },
    { name: 'STIR/SHAKEN', description: 'Full Compliance' },
    { name: 'HIPAA', description: 'Compliant' },
    { name: 'TCPA', description: 'Compliant' },
];

export default function Welcome({ auth }: PageProps) {
    const [isHeaderVisible, setIsHeaderVisible] = useState(true);
    const [lastScrollY, setLastScrollY] = useState(0);
    const [isDark, setIsDark] = useState(true);

    // Initialize theme from localStorage or system preference
    useEffect(() => {
        const savedTheme = localStorage.getItem('brandcall-theme');
        if (savedTheme) {
            setIsDark(savedTheme === 'dark');
        } else {
            setIsDark(window.matchMedia('(prefers-color-scheme: dark)').matches);
        }
    }, []);

    // Apply theme class to document
    useEffect(() => {
        if (isDark) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
        localStorage.setItem('brandcall-theme', isDark ? 'dark' : 'light');
    }, [isDark]);

    useEffect(() => {
        const handleScroll = () => {
            const currentScrollY = window.scrollY;
            const scrollThreshold = 10;
            
            if (Math.abs(currentScrollY - lastScrollY) < scrollThreshold) return;
            
            if (currentScrollY < 50) {
                setIsHeaderVisible(true);
            } else if (currentScrollY > lastScrollY) {
                setIsHeaderVisible(false);
            } else {
                setIsHeaderVisible(true);
            }
            
            setLastScrollY(currentScrollY);
        };

        window.addEventListener('scroll', handleScroll, { passive: true });
        return () => window.removeEventListener('scroll', handleScroll);
    }, [lastScrollY]);

    const toggleTheme = () => setIsDark(!isDark);

    return (
        <div>
            <Head title="BrandCall - Enterprise Branded Caller ID Platform" />
            
            <div className="relative min-h-screen bg-theme-primary transition-colors duration-300">
                {/* Content */}
                <div className="relative z-10">
                    {/* Navigation */}
                    <nav 
                        className={`fixed top-0 left-0 right-0 z-50 px-5 sm:px-6 py-3 sm:py-4 bg-theme-primary/95 backdrop-blur-lg border-b border-theme-primary transition-all duration-300 ${
                            isHeaderVisible ? 'translate-y-0' : '-translate-y-full'
                        }`}
                    >
                        <div className="max-w-7xl mx-auto flex items-center justify-between">
                            <Link href="/" className="flex items-center gap-2 sm:gap-3">
                                <div className="flex h-8 w-8 sm:h-10 sm:w-10 items-center justify-center rounded-lg bg-brand-600">
                                    <div className="h-4 w-4 sm:h-5 sm:w-5 text-white">
                                        {Icons.phone}
                                    </div>
                                </div>
                                <span className="text-lg sm:text-xl font-semibold text-theme-primary">BrandCall</span>
                            </Link>

                            <div className="hidden md:flex items-center gap-8">
                                <a href="#features" className="text-sm font-medium text-theme-tertiary hover:text-theme-primary transition-colors">Features</a>
                                <a href="#solutions" className="text-sm font-medium text-theme-tertiary hover:text-theme-primary transition-colors">Solutions</a>
                                <a href="#compliance" className="text-sm font-medium text-theme-tertiary hover:text-theme-primary transition-colors">Compliance</a>
                                <a href="#" className="text-sm font-medium text-theme-tertiary hover:text-theme-primary transition-colors">Pricing</a>
                            </div>

                            <div className="flex items-center gap-2 sm:gap-4">
                                {/* Theme Toggle */}
                                <button
                                    onClick={toggleTheme}
                                    className="p-2 rounded-md text-theme-tertiary hover:text-theme-primary hover:bg-theme-tertiary/50 transition-colors"
                                    aria-label="Toggle theme"
                                >
                                    <div className="h-5 w-5">
                                        {isDark ? Icons.sun : Icons.moon}
                                    </div>
                                </button>

                                {auth.user ? (
                                    <Link 
                                        href={route('dashboard')} 
                                        className="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-brand-600 rounded-md hover:bg-brand-500 transition-colors"
                                    >
                                        Dashboard
                                    </Link>
                                ) : (
                                    <>
                                        <Link
                                            href={route('login')}
                                            className="hidden sm:block px-4 py-2 text-sm font-medium text-theme-tertiary hover:text-theme-primary transition-colors"
                                        >
                                            Sign In
                                        </Link>
                                        <Link 
                                            href={route('register')} 
                                            className="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-brand-600 rounded-md hover:bg-brand-500 transition-colors"
                                        >
                                            Request Demo
                                        </Link>
                                    </>
                                )}
                            </div>
                        </div>
                    </nav>
                    
                    <div className="h-14 sm:h-16" />

                    {/* Hero Section */}
                    <section className="py-16 sm:py-20 lg:py-28">
                        <div className="max-w-7xl mx-auto px-6 sm:px-8">
                            <div className="max-w-4xl">
                                <div className="flex items-center gap-3 mb-6">
                                    <span className="inline-flex items-center px-3 py-1 text-xs font-medium text-brand-600 dark:text-brand-400 bg-brand-600/10 rounded-full border border-brand-600/20">
                                        Enterprise Solution
                                    </span>
                                </div>

                                <h1 className="text-3xl sm:text-4xl lg:text-5xl xl:text-6xl font-semibold tracking-tight text-theme-primary leading-[1.1] mb-6">
                                    Branded Caller ID<br />
                                    <span className="text-theme-tertiary">for the Enterprise</span>
                                </h1>

                                <p className="text-base sm:text-lg lg:text-xl text-theme-secondary max-w-2xl mb-8 leading-relaxed">
                                    Display your verified business identity on every outbound call. 
                                    Increase answer rates, maintain compliance, and protect your brand reputation.
                                </p>

                                <div className="flex flex-col sm:flex-row items-start gap-3 sm:gap-4 mb-12">
                                    <Link 
                                        href={route('register')} 
                                        className="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium text-white bg-brand-600 rounded-md hover:bg-brand-500 transition-colors"
                                    >
                                        Request a Demo
                                        <div className="h-4 w-4">{Icons.arrow}</div>
                                    </Link>
                                    <Link 
                                        href="#features"
                                        className="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium text-theme-secondary border border-theme-primary rounded-md hover:bg-theme-tertiary transition-colors"
                                    >
                                        View Documentation
                                    </Link>
                                </div>

                                <div className="grid grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8 pt-8 border-t border-theme-primary">
                                    {stats.map((stat, index) => (
                                        <div key={index}>
                                            <div className="text-2xl sm:text-3xl font-semibold text-theme-primary mb-1">
                                                {stat.value}
                                            </div>
                                            <div className="text-sm text-theme-muted">
                                                {stat.label}
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        </div>
                    </section>

                    {/* Compliance Badges */}
                    <section id="compliance" className="py-12 border-t border-theme-primary">
                        <div className="max-w-7xl mx-auto px-6 sm:px-8">
                            <div className="flex flex-col sm:flex-row items-center justify-between gap-6">
                                <p className="text-sm text-theme-muted font-medium">
                                    Compliance & Certifications
                                </p>
                                <div className="flex flex-wrap items-center justify-center gap-6 sm:gap-10">
                                    {complianceBadges.map((badge, index) => (
                                        <div key={index} className="flex items-center gap-2">
                                            <div className="h-5 w-5 text-green-500">
                                                {Icons.check}
                                            </div>
                                            <div>
                                                <span className="text-sm font-medium text-theme-primary">{badge.name}</span>
                                                <span className="text-theme-muted text-sm ml-1.5">{badge.description}</span>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        </div>
                    </section>

                    {/* Features Section */}
                    <section id="features" className="py-16 sm:py-20 lg:py-24 border-t border-theme-primary">
                        <div className="max-w-7xl mx-auto px-6 sm:px-8">
                            <div className="max-w-2xl mb-12 lg:mb-16">
                                <p className="text-sm font-medium text-brand-600 dark:text-brand-400 mb-3">Platform Features</p>
                                <h2 className="text-2xl sm:text-3xl lg:text-4xl font-semibold text-theme-primary mb-4">
                                    Enterprise-Grade Caller ID Management
                                </h2>
                                <p className="text-base lg:text-lg text-theme-secondary">
                                    A complete platform for managing branded calling at scale, with built-in compliance and security.
                                </p>
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                                {features.map((feature, index) => (
                                    <div 
                                        key={index} 
                                        className="p-6 rounded-lg bg-theme-secondary border border-theme-primary hover:border-theme-secondary transition-colors"
                                    >
                                        <div className="flex h-10 w-10 items-center justify-center rounded-md bg-brand-600/10 text-brand-600 dark:text-brand-400 mb-4">
                                            <div className="h-6 w-6">{feature.icon}</div>
                                        </div>
                                        <h3 className="text-base font-semibold text-theme-primary mb-2">
                                            {feature.title}
                                        </h3>
                                        <p className="text-sm text-theme-tertiary leading-relaxed">
                                            {feature.description}
                                        </p>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </section>

                    {/* Solutions Section */}
                    <section id="solutions" className="py-16 sm:py-20 lg:py-24 border-t border-theme-primary bg-theme-secondary">
                        <div className="max-w-7xl mx-auto px-6 sm:px-8">
                            <div className="max-w-2xl mb-12 lg:mb-16">
                                <p className="text-sm font-medium text-brand-600 dark:text-brand-400 mb-3">Industry Solutions</p>
                                <h2 className="text-2xl sm:text-3xl lg:text-4xl font-semibold text-theme-primary mb-4">
                                    Built for Regulated Industries
                                </h2>
                                <p className="text-base lg:text-lg text-theme-secondary">
                                    Purpose-built features for industries with strict compliance requirements and high call volumes.
                                </p>
                            </div>

                            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                                {useCases.map((useCase, index) => (
                                    <div 
                                        key={index} 
                                        className="p-6 rounded-lg bg-theme-primary border border-theme-primary hover:border-theme-secondary transition-colors"
                                    >
                                        <div className="h-8 w-8 text-theme-muted mb-4">
                                            {useCase.icon}
                                        </div>
                                        <h3 className="text-lg font-semibold text-theme-primary mb-2">
                                            {useCase.title}
                                        </h3>
                                        <p className="text-sm text-theme-tertiary leading-relaxed">
                                            {useCase.description}
                                        </p>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </section>

                    {/* CTA Section */}
                    <section className="py-16 sm:py-20 lg:py-24 border-t border-theme-primary">
                        <div className="max-w-3xl mx-auto px-6 sm:px-8 text-center">
                            <h2 className="text-2xl sm:text-3xl lg:text-4xl font-semibold text-theme-primary mb-4">
                                Ready to transform your outbound calling?
                            </h2>
                            <p className="text-base lg:text-lg text-theme-secondary max-w-xl mx-auto mb-8">
                                Schedule a demo with our team to see how BrandCall can improve your answer rates and protect your brand.
                            </p>
                            <div className="flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4">
                                <Link 
                                    href={route('register')} 
                                    className="inline-flex items-center px-6 py-3 text-sm font-medium text-white bg-brand-600 rounded-md hover:bg-brand-500 transition-colors"
                                >
                                    Request a Demo
                                </Link>
                                <a 
                                    href="mailto:sales@brandcall.io"
                                    className="inline-flex items-center px-6 py-3 text-sm font-medium text-theme-secondary border border-theme-primary rounded-md hover:bg-theme-tertiary transition-colors"
                                >
                                    Contact Sales
                                </a>
                            </div>
                        </div>
                    </section>

                    {/* Footer */}
                    <footer className="py-12 border-t border-theme-primary">
                        <div className="max-w-7xl mx-auto px-6 sm:px-8">
                            <div className="grid grid-cols-2 md:grid-cols-4 gap-8 mb-12">
                                <div className="col-span-2 md:col-span-1">
                                    <Link href="/" className="flex items-center gap-2 mb-4">
                                        <div className="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-600">
                                            <div className="h-4 w-4 text-white">{Icons.phone}</div>
                                        </div>
                                        <span className="text-base font-semibold text-theme-primary">BrandCall</span>
                                    </Link>
                                    <p className="text-sm text-theme-muted leading-relaxed">
                                        Enterprise branded caller ID platform for businesses that need compliance and scale.
                                    </p>
                                </div>
                                <div>
                                    <h4 className="text-sm font-semibold text-theme-primary mb-4">Product</h4>
                                    <ul className="space-y-3">
                                        <li><a href="#features" className="text-sm text-theme-tertiary hover:text-theme-primary transition-colors">Features</a></li>
                                        <li><a href="#" className="text-sm text-theme-tertiary hover:text-theme-primary transition-colors">Pricing</a></li>
                                        <li><a href="#" className="text-sm text-theme-tertiary hover:text-theme-primary transition-colors">API Documentation</a></li>
                                        <li><a href="#" className="text-sm text-theme-tertiary hover:text-theme-primary transition-colors">Integrations</a></li>
                                    </ul>
                                </div>
                                <div>
                                    <h4 className="text-sm font-semibold text-theme-primary mb-4">Company</h4>
                                    <ul className="space-y-3">
                                        <li><a href="#" className="text-sm text-theme-tertiary hover:text-theme-primary transition-colors">About</a></li>
                                        <li><a href="#" className="text-sm text-theme-tertiary hover:text-theme-primary transition-colors">Blog</a></li>
                                        <li><a href="#" className="text-sm text-theme-tertiary hover:text-theme-primary transition-colors">Careers</a></li>
                                        <li><a href="mailto:sales@brandcall.io" className="text-sm text-theme-tertiary hover:text-theme-primary transition-colors">Contact</a></li>
                                    </ul>
                                </div>
                                <div>
                                    <h4 className="text-sm font-semibold text-theme-primary mb-4">Legal</h4>
                                    <ul className="space-y-3">
                                        <li><a href="#" className="text-sm text-theme-tertiary hover:text-theme-primary transition-colors">Privacy Policy</a></li>
                                        <li><a href="#" className="text-sm text-theme-tertiary hover:text-theme-primary transition-colors">Terms of Service</a></li>
                                        <li><a href="#" className="text-sm text-theme-tertiary hover:text-theme-primary transition-colors">Security</a></li>
                                        <li><a href="#" className="text-sm text-theme-tertiary hover:text-theme-primary transition-colors">Compliance</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div className="pt-8 border-t border-theme-primary flex flex-col sm:flex-row items-center justify-between gap-4">
                                <p className="text-sm text-theme-muted">
                                    © 2026 BrandCall. All rights reserved.
                                </p>
                                <div className="flex items-center gap-6">
                                    <a href="#" className="text-theme-muted hover:text-theme-primary transition-colors">
                                        <span className="sr-only">LinkedIn</span>
                                        <svg className="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                                        </svg>
                                    </a>
                                    <a href="#" className="text-theme-muted hover:text-theme-primary transition-colors">
                                        <span className="sr-only">Twitter</span>
                                        <svg className="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </footer>
                </div>
            </div>
        </div>
    );
}
