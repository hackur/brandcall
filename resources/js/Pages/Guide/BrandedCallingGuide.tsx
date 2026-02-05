import { Head, Link } from '@inertiajs/react';

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
    x: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
        </svg>
    ),
    alert: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <path d="M12 9v4M12 17h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
        </svg>
    ),
    trending: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <path d="M23 6l-9.5 9.5-5-5L1 18" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
            <path d="M17 6h6v6" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
        </svg>
    ),
    building: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <path d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-4h6v4M9 9h.01M15 9h.01M9 13h.01M15 13h.01" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
        </svg>
    ),
    lock: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <rect x="3" y="11" width="18" height="11" rx="2" stroke="currentColor" strokeWidth="1.5"/>
            <path d="M7 11V7a5 5 0 0110 0v4" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round"/>
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
    arrowRight: (
        <svg viewBox="0 0 24 24" fill="none" className="w-full h-full">
            <path d="M5 12h14M12 5l7 7-7 7" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
        </svg>
    ),
};

// Table of Contents Item
const TocItem = ({ number, title, href }: { number: string; title: string; href: string }) => (
    <a href={href} className="flex items-center gap-3 group py-2 px-4 rounded-lg hover:bg-slate-800/50 transition-colors">
        <span className="text-brand-500 font-bold">{number}</span>
        <span className="text-slate-300 group-hover:text-white transition-colors">{title}</span>
    </a>
);

// Section Header
const SectionHeader = ({ id, title, subtitle }: { id: string; title: string; subtitle?: string }) => (
    <div id={id} className="scroll-mt-24 mb-8">
        <h2 className="text-3xl md:text-4xl font-bold text-white mb-2">{title}</h2>
        {subtitle && <p className="text-lg text-slate-400">{subtitle}</p>}
    </div>
);

// Feature Card
const FeatureCard = ({ icon, title, description }: { icon: React.ReactNode; title: string; description: string }) => (
    <div className="bg-slate-800/50 border border-slate-700/50 rounded-xl p-6 hover:border-brand-500/30 transition-colors">
        <div className="w-10 h-10 text-brand-400 mb-4">{icon}</div>
        <h4 className="text-lg font-semibold text-white mb-2">{title}</h4>
        <p className="text-slate-400 text-sm leading-relaxed">{description}</p>
    </div>
);

// Problem/Solution Row
const ProblemSolutionRow = ({ problem, solution }: { problem: string; solution: string }) => (
    <div className="grid md:grid-cols-2 gap-4 py-4 border-b border-slate-700/50 last:border-0">
        <div className="flex gap-3">
            <div className="w-5 h-5 text-red-400 flex-shrink-0 mt-0.5">{Icons.x}</div>
            <p className="text-slate-400 text-sm">{problem}</p>
        </div>
        <div className="flex gap-3">
            <div className="w-5 h-5 text-green-400 flex-shrink-0 mt-0.5">{Icons.check}</div>
            <p className="text-slate-300 text-sm">{solution}</p>
        </div>
    </div>
);

// Stat Card
const StatCard = ({ value, label, description }: { value: string; label: string; description?: string }) => (
    <div className="bg-gradient-to-br from-brand-600/20 to-brand-800/10 border border-brand-500/20 rounded-xl p-6 text-center">
        <div className="text-4xl font-bold text-brand-400 mb-1">{value}</div>
        <div className="text-white font-semibold mb-1">{label}</div>
        {description && <div className="text-slate-400 text-sm">{description}</div>}
    </div>
);

