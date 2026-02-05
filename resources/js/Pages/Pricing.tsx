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
    check: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <path d="M20 6L9 17l-5-5" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
        </svg>
    ),
    x: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
        </svg>
    ),
    arrow: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <path d="M5 12h14M12 5l7 7-7 7" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
        </svg>
    ),
    star: (
        <svg viewBox="0 0 24 24" fill="currentColor" className="w-full h-full">
            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
        </svg>
    ),
    shield: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
            <path d="M9 12l2 2 4-4" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
        </svg>
    ),
    headphones: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <path d="M3 18v-6a9 9 0 0118 0v6" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
            <path d="M21 19a2 2 0 01-2 2h-1a2 2 0 01-2-2v-3a2 2 0 012-2h3v5zM3 19a2 2 0 002 2h1a2 2 0 002-2v-3a2 2 0 00-2-2H3v5z" stroke="currentColor" strokeWidth="1.5"/>
        </svg>
    ),
    zap: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
        </svg>
    ),
    building: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <path d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-4h6v4M9 9h.01M15 9h.01M9 13h.01M15 13h.01" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
        </svg>
    ),
    helpCircle: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <circle cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="1.5"/>
            <path d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3M12 17h.01" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
        </svg>
    ),
};

interface PricingTier {
    name: string;
    price: string;
    period: string;
    description: string;
    features: string[];
    cta: string;
    popular?: boolean;
    enterprise?: boolean;
}

const pricingTiers: PricingTier[] = [
    {
        name: 'Starter',
        price: '$299',
        period: '/month',
        description: 'Perfect for small teams getting started with branded calling.',
        features: [
            'Up to 5 phone numbers',
            'STIR/SHAKEN A attestation',
            'Basic caller ID branding',
            'Number reputation monitoring',
            'Email support',
            'Dashboard analytics',
            '5,000 calls/month included',
        ],
        cta: 'Start Free Trial',
    },
    {
        name: 'Growth',
        price: '$999',
        period: '/month',
        description: 'For growing teams that need API access and more capacity.',
        features: [
            'Up to 50 phone numbers',
            'Everything in Starter, plus:',
            'Rich Call Data (logo display)',
            'Full REST API access',
            'Webhook integrations',
            'Priority email support',
            'Advanced analytics',
            '25,000 calls/month included',
            'Custom CNAM registration',
        ],
        cta: 'Start Free Trial',
        popular: true,
    },
    {
        name: 'Business',
        price: '$2,499',
        period: '/month',
        description: 'For established teams with high volume and compliance needs.',
        features: [
            'Up to 250 phone numbers',
            'Everything in Growth, plus:',
            'Priority phone support',
            'Dedicated success manager',
            'SSO/SAML integration',
            'Custom integrations',
            'SLA guarantee (99.9%)',
            '100,000 calls/month included',
            'Multi-user role management',
            'Audit logging',
        ],
        cta: 'Contact Sales',
    },
    {
        name: 'Enterprise',
        price: 'Custom',
        period: 'pricing',
        description: 'Unlimited scale with white-label options and dedicated support.',
        features: [
            'Unlimited phone numbers',
            'Everything in Business, plus:',
            'White-label portal',
            'Multi-tenant architecture',
            'Dedicated infrastructure',
            'Custom contract terms',
            '24/7 priority support',
            'Unlimited calls',
            'Volume discounts',
            'On-premise deployment option',
        ],
        cta: 'Contact Sales',
        enterprise: true,
    },
];

interface FeatureComparison {
    category: string;
    features: {
        name: string;
        starter: boolean | string;
        growth: boolean | string;
        business: boolean | string;
        enterprise: boolean | string;
    }[];
}

