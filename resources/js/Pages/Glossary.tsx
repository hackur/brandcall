import { Head, Link } from '@inertiajs/react';
import MarketingLayout from '@/Layouts/MarketingLayout';
import { useState, useMemo } from 'react';

interface GlossaryTerm {
    term: string;
    definition: string;
    category: string;
}

const terms: GlossaryTerm[] = [
    // Core Terminology
    { term: 'Branded Caller ID', definition: 'The display of a verified business name, logo, and/or call reason on a recipient\'s mobile device when receiving an inbound call. Replaces the generic phone number display with recognizable business identity.', category: 'Core' },
    { term: 'Rich Call Data (RCD)', definition: 'Extended call metadata conveyed via STIR/SHAKEN that includes name, logo, and call reason. The "payload" that makes branded calls informative beyond just the phone number.', category: 'Core' },
    { term: 'STIR/SHAKEN', definition: 'Secure Telephone Identity Revisited / Signature-based Handling of Asserted Information Using toKENs. Industry standards for caller ID authentication using cryptographic signing to verify calls originate from legitimate sources. Required for all U.S. carriers since June 2021.', category: 'Core' },
    { term: 'CNAM', definition: 'Caller Name Delivery. Legacy caller ID technology for landlines, limited to 15 characters with no logos — wireline only. Branded caller ID supersedes this for mobile networks.', category: 'Core' },
    { term: 'Splash', definition: 'Industry term for the visual "splash screen" or branded display that appears on the recipient\'s phone when a call comes in. The moment of truth when branding either convinces someone to answer or reject.', category: 'Core' },
    { term: 'Voice Pick Up (VPU)', definition: 'Answer rate metric — the percentage of outbound calls that result in a live human connection. The primary KPI that branded calling aims to improve.', category: 'Core' },
    { term: 'Contact Rate', definition: 'Percentage of call attempts that result in reaching the intended recipient (answered calls / total attempts). Industry standard hovers around 20-25% for unidentified calls.', category: 'Core' },
    { term: 'Callback Rate', definition: 'Percentage of missed calls where the recipient calls back. Branded calls significantly improve this — people trust calling back a number they recognize.', category: 'Core' },
    { term: 'BCID', definition: 'Branded Calling ID — CTIA\'s industry-governed ecosystem for branded calling. Authorized Partners are vetted and audited, and enterprise numbers, names, and logos are verified with delivery confirmation.', category: 'Core' },

    // Anti-Spam & Reputation
    { term: 'Spam Label / Spam Likely', definition: 'Warning tags applied by carriers and analytics engines (e.g., "Spam Likely", "Scam Risk", "Potential Fraud"). Appears in caller ID and causes most recipients to ignore the call.', category: 'Reputation' },
    { term: 'Caller ID Reputation', definition: 'A credibility score for phone numbers determined by call patterns, volume, answer rates, call durations, consumer complaints, and historical behavior from carrier analytics engines.', category: 'Reputation' },
    { term: 'Number Reputation Management (NRM)', definition: 'Ongoing monitoring and remediation of phone number reputation across carriers and analytics providers. Includes monitoring for spam labels, remediation, identity verification (KYC), and best practices consulting.', category: 'Reputation' },
    { term: 'Remediation', definition: 'The process of actively removing spam/scam labels from phone numbers through carrier relationships. More than just monitoring — actually fixing problems. Typically takes 1-3 business days.', category: 'Reputation' },
    { term: 'Number Burn', definition: 'When a phone number accumulates enough spam labels to become effectively unusable. Bad actors burn through numbers rapidly; legitimate businesses preserve them through reputation management.', category: 'Reputation' },
    { term: 'Number Rotation / Cycling', definition: 'Practice of rotating through phone number pools to distribute call volume, allow cooling-off periods, and manage reputation. Can be legitimate (load balancing) or abusive (avoiding detection).', category: 'Reputation' },

    // Compliance & Authentication
    { term: 'Attestation', definition: 'STIR/SHAKEN verification level. A (Full) = carrier knows the customer and they\'re authorized to use the number. B (Partial) = carrier knows the customer but not the number. C (Gateway) = carrier doesn\'t know the originator.', category: 'Compliance' },
    { term: 'KYC', definition: 'Know Your Customer — identity verification process for businesses using branded calling. Includes business registration verification, use case documentation, calling practices audit, and compliance attestation.', category: 'Compliance' },
    { term: 'LOA', definition: 'Letter of Authorization — a document proving phone number ownership or the right to use specific phone numbers for outbound calling.', category: 'Compliance' },
    { term: 'TCPA', definition: 'Telephone Consumer Protection Act — federal law governing telemarketing calls requiring prior express consent, Do Not Call registry compliance, time-of-day restrictions, with penalties up to $1,500 per violation.', category: 'Compliance' },
    { term: 'DNC Registry', definition: 'Do Not Call Registry — national database of phone numbers that have opted out of telemarketing. Compliance is mandatory for all businesses making outbound sales calls.', category: 'Compliance' },
    { term: 'Confirmed Call Delivery', definition: 'Verification that branded information was actually displayed to the recipient. Critical for proving ROI — ensures you only pay for calls where your brand was seen.', category: 'Compliance' },

    // Fraud Prevention
    { term: 'Spoofing', definition: 'Falsifying caller ID information to appear as a different number. STIR/SHAKEN combats this with cryptographic attestation, but sophisticated spoofing still persists.', category: 'Fraud Prevention' },
    { term: 'Velocity Analysis', definition: 'Detecting fraud by analyzing call frequency patterns. Too many calls too fast from a single source is a strong indicator of robocalling or spam operations.', category: 'Fraud Prevention' },
    { term: 'Burst Detection', definition: 'Flagging sudden increases in call volume from an account. Normal calling is relatively steady; bursts may indicate either a legitimate campaign launch (requires pre-registration) or spam operations.', category: 'Fraud Prevention' },

    // Call Center & Operations
    { term: 'Predictive Dialing', definition: 'Automated, high-velocity dialing where the system calls multiple numbers simultaneously and connects answered calls to available agents. Triggers spam flags due to high volume and short call durations.', category: 'Operations' },
    { term: 'Progressive Dialing', definition: 'Agent-paced dialing at moderate velocity where the system dials the next number when an agent becomes available. Better for reputation than predictive dialing.', category: 'Operations' },
    { term: 'Dialing Cadence', definition: 'The pattern and timing of outbound calls. Aggressive predictive dialing destroys number reputation, while measured progressive or preview dialing preserves it.', category: 'Operations' },
    { term: 'Rate Limiting', definition: 'Caps on calls per hour/day from specific numbers or accounts. Typical legitimate use: 50-200 calls per day per number. Helps prevent reputation damage and carrier flagging.', category: 'Operations' },
    { term: 'BPO', definition: 'Business Process Outsourcing — third-party call centers that handle outbound calling on behalf of businesses. BPOs need white-label branded calling solutions for their clients.', category: 'Operations' },
    { term: 'CPaaS', definition: 'Communications Platform as a Service — cloud-based platforms (like Twilio, Vonage) that provide voice, messaging, and video APIs. BrandCall integrates with any CPaaS provider.', category: 'Operations' },
];

