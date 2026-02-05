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
    globe: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <circle cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="1.5"/>
            <path d="M2 12h20M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z" stroke="currentColor" strokeWidth="1.5"/>
        </svg>
    ),
    zap: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
        </svg>
    ),
    database: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <ellipse cx="12" cy="5" rx="9" ry="3" stroke="currentColor" strokeWidth="1.5"/>
            <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3" stroke="currentColor" strokeWidth="1.5"/>
            <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5" stroke="currentColor" strokeWidth="1.5"/>
        </svg>
    ),
    refresh: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <path d="M1 4v6h6M23 20v-6h-6" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
            <path d="M20.49 9A9 9 0 005.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 013.51 15" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
        </svg>
    ),
    settings: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <circle cx="12" cy="12" r="3" stroke="currentColor" strokeWidth="1.5"/>
            <path d="M12 1v2m0 18v2m11-11h-2M3 12H1m18.07-7.07l-1.41 1.41M6.34 17.66l-1.41 1.41m0-14.14l1.41 1.41m11.32 11.32l1.41 1.41" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round"/>
        </svg>
    ),
};

interface Feature {
    icon: JSX.Element;
    title: string;
    description: string;
    benefits: string[];
    technicalSpecs: string[];
}

const features: Feature[] = [
    {
        icon: Icons.shield,
        title: 'STIR/SHAKEN Attestation',
        description: 'Full A-level attestation ensures your calls are verified and trusted. Our platform automatically handles certificate management, signing, and compliance with FCC regulations for all outbound calls.',
        benefits: [
            'Highest trust level (A attestation) for legitimate business calls',
            'Automatic FCC STIR/SHAKEN compliance',
            'Reduced spam flagging and call blocking',
            'Increased answer rates up to 30%',
            'Real-time attestation status monitoring',
        ],
        technicalSpecs: [
            'SHA-256 digital signatures',
            'X.509 certificate management',
            'Real-time SIP header injection',
            'Automatic certificate rotation',
            'Multi-carrier support',
        ],
    },
    {
        icon: Icons.brandIdentity,
        title: 'Rich Call Data (RCD)',
        description: 'Display your company logo, verified business name, and call purpose directly on the recipient\'s phone screen. Make every call a branded experience that builds trust before they answer.',
        benefits: [
            'Company logo displayed on incoming call screen',
            'Verified business name increases recognition',
            'Call reason/purpose reduces uncertainty',
            'Supports iOS and Android devices',
            'Works with major carriers nationwide',
        ],
        technicalSpecs: [
            'CNAM integration with Tier-1 carriers',
            'Logo optimization for all screen sizes',
            'Real-time carrier database updates',
            'Fallback display for unsupported devices',
            'JSON-LD structured data support',
        ],
    },
    {
        icon: Icons.analytics,
        title: 'Number Reputation Management',
        description: 'Proactively monitor and manage your phone number reputation across all major carrier databases. Get alerts before numbers get flagged and automated remediation when issues arise.',
        benefits: [
            'Real-time monitoring across 200+ carrier databases',
            'Automated alerts for reputation changes',
            'One-click remediation requests',
            'Historical reputation tracking and trends',
            'Proactive spam label prevention',
        ],
        technicalSpecs: [
            'Integration with Hiya, TNS, First Orion',
            'Nomorobo and YouMail monitoring',
            'Carrier-specific reputation APIs',
            'Batch number scanning (up to 10K/hour)',
            'Webhook alerts for status changes',
        ],
    },
    {
        icon: Icons.lock,
        title: 'Enterprise Security',
        description: 'Bank-level security with SOC 2 Type II certification, end-to-end encryption, and comprehensive access controls. Built for enterprises with strict compliance requirements.',
        benefits: [
            'SOC 2 Type II certified infrastructure',
            'End-to-end TLS 1.3 encryption',
            'Role-based access control (RBAC)',
            'Complete audit logging',
            'SSO/SAML integration support',
        ],
        technicalSpecs: [
            'AES-256 encryption at rest',
            'TLS 1.3 in transit',
            'Multi-factor authentication',
            '99.99% uptime SLA',
            'Geo-redundant data centers',
        ],
    },
    {
        icon: Icons.api,
        title: 'API-First Platform',
        description: 'Comprehensive RESTful APIs with extensive documentation, SDKs, and webhooks. Integrate branded calling into your existing systems in minutes, not months.',
        benefits: [
            'RESTful APIs with OpenAPI specs',
            'SDKs for Python, Node.js, PHP, Java',
            'Real-time webhooks for all events',
            'Sandbox environment for testing',
            'Rate limiting with burst capacity',
        ],
        technicalSpecs: [
            'OAuth 2.0 authentication',
            'JSON request/response format',
            '10,000 requests/minute rate limit',
            'Webhook retry with exponential backoff',
            'GraphQL API (beta)',
        ],
    },
    {
        icon: Icons.users,
        title: 'Multi-Tenant Support',
        description: 'Manage multiple brands, campaigns, and organizational hierarchies with ease. Perfect for enterprises, BPOs, and agencies managing calling operations for multiple clients.',
        benefits: [
            'Unlimited brands and campaigns',
            'Hierarchical organization structure',
            'Delegated administration',
            'Brand-specific caller ID profiles',
            'Consolidated billing or per-tenant',
        ],
        technicalSpecs: [
            'Tenant isolation with data segregation',
            'Custom subdomain support',
            'White-label portal option',
            'API key scoping by tenant',
            'Cross-tenant reporting (admin)',
        ],
    },
];

