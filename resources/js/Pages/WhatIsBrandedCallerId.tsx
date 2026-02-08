import { Head, Link } from '@inertiajs/react';
import MarketingLayout from '@/Layouts/MarketingLayout';

export default function WhatIsBrandedCallerId() {
    return (
        <MarketingLayout title="What is Branded Caller ID?">
            <Head>
                <title>What is Branded Caller ID? | How It Works & Why It Matters</title>
                <meta name="description" content="Branded caller ID displays your business name, logo, and call reason on recipients' phones. Learn how it works, why 95% of people ignore unknown numbers, and how to get started." />
                <meta name="keywords" content="what is branded caller id, branded caller id, branded calling, caller id branding, business caller id" />
                <meta property="og:title" content="What is Branded Caller ID? | BrandCall" />
                <meta property="og:description" content="Branded caller ID displays your verified business name, logo, and call reason on every outbound call. Boost answer rates by 48%+." />
                <meta property="og:type" content="article" />
                <link rel="canonical" href="https://brandcall.io/what-is-branded-caller-id" />
            </Head>

            {/* Hero */}
            <section className="py-16 sm:py-24">
                <div className="max-w-4xl mx-auto px-6 text-center">
                    <span className="inline-flex items-center px-3 py-1 text-xs font-medium text-brand-600 dark:text-brand-400 bg-brand-600/10 rounded-full border border-brand-600/20 mb-6">
                        Explainer Guide
                    </span>
                    <h1 className="text-4xl sm:text-5xl lg:text-6xl font-bold text-theme-primary mb-6">
                        What is <span className="bg-gradient-to-r from-brand-400 via-purple-400 to-brand-400 bg-clip-text text-transparent">Branded Caller ID</span>?
                    </h1>
                    <p className="text-lg sm:text-xl text-theme-secondary max-w-2xl mx-auto leading-relaxed">
                        Branded caller ID lets recipients see your business name, logo, and call reason 
                        before they pick up — turning unknown numbers into trusted connections.
                    </p>
                </div>
            </section>

            {/* The Problem */}
            <section className="pb-16 sm:pb-24">
                <div className="max-w-4xl mx-auto px-6">
                    <div className="grid md:grid-cols-2 gap-8 mb-16">
                        <div className="bg-red-500/5 border border-red-500/20 rounded-2xl p-8">
                            <div className="text-red-500 text-sm font-semibold uppercase tracking-wide mb-3">Without Branded Caller ID</div>
                            <div className="space-y-4">
                                <div className="bg-theme-primary rounded-xl p-4 border border-theme-primary">
                                    <div className="text-xs text-theme-muted mb-1">Incoming call</div>
                                    <div className="text-lg font-semibold text-theme-primary">(555) 123-4567</div>
                                    <div className="text-sm text-red-500 mt-1">⚠ Spam Likely</div>
                                </div>
                                <p className="text-sm text-theme-secondary">Recipients see an unknown number — or worse, a spam warning. <strong className="text-theme-primary">95% will ignore this call.</strong></p>
                            </div>
                        </div>
                        <div className="bg-green-500/5 border border-green-500/20 rounded-2xl p-8">
                            <div className="text-green-500 text-sm font-semibold uppercase tracking-wide mb-3">With Branded Caller ID</div>
                            <div className="space-y-4">
                                <div className="bg-theme-primary rounded-xl p-4 border border-theme-primary">
                                    <div className="text-xs text-theme-muted mb-1">Incoming call</div>
                                    <div className="flex items-center gap-3">
                                        <div className="h-10 w-10 rounded-full bg-brand-600 flex items-center justify-center text-white text-sm font-bold">BC</div>
                                        <div>
                                            <div className="text-lg font-semibold text-theme-primary">BrandCall Inc.</div>
                                            <div className="text-sm text-green-500">✓ Verified • Appointment Reminder</div>
                                        </div>
                                    </div>
                                </div>
                                <p className="text-sm text-theme-secondary">Recipients see exactly who's calling and why. <strong className="text-theme-primary">Answer rates jump 48%+.</strong></p>
                            </div>
                        </div>
                    </div>

                    {/* How it Works */}
                    <div className="mb-16">
                        <h2 className="text-3xl font-bold text-theme-primary mb-8">How Does Branded Caller ID Work?</h2>
                        <p className="text-lg text-theme-secondary mb-8 leading-relaxed">
                            Branded caller ID — sometimes called Rich Call Data (RCD) — works by attaching verified business information 
                            to your outbound calls using the <Link href="/stir-shaken-explained" className="text-brand-500 hover:text-brand-400 underline">STIR/SHAKEN framework</Link>. 
                            Here's the simple version:
                        </p>
                        <div className="space-y-6">
                            {[
                                { step: '1', title: 'You verify your business', desc: 'Complete a quick KYC process — upload your business license and ID. This proves you\'re a legitimate business, not a spammer.' },
                                { step: '2', title: 'You register your phone numbers', desc: 'Connect the numbers you call from. BrandCall verifies you\'re authorized to use them and registers them with carrier databases.' },
                                { step: '3', title: 'You set your brand profile', desc: 'Upload your logo, set your business name, and define call reasons (e.g., "Appointment Reminder" or "Delivery Update").' },
                                { step: '4', title: 'Every call carries your identity', desc: 'When you dial out, your brand info is cryptographically signed and delivered to the recipient\'s phone — appearing on-screen before they answer.' },
                            ].map((item) => (
                                <div key={item.step} className="flex gap-4">
                                    <div className="flex-shrink-0 h-10 w-10 rounded-full bg-brand-600 text-white flex items-center justify-center font-bold text-sm">
                                        {item.step}
                                    </div>
                                    <div>
                                        <h3 className="text-lg font-semibold text-theme-primary mb-1">{item.title}</h3>
                                        <p className="text-theme-secondary leading-relaxed">{item.desc}</p>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>

                    {/* Why It Matters */}
                    <div className="mb-16">
                        <h2 className="text-3xl font-bold text-theme-primary mb-8">Why Does Branded Caller ID Matter?</h2>
                        <div className="grid sm:grid-cols-3 gap-6">
                            {[
                                { stat: '95%', label: 'of people ignore calls from unknown numbers', source: 'Hiya 2025' },
                                { stat: '48%+', label: 'improvement in answer rates with branded calling', source: 'Industry average' },
                                { stat: '$14B', label: 'lost annually to robocall fraud in the U.S.', source: 'FCC estimate' },
                            ].map((item) => (
                                <div key={item.stat} className="bg-theme-secondary border border-theme-primary rounded-xl p-6 text-center">
                                    <div className="text-3xl sm:text-4xl font-bold text-brand-500 mb-2">{item.stat}</div>
                                    <p className="text-sm text-theme-secondary mb-2">{item.label}</p>
                                    <p className="text-xs text-theme-muted">{item.source}</p>
                                </div>
                            ))}
                        </div>
                    </div>

                    {/* Who Uses It */}
                    <div className="mb-16">
                        <h2 className="text-3xl font-bold text-theme-primary mb-8">Who Uses Branded Caller ID?</h2>
                        <div className="grid sm:grid-cols-2 gap-6">
                            {[
                                { title: 'Healthcare Providers', desc: 'Appointment reminders, test results, telehealth follow-ups. Patients answer when they see their doctor\'s name.' },
                                { title: 'Financial Services', desc: 'Fraud alerts, account notifications, loan updates. Customers trust calls from verified banks.' },
                                { title: 'Call Centers & BPOs', desc: 'Higher connect rates mean more conversations per hour and lower cost per contact.' },
                                { title: 'Sales Teams', desc: 'Prospects are 48% more likely to answer a branded call than an unknown number.' },
                            ].map((item) => (
                                <div key={item.title} className="bg-theme-secondary border border-theme-primary rounded-xl p-6">
                                    <h3 className="text-lg font-semibold text-theme-primary mb-2">{item.title}</h3>
                                    <p className="text-sm text-theme-secondary leading-relaxed">{item.desc}</p>
                                </div>
                            ))}
                        </div>
                    </div>

                    {/* Branded Caller ID vs Traditional Caller ID */}
                    <div className="mb-16">
                        <h2 className="text-3xl font-bold text-theme-primary mb-8">Branded Caller ID vs. Traditional CNAM</h2>
                        <div className="overflow-x-auto">
                            <table className="w-full text-sm">
                                <thead>
                                    <tr className="border-b border-theme-primary">
                                        <th className="text-left py-3 pr-4 text-theme-primary font-semibold">Feature</th>
                                        <th className="text-left py-3 px-4 text-theme-muted font-semibold">Traditional CNAM</th>
                                        <th className="text-left py-3 pl-4 text-brand-500 font-semibold">Branded Caller ID</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-theme-primary">
                                    {[
                                        ['Business name', '15 characters max', 'Full name displayed'],
                                        ['Logo', '✗ Not supported', '✓ Full-color logo'],
                                        ['Call reason', '✗ Not supported', '✓ "Appointment Reminder"'],
                                        ['Verification', 'None', '✓ Cryptographic signing'],
                                        ['Spam protection', 'None', '✓ Active reputation management'],
                                        ['Carrier support', 'Landlines mainly', 'All major mobile carriers'],
                                    ].map(([feature, traditional, branded]) => (
                                        <tr key={feature}>
                                            <td className="py-3 pr-4 text-theme-primary font-medium">{feature}</td>
                                            <td className="py-3 px-4 text-theme-muted">{traditional}</td>
                                            <td className="py-3 pl-4 text-theme-secondary">{branded}</td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {/* FAQ Schema-friendly section */}
                    <div className="mb-16">
                        <h2 className="text-3xl font-bold text-theme-primary mb-8">Common Questions</h2>
                        <div className="space-y-6">
                            {[
                                { q: 'Does branded caller ID work on all phones?', a: 'It works on all major U.S. carriers (AT&T, Verizon, T-Mobile) and most modern smartphones. Display varies slightly by device and OS.' },
                                { q: 'How long does it take to set up?', a: 'Most businesses are live within minutes. Complete KYC verification, upload your brand assets, and start calling. No weeks-long carrier negotiations.' },
                                { q: 'Is branded caller ID the same as CNAM?', a: 'No. CNAM is a legacy system limited to 15 characters with no logo or call reason. Branded caller ID uses modern STIR/SHAKEN protocols to deliver rich information including your logo and verified business name.' },
                                { q: 'How much does it cost?', a: 'BrandCall uses transparent per-call pricing with no setup fees. Visit our pricing page for current rates.' },
                            ].map((item) => (
                                <div key={item.q} className="border border-theme-primary rounded-xl p-6">
                                    <h3 className="text-base font-semibold text-theme-primary mb-2">{item.q}</h3>
                                    <p className="text-sm text-theme-secondary leading-relaxed">{item.a}</p>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            </section>

            {/* CTA */}
            <section className="pb-24">
                <div className="max-w-3xl mx-auto px-6">
                    <div className="bg-brand-600/10 border border-brand-600/20 rounded-2xl p-8 sm:p-12 text-center">
                        <h2 className="text-2xl font-bold text-theme-primary mb-3">
                            Ready to brand your calls?
                        </h2>
                        <p className="text-theme-secondary mb-6">
                            Set up in minutes, not weeks. See your business name on every outbound call.
                        </p>
                        <div className="flex flex-col sm:flex-row items-center justify-center gap-4">
                            <Link
                                href={route('register')}
                                className="inline-flex items-center px-6 py-3 text-sm font-medium text-white bg-brand-600 rounded-lg hover:bg-brand-500 transition-colors"
                            >
                                Get Started Free
                            </Link>
                            <Link
                                href="/pricing"
                                className="inline-flex items-center px-6 py-3 text-sm font-medium text-theme-primary border border-theme-primary rounded-lg hover:bg-theme-tertiary transition-colors"
                            >
                                View Pricing
                            </Link>
                        </div>
                    </div>
                </div>
            </section>
        </MarketingLayout>
    );
}
