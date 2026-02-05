import { PageProps } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import { useTheme } from '@/Contexts/ThemeContext';
import ThemeToggle from '@/Components/ThemeToggle';

// Custom SVG Icons
const Icons = {
    phone: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
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
            <path d="M9 12l2 2 4-4" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
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
    headphones: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <path d="M3 18v-6a9 9 0 0118 0v6" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
            <path d="M21 19a2 2 0 01-2 2h-1a2 2 0 01-2-2v-3a2 2 0 012-2h3v5zM3 19a2 2 0 002 2h1a2 2 0 002-2v-3a2 2 0 00-2-2H3v5z" stroke="currentColor" strokeWidth="1.5"/>
        </svg>
    ),
    stethoscope: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <path d="M4.8 2.3A.3.3 0 105 2.7V5a3 3 0 006 0V2.7a.3.3 0 01.3-.3h.4a.3.3 0 01.3.3V5a5 5 0 01-10 0V2.7a.3.3 0 01.3-.3h.5z" stroke="currentColor" strokeWidth="1.5"/>
            <path d="M8 5a1 1 0 01-1 1H5a1 1 0 01-1-1M8 15v4a3 3 0 106 0v-4" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round"/>
            <circle cx="17" cy="15" r="3" stroke="currentColor" strokeWidth="1.5"/>
        </svg>
    ),
    bank: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <path d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11M20 10v11M8 14v3M12 14v3M16 14v3" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
        </svg>
    ),
    umbrella: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <path d="M23 12a11.05 11.05 0 00-22 0zm-5 7a3 3 0 01-6 0v-7" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
        </svg>
    ),
    alertCircle: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <circle cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="1.5"/>
            <path d="M12 8v4M12 16h.01" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round"/>
        </svg>
    ),
    trendingUp: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <path d="M23 6l-9.5 9.5-5-5L1 18" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
            <path d="M17 6h6v6" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
        </svg>
    ),
};

interface Solution {
    id: string;
    icon: JSX.Element;
    title: string;
    subtitle: string;
    description: string;
    painPoints: {
        title: string;
        description: string;
    }[];
    solutions: {
        title: string;
        description: string;
    }[];
    stats: {
        value: string;
        label: string;
    }[];
    compliance: string[];
    ctaText: string;
}