interface ComparisonRow {
    feature: string;
    brandcall: string | boolean;
    traditional: string | boolean;
    diy: string | boolean;
}

const comparisonData: ComparisonRow[] = [
    { feature: 'STIR/SHAKEN A-Level Attestation', brandcall: true, traditional: false, diy: 'Partial' },
    { feature: 'Rich Call Data (Logo + Name)', brandcall: true, traditional: false, diy: false },
    { feature: 'Real-time Reputation Monitoring', brandcall: true, traditional: 'Manual', diy: false },
    { feature: 'Automated Remediation', brandcall: true, traditional: false, diy: false },
    { feature: 'Multi-Carrier Support', brandcall: '100+ carriers', traditional: '1-3 carriers', diy: 'Varies' },
    { feature: 'API Integration', brandcall: 'Full REST API', traditional: 'Limited', diy: 'Custom' },
    { feature: 'Setup Time', brandcall: '< 24 hours', traditional: '2-4 weeks', diy: '3-6 months' },
    { feature: 'SOC 2 Certified', brandcall: true, traditional: 'Varies', diy: false },
    { feature: 'Enterprise SSO', brandcall: true, traditional: false, diy: 'Custom' },
    { feature: 'White-Label Option', brandcall: true, traditional: false, diy: 'N/A' },
];