const featureComparison: FeatureComparison[] = [
    {
        category: 'Core Features',
        features: [
            { name: 'STIR/SHAKEN A-Level Attestation', starter: true, growth: true, business: true, enterprise: true },
            { name: 'Basic Caller ID (CNAM)', starter: true, growth: true, business: true, enterprise: true },
            { name: 'Rich Call Data (Logo + Name)', starter: false, growth: true, business: true, enterprise: true },
            { name: 'Number Reputation Monitoring', starter: 'Basic', growth: 'Full', business: 'Full', enterprise: 'Full' },
            { name: 'Automated Remediation', starter: false, growth: true, business: true, enterprise: true },
        ],
    },
    {
        category: 'Platform & API',
        features: [
            { name: 'Dashboard Access', starter: true, growth: true, business: true, enterprise: true },
            { name: 'REST API Access', starter: false, growth: true, business: true, enterprise: true },
            { name: 'Webhooks', starter: false, growth: true, business: true, enterprise: true },
            { name: 'SDKs (Python, Node, PHP)', starter: false, growth: true, business: true, enterprise: true },
            { name: 'GraphQL API', starter: false, growth: false, business: true, enterprise: true },
        ],
    },
    {
        category: 'Support & SLA',
        features: [
            { name: 'Email Support', starter: true, growth: 'Priority', business: 'Priority', enterprise: 'Priority' },
            { name: 'Phone Support', starter: false, growth: false, business: true, enterprise: '24/7' },
            { name: 'Dedicated Success Manager', starter: false, growth: false, business: true, enterprise: true },
            { name: 'SLA Guarantee', starter: '99.5%', growth: '99.5%', business: '99.9%', enterprise: '99.99%' },
            { name: 'Response Time', starter: '24h', growth: '12h', business: '4h', enterprise: '1h' },
        ],
    },
    {
        category: 'Security & Compliance',
        features: [
            { name: 'SOC 2 Type II', starter: true, growth: true, business: true, enterprise: true },
            { name: 'SSO/SAML', starter: false, growth: false, business: true, enterprise: true },
            { name: 'Role-Based Access Control', starter: 'Basic', growth: 'Standard', business: 'Advanced', enterprise: 'Custom' },
            { name: 'Audit Logging', starter: '30 days', growth: '90 days', business: '1 year', enterprise: 'Unlimited' },
            { name: 'HIPAA BAA', starter: false, growth: false, business: true, enterprise: true },
        ],
    },
];

interface Addon {
    name: string;
    price: string;
    unit: string;
    description: string;
}

const addons: Addon[] = [
    { name: 'Additional Phone Numbers', price: '$10', unit: '/number/month', description: 'Add more branded numbers beyond your plan limit' },
    { name: 'Additional Calls', price: '$0.01', unit: '/call', description: 'Pay-as-you-go for calls beyond included volume' },
    { name: 'Premium Support', price: '$500', unit: '/month', description: 'Priority support with 2-hour response time' },
    { name: 'Custom Integration', price: '$2,500', unit: 'one-time', description: 'Custom API integration development' },
];

interface FAQ {
    question: string;
    answer: string;
}

const faqs: FAQ[] = [
    {
        question: 'How does the free trial work?',
        answer: 'Start with a 14-day free trial on any plan. No credit card required. You\'ll have full access to all features in your chosen tier. At the end of the trial, you can upgrade to a paid plan or downgrade.',
    },
    {
        question: 'Can I change plans later?',
        answer: 'Yes, you can upgrade or downgrade at any time. Upgrades take effect immediately with prorated billing. Downgrades take effect at the start of your next billing cycle.',
    },
    {
        question: 'What happens if I exceed my call limit?',
        answer: 'You\'ll be charged $0.01 per additional call. We\'ll notify you when you\'re approaching your limit so there are no surprises. Enterprise plans include unlimited calls.',
    },
    {
        question: 'Do you offer annual billing discounts?',
        answer: 'Yes! Pay annually and save 20% on any plan. Contact sales for annual pricing details.',
    },
    {
        question: 'What\'s included in the setup process?',
        answer: 'All plans include onboarding assistance, number porting support, and initial configuration. Business and Enterprise plans include dedicated onboarding with a success manager.',
    },
    {
        question: 'Is there a contract or commitment?',
        answer: 'Monthly plans have no long-term commitment—cancel anytime. Annual plans require a 12-month commitment but offer significant savings.',
    },
];

