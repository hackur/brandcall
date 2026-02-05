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
    check: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <path d="M20 6L9 17l-5-5" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
        </svg>
    ),
    arrow: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <path d="M5 12h14M12 5l7 7-7 7" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
        </svg>
    ),
    lock: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <rect x="3" y="11" width="18" height="11" rx="2" stroke="currentColor" strokeWidth="1.5"/>
            <path d="M7 11V7a5 5 0 0110 0v4" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round"/>
            <circle cx="12" cy="16" r="1" fill="currentColor"/>
        </svg>
    ),
    eye: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
            <circle cx="12" cy="12" r="3" stroke="currentColor" strokeWidth="1.5"/>
        </svg>
    ),
    fileText: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
            <path d="M14 2v6h6M16 13H8M16 17H8M10 9H8" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
        </svg>
    ),
    server: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <rect x="2" y="2" width="20" height="8" rx="2" stroke="currentColor" strokeWidth="1.5"/>
            <rect x="2" y="14" width="20" height="8" rx="2" stroke="currentColor" strokeWidth="1.5"/>
            <circle cx="6" cy="6" r="1" fill="currentColor"/>
            <circle cx="6" cy="18" r="1" fill="currentColor"/>
        </svg>
    ),
    key: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 11-7.778 7.778 5.5 5.5 0 017.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
        </svg>
    ),
    activity: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <path d="M22 12h-4l-3 9L9 3l-3 9H2" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
        </svg>
    ),
    globe: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <circle cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="1.5"/>
            <path d="M2 12h20M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z" stroke="currentColor" strokeWidth="1.5"/>
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
    clock: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <circle cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="1.5"/>
            <path d="M12 6v6l4 2" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round"/>
        </svg>
    ),
    download: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
        </svg>
    ),
    zap: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
        </svg>
    ),
};

interface Certification {
    id: string;
    name: string;
    fullName: string;
    icon: JSX.Element;
    description: string;
    details: string[];
    documentAvailable: boolean;
}

const certifications: Certification[] = [
    {
        id: 'soc2',
        name: 'SOC 2 Type II',
        fullName: 'Service Organization Control 2 Type II',
        icon: Icons.shield,
        description: 'Our SOC 2 Type II certification demonstrates our commitment to maintaining the highest standards of security, availability, and confidentiality.',
        details: [
            'Annual third-party audit by licensed CPA firm',
            'Covers security, availability, and confidentiality trust principles',
            'Continuous monitoring and testing of controls',
            'Detailed examination of operating effectiveness over 12-month period',
            'Report available under NDA for enterprise customers',
        ],
        documentAvailable: true,
    },
    {
        id: 'stir-shaken',
        name: 'STIR/SHAKEN',
        fullName: 'Secure Telephone Identity Revisited / Signature-based Handling of Asserted information using toKENs',
        icon: Icons.phone,
        description: 'Full compliance with FCC STIR/SHAKEN mandate for caller ID authentication. We provide A-level attestation for all verified business calls.',
        details: [
            'A-level attestation (highest trust level) for verified calls',
            'Real-time digital signature of call origin',
            'Automatic certificate management and rotation',
            'Integration with major carriers for end-to-end verification',
            'Compliant with FCC June 2021 mandate',
        ],
        documentAvailable: false,
    },
    {
        id: 'hipaa',
        name: 'HIPAA',
        fullName: 'Health Insurance Portability and Accountability Act',
        icon: Icons.activity,
        description: 'HIPAA-compliant infrastructure for healthcare organizations. We sign Business Associate Agreements and maintain required safeguards for PHI.',
        details: [
            'Business Associate Agreement (BAA) available',
            'Administrative, physical, and technical safeguards',
            'PHI encryption in transit and at rest',
            'Access controls and audit logging',
            'Employee HIPAA training and compliance program',
        ],
        documentAvailable: true,
    },
    {
        id: 'tcpa',
        name: 'TCPA',
        fullName: 'Telephone Consumer Protection Act',
        icon: Icons.users,
        description: 'Tools and features to help you maintain TCPA compliance in your outbound calling campaigns, including consent management and DNC list integration.',
        details: [
            'Consent tracking and management features',
            'Do Not Call (DNC) list integration',
            'Call time restrictions by timezone',
            'Calling frequency limits',
            'Documentation for compliance audits',
        ],
        documentAvailable: false,
    },
];

interface SecurityFeature {
    icon: JSX.Element;
    title: string;
    description: string;
}