export default function Features({ auth }: PageProps) {
    const [isHeaderVisible, setIsHeaderVisible] = useState(true);
    const [lastScrollY, setLastScrollY] = useState(0);
    const [activeFeature, setActiveFeature] = useState(0);

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

    return (
        <div>
            <Head title="Features - BrandCall Enterprise Caller ID Platform" />
            
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
                                <Link href={route('features')} className="text-sm font-medium text-brand-500 transition-colors">Features</Link>
                                <Link href={route('solutions')} className="text-sm font-medium text-theme-tertiary hover:text-theme-primary transition-colors">Solutions</Link>
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
                                <p className="text-sm font-medium text-brand-500 mb-3">Platform Features</p>
                                <h1 className="text-3xl sm:text-4xl lg:text-5xl font-bold text-theme-primary mb-6">
                                    Enterprise-Grade Caller ID Management
                                </h1>
                                <p className="text-lg text-theme-secondary mb-8">
                                    Everything you need to transform your outbound calling operations. Built for scale, 
                                    designed for compliance, and engineered for results.
                                </p>
                                <div className="flex flex-col sm:flex-row gap-4">
                                    <Link 
                                        href={route('register')} 
                                        className="inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-medium text-white bg-brand-600 rounded-md hover:bg-brand-500 transition-colors"
                                    >
                                        Start Free Trial
                                        <div className="h-4 w-4">{Icons.arrow}</div>
                                    </Link>
                                    <Link 
                                        href={route('pricing')}
                                        className="inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-medium text-theme-secondary border border-theme-primary rounded-md hover:bg-theme-tertiary transition-colors"
                                    >
                                        View Pricing
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </section>

                    {/* Feature Overview Grid */}
                    <section className="py-16 border-t border-theme-primary">
                        <div className="max-w-7xl mx-auto px-6 sm:px-8">
                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                {features.map((feature, index) => (
                                    <button
                                        key={index}
                                        onClick={() => setActiveFeature(index)}
                                        className={`p-6 rounded-lg text-left transition-all ${
                                            activeFeature === index 
                                                ? 'bg-brand-600/10 border-2 border-brand-500' 
                                                : 'bg-theme-secondary border border-theme-primary hover:border-theme-secondary'
                                        }`}
                                    >
                                        <div className={`flex h-10 w-10 items-center justify-center rounded-md mb-4 ${
                                            activeFeature === index 
                                                ? 'bg-brand-600 text-white' 
                                                : 'bg-brand-600/10 text-brand-500'
                                        }`}>
                                            <div className="h-6 w-6">{feature.icon}</div>
                                        </div>
                                        <h3 className="text-base font-semibold text-theme-primary mb-2">
                                            {feature.title}
                                        </h3>
                                        <p className="text-sm text-theme-tertiary line-clamp-2">
                                            {feature.description}
                                        </p>
                                    </button>
                                ))}
                            </div>
                        </div>
                    </section>

                    {/* Feature Deep Dive */}
                    <section className="py-16 bg-theme-secondary border-t border-theme-primary">
                        <div className="max-w-7xl mx-auto px-6 sm:px-8">
                            <div className="grid lg:grid-cols-2 gap-12 items-start">
                                <div>
                                    <div className="flex h-14 w-14 items-center justify-center rounded-lg bg-brand-600 text-white mb-6">
                                        <div className="h-8 w-8">{features[activeFeature].icon}</div>
                                    </div>
                                    <h2 className="text-2xl sm:text-3xl font-semibold text-theme-primary mb-4">
                                        {features[activeFeature].title}
                                    </h2>
                                    <p className="text-base text-theme-secondary mb-8 leading-relaxed">
                                        {features[activeFeature].description}
                                    </p>

                                    {/* Screenshot Placeholder */}
                                    <div className="aspect-video bg-slate-800 rounded-lg border border-theme-primary flex items-center justify-center mb-8">
                                        <div className="text-center">
                                            <div className="h-12 w-12 text-theme-muted mx-auto mb-2">
                                                {features[activeFeature].icon}
                                            </div>
                                            <p className="text-sm text-theme-muted">Feature Screenshot</p>
                                        </div>
                                    </div>
                                </div>

                                <div className="space-y-8">
                                    {/* Benefits */}
                                    <div className="p-6 rounded-lg bg-theme-primary border border-theme-primary">
                                        <h3 className="text-lg font-semibold text-theme-primary mb-4 flex items-center gap-2">
                                            <div className="h-5 w-5 text-green-500">{Icons.check}</div>
                                            Key Benefits
                                        </h3>
                                        <ul className="space-y-3">
                                            {features[activeFeature].benefits.map((benefit, idx) => (
                                                <li key={idx} className="flex items-start gap-3">
                                                    <div className="h-5 w-5 text-green-500 flex-shrink-0 mt-0.5">
                                                        {Icons.check}
                                                    </div>
                                                    <span className="text-sm text-theme-secondary">{benefit}</span>
                                                </li>
                                            ))}
                                        </ul>
                                    </div>

                                    {/* Technical Specs */}
                                    <div className="p-6 rounded-lg bg-theme-primary border border-theme-primary">
                                        <h3 className="text-lg font-semibold text-theme-primary mb-4 flex items-center gap-2">
                                            <div className="h-5 w-5 text-brand-500">{Icons.settings}</div>
                                            Technical Specifications
                                        </h3>
                                        <ul className="space-y-3">
                                            {features[activeFeature].technicalSpecs.map((spec, idx) => (
                                                <li key={idx} className="flex items-start gap-3">
                                                    <div className="h-5 w-5 text-brand-500 flex-shrink-0 mt-0.5">
                                                        {Icons.zap}
                                                    </div>
                                                    <span className="text-sm text-theme-secondary">{spec}</span>
                                                </li>
                                            ))}
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    {/* Comparison Table */}
                    <section className="py-16 lg:py-24 border-t border-theme-primary">
                        <div className="max-w-7xl mx-auto px-6 sm:px-8">
                            <div className="max-w-2xl mb-12">
                                <p className="text-sm font-medium text-brand-500 mb-3">Comparison</p>
                                <h2 className="text-2xl sm:text-3xl font-semibold text-theme-primary mb-4">
                                    Why Choose BrandCall?
                                </h2>
                                <p className="text-base text-theme-secondary">
                                    See how BrandCall compares to traditional caller ID solutions and DIY approaches.
                                </p>
                            </div>

                            <div className="overflow-x-auto">
                                <table className="w-full min-w-[640px]">
                                    <thead>
                                        <tr className="border-b border-theme-primary">
                                            <th className="text-left py-4 px-4 text-sm font-medium text-theme-muted">Feature</th>
                                            <th className="text-center py-4 px-4 text-sm font-medium text-brand-500">BrandCall</th>
                                            <th className="text-center py-4 px-4 text-sm font-medium text-theme-muted">Traditional CNAM</th>
                                            <th className="text-center py-4 px-4 text-sm font-medium text-theme-muted">DIY Solution</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {comparisonData.map((row, idx) => (
                                            <tr key={idx} className="border-b border-theme-primary">
                                                <td className="py-4 px-4 text-sm text-theme-secondary">{row.feature}</td>
                                                <td className="text-center py-4 px-4">
                                                    {typeof row.brandcall === 'boolean' ? (
                                                        row.brandcall ? (
                                                            <div className="h-5 w-5 text-green-500 mx-auto">{Icons.check}</div>
                                                        ) : (
                                                            <span className="text-theme-muted">—</span>
                                                        )
                                                    ) : (
                                                        <span className="text-sm text-green-500 font-medium">{row.brandcall}</span>
                                                    )}
                                                </td>
                                                <td className="text-center py-4 px-4">
                                                    {typeof row.traditional === 'boolean' ? (
                                                        row.traditional ? (
                                                            <div className="h-5 w-5 text-green-500 mx-auto">{Icons.check}</div>
                                                        ) : (
                                                            <span className="text-theme-muted">—</span>
                                                        )
                                                    ) : (
                                                        <span className="text-sm text-theme-tertiary">{row.traditional}</span>
                                                    )}
                                                </td>
                                                <td className="text-center py-4 px-4">
                                                    {typeof row.diy === 'boolean' ? (
                                                        row.diy ? (
                                                            <div className="h-5 w-5 text-green-500 mx-auto">{Icons.check}</div>
                                                        ) : (
                                                            <span className="text-theme-muted">—</span>
                                                        )
                                                    ) : (
                                                        <span className="text-sm text-theme-tertiary">{row.diy}</span>
                                                    )}
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </section>

                    {/* CTA Section */}
                    <section className="py-16 sm:py-20 lg:py-24 border-t border-theme-primary bg-theme-secondary">
                        <div className="max-w-3xl mx-auto px-6 sm:px-8 text-center">
                            <h2 className="text-2xl sm:text-3xl lg:text-4xl font-semibold text-theme-primary mb-4">
                                Ready to see these features in action?
                            </h2>
                            <p className="text-base lg:text-lg text-theme-secondary max-w-xl mx-auto mb-8">
                                Schedule a personalized demo and see how BrandCall can transform your calling operations.
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