const trustBadges = [
    { name: 'SOC 2', description: 'Type II Certified' },
    { name: 'HIPAA', description: 'Compliant' },
    { name: '99.9%', description: 'Uptime SLA' },
    { name: '500+', description: 'Enterprise Clients' },
];

export default function Pricing({ auth }: PageProps) {
    const [isHeaderVisible, setIsHeaderVisible] = useState(true);
    const [lastScrollY, setLastScrollY] = useState(0);
    const [isAnnual, setIsAnnual] = useState(false);
    const [expandedFaq, setExpandedFaq] = useState<number | null>(null);

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

    const getPrice = (price: string) => {
        if (price === 'Custom') return price;
        const numPrice = parseInt(price.replace('$', ''));
        if (isAnnual) {
            return `$${Math.round(numPrice * 0.8)}`;
        }
        return price;
    };

    return (
        <div>
            <Head title="Pricing - BrandCall Enterprise Caller ID Platform" />
            
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
                                <Link href={route('compliance')} className="text-sm font-medium text-theme-tertiary hover:text-theme-primary transition-colors">Compliance</Link>
                                <Link href={route('pricing')} className="text-sm font-medium text-brand-500 transition-colors">Pricing</Link>
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
                        <div className="max-w-7xl mx-auto px-6 sm:px-8 text-center">
                            <p className="text-sm font-medium text-brand-500 mb-3">Pricing</p>
                            <h1 className="text-3xl sm:text-4xl lg:text-5xl font-bold text-theme-primary mb-6">
                                Simple, Transparent Pricing
                            </h1>
                            <p className="text-lg text-theme-secondary max-w-2xl mx-auto mb-8">
                                Choose the plan that fits your needs. All plans include STIR/SHAKEN compliance 
                                and our core branded calling features.
                            </p>

                            {/* Billing Toggle */}
                            <div className="flex items-center justify-center gap-3 mb-12">
                                <span className={`text-sm font-medium ${!isAnnual ? 'text-theme-primary' : 'text-theme-muted'}`}>
                                    Monthly
                                </span>
                                <button
                                    onClick={() => setIsAnnual(!isAnnual)}
                                    className={`relative w-14 h-7 rounded-full transition-colors ${
                                        isAnnual ? 'bg-brand-600' : 'bg-slate-700'
                                    }`}
                                >
                                    <div 
                                        className={`absolute top-1 w-5 h-5 rounded-full bg-white transition-transform ${
                                            isAnnual ? 'translate-x-8' : 'translate-x-1'
                                        }`}
                                    />
                                </button>
                                <span className={`text-sm font-medium ${isAnnual ? 'text-theme-primary' : 'text-theme-muted'}`}>
                                    Annual
                                </span>
                                {isAnnual && (
                                    <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-500/10 text-green-500 border border-green-500/20">
                                        Save 20%
                                    </span>
                                )}
                            </div>
                        </div>
                    </section>

                    {/* Pricing Cards */}
                    <section className="pb-16">
                        <div className="max-w-7xl mx-auto px-6 sm:px-8">
                            <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                                {pricingTiers.map((tier, idx) => (
                                    <div 
                                        key={idx}
                                        className={`relative p-6 rounded-lg flex flex-col ${
                                            tier.popular 
                                                ? 'bg-brand-600/10 border-2 border-brand-500 lg:-mt-4 lg:mb-4 lg:py-10' 
                                                : 'bg-theme-secondary border border-theme-primary'
                                        }`}
                                    >
                                        {tier.popular && (
                                            <div className="absolute -top-3 left-1/2 -translate-x-1/2">
                                                <span className="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-brand-600 text-white">
                                                    <div className="h-3 w-3">{Icons.star}</div>
                                                    Most Popular
                                                </span>
                                            </div>
                                        )}

                                        <div className="mb-6">
                                            <h3 className="text-lg font-semibold text-theme-primary mb-2">
                                                {tier.name}
                                            </h3>
                                            <div className="flex items-baseline gap-1 mb-2">
                                                <span className="text-3xl font-bold text-theme-primary">
                                                    {getPrice(tier.price)}
                                                </span>
                                                <span className="text-theme-muted">{tier.period}</span>
                                            </div>
                                            <p className="text-sm text-theme-tertiary">
                                                {tier.description}
                                            </p>
                                        </div>

                                        <ul className="space-y-3 mb-8 flex-grow">
                                            {tier.features.map((feature, fidx) => (
                                                <li key={fidx} className="flex items-start gap-2">
                                                    <div className="h-5 w-5 text-green-500 flex-shrink-0">
                                                        {Icons.check}
                                                    </div>
                                                    <span className="text-sm text-theme-secondary">{feature}</span>
                                                </li>
                                            ))}
                                        </ul>

                                        <Link
                                            href={tier.enterprise ? '#' : route('register')}
                                            className={`w-full text-center py-3 px-4 rounded-md text-sm font-medium transition-colors ${
                                                tier.popular
                                                    ? 'bg-brand-600 text-white hover:bg-brand-500'
                                                    : 'bg-theme-tertiary text-theme-primary hover:bg-theme-secondary border border-theme-primary'
                                            }`}
                                        >
                                            {tier.cta}
                                        </Link>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </section>

                    {/* Trust Badges */}
                    <section className="py-12 border-t border-theme-primary">
                        <div className="max-w-7xl mx-auto px-6 sm:px-8">
                            <div className="flex flex-wrap items-center justify-center gap-8 sm:gap-12">
                                {trustBadges.map((badge, idx) => (
                                    <div key={idx} className="flex items-center gap-2">
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
                    </section>

                    {/* Feature Comparison */}
                    <section className="py-16 lg:py-24 border-t border-theme-primary bg-theme-secondary">
                        <div className="max-w-7xl mx-auto px-6 sm:px-8">
                            <div className="max-w-2xl mb-12">
                                <h2 className="text-2xl sm:text-3xl font-semibold text-theme-primary mb-4">
                                    Compare Plans
                                </h2>
                                <p className="text-base text-theme-secondary">
                                    See exactly what's included in each plan to find the right fit for your team.
                                </p>
                            </div>

                            <div className="overflow-x-auto">
                                <table className="w-full min-w-[800px]">
                                    <thead>
                                        <tr className="border-b border-theme-primary">
                                            <th className="text-left py-4 px-4 text-sm font-medium text-theme-muted w-1/3">Feature</th>
                                            <th className="text-center py-4 px-4 text-sm font-medium text-theme-primary">Starter</th>
                                            <th className="text-center py-4 px-4 text-sm font-medium text-brand-500">Growth</th>
                                            <th className="text-center py-4 px-4 text-sm font-medium text-theme-primary">Business</th>
                                            <th className="text-center py-4 px-4 text-sm font-medium text-theme-primary">Enterprise</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {featureComparison.map((category, cidx) => (
                                            <>
                                                <tr key={`cat-${cidx}`} className="bg-theme-tertiary">
                                                    <td colSpan={5} className="py-3 px-4 text-sm font-semibold text-theme-primary">
                                                        {category.category}
                                                    </td>
                                                </tr>
                                                {category.features.map((feature, fidx) => (
                                                    <tr key={`feat-${cidx}-${fidx}`} className="border-b border-theme-primary">
                                                        <td className="py-4 px-4 text-sm text-theme-secondary">{feature.name}</td>
                                                        {(['starter', 'growth', 'business', 'enterprise'] as const).map((tier) => (
                                                            <td key={tier} className="text-center py-4 px-4">
                                                                {typeof feature[tier] === 'boolean' ? (
                                                                    feature[tier] ? (
                                                                        <div className="h-5 w-5 text-green-500 mx-auto">{Icons.check}</div>
                                                                    ) : (
                                                                        <div className="h-5 w-5 text-theme-muted mx-auto">{Icons.x}</div>
                                                                    )
                                                                ) : (
                                                                    <span className="text-sm text-theme-secondary">{feature[tier]}</span>
                                                                )}
                                                            </td>
                                                        ))}
                                                    </tr>
                                                ))}
                                            </>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </section>

                    {/* Add-ons */}
                    <section className="py-16 lg:py-24 border-t border-theme-primary">
                        <div className="max-w-7xl mx-auto px-6 sm:px-8">
                            <div className="max-w-2xl mb-12">
                                <h2 className="text-2xl sm:text-3xl font-semibold text-theme-primary mb-4">
                                    Usage-Based Add-ons
                                </h2>
                                <p className="text-base text-theme-secondary">
                                    Need more? Add capacity and services as you grow.
                                </p>
                            </div>

                            <div className="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                                {addons.map((addon, idx) => (
                                    <div key={idx} className="p-6 rounded-lg bg-theme-secondary border border-theme-primary">
                                        <h3 className="text-base font-semibold text-theme-primary mb-2">
                                            {addon.name}
                                        </h3>
                                        <div className="flex items-baseline gap-1 mb-2">
                                            <span className="text-xl font-bold text-brand-500">{addon.price}</span>
                                            <span className="text-sm text-theme-muted">{addon.unit}</span>
                                        </div>
                                        <p className="text-sm text-theme-tertiary">
                                            {addon.description}
                                        </p>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </section>

                    {/* FAQ */}
                    <section className="py-16 lg:py-24 border-t border-theme-primary bg-theme-secondary">
                        <div className="max-w-3xl mx-auto px-6 sm:px-8">
                            <div className="text-center mb-12">
                                <h2 className="text-2xl sm:text-3xl font-semibold text-theme-primary mb-4">
                                    Frequently Asked Questions
                                </h2>
                                <p className="text-base text-theme-secondary">
                                    Have questions? We have answers.
                                </p>
                            </div>

                            <div className="space-y-4">
                                {faqs.map((faq, idx) => (
                                    <div 
                                        key={idx}
                                        className="rounded-lg border border-theme-primary bg-theme-primary overflow-hidden"
                                    >
                                        <button
                                            onClick={() => setExpandedFaq(expandedFaq === idx ? null : idx)}
                                            className="w-full flex items-center justify-between p-4 text-left"
                                        >
                                            <span className="text-sm font-medium text-theme-primary">{faq.question}</span>
                                            <div className={`h-5 w-5 text-theme-muted transition-transform ${expandedFaq === idx ? 'rotate-180' : ''}`}>
                                                <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
                                                    <path d="M6 9l6 6 6-6" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
                                                </svg>
                                            </div>
                                        </button>
                                        {expandedFaq === idx && (
                                            <div className="px-4 pb-4">
                                                <p className="text-sm text-theme-tertiary leading-relaxed">{faq.answer}</p>
                                            </div>
                                        )}
                                    </div>
                                ))}
                            </div>
                        </div>
                    </section>

                    {/* CTA Section */}
                    <section className="py-16 sm:py-20 lg:py-24 border-t border-theme-primary">
                        <div className="max-w-3xl mx-auto px-6 sm:px-8 text-center">
                            <h2 className="text-2xl sm:text-3xl lg:text-4xl font-semibold text-theme-primary mb-4">
                                Ready to get started?
                            </h2>
                            <p className="text-base lg:text-lg text-theme-secondary max-w-xl mx-auto mb-8">
                                Start your 14-day free trial today. No credit card required.
                            </p>
                            <div className="flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4">
                                <Link 
                                    href={route('register')} 
                                    className="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium text-white bg-brand-600 rounded-md hover:bg-brand-500 transition-colors"
                                >
                                    Start Free Trial
                                    <div className="h-4 w-4">{Icons.arrow}</div>
                                </Link>
                                <a 
                                    href="mailto:sales@brandcall.io"
                                    className="inline-flex items-center px-6 py-3 text-sm font-medium text-theme-secondary border border-theme-primary rounded-md hover:bg-theme-tertiary transition-colors"
                                >
                                    Talk to Sales
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
