import { Head, Link } from '@inertiajs/react';
import MarketingLayout from '@/Layouts/MarketingLayout';

const Icons = {
    shield: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
            <path d="M9 12l2 2 4-4" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
        </svg>
    ),
    lock: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <rect x="3" y="11" width="18" height="11" rx="2" stroke="currentColor" strokeWidth="1.5"/>
            <path d="M7 11V7a5 5 0 0110 0v4" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round"/>
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
    file: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
            <path d="M14 2v6h6M16 13H8M16 17H8M10 9H8" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
        </svg>
    ),
    eye: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
            <circle cx="12" cy="12" r="3" stroke="currentColor" strokeWidth="1.5"/>
        </svg>
    ),
    server: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <rect x="2" y="3" width="20" height="14" rx="2" stroke="currentColor" strokeWidth="1.5"/>
            <path d="M8 21h8M12 17v4" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
        </svg>
    ),
};

const certifications = [
    {
        name: 'SOC 2 Type II',
        description: 'Audited security controls for data protection, availability, and confidentiality.',
        icon: Icons.shield,
        details: [
            'Annual third-party audits',
            'Continuous monitoring',
            'Documented security policies',
            'Employee security training',
            'Incident response procedures',
        ],
    },
    {
        name: 'STIR/SHAKEN',
        description: 'FCC-mandated call authentication protocol for verified caller identity.',
        icon: Icons.lock,
        details: [
            'Full A-level attestation',
            'Certificate authority integration',
            'PASSporT generation',
            'Carrier verification',
            'Real-time signing',
        ],
    },
    {
        name: 'HIPAA',
        description: 'Healthcare data protection for covered entities and business associates.',
        icon: Icons.file,
        details: [
            'Business Associate Agreement (BAA) available',
            'PHI encryption at rest and in transit',
            'Access logging and audit trails',
            'Minimum necessary access controls',
            'Employee HIPAA training',
        ],
    },
    {
        name: 'TCPA',
        description: 'Telephone Consumer Protection Act compliance for outbound calling.',
        icon: Icons.check,
        details: [
            'Consent tracking capabilities',
            'Do-Not-Call list integration',
            'Time-of-day calling restrictions',
            'Caller ID transmission',
            'Record retention support',
        ],
    },
];

const securityFeatures = [
    {
        title: 'Encryption',
        description: 'All data is encrypted at rest using AES-256 and in transit using TLS 1.3.',
        icon: Icons.lock,
    },
    {
        title: 'Access Control',
        description: 'Role-based access control (RBAC) with principle of least privilege.',
        icon: Icons.eye,
    },
    {
        title: 'Audit Logging',
        description: 'Comprehensive audit logs for all system access and changes.',
        icon: Icons.file,
    },
    {
        title: 'Infrastructure',
        description: 'Hosted on SOC 2 compliant cloud infrastructure with redundancy.',
        icon: Icons.server,
    },
];

const dataProtection = [
    'Data classified and handled according to sensitivity',
    'Regular backups with point-in-time recovery',
    'Data retention policies aligned with regulations',
    'Secure data deletion procedures',
    'Vendor security assessments',
    'Data processing agreements (DPA) available',
];

const penetrationTesting = [
    'Annual third-party penetration testing',
    'Quarterly vulnerability assessments',
    'Bug bounty program',
    'Automated security scanning',
    'Dependency vulnerability monitoring',
];