const solutions: Solution[] = [
    {
        id: 'contact-centers',
        icon: Icons.headphones,
        title: 'Contact Centers',
        subtitle: 'Increase connection rates and agent productivity',
        description: 'Transform your contact center operations with branded calling. Reduce call abandonment, increase answer rates, and improve customer satisfaction scores across all outbound campaigns.',
        painPoints: [
            {
                title: 'Low Answer Rates',
                description: 'Calls from unknown numbers are ignored 87% of the time, killing your agent productivity and campaign ROI.',
            },
            {
                title: 'Spam Flagging',
                description: 'High-volume dialing triggers spam flags, causing numbers to get blocked by carriers.',
            },
            {
                title: 'Compliance Risk',
                description: 'STIR/SHAKEN requirements create technical overhead and compliance burden.',
            },
            {
                title: 'Brand Reputation',
                description: 'Negative caller ID experiences damage customer relationships before conversations begin.',
            },
        ],
        solutions: [
            {
                title: 'Branded Caller ID',
                description: 'Display your company name and logo on every outbound call, increasing recognition and trust.',
            },
            {
                title: 'Number Rotation',
                description: 'Intelligent number pool management prevents spam flagging while maintaining call quality.',
            },
            {
                title: 'Real-time Analytics',
                description: 'Monitor answer rates, flag status, and reputation across all numbers in one dashboard.',
            },
            {
                title: 'API Integration',
                description: 'Seamlessly integrate with Five9, NICE, Genesys, and other leading CCaaS platforms.',
            },
        ],
        stats: [
            { value: '32%', label: 'Higher Answer Rates' },
            { value: '45%', label: 'Reduced Call Abandonment' },
            { value: '2.5x', label: 'Agent Productivity Boost' },
            { value: '60%', label: 'Fewer Spam Flags' },
        ],
        compliance: ['TCPA', 'STIR/SHAKEN', 'DNC Lists', 'State Regulations'],
        ctaText: 'Transform Your Contact Center',
    },
    {
        id: 'healthcare',
        icon: Icons.stethoscope,
        title: 'Healthcare',
        subtitle: 'HIPAA-compliant patient communication',
        description: 'Improve patient engagement and reduce no-shows with verified, branded calling. Our HIPAA-compliant platform ensures secure communication while displaying trusted provider identification.',
        painPoints: [
            {
                title: 'Missed Appointments',
                description: 'Patients ignore reminder calls from unknown numbers, leading to costly no-shows and care gaps.',
            },
            {
                title: 'HIPAA Compliance',
                description: 'Voice communication requires strict security controls and audit capabilities.',
            },
            {
                title: 'Care Coordination',
                description: 'Patients don\'t recognize calls from specialists, labs, or billing departments.',
            },
            {
                title: 'Emergency Contact',
                description: 'Critical test results and urgent communications go unanswered.',
            },
        ],
        solutions: [
            {
                title: 'Verified Provider ID',
                description: 'Display your practice name, logo, and NPI verification for instant patient recognition.',
            },
            {
                title: 'HIPAA-Compliant Infrastructure',
                description: 'End-to-end encryption, BAAs, and complete audit logging for all patient communications.',
            },
            {
                title: 'EHR Integration',
                description: 'Connect with Epic, Cerner, and other EHR systems for automated outreach workflows.',
            },
            {
                title: 'Multi-Location Support',
                description: 'Manage caller ID across all practice locations with centralized administration.',
            },
        ],
        stats: [
            { value: '28%', label: 'Reduction in No-Shows' },
            { value: '41%', label: 'Higher Patient Answer Rate' },
            { value: '100%', label: 'HIPAA Compliant' },
            { value: '$2.1M', label: 'Avg. Annual Savings' },
        ],
        compliance: ['HIPAA', 'HITECH', 'STIR/SHAKEN', 'State Privacy Laws'],
        ctaText: 'Improve Patient Engagement',
    },
    {
        id: 'financial-services',
        icon: Icons.bank,
        title: 'Financial Services',
        subtitle: 'Build trust with verified financial communications',
        description: 'Enhance customer trust and security with branded calling for collections, fraud alerts, and account notifications. Our platform meets stringent financial compliance requirements.',
        painPoints: [
            {
                title: 'Fraud Impersonation',
                description: 'Customers are wary of calls claiming to be from their bank due to widespread phone fraud.',
            },
            {
                title: 'Collection Contact Rates',
                description: 'Delinquent account holders avoid unknown numbers, extending collection cycles.',
            },
            {
                title: 'Time-Sensitive Alerts',
                description: 'Fraud alerts and security notifications go unanswered, leading to preventable losses.',
            },
            {
                title: 'Regulatory Scrutiny',
                description: 'Financial regulators demand robust call documentation and compliance evidence.',
            },
        ],
        solutions: [
            {
                title: 'Verified Institution Identity',
                description: 'Display your bank or credit union\'s verified name and logo to distinguish from fraudsters.',
            },
            {
                title: 'Fraud Alert Prioritization',
                description: 'Special caller ID treatment for security-critical communications.',
            },
            {
                title: 'Complete Audit Trail',
                description: 'Every call attempt, connection, and outcome logged for regulatory compliance.',
            },
            {
                title: 'Core Banking Integration',
                description: 'Connect with FIS, Fiserv, Jack Henry, and other core systems.',
            },
        ],
        stats: [
            { value: '35%', label: 'Higher Contact Rates' },
            { value: '22%', label: 'Faster Collections' },
            { value: '67%', label: 'Fraud Alert Response' },
            { value: 'SOC 2', label: 'Type II Certified' },
        ],
        compliance: ['GLBA', 'FDCPA', 'CFPB Rules', 'STIR/SHAKEN', 'State Regulations'],
        ctaText: 'Strengthen Customer Trust',
    },
    {
        id: 'insurance',
        icon: Icons.umbrella,
        title: 'Insurance',
        subtitle: 'Improve policyholder engagement and claims communication',
        description: 'Increase policyholder satisfaction and streamline claims communication with branded calling. Ensure critical policy updates and claims information reach your customers.',
        painPoints: [
            {
                title: 'Policy Lapse Prevention',
                description: 'Payment reminder calls go unanswered, leading to unintended policy lapses.',
            },
            {
                title: 'Claims Communication',
                description: 'Adjusters struggle to reach claimants, delaying settlements and reducing satisfaction.',
            },
            {
                title: 'Agent Recognition',
                description: 'Calls from independent agents aren\'t associated with the carrier\'s brand.',
            },
            {
                title: 'Renewal Campaigns',
                description: 'Outbound renewal campaigns suffer from low connection rates.',
            },
        ],
        solutions: [
            {
                title: 'Carrier Branded Calling',
                description: 'Display carrier name and logo for all policyholder communications.',
            },
            {
                title: 'Claims Caller ID',
                description: 'Special identification for claims-related calls to improve response rates.',
            },
            {
                title: 'Agent Network Support',
                description: 'Extend branded calling to independent agents and brokers.',
            },
            {
                title: 'Policy Admin Integration',
                description: 'Connect with Guidewire, Duck Creek, and other policy admin systems.',
            },
        ],
        stats: [
            { value: '29%', label: 'Higher Answer Rates' },
            { value: '18%', label: 'Reduced Policy Lapses' },
            { value: '3.2x', label: 'Claims Contact Success' },
            { value: '94%', label: 'Policyholder Satisfaction' },
        ],
        compliance: ['NAIC Model Laws', 'State Insurance Regs', 'STIR/SHAKEN', 'TCPA'],
        ctaText: 'Engage Your Policyholders',
    },
];