const securityFeatures: SecurityFeature[] = [
    {
        icon: Icons.lock,
        title: 'End-to-End Encryption',
        description: 'All data encrypted with AES-256 at rest and TLS 1.3 in transit. We never store unencrypted sensitive data.',
    },
    {
        icon: Icons.key,
        title: 'Access Control',
        description: 'Role-based access control (RBAC) with principle of least privilege. SSO/SAML integration for enterprise identity management.',
    },
    {
        icon: Icons.server,
        title: 'Infrastructure Security',
        description: 'Hosted on SOC 2 certified cloud infrastructure with geo-redundant data centers. Regular penetration testing and vulnerability scanning.',
    },
    {
        icon: Icons.eye,
        title: 'Audit Logging',
        description: 'Comprehensive audit trails for all system access and changes. Logs retained for 1 year with tamper-evident storage.',
    },
    {
        icon: Icons.globe,
        title: 'Network Security',
        description: 'Enterprise-grade firewalls, DDoS protection, and intrusion detection. All API traffic monitored for anomalies.',
    },
    {
        icon: Icons.users,
        title: 'Employee Security',
        description: 'Background checks for all employees. Security awareness training and strict data access policies.',
    },
];

interface DataProtection {
    title: string;
    items: string[];
}

const dataProtection: DataProtection[] = [
    {
        title: 'Data Minimization',
        items: [
            'We only collect data necessary for service delivery',
            'Automatic data purging based on retention policies',
            'No sale of customer data to third parties',
            'Clear data processing agreements',
        ],
    },
    {
        title: 'Data Residency',
        items: [
            'Primary data centers in United States',
            'EU data residency options available',
            'Data never leaves specified regions',
            'Compliant with data sovereignty requirements',
        ],
    },
    {
        title: 'Breach Response',
        items: [
            'Incident response team available 24/7',
            'Notification within 72 hours per GDPR',
            'Documented incident response procedures',
            'Regular tabletop exercises',
        ],
    },
    {
        title: 'Your Rights',
        items: [
            'Access your data at any time',
            'Request data deletion (right to be forgotten)',
            'Data portability in standard formats',
            'Opt-out of non-essential processing',
        ],
    },
];