export default function Compliance() {
    return (
        <MarketingLayout>
            <Head title="Compliance & Security - BrandCall" />

            {/* Hero */}
            <section className="py-16 md:py-24">
                <div className="max-w-7xl mx-auto px-6 sm:px-8">
                    <div className="max-w-3xl">
                        <div className="inline-flex items-center gap-2 bg-green-500/10 border border-green-500/20 rounded-full px-4 py-1.5 text-sm text-green-500 mb-6">
                            <div className="h-4 w-4">{Icons.shield}</div>
                            Trust Center
                        </div>
                        <h1 className="text-4xl md:text-5xl font-bold text-theme-primary mb-6 leading-tight">
                            Security & Compliance
                        </h1>
                        <p className="text-xl text-theme-secondary leading-relaxed">
                            BrandCall is built with enterprise-grade security and regulatory compliance at its core. We protect your data and help you meet your compliance obligations.
                        </p>
                    </div>
                </div>
            </section>

            {/* Quick Badges */}
            <section className="pb-16">
                <div className="max-w-7xl mx-auto px-6 sm:px-8">
                    <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                        {certifications.map((cert) => (
                            <a 
                                key={cert.name}
                                href={`#${cert.name.toLowerCase().replace(/\s+/g, '-')}`}
                                className="bg-theme-secondary rounded-xl p-6 border border-theme-primary hover:border-green-500/50 transition-colors text-center"
                            >
                                <div className="h-10 w-10 text-green-500 mx-auto mb-3">{cert.icon}</div>
                                <h3 className="font-bold text-theme-primary">{cert.name}</h3>
                                <p className="text-xs text-green-500 mt-1">Compliant</p>
                            </a>
                        ))}
                    </div>
                </div>
            </section>

            {/* Certifications Detail */}
            <section className="py-16 border-y border-theme-primary bg-theme-secondary">
                <div className="max-w-7xl mx-auto px-6 sm:px-8">
                    <h2 className="text-2xl font-bold text-theme-primary mb-12 text-center">Certifications & Compliance</h2>
                    <div className="grid md:grid-cols-2 gap-8">
                        {certifications.map((cert) => (
                            <div 
                                key={cert.name}
                                id={cert.name.toLowerCase().replace(/\s+/g, '-')}
                                className="bg-theme-primary rounded-xl p-8 border border-theme-primary scroll-mt-24"
                            >
                                <div className="flex items-center gap-4 mb-4">
                                    <div className="h-12 w-12 bg-green-500/10 text-green-500 rounded-xl flex items-center justify-center">
                                        <div className="h-6 w-6">{cert.icon}</div>
                                    </div>
                                    <div>
                                        <h3 className="text-xl font-bold text-theme-primary">{cert.name}</h3>
                                        <p className="text-sm text-green-500">Compliant</p>
                                    </div>
                                </div>
                                <p className="text-theme-tertiary mb-6">{cert.description}</p>
                                <ul className="space-y-2">
                                    {cert.details.map((detail, i) => (
                                        <li key={i} className="flex items-center gap-2 text-sm text-theme-muted">
                                            <div className="h-4 w-4 text-green-500">{Icons.check}</div>
                                            {detail}
                                        </li>
                                    ))}
                                </ul>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* Security Features */}
            <section className="py-16">
                <div className="max-w-7xl mx-auto px-6 sm:px-8">
                    <h2 className="text-2xl font-bold text-theme-primary mb-4 text-center">Security Architecture</h2>
                    <p className="text-theme-secondary text-center max-w-2xl mx-auto mb-12">
                        Multiple layers of security protect your data at every level of our infrastructure.
                    </p>
                    <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                        {securityFeatures.map((feature) => (
                            <div key={feature.title} className="bg-theme-secondary rounded-xl p-6 border border-theme-primary">
                                <div className="h-10 w-10 bg-brand-500/10 text-brand-500 rounded-lg flex items-center justify-center mb-4">
                                    <div className="h-5 w-5">{feature.icon}</div>
                                </div>
                                <h3 className="font-semibold text-theme-primary mb-2">{feature.title}</h3>
                                <p className="text-sm text-theme-tertiary">{feature.description}</p>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* Data Protection & Testing */}
            <section className="py-16 border-y border-theme-primary bg-theme-secondary">
                <div className="max-w-7xl mx-auto px-6 sm:px-8">
                    <div className="grid lg:grid-cols-2 gap-12">
                        <div>
                            <h2 className="text-2xl font-bold text-theme-primary mb-6">Data Protection</h2>
                            <ul className="space-y-3">
                                {dataProtection.map((item, i) => (
                                    <li key={i} className="flex items-start gap-3">
                                        <div className="h-5 w-5 text-green-500 flex-shrink-0 mt-0.5">{Icons.check}</div>
                                        <span className="text-theme-tertiary">{item}</span>
                                    </li>
                                ))}
                            </ul>
                        </div>
                        <div>
                            <h2 className="text-2xl font-bold text-theme-primary mb-6">Security Testing</h2>
                            <ul className="space-y-3">
                                {penetrationTesting.map((item, i) => (
                                    <li key={i} className="flex items-start gap-3">
                                        <div className="h-5 w-5 text-green-500 flex-shrink-0 mt-0.5">{Icons.check}</div>
                                        <span className="text-theme-tertiary">{item}</span>
                                    </li>
                                ))}
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            {/* BCID Ecosystem */}
            <section className="py-16">
                <div className="max-w-4xl mx-auto px-6 sm:px-8">
                    <h2 className="text-2xl font-bold text-theme-primary mb-4 text-center">BCID Ecosystem Compliance</h2>
                    <p className="text-theme-secondary text-center mb-12">
                        BrandCall operates within the CTIA-governed Branded Calling ID ecosystem, ensuring standardized, industry-wide compliance.
                    </p>
                    <div className="bg-theme-secondary rounded-xl p-8 border border-theme-primary">
                        <div className="grid md:grid-cols-2 gap-8">
                            <div>
                                <h3 className="font-semibold text-theme-primary mb-4">Ecosystem Roles</h3>
                                <ul className="space-y-3 text-sm text-theme-tertiary">
                                    <li><strong className="text-theme-primary">Onboarding Agent:</strong> Register and onboard enterprises</li>
                                    <li><strong className="text-theme-primary">Vetting Agent:</strong> Verify business identity and numbers</li>
                                    <li><strong className="text-theme-primary">Signing Agent:</strong> Generate SHAKEN PASSporTs with RCD</li>
                                    <li><strong className="text-theme-primary">OSP:</strong> Originate authenticated branded calls</li>
                                </ul>
                            </div>
                            <div>
                                <h3 className="font-semibold text-theme-primary mb-4">CTIA Standards</h3>
                                <ul className="space-y-3 text-sm text-theme-tertiary">
                                    <li><strong className="text-theme-primary">Best Practices:</strong> Following CTIA branded calling guidelines</li>
                                    <li><strong className="text-theme-primary">Interoperability:</strong> Compatible with all BCID participants</li>
                                    <li><strong className="text-theme-primary">Fair Pricing:</strong> Non-monopolistic, transparent pricing</li>
                                    <li><strong className="text-theme-primary">Governance:</strong> Subject to CTIA oversight and enforcement</li>
                                </ul>
                            </div>
                        </div>
                        <div className="mt-8 pt-6 border-t border-theme-primary text-center">
                            <Link 
                                href="/guide/branded-calling"
                                className="inline-flex items-center gap-2 text-brand-500 hover:text-brand-400 font-medium"
                            >
                                Learn more about BCID
                                <div className="h-4 w-4">{Icons.arrow}</div>
                            </Link>
                        </div>
                    </div>
                </div>
            </section>

            {/* Request Documents */}
            <section className="py-16 border-t border-theme-primary">
                <div className="max-w-3xl mx-auto px-6 sm:px-8 text-center">
                    <h2 className="text-3xl font-bold text-theme-primary mb-4">
                        Need Compliance Documentation?
                    </h2>
                    <p className="text-lg text-theme-secondary mb-8">
                        Request SOC 2 reports, security questionnaires, or BAAs from our security team.
                    </p>
                    <div className="flex flex-col sm:flex-row items-center justify-center gap-4">
                        <a 
                            href="mailto:security@brandcall.io"
                            className="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium text-white bg-brand-600 rounded-lg hover:bg-brand-500 transition-colors"
                        >
                            Request Documentation
                            <div className="h-4 w-4">{Icons.arrow}</div>
                        </a>
                        <Link 
                            href="/register"
                            className="inline-flex items-center px-6 py-3 text-sm font-medium text-theme-secondary border border-theme-primary rounded-lg hover:bg-theme-tertiary transition-colors"
                        >
                            Start Free Trial
                        </Link>
                    </div>
                </div>
            </section>
        </MarketingLayout>
    );
}