export default function Solutions({ auth }: PageProps) {
    const [isHeaderVisible, setIsHeaderVisible] = useState(true);
    const [lastScrollY, setLastScrollY] = useState(0);
    const [activeSolution, setActiveSolution] = useState('contact-centers');

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

    const currentSolution = solutions.find(s => s.id === activeSolution) || solutions[0];

    return (
        <div>
            <Head title="Industry Solutions - BrandCall Enterprise Caller ID" />
            
            <div className="relative min-h-screen bg-theme-primary transition-colors duration-300">
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
                                <Link href={route('features')} className="text-sm font-medium text-theme-tertiary hover:text-theme-primary transition-colors">Features</Link>
                                <Link href={route('solutions')} className="text-sm font-medium text-brand-500 transition-colors">Solutions</Link>
                                <Link href={route('compliance')} className="text-sm font-medium text-theme-tertiary hover:text-theme-primary transition-colors">Compliance</Link>
                                <Link href={route('pricing')} className="text-sm font-medium text-theme-tertiary hover:text-theme-primary transition-colors">Pricing</Link>
                            </div>

                            <div className="flex items-center gap-2 sm:gap-4">
                                <ThemeToggle />

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
                    <section className="py-16 sm:py-20 lg:py-24">
                        <div className="max-w-7xl mx-auto px-6 sm:px-8">
                            <div className="max-w-3xl">
                                <p className="text-sm font-medium text-brand-500 mb-3">Industry Solutions</p>
                                <h1 className="text-3xl sm:text-4xl lg:text-5xl font-bold text-theme-primary mb-6">
                                    Built for Regulated Industries
                                </h1>
                                <p className="text-lg text-theme-secondary mb-8">
                                    Purpose-built branded calling solutions for industries with strict compliance requirements, 
                                    high call volumes, and critical customer communication needs.
                                </p>
                            </div>
                        </div>
                    </section>

                    {/* Industry Selector */}
                    <section className="border-t border-theme-primary">
                        <div className="max-w-7xl mx-auto px-6 sm:px-8">
                            <div className="flex overflow-x-auto py-4 gap-2 -mx-6 px-6 sm:mx-0 sm:px-0">
                                {solutions.map((solution) => (
                                    <button
                                        key={solution.id}
                                        onClick={() => setActiveSolution(solution.id)}
                                        className={`flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-all ${
                                            activeSolution === solution.id
                                                ? 'bg-brand-600 text-white'
                                                : 'bg-theme-secondary text-theme-tertiary hover:bg-theme-tertiary hover:text-theme-primary border border-theme-primary'
                                        }`}
                                    >
                                        <div className="h-4 w-4">{solution.icon}</div>
                                        {solution.title}
                                    </button>
                                ))}
                            </div>
                        </div>
                    </section>

                    {/* Solution Detail */}
                    <section className="py-16 bg-theme-secondary border-t border-theme-primary">
                        <div className="max-w-7xl mx-auto px-6 sm:px-8">
                            {/* Header */}
                            <div className="flex items-start gap-4 mb-8">
                                <div className="flex h-14 w-14 items-center justify-center rounded-lg bg-brand-600 text-white flex-shrink-0">
                                    <div className="h-8 w-8">{currentSolution.icon}</div>
                                </div>
                                <div>
                                    <h2 className="text-2xl sm:text-3xl font-semibold text-theme-primary mb-2">
                                        {currentSolution.title}
                                    </h2>
                                    <p className="text-lg text-theme-secondary">
                                        {currentSolution.subtitle}
                                    </p>
                                </div>
                            </div>

                            <p className="text-base text-theme-secondary mb-12 max-w-3xl leading-relaxed">
                                {currentSolution.description}
                            </p>

                            {/* Stats */}
                            <div className="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
                                {currentSolution.stats.map((stat, idx) => (
                                    <div key={idx} className="p-6 rounded-lg bg-theme-primary border border-theme-primary text-center">
                                        <div className="text-3xl font-bold text-brand-500 mb-1">
                                            {stat.value}
                                        </div>
                                        <div className="text-sm text-theme-muted">
                                            {stat.label}
                                        </div>
                                    </div>
                                ))}
                            </div>

                            {/* Pain Points & Solutions */}
                            <div className="grid lg:grid-cols-2 gap-8 mb-12">
                                {/* Pain Points */}
                                <div>
                                    <h3 className="text-lg font-semibold text-theme-primary mb-6 flex items-center gap-2">
                                        <div className="h-5 w-5 text-red-500">{Icons.alertCircle}</div>
                                        Industry Challenges
                                    </h3>
                                    <div className="space-y-4">
                                        {currentSolution.painPoints.map((point, idx) => (
                                            <div key={idx} className="p-4 rounded-lg bg-red-500/5 border border-red-500/20">
                                                <h4 className="text-sm font-semibold text-theme-primary mb-1">
                                                    {point.title}
                                                </h4>
                                                <p className="text-sm text-theme-tertiary">
                                                    {point.description}
                                                </p>
                                            </div>
                                        ))}
                                    </div>
                                </div>

                                {/* Solutions */}
                                <div>
                                    <h3 className="text-lg font-semibold text-theme-primary mb-6 flex items-center gap-2">
                                        <div className="h-5 w-5 text-green-500">{Icons.check}</div>
                                        BrandCall Solutions
                                    </h3>
                                    <div className="space-y-4">
                                        {currentSolution.solutions.map((solution, idx) => (
                                            <div key={idx} className="p-4 rounded-lg bg-green-500/5 border border-green-500/20">
                                                <h4 className="text-sm font-semibold text-theme-primary mb-1">
                                                    {solution.title}
                                                </h4>
                                                <p className="text-sm text-theme-tertiary">
                                                    {solution.description}
                                                </p>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            </div>

                            {/* Compliance */}
                            <div className="p-6 rounded-lg bg-theme-primary border border-theme-primary mb-8">
                                <h3 className="text-lg font-semibold text-theme-primary mb-4 flex items-center gap-2">
                                    <div className="h-5 w-5 text-brand-500">{Icons.shieldCheck}</div>
                                    Compliance Coverage
                                </h3>
                                <div className="flex flex-wrap gap-2">
                                    {currentSolution.compliance.map((item, idx) => (
                                        <span 
                                            key={idx}
                                            className="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-brand-500 bg-brand-600/10 rounded-full border border-brand-500/20"
                                        >
                                            <div className="h-4 w-4">{Icons.check}</div>
                                            {item}
                                        </span>
                                    ))}
                                </div>
                            </div>

                            {/* CTA */}
                            <div className="flex flex-col sm:flex-row gap-4">
                                <Link 
                                    href={route('register')} 
                                    className="inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-medium text-white bg-brand-600 rounded-md hover:bg-brand-500 transition-colors"
                                >
                                    {currentSolution.ctaText}
                                    <div className="h-4 w-4">{Icons.arrow}</div>
                                </Link>
                                <a 
                                    href="mailto:sales@brandcall.io"
                                    className="inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-medium text-theme-secondary border border-theme-primary rounded-md hover:bg-theme-tertiary transition-colors"
                                >
                                    Speak with Industry Expert
                                </a>
                            </div>
                        </div>
                    </section>

                    {/* All Industries Overview */}
                    <section className="py-16 lg:py-24 border-t border-theme-primary">
                        <div className="max-w-7xl mx-auto px-6 sm:px-8">
                            <div className="max-w-2xl mb-12">
                                <h2 className="text-2xl sm:text-3xl font-semibold text-theme-primary mb-4">
                                    Trusted Across Industries
                                </h2>
                                <p className="text-base text-theme-secondary">
                                    See how organizations in every industry use BrandCall to improve their calling operations.
                                </p>
                            </div>

                            <div className="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                                {solutions.map((solution) => (
                                    <button
                                        key={solution.id}
                                        onClick={() => {
                                            setActiveSolution(solution.id);
                                            window.scrollTo({ top: 400, behavior: 'smooth' });
                                        }}
                                        className={`p-6 rounded-lg text-left transition-all ${
                                            activeSolution === solution.id
                                                ? 'bg-brand-600/10 border-2 border-brand-500'
                                                : 'bg-theme-secondary border border-theme-primary hover:border-theme-secondary'
                                        }`}
                                    >
                                        <div className={`h-10 w-10 rounded-lg flex items-center justify-center mb-4 ${
                                            activeSolution === solution.id
                                                ? 'bg-brand-600 text-white'
                                                : 'bg-theme-tertiary text-theme-muted'
                                        }`}>
                                            <div className="h-6 w-6">{solution.icon}</div>
                                        </div>
                                        <h3 className="text-lg font-semibold text-theme-primary mb-2">
                                            {solution.title}
                                        </h3>
                                        <p className="text-sm text-theme-tertiary">
                                            {solution.subtitle}
                                        </p>
                                    </button>
                                ))}
                            </div>
                        </div>
                    </section>

                    {/* CTA Section */}
                    <section className="py-16 sm:py-20 lg:py-24 border-t border-theme-primary bg-theme-secondary">
                        <div className="max-w-3xl mx-auto px-6 sm:px-8 text-center">
                            <h2 className="text-2xl sm:text-3xl lg:text-4xl font-semibold text-theme-primary mb-4">
                                Not seeing your industry?
                            </h2>
                            <p className="text-base lg:text-lg text-theme-secondary max-w-xl mx-auto mb-8">
                                BrandCall works for any organization that makes outbound calls. 
                                Contact us to discuss your specific requirements.
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
                            <div className="flex flex-col sm:flex-row items-center justify-between gap-4">
                                <Link href="/" className="flex items-center gap-2">
                                    <div className="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-600">
                                        <div className="h-4 w-4 text-white">{Icons.phone}</div>
                                    </div>
                                    <span className="text-base font-semibold text-theme-primary">BrandCall</span>
                                </Link>
                                <p className="text-sm text-theme-muted">
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