export default function Compliance({ auth }: PageProps) {
    const [isHeaderVisible, setIsHeaderVisible] = useState(true);
    const [lastScrollY, setLastScrollY] = useState(0);
    const [activeCert, setActiveCert] = useState('soc2');

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

    const currentCert = certifications.find(c => c.id === activeCert) || certifications[0];

    return (
        <div>
            <Head title="Compliance & Security - BrandCall Trust Center" />
            
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
                                <Link href={route('solutions')} className="text-sm font-medium text-theme-tertiary hover:text-theme-primary transition-colors">Solutions</Link>
                                <Link href={route('compliance')} className="text-sm font-medium text-brand-500 transition-colors">Compliance</Link>
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
                                <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-green-500/10 border border-green-500/20 text-green-500 text-sm font-medium mb-6">
                                    <div className="h-4 w-4">{Icons.shield}</div>
                                    Trust Center
                                </div>
                                <h1 className="text-3xl sm:text-4xl lg:text-5xl font-bold text-theme-primary mb-6">
                                    Security & Compliance
                                </h1>
                                <p className="text-lg text-theme-secondary mb-8">
                                    At BrandCall, security isn't an afterthought—it's foundational. We maintain the highest 
                                    standards of compliance and security to protect your data and help you meet your regulatory requirements.
                                </p>
                                <div className="flex flex-wrap gap-3">
                                    {certifications.map((cert) => (
                                        <div 
                                            key={cert.id}
                                            className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-brand-600/10 border border-brand-500/20 text-brand-500 text-sm font-medium"
                                        >
                                            <div className="h-4 w-4">{Icons.check}</div>
                                            {cert.name}
                                        </div>
                                    ))}
                                </div>
                            </div>
                        </div>
                    </section>

                    {/* Certifications */}
                    <section className="py-16 border-t border-theme-primary bg-theme-secondary">
                        <div className="max-w-7xl mx-auto px-6 sm:px-8">
                            <div className="max-w-2xl mb-12">
                                <p className="text-sm font-medium text-brand-500 mb-3">Certifications & Compliance</p>
                                <h2 className="text-2xl sm:text-3xl font-semibold text-theme-primary mb-4">
                                    Industry-Leading Standards
                                </h2>
                                <p className="text-base text-theme-secondary">
                                    We maintain certifications and compliance with the standards that matter most to regulated industries.
                                </p>
                            </div>

                            {/* Certification Tabs */}
                            <div className="flex overflow-x-auto gap-2 mb-8 -mx-6 px-6 sm:mx-0 sm:px-0">
                                {certifications.map((cert) => (
                                    <button
                                        key={cert.id}
                                        onClick={() => setActiveCert(cert.id)}
                                        className={`flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-all ${
                                            activeCert === cert.id
                                                ? 'bg-brand-600 text-white'
                                                : 'bg-theme-primary text-theme-tertiary hover:text-theme-primary border border-theme-primary'
                                        }`}
                                    >
                                        <div className="h-4 w-4">{cert.icon}</div>
                                        {cert.name}
                                    </button>
                                ))}
                            </div>

                            {/* Certification Detail */}
                            <div className="grid lg:grid-cols-2 gap-8">
                                <div className="p-8 rounded-lg bg-theme-primary border border-theme-primary">
                                    <div className="flex items-center gap-4 mb-6">
                                        <div className="flex h-14 w-14 items-center justify-center rounded-lg bg-brand-600 text-white">
                                            <div className="h-8 w-8">{currentCert.icon}</div>
                                        </div>
                                        <div>
                                            <h3 className="text-xl font-semibold text-theme-primary">{currentCert.name}</h3>
                                            <p className="text-sm text-theme-muted">{currentCert.fullName}</p>
                                        </div>
                                    </div>
                                    <p className="text-base text-theme-secondary mb-6 leading-relaxed">
                                        {currentCert.description}
                                    </p>
                                    <ul className="space-y-3">
                                        {currentCert.details.map((detail, idx) => (
                                            <li key={idx} className="flex items-start gap-3">
                                                <div className="h-5 w-5 text-green-500 flex-shrink-0 mt-0.5">
                                                    {Icons.check}
                                                </div>
                                                <span className="text-sm text-theme-secondary">{detail}</span>
                                            </li>
                                        ))}
                                    </ul>
                                    {currentCert.documentAvailable && (
                                        <button className="mt-6 inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-brand-500 bg-brand-600/10 rounded-md hover:bg-brand-600/20 transition-colors">
                                            <div className="h-4 w-4">{Icons.download}</div>
                                            Request Documentation
                                        </button>
                                    )}
                                </div>

                                {/* Compliance Stats */}
                                <div className="grid grid-cols-2 gap-4">
                                    <div className="p-6 rounded-lg bg-theme-primary border border-theme-primary">
                                        <div className="h-10 w-10 text-brand-500 mb-4">{Icons.shield}</div>
                                        <div className="text-3xl font-bold text-theme-primary mb-1">99.99%</div>
                                        <div className="text-sm text-theme-muted">Uptime SLA</div>
                                    </div>
                                    <div className="p-6 rounded-lg bg-theme-primary border border-theme-primary">
                                        <div className="h-10 w-10 text-brand-500 mb-4">{Icons.clock}</div>
                                        <div className="text-3xl font-bold text-theme-primary mb-1">24/7</div>
                                        <div className="text-sm text-theme-muted">Security Monitoring</div>
                                    </div>
                                    <div className="p-6 rounded-lg bg-theme-primary border border-theme-primary">
                                        <div className="h-10 w-10 text-brand-500 mb-4">{Icons.zap}</div>
                                        <div className="text-3xl font-bold text-theme-primary mb-1">&lt;1hr</div>
                                        <div className="text-sm text-theme-muted">Incident Response</div>
                                    </div>
                                    <div className="p-6 rounded-lg bg-theme-primary border border-theme-primary">
                                        <div className="h-10 w-10 text-brand-500 mb-4">{Icons.fileText}</div>
                                        <div className="text-3xl font-bold text-theme-primary mb-1">1 Year</div>
                                        <div className="text-sm text-theme-muted">Log Retention</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    {/* Security Features */}
                    <section className="py-16 lg:py-24 border-t border-theme-primary">
                        <div className="max-w-7xl mx-auto px-6 sm:px-8">
                            <div className="max-w-2xl mb-12">
                                <p className="text-sm font-medium text-brand-500 mb-3">Security Features</p>
                                <h2 className="text-2xl sm:text-3xl font-semibold text-theme-primary mb-4">
                                    Enterprise-Grade Security
                                </h2>
                                <p className="text-base text-theme-secondary">
                                    Multiple layers of security protect your data at every level of our infrastructure.
                                </p>
                            </div>

                            <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                {securityFeatures.map((feature, idx) => (
                                    <div key={idx} className="p-6 rounded-lg bg-theme-secondary border border-theme-primary">
                                        <div className="flex h-10 w-10 items-center justify-center rounded-md bg-brand-600/10 text-brand-500 mb-4">
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

                    {/* Data Protection */}
                    <section className="py-16 lg:py-24 border-t border-theme-primary bg-theme-secondary">
                        <div className="max-w-7xl mx-auto px-6 sm:px-8">
                            <div className="max-w-2xl mb-12">
                                <p className="text-sm font-medium text-brand-500 mb-3">Data Protection</p>
                                <h2 className="text-2xl sm:text-3xl font-semibold text-theme-primary mb-4">
                                    Your Data, Protected
                                </h2>
                                <p className="text-base text-theme-secondary">
                                    We take data protection seriously. Here's how we safeguard your information.
                                </p>
                            </div>

                            <div className="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                                {dataProtection.map((section, idx) => (
                                    <div key={idx} className="p-6 rounded-lg bg-theme-primary border border-theme-primary">
                                        <h3 className="text-base font-semibold text-theme-primary mb-4">
                                            {section.title}
                                        </h3>
                                        <ul className="space-y-3">
                                            {section.items.map((item, iidx) => (
                                                <li key={iidx} className="flex items-start gap-2">
                                                    <div className="h-4 w-4 text-green-500 flex-shrink-0 mt-0.5">
                                                        {Icons.check}
                                                    </div>
                                                    <span className="text-sm text-theme-tertiary">{item}</span>
                                                </li>
                                            ))}
                                        </ul>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </section>

                    {/* Audit Capabilities */}
                    <section className="py-16 lg:py-24 border-t border-theme-primary">
                        <div className="max-w-7xl mx-auto px-6 sm:px-8">
                            <div className="grid lg:grid-cols-2 gap-12 items-center">
                                <div>
                                    <p className="text-sm font-medium text-brand-500 mb-3">Audit Capabilities</p>
                                    <h2 className="text-2xl sm:text-3xl font-semibold text-theme-primary mb-4">
                                        Complete Visibility & Control
                                    </h2>
                                    <p className="text-base text-theme-secondary mb-8 leading-relaxed">
                                        Our comprehensive audit capabilities give you full visibility into system access 
                                        and changes, helping you maintain compliance and investigate incidents.
                                    </p>
                                    <ul className="space-y-4">
                                        {[
                                            'Detailed audit logs for all user actions',
                                            'API access logging with request/response data',
                                            'Authentication and authorization events',
                                            'Configuration change tracking',
                                            'Exportable logs in standard formats (JSON, CSV)',
                                            'Real-time alerts for suspicious activity',
                                            'Integration with SIEM platforms',
                                            'Customizable retention policies',
                                        ].map((item, idx) => (
                                            <li key={idx} className="flex items-start gap-3">
                                                <div className="h-5 w-5 text-green-500 flex-shrink-0 mt-0.5">
                                                    {Icons.check}
                                                </div>
                                                <span className="text-theme-secondary">{item}</span>
                                            </li>
                                        ))}
                                    </ul>
                                </div>
                                <div className="p-8 rounded-lg bg-theme-secondary border border-theme-primary">
                                    {/* Mock Audit Log */}
                                    <div className="flex items-center justify-between mb-4">
                                        <h3 className="text-sm font-semibold text-theme-primary">Audit Log Preview</h3>
                                        <span className="text-xs text-theme-muted">Live Demo</span>
                                    </div>
                                    <div className="space-y-3 font-mono text-xs">
                                        {[
                                            { time: '14:32:15', action: 'user.login', user: 'admin@company.com', status: 'success' },
                                            { time: '14:32:18', action: 'brand.update', user: 'admin@company.com', status: 'success' },
                                            { time: '14:33:01', action: 'api.call', user: 'api-key-***x4f2', status: 'success' },
                                            { time: '14:33:45', action: 'number.add', user: 'john@company.com', status: 'success' },
                                            { time: '14:34:12', action: 'report.export', user: 'admin@company.com', status: 'success' },
                                        ].map((log, idx) => (
                                            <div key={idx} className="flex items-center gap-3 p-2 rounded bg-slate-800/50">
                                                <span className="text-theme-muted">{log.time}</span>
                                                <span className="text-brand-400">{log.action}</span>
                                                <span className="text-theme-tertiary flex-1 truncate">{log.user}</span>
                                                <span className="text-green-500">{log.status}</span>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    {/* CTA Section */}
                    <section className="py-16 sm:py-20 lg:py-24 border-t border-theme-primary bg-theme-secondary">
                        <div className="max-w-3xl mx-auto px-6 sm:px-8 text-center">
                            <h2 className="text-2xl sm:text-3xl lg:text-4xl font-semibold text-theme-primary mb-4">
                                Questions about our security practices?
                            </h2>
                            <p className="text-base lg:text-lg text-theme-secondary max-w-xl mx-auto mb-8">
                                Our security team is happy to answer questions and provide documentation 
                                for your vendor security review.
                            </p>
                            <div className="flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4">
                                <a 
                                    href="mailto:security@brandcall.io"
                                    className="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium text-white bg-brand-600 rounded-md hover:bg-brand-500 transition-colors"
                                >
                                    Contact Security Team
                                    <div className="h-4 w-4">{Icons.arrow}</div>
                                </a>
                                <Link 
                                    href={route('register')}
                                    className="inline-flex items-center px-6 py-3 text-sm font-medium text-theme-secondary border border-theme-primary rounded-md hover:bg-theme-tertiary transition-colors"
                                >
                                    Request Demo
                                </Link>
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