const categories = ['All', 'Core', 'Reputation', 'Compliance', 'Fraud Prevention', 'Operations'];

export default function Glossary() {
    const [search, setSearch] = useState('');
    const [activeCategory, setActiveCategory] = useState('All');

    const filtered = useMemo(() => {
        return terms
            .filter((t) => {
                const matchesSearch = !search || 
                    t.term.toLowerCase().includes(search.toLowerCase()) ||
                    t.definition.toLowerCase().includes(search.toLowerCase());
                const matchesCategory = activeCategory === 'All' || t.category === activeCategory;
                return matchesSearch && matchesCategory;
            })
            .sort((a, b) => a.term.localeCompare(b.term));
    }, [search, activeCategory]);

    // Group by first letter
    const grouped = useMemo(() => {
        const groups: Record<string, GlossaryTerm[]> = {};
        filtered.forEach((term) => {
            const letter = term.term[0].toUpperCase();
            if (!groups[letter]) groups[letter] = [];
            groups[letter].push(term);
        });
        return groups;
    }, [filtered]);

    return (
        <MarketingLayout>
            <Head title="Industry Glossary - Branded Caller ID Terms" />

            {/* Hero */}
            <section className="py-16 sm:py-24">
                <div className="max-w-4xl mx-auto px-6 text-center">
                    <h1 className="text-4xl sm:text-5xl font-bold text-theme-primary mb-4">
                        Industry Glossary
                    </h1>
                    <p className="text-lg text-theme-secondary max-w-2xl mx-auto">
                        A comprehensive guide to branded caller ID, STIR/SHAKEN, call authentication, 
                        and telecommunications industry terminology.
                    </p>
                </div>
            </section>

            {/* Search + Filters */}
            <section className="pb-8">
                <div className="max-w-4xl mx-auto px-6">
                    <div className="flex flex-col sm:flex-row gap-4 items-start sm:items-center">
                        <div className="relative flex-1 w-full">
                            <svg className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-theme-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input
                                type="text"
                                placeholder="Search terms..."
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                className="w-full pl-10 pr-4 py-3 bg-theme-secondary border border-theme-primary rounded-xl text-theme-primary placeholder:text-theme-muted focus:outline-none focus:ring-2 focus:ring-brand-600 focus:border-transparent"
                            />
                        </div>
                        <div className="flex flex-wrap gap-2">
                            {categories.map((cat) => (
                                <button
                                    key={cat}
                                    onClick={() => setActiveCategory(cat)}
                                    className={`px-3 py-1.5 text-sm font-medium rounded-lg transition-colors ${
                                        activeCategory === cat
                                            ? 'bg-brand-600 text-white'
                                            : 'bg-theme-tertiary text-theme-secondary hover:text-theme-primary'
                                    }`}
                                >
                                    {cat}
                                </button>
                            ))}
                        </div>
                    </div>
                    <p className="mt-3 text-sm text-theme-muted">{filtered.length} terms</p>
                </div>
            </section>

            {/* Terms */}
            <section className="pb-24">
                <div className="max-w-4xl mx-auto px-6 space-y-10">
                    {Object.keys(grouped).sort().map((letter) => (
                        <div key={letter}>
                            <div className="flex items-center gap-3 mb-4">
                                <span className="flex items-center justify-center w-10 h-10 rounded-xl bg-brand-600/10 text-brand-500 text-lg font-bold">
                                    {letter}
                                </span>
                                <div className="flex-1 h-px bg-theme-primary" />
                            </div>
                            <div className="space-y-4">
                                {grouped[letter].map((term) => (
                                    <div key={term.term} className="pl-4 border-l-2 border-theme-primary hover:border-brand-600 transition-colors">
                                        <h3 className="text-base font-semibold text-theme-primary">{term.term}</h3>
                                        <span className="inline-block mt-1 mb-1.5 px-2 py-0.5 text-xs font-medium bg-theme-tertiary text-theme-muted rounded">
                                            {term.category}
                                        </span>
                                        <p className="text-sm text-theme-secondary leading-relaxed">{term.definition}</p>
                                    </div>
                                ))}
                            </div>
                        </div>
                    ))}

                    {filtered.length === 0 && (
                        <div className="text-center py-16">
                            <p className="text-theme-muted text-lg">No terms found matching your search.</p>
                        </div>
                    )}
                </div>
            </section>

            {/* CTA */}
            <section className="pb-24">
                <div className="max-w-4xl mx-auto px-6">
                    <div className="bg-brand-600/10 border border-brand-600/20 rounded-2xl p-8 sm:p-12 text-center">
                        <h2 className="text-2xl font-bold text-theme-primary mb-3">
                            Ready to improve your answer rates?
                        </h2>
                        <p className="text-theme-secondary mb-6">
                            Now that you know the terminology, see how BrandCall puts it all into practice.
                        </p>
                        <div className="flex flex-col sm:flex-row items-center justify-center gap-4">
                            <Link
                                href={route('register')}
                                className="inline-flex items-center px-6 py-3 text-sm font-medium text-white bg-brand-600 rounded-lg hover:bg-brand-500 transition-colors"
                            >
                                Get Started
                            </Link>
                            <Link
                                href="/guide/branded-calling"
                                className="inline-flex items-center px-6 py-3 text-sm font-medium text-theme-primary border border-theme-primary rounded-lg hover:bg-theme-tertiary transition-colors"
                            >
                                What is Branded Caller ID?
                            </Link>
                        </div>
                    </div>
                </div>
            </section>
        </MarketingLayout>
    );
}