export default function BrandedCallingGuide() {
    return (
        <>
            <Head title="The Complete Guide to Branded Calling ID | BrandCall" />

            <div className="min-h-screen bg-slate-950">
                {/* Header */}
                <header className="sticky top-0 z-50 bg-slate-950/80 backdrop-blur-xl border-b border-slate-800">
                    <div className="max-w-6xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
                        <Link href="/" className="flex items-center gap-2">
                            <div className="w-8 h-8 bg-gradient-to-br from-brand-500 to-brand-600 rounded-lg flex items-center justify-center">
                                <div className="w-5 h-5 text-white">{Icons.phone}</div>
                            </div>
                            <span className="text-xl font-bold text-white">BrandCall</span>
                        </Link>
                        <Link
                            href="/register"
                            className="bg-gradient-to-r from-brand-500 to-brand-600 text-white px-4 py-2 rounded-lg font-medium text-sm hover:opacity-90 transition-opacity"
                        >
                            Get Started
                        </Link>
                    </div>
                </header>

                {/* Hero */}
                <section className="relative py-16 md:py-24 overflow-hidden">
                    <div className="absolute inset-0 bg-gradient-to-b from-brand-600/10 via-transparent to-transparent" />
                    <div className="max-w-6xl mx-auto px-4 sm:px-6 relative">
                        <div className="max-w-3xl">
                            <div className="inline-flex items-center gap-2 bg-brand-500/10 border border-brand-500/20 rounded-full px-4 py-1.5 text-sm text-brand-400 mb-6">
                                <div className="w-4 h-4">{Icons.shield}</div>
                                <span>Industry Guide</span>
                            </div>
                            <h1 className="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6 leading-tight">
                                The Complete Guide to{' '}
                                <span className="text-transparent bg-clip-text bg-gradient-to-r from-brand-400 to-brand-600">
                                    Branded Calling ID
                                </span>
                            </h1>
                            <p className="text-xl text-slate-400 mb-8 leading-relaxed">
                                Everything you need to know about BCID: what it is, why it matters, how it works, and how it will transform the way businesses connect with customers.
                            </p>
                            <div className="flex flex-wrap gap-4">
                                <a
                                    href="#introduction"
                                    className="bg-gradient-to-r from-brand-500 to-brand-600 text-white px-6 py-3 rounded-lg font-semibold hover:opacity-90 transition-opacity flex items-center gap-2"
                                >
                                    Start Reading
                                    <div className="w-5 h-5">{Icons.arrowRight}</div>
                                </a>
                                <Link
                                    href="/register"
                                    className="border border-slate-700 text-slate-300 px-6 py-3 rounded-lg font-semibold hover:bg-slate-800 hover:text-white transition-colors"
                                >
                                    Try BrandCall Free
                                </Link>
                            </div>
                        </div>
                    </div>
                </section>

                {/* Quick Stats */}
                <section className="py-12 border-y border-slate-800 bg-slate-900/50">
                    <div className="max-w-6xl mx-auto px-4 sm:px-6">
                        <div className="grid grid-cols-2 md:grid-cols-4 gap-6">
                            <StatCard value="78%" label="Ignore Unknown Calls" description="Morning Consult, 2024" />
                            <StatCard value="32M+" label="Businesses Affected" description="Without trusted call delivery" />
                            <StatCard value="30-55%" label="Higher Answer Rates" description="With branded calling" />
                            <StatCard value="2024" label="FCC Mandate" description="Enhanced compliance required" />
                        </div>
                    </div>
                </section>

                {/* Table of Contents */}
                <section className="py-12">
                    <div className="max-w-6xl mx-auto px-4 sm:px-6">
                        <div className="bg-slate-800/30 border border-slate-700/50 rounded-2xl p-6 md:p-8">
                            <h3 className="text-xl font-bold text-white mb-6">Table of Contents</h3>
                            <div className="grid md:grid-cols-2 gap-2">
                                <TocItem number="01" title="Introduction to Branded Calling ID" href="#introduction" />
                                <TocItem number="02" title="Why BCID Exists" href="#why-bcid" />
                                <TocItem number="03" title="How BCID is Different" href="#different" />
                                <TocItem number="04" title="Limitations of Legacy Solutions" href="#limitations" />
                                <TocItem number="05" title="BCID as an Industry Solution" href="#industry-solution" />
                                <TocItem number="06" title="Benefits for Your Business" href="#benefits" />
                                <TocItem number="07" title="The Future of BCID" href="#future" />
                                <TocItem number="08" title="Getting Started with BrandCall" href="#get-started" />
                            </div>
                        </div>
                    </div>
                </section>

                {/* Main Content */}
                <main className="py-12">
                    <div className="max-w-4xl mx-auto px-4 sm:px-6">
                        {/* Section 1: Introduction */}
                        <section className="mb-20">
                            <SectionHeader
                                id="introduction"
                                title="What is Branded Calling ID?"
                                subtitle="The next generation of caller identification"
                            />

                            <div className="prose prose-invert prose-slate max-w-none">
                                <p className="text-lg text-slate-300 leading-relaxed mb-6">
                                    In an age where robocalls and spam messages plague our phones daily, consumers have become increasingly reluctant to answer calls. According to recent statistics, <strong className="text-white">78% of consumers have stopped answering unknown telephone numbers</strong>.
                                </p>

                                <p className="text-slate-400 leading-relaxed mb-8">
                                    Enter Branded Calling ID (BCID), a revolutionary technology that ensures businesses can be identified by consumers before they even pick up, helping to restore trust in voice communications.
                                </p>

                                <div className="grid md:grid-cols-2 gap-6 mb-8">
                                    <FeatureCard
                                        icon={Icons.shield}
                                        title="What is BCID?"
                                        description="BCID is an industry-adopted, CTIA-governed framework designed to restore trust in phone calls by enabling verified businesses to display their brand name, logo, call reason, and number directly on consumer devices."
                                    />
                                    <FeatureCard
                                        icon={Icons.lock}
                                        title="How Does it Work?"
                                        description="BCID integrates Rich Call Data (RCD) and STIR/SHAKEN authentication, ensuring that only legitimate, vetted businesses can display branded caller ID information. This protects against spoofing and fraud."
                                    />
                                    <FeatureCard
                                        icon={Icons.trending}
                                        title="Why Does it Matter?"
                                        description="When familiar branding displays on their phone screen, consumers know it's a vetted and authentic call. This means more meaningful connections, more conversions, and ultimately, more revenue."
                                    />
                                    <FeatureCard
                                        icon={Icons.building}
                                        title="Who is it For?"
                                        description="Any business that relies on outbound calling: healthcare, insurance, financial services, contact centers, home services, automotive, and more. If you make calls, BCID helps them get answered."
                                    />
                                </div>
                            </div>
                        </section>

                        {/* Section 2: Why BCID Exists */}
                        <section className="mb-20">
                            <SectionHeader
                                id="why-bcid"
                                title="Why BCID Exists"
                                subtitle="The problems driving industry-wide change"
                            />

                            <p className="text-slate-400 mb-8 leading-relaxed">
                                As consumer trust in phone calls declines, businesses face critical challenges in reaching their customers. Branded Calling ID was developed <strong className="text-white">in response to the problems of the industry</strong>:
                            </p>

                            <div className="space-y-4 mb-8">
                                <div className="bg-slate-800/30 border border-slate-700/50 rounded-xl p-5">
                                    <div className="flex gap-4">
                                        <div className="w-10 h-10 bg-red-500/10 text-red-400 rounded-lg flex items-center justify-center flex-shrink-0">
                                            {Icons.alert}
                                        </div>
                                        <div>
                                            <h4 className="text-white font-semibold mb-1">Declining Answer Rates</h4>
                                            <p className="text-slate-400 text-sm">78% of consumers ignore calls from unknown numbers, leading to missed business opportunities and reduced customer engagement.</p>
                                        </div>
                                    </div>
                                </div>

                                <div className="bg-slate-800/30 border border-slate-700/50 rounded-xl p-5">
                                    <div className="flex gap-4">
                                        <div className="w-10 h-10 bg-red-500/10 text-red-400 rounded-lg flex items-center justify-center flex-shrink-0">
                                            {Icons.alert}
                                        </div>
                                        <div>
                                            <h4 className="text-white font-semibold mb-1">Lack of Standardization</h4>
                                            <p className="text-slate-400 text-sm">Previous branded calling solutions operated in closed ecosystems, leading to inconsistent branding, lack of oversight, and vendor monopolies.</p>
                                        </div>
                                    </div>
                                </div>

                                <div className="bg-slate-800/30 border border-slate-700/50 rounded-xl p-5">
                                    <div className="flex gap-4">
                                        <div className="w-10 h-10 bg-red-500/10 text-red-400 rounded-lg flex items-center justify-center flex-shrink-0">
                                            {Icons.alert}
                                        </div>
                                        <div>
                                            <h4 className="text-white font-semibold mb-1">Limitations of STIR/SHAKEN</h4>
                                            <p className="text-slate-400 text-sm">While STIR/SHAKEN authenticates the calling number, it does not authenticate branding (caller name, logo, call reason), leading to misclassified calls and spam labeling issues.</p>
                                        </div>
                                    </div>
                                </div>

                                <div className="bg-slate-800/30 border border-slate-700/50 rounded-xl p-5">
                                    <div className="flex gap-4">
                                        <div className="w-10 h-10 bg-red-500/10 text-red-400 rounded-lg flex items-center justify-center flex-shrink-0">
                                            {Icons.alert}
                                        </div>
                                        <div>
                                            <h4 className="text-white font-semibold mb-1">Rising Fraud &amp; Spoofing</h4>
                                            <p className="text-slate-400 text-sm">Without cryptographic brand authentication, fraudsters continue to spoof legitimate business numbers, further damaging consumer trust in phone calls.</p>
                                        </div>
                                    </div>
                                </div>

                                <div className="bg-slate-800/30 border border-slate-700/50 rounded-xl p-5">
                                    <div className="flex gap-4">
                                        <div className="w-10 h-10 bg-red-500/10 text-red-400 rounded-lg flex items-center justify-center flex-shrink-0">
                                            {Icons.alert}
                                        </div>
                                        <div>
                                            <h4 className="text-white font-semibold mb-1">Regulatory Pressures</h4>
                                            <p className="text-slate-400 text-sm">The FCC Eighth Report &amp; Order mandates stricter call authentication, requiring businesses to adopt fully verifiable, industry-standard branded calling solutions.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        {/* Section 3: How BCID is Different */}
                        <section className="mb-20">
                            <SectionHeader
                                id="different"
                                title="5 Ways BCID is Different"
                                subtitle="A governed, industry-wide solution"
                            />

                            <p className="text-slate-400 mb-8 leading-relaxed">
                                Unlike traditional branded calling models, Branded Calling ID (BCID) was designed as a <strong className="text-white">governed, industry-wide solution</strong> that:
                            </p>

                            <div className="space-y-4">
                                {[
                                    {
                                        title: 'Fully Governed by CTIA',
                                        description: 'Ensuring compliance, standardization, and long-term adoption across the entire telecom industry.',
                                    },
                                    {
                                        title: 'Integrates Directly with STIR/SHAKEN',
                                        description: 'Combining cryptographic authentication with branding verification for end-to-end security.',
                                    },
                                    {
                                        title: 'Eliminates Vendor Monopolies',
                                        description: 'Creating an open ecosystem where enterprises own their branding and service providers control their business models.',
                                    },
                                    {
                                        title: 'Standardizes Branding Accuracy',
                                        description: 'Ensuring consistent display of business names, logos, and call reasons across all major carriers.',
                                    },
                                    {
                                        title: 'Provides an Open, Competitive Marketplace',
                                        description: 'Allowing businesses to choose their provider, reducing costs, and improving service flexibility.',
                                    },
                                ].map((item, index) => (
                                    <div key={index} className="flex gap-4 items-start">
                                        <div className="w-8 h-8 bg-brand-500/10 text-brand-400 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <div className="w-5 h-5">{Icons.check}</div>
                                        </div>
                                        <div>
                                            <h4 className="text-white font-semibold mb-1">{item.title}</h4>
                                            <p className="text-slate-400 text-sm">{item.description}</p>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </section>

                        {/* Section 4: Limitations of Legacy Solutions */}
                        <section className="mb-20">
                            <SectionHeader
                                id="limitations"
                                title="Limitations of Traditional Solutions"
                                subtitle="Why legacy branded calling has failed"
                            />

                            <p className="text-slate-400 mb-8 leading-relaxed">
                                Traditional branded calling models have failed to meet the needs of the industry due to:
                            </p>

                            <div className="grid md:grid-cols-2 gap-6">
                                <div className="bg-slate-800/30 border border-slate-700/50 rounded-xl p-6">
                                    <h4 className="text-white font-semibold mb-2">Outdated Caller ID Systems</h4>
                                    <p className="text-slate-400 text-sm">CNAM databases were designed for landlines, leading to branding inconsistencies on mobile networks and unreliable caller identification.</p>
                                </div>
                                <div className="bg-slate-800/30 border border-slate-700/50 rounded-xl p-6">
                                    <h4 className="text-white font-semibold mb-2">Out-of-Band Branding Models</h4>
                                    <p className="text-slate-400 text-sm">Some solutions applied branding outside the call path, making spoofing easier and allowing fraudsters to manipulate caller ID information.</p>
                                </div>
                                <div className="bg-slate-800/30 border border-slate-700/50 rounded-xl p-6">
                                    <h4 className="text-white font-semibold mb-2">Lack of Centralized Governance</h4>
                                    <p className="text-slate-400 text-sm">No independent enforcement existed to ensure branding accuracy or prevent bad actors from abusing branded caller ID.</p>
                                </div>
                                <div className="bg-slate-800/30 border border-slate-700/50 rounded-xl p-6">
                                    <h4 className="text-white font-semibold mb-2">Opaque Pricing &amp; Vendor Lock-In</h4>
                                    <p className="text-slate-400 text-sm">Legacy vendors controlled branding, dictating prices, limiting competition, and forcing businesses into restrictive agreements that lacked transparency.</p>
                                </div>
                            </div>

                            <div className="mt-8 p-6 bg-brand-500/10 border border-brand-500/20 rounded-xl">
                                <p className="text-slate-300 text-center">
                                    Unlike these traditional models, <strong className="text-white">Branded Calling ID (BCID) is an open, standardized solution</strong> built to serve the entire telecom industry including enterprises, service providers, and consumers.
                                </p>
                            </div>
                        </section>

                        {/* Section 5: BCID as an Industry Solution */}
                        <section className="mb-20">
                            <SectionHeader
                                id="industry-solution"
                                title="BCID as an Industry Solution"
                                subtitle="Problem vs. Solution"
                            />

                            <div className="bg-slate-800/30 border border-slate-700/50 rounded-xl overflow-hidden">
                                <div className="grid md:grid-cols-2 border-b border-slate-700/50">
                                    <div className="p-4 bg-red-500/5 border-r border-slate-700/50">
                                        <h4 className="font-semibold text-red-400 text-center">The Problem</h4>
                                    </div>
                                    <div className="p-4 bg-green-500/5">
                                        <h4 className="font-semibold text-green-400 text-center">The Solution</h4>
                                    </div>
                                </div>
                                <div className="p-6">
                                    <ProblemSolutionRow
                                        problem="Only 5,000 enterprise businesses currently benefit from branded calling, leaving over 32 million businesses without trusted call delivery."
                                        solution="Enables every legitimate business, regardless of size, to be recognized, trusted, and answered."
                                    />
                                    <ProblemSolutionRow
                                        problem="Consumers aren't answering calls from legitimate businesses due to an inability to identify the call, reducing trust."
                                        solution="Verified, cryptographically signed caller identity, reducing spoofing and restoring trust."
                                    />
                                    <ProblemSolutionRow
                                        problem="Existing branded calling providers have failed to offer a consistent, affordable solution available to all."
                                        solution="Creates an open, competitive ecosystem governed by CTIA, offering businesses control without restrictive contracts."
                                    />
                                    <ProblemSolutionRow
                                        problem="The FCC STIR/SHAKEN mandate is still being implemented, with providers undermining its effectiveness."
                                        solution="Educates about the FCC's mandate to deploy end-to-end STIR/SHAKEN, ensuring nationwide adoption."
                                    />
                                    <ProblemSolutionRow
                                        problem="Legacy branded calling solutions are monopolistic and limit competition."
                                        solution="An open, standards-based solution that eliminates vendor lock-in, giving enterprises and OSPs full control over their branding."
                                    />
                                    <ProblemSolutionRow
                                        problem="No independent enforcement mechanism exists to ensure branding compliance."
                                        solution="Standardized governance under CTIA, ensuring consistent, compliant branding across the telecom ecosystem."
                                    />
                                </div>
                            </div>
                        </section>

                        {/* Section 6: Benefits for Your Business */}
                        <section className="mb-20">
                            <SectionHeader
                                id="benefits"
                                title="Benefits for Your Business"
                                subtitle="What BCID will do for you"
                            />

                            <div className="grid gap-6">
                                {[
                                    {
                                        icon: Icons.shield,
                                        title: 'Automated Compliance',
                                        description: 'Automates compliance with FCC, CTIA, and STIR/SHAKEN mandates, ensuring your calls are legally and securely authenticated without the burden.',
                                    },
                                    {
                                        icon: Icons.phone,
                                        title: 'Higher Answer Rates',
                                        description: 'Ensures your calls display a verified business name, logo, and call reason, which boosts answer rates, enhances customer experience, and increases revenue.',
                                    },
                                    {
                                        icon: Icons.building,
                                        title: 'Direct Brand Control',
                                        description: 'Direct control over your brand, ensuring accurate and consistent branding across all carrier networks.',
                                    },
                                    {
                                        icon: Icons.lock,
                                        title: 'Reduced Mislabeling Risk',
                                        description: 'Reduces the risk of legitimate calls being mislabeled, improves outbound call identity with a branded call presentation.',
                                    },
                                    {
                                        icon: Icons.users,
                                        title: 'Industry-Specific Solutions',
                                        description: 'Compliant, industry-specific branded calling solutions, ensuring secure communications while meeting regulatory needs.',
                                    },
                                    {
                                        icon: Icons.trending,
                                        title: 'Real-Time Reporting',
                                        description: 'Real-time reporting on your branded call performance, enabling you to track success, measure engagement, and optimize calling strategies for improved ROI.',
                                    },
                                ].map((item, index) => (
                                    <div key={index} className="bg-slate-800/30 border border-slate-700/50 rounded-xl p-6 flex gap-5">
                                        <div className="w-12 h-12 bg-brand-500/10 text-brand-400 rounded-xl flex items-center justify-center flex-shrink-0">
                                            <div className="w-6 h-6">{item.icon}</div>
                                        </div>
                                        <div>
                                            <h4 className="text-white font-semibold mb-2">{item.title}</h4>
                                            <p className="text-slate-400 text-sm leading-relaxed">{item.description}</p>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </section>

                        {/* Section 7: The Future of BCID */}
                        <section className="mb-20">
                            <SectionHeader
                                id="future"
                                title="The Future of BCID"
                                subtitle="Industry adoption and FCC mandates"
                            />

                            <div className="prose prose-invert prose-slate max-w-none">
                                <p className="text-lg text-slate-300 leading-relaxed mb-6">
                                    The future of branded calling is not just a vision—it is already in motion. The next-generation branded calling solution of Branded Calling ID (BCID) is backed by strong regulatory momentum, carrier participation, and growing enterprise demand.
                                </p>

                                <p className="text-slate-400 leading-relaxed mb-6">
                                    As robocalls and impersonation threats continue to erode consumer trust, the FCC has responded by mandating industry-wide adoption of the STIR/SHAKEN framework. These mandates require originating service providers to implement cryptographically signed caller identity and ensure traceback capabilities for all voice traffic.
                                </p>

                                <div className="bg-slate-800/30 border border-slate-700/50 rounded-xl p-6 mb-8">
                                    <h4 className="text-white font-semibold mb-4">Carrier Adoption Status</h4>
                                    <div className="space-y-3">
                                        <div className="flex items-center gap-3">
                                            <div className="w-5 h-5 text-green-400">{Icons.check}</div>
                                            <span className="text-slate-300"><strong className="text-white">T-Mobile</strong> — Fully live with BCID</span>
                                        </div>
                                        <div className="flex items-center gap-3">
                                            <div className="w-5 h-5 text-yellow-400">{Icons.alert}</div>
                                            <span className="text-slate-300"><strong className="text-white">Verizon</strong> — Launching soon</span>
                                        </div>
                                        <div className="flex items-center gap-3">
                                            <div className="w-5 h-5 text-yellow-400">{Icons.alert}</div>
                                            <span className="text-slate-300"><strong className="text-white">AT&amp;T</strong> — Regulatory expectations mounting</span>
                                        </div>
                                    </div>
                                </div>

                                <p className="text-slate-400 leading-relaxed mb-6">
                                    BCID is rapidly becoming the preferred framework for authenticated branded calling. The CTIA-led structure ensures fairness, transparency, and industry alignment, which proprietary models from legacy providers have failed to deliver over the past seven years.
                                </p>

                                <blockquote className="border-l-4 border-brand-500 pl-6 my-8">
                                    <p className="text-lg text-slate-300 italic mb-2">
                                        "Vendor-specific solutions have plateaued. They serve a small portion of the market, and their proprietary infrastructure locks out innovation. BCID empowers carriers and service providers to participate directly. It is a model built for long-term, industry-wide trust."
                                    </p>
                                    <cite className="text-slate-500 text-sm not-italic">— Doug Ranalli, Gated Networks</cite>
                                </blockquote>
                            </div>
                        </section>

                        {/* Section 8: Getting Started */}
                        <section className="mb-12">
                            <SectionHeader
                                id="get-started"
                                title="Getting Started with BrandCall"
                                subtitle="Ready to transform your outbound calling?"
                            />

                            <div className="bg-gradient-to-br from-brand-600/20 to-brand-800/10 border border-brand-500/30 rounded-2xl p-8 md:p-12 text-center">
                                <div className="w-16 h-16 bg-brand-500/20 text-brand-400 rounded-2xl flex items-center justify-center mx-auto mb-6">
                                    <div className="w-8 h-8">{Icons.phone}</div>
                                </div>
                                <h3 className="text-2xl md:text-3xl font-bold text-white mb-4">
                                    Stop Getting Ignored. Start Getting Answered.
                                </h3>
                                <p className="text-slate-400 mb-8 max-w-xl mx-auto">
                                    BrandCall makes it easy to implement BCID for your business. Display your company name, logo, and call reason on every outbound call. Boost answer rates by 30-55%.
                                </p>
                                <div className="flex flex-col sm:flex-row gap-4 justify-center">
                                    <Link
                                        href="/register"
                                        className="bg-gradient-to-r from-brand-500 to-brand-600 text-white px-8 py-4 rounded-xl font-semibold text-lg hover:opacity-90 transition-opacity"
                                    >
                                        Start Your Free Trial
                                    </Link>
                                    <Link
                                        href="/"
                                        className="border border-slate-600 text-slate-300 px-8 py-4 rounded-xl font-semibold text-lg hover:bg-slate-800 hover:text-white transition-colors"
                                    >
                                        Learn More
                                    </Link>
                                </div>
                            </div>
                        </section>
                    </div>
                </main>

                {/* Footer */}
                <footer className="border-t border-slate-800 py-12">
                    <div className="max-w-6xl mx-auto px-4 sm:px-6">
                        <div className="flex flex-col md:flex-row items-center justify-between gap-4">
                            <div className="flex items-center gap-2">
                                <div className="w-6 h-6 bg-gradient-to-br from-brand-500 to-brand-600 rounded flex items-center justify-center">
                                    <div className="w-4 h-4 text-white">{Icons.phone}</div>
                                </div>
                                <span className="font-semibold text-white">BrandCall</span>
                            </div>
                            <div className="flex gap-6 text-sm text-slate-500">
                                <Link href="/" className="hover:text-slate-300 transition-colors">Home</Link>
                                <Link href="/register" className="hover:text-slate-300 transition-colors">Get Started</Link>
                                <a href="https://www.ctia.org/" target="_blank" rel="noopener noreferrer" className="hover:text-slate-300 transition-colors">CTIA</a>
                                <a href="https://brandedcallingid.com/" target="_blank" rel="noopener noreferrer" className="hover:text-slate-300 transition-colors">BCID Info</a>
                            </div>
                            <p className="text-sm text-slate-500">
                                © {new Date().getFullYear()} BrandCall. All rights reserved.
                            </p>
                        </div>
                    </div>
                </footer>
            </div>
        </>
    );
}
