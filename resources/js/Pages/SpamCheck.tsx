import { Head, Link } from '@inertiajs/react';
import MarketingLayout from '@/Layouts/MarketingLayout';

export default function SpamCheck() {
    return (
        <MarketingLayout title="Is Your Number Showing as Spam?">
            <Head>
                <title>Is Your Business Number Showing as Spam? | Free Reputation Check</title>
                <meta name="description" content="If your business calls show as 'Spam Likely' or 'Scam,' you're losing 95% of your connections. Check your number reputation and fix it in days, not weeks." />
                <meta name="keywords" content="spam likely fix, phone number spam label, remove spam label, number reputation, caller id spam" />
                <meta property="og:title" content="Is Your Number Showing as Spam? | BrandCall" />
                <meta property="og:description" content="Stop losing customers to spam labels. Check your number reputation and get it fixed." />
                <meta property="og:type" content="website" />
                <link rel="canonical" href="https://brandcall.io/spam-check" />
            </Head>

            {/* Hero - High Impact */}
            <section className="py-16 sm:py-24 bg-gradient-to-b from-red-500/5 to-transparent">
                <div className="max-w-4xl mx-auto px-6 text-center">
                    <div className="inline-flex items-center gap-2 px-4 py-2 bg-red-500/10 border border-red-500/20 rounded-full text-red-500 text-sm font-medium mb-8">
                        <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" /></svg>
                        Your calls might be getting blocked right now
                    </div>
                    <h1 className="text-4xl sm:text-5xl lg:text-6xl font-bold text-theme-primary mb-6">
                        Is Your Number Showing as{' '}
                        <span className="text-red-500">"Spam Likely"</span>?
                    </h1>
                    <p className="text-lg sm:text-xl text-theme-secondary max-w-2xl mx-auto mb-10 leading-relaxed">
                        If your outbound calls display spam warnings, <strong>95% of recipients will never answer</strong>. 
                        Every ignored call costs you money, customers, and revenue.
                    </p>
                    <div className="flex flex-col sm:flex-row items-center justify-center gap-4">
                        <Link
                            href={route('register')}
                            className="inline-flex items-center px-8 py-4 text-base font-semibold text-white bg-brand-600 rounded-lg hover:bg-brand-500 transition-colors shadow-lg shadow-brand-600/20"
                        >
                            Check My Number Reputation
                        </Link>
                        <a
                            href="mailto:sales@brandcall.io"
                            className="inline-flex items-center px-6 py-4 text-base font-medium text-theme-primary border border-theme-primary rounded-lg hover:bg-theme-tertiary transition-colors"
                        >
                            Talk to an Expert
                        </a>
                    </div>
                </div>
            </section>

            {/* The Cost of Spam Labels */}
            <section className="pb-16 sm:pb-24">
                <div className="max-w-5xl mx-auto px-6">
                    <h2 className="text-3xl font-bold text-theme-primary mb-4 text-center">The Real Cost of Spam Labels</h2>
                    <p className="text-lg text-theme-secondary text-center max-w-2xl mx-auto mb-12">
                        A single spam label doesn't just hurt one call — it cascades across your entire operation.
                    </p>
                    <div className="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        {[
                            { icon: '📉', stat: '80%', label: 'of flagged calls go unanswered', color: 'red' },
                            { icon: '💸', stat: '$0.25+', label: 'wasted per unanswered dial attempt', color: 'red' },
                            { icon: '🔄', stat: '3-5x', label: 'more attempts needed to reach the same contact', color: 'red' },
                            { icon: '🏚️', stat: '72hrs', label: 'before a flagged number becomes "burned"', color: 'red' },
                        ].map((item) => (
                            <div key={item.label} className="bg-theme-secondary border border-theme-primary rounded-xl p-6 text-center">
                                <div className="text-3xl mb-3">{item.icon}</div>
                                <div className="text-2xl font-bold text-red-500 mb-1">{item.stat}</div>
                                <p className="text-sm text-theme-secondary">{item.label}</p>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* Why Numbers Get Flagged */}
            <section className="pb-16 sm:pb-24">
                <div className="max-w-4xl mx-auto px-6">
                    <h2 className="text-3xl font-bold text-theme-primary mb-8">Why Does This Happen?</h2>
                    <p className="text-lg text-theme-secondary mb-8">
                        Carriers like AT&T, Verizon, and T-Mobile use analytics engines to flag suspicious calling patterns. 
                        Even legitimate businesses get caught in the crossfire:
                    </p>
                    <div className="space-y-4">
                        {[
                            { title: 'High call volume from a single number', desc: 'Calling too many people from one number triggers robocall detection.' },
                            { title: 'Low answer rates', desc: 'If most people don\'t pick up, carriers assume you\'re a spammer.' },
                            { title: 'Consumer complaints', desc: 'Even one "Report Spam" tap can cascade into a label across all carriers.' },
                            { title: 'No STIR/SHAKEN attestation', desc: 'Calls without cryptographic verification are treated as suspicious by default.' },
                            { title: 'Predictive dialer patterns', desc: 'Aggressive dialing cadences look identical to robocall behavior.' },
                        ].map((item) => (
                            <div key={item.title} className="flex gap-4 p-4 bg-theme-secondary border border-theme-primary rounded-xl">
                                <div className="flex-shrink-0 h-6 w-6 text-red-500 mt-0.5">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01" /><circle cx="12" cy="12" r="10" strokeWidth={2} /></svg>
                                </div>
                                <div>
                                    <h3 className="font-semibold text-theme-primary">{item.title}</h3>
                                    <p className="text-sm text-theme-secondary">{item.desc}</p>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* The Fix */}
            <section className="pb-16 sm:pb-24 bg-theme-secondary border-y border-theme-primary py-16 sm:py-24">
                <div className="max-w-4xl mx-auto px-6">
                    <h2 className="text-3xl font-bold text-theme-primary mb-4 text-center">How BrandCall Fixes It</h2>
                    <p className="text-lg text-theme-secondary text-center max-w-2xl mx-auto mb-12">
                        We don't just remove spam labels — we prevent them from ever appearing.
                    </p>
                    <div className="space-y-8">
                        {[
                            { step: '1', title: 'Reputation Audit', desc: 'We scan your numbers across all carrier analytics engines and show you exactly where you stand — which numbers are clean, flagged, or burned.', time: 'Instant' },
                            { step: '2', title: 'Spam Label Remediation', desc: 'Our team works directly with carrier databases to remove existing spam/scam labels from your numbers. Most labels cleared in 1-3 business days.', time: '1-3 days' },
                            { step: '3', title: 'STIR/SHAKEN Verification', desc: 'We ensure every call gets A-level attestation — the highest trust signal carriers recognize. This alone prevents most future flagging.', time: 'Same day' },
                            { step: '4', title: 'Brand Your Calls', desc: 'Display your verified business name, logo, and call reason so recipients know exactly who\'s calling. Answer rates improve 48%+.', time: 'Same day' },
                            { step: '5', title: 'Ongoing Monitoring', desc: 'Real-time alerts if any number shows signs of reputation degradation. We catch problems before they become spam labels.', time: 'Always on' },
                        ].map((item) => (
                            <div key={item.step} className="flex gap-6">
                                <div className="flex-shrink-0">
                                    <div className="h-12 w-12 rounded-full bg-brand-600 text-white flex items-center justify-center font-bold text-lg">
                                        {item.step}
                                    </div>
                                </div>
                                <div className="flex-1">
                                    <div className="flex items-center gap-3 mb-1">
                                        <h3 className="text-lg font-semibold text-theme-primary">{item.title}</h3>
                                        <span className="text-xs font-medium text-brand-500 bg-brand-600/10 px-2 py-0.5 rounded-full">{item.time}</span>
                                    </div>
                                    <p className="text-theme-secondary leading-relaxed">{item.desc}</p>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* Social Proof / Stats */}
            <section className="py-16 sm:py-24">
                <div className="max-w-5xl mx-auto px-6">
                    <div className="grid sm:grid-cols-3 gap-8 text-center">
                        {[
                            { stat: '48%+', label: 'Average answer rate improvement', desc: 'after implementing branded caller ID' },
                            { stat: '< 3 days', label: 'Average remediation time', desc: 'to clear spam labels from your numbers' },
                            { stat: 'Minutes', label: 'Setup time', desc: 'not weeks — go live the same day you sign up' },
                        ].map((item) => (
                            <div key={item.stat}>
                                <div className="text-4xl sm:text-5xl font-bold text-brand-500 mb-2">{item.stat}</div>
                                <div className="text-lg font-semibold text-theme-primary mb-1">{item.label}</div>
                                <p className="text-sm text-theme-muted">{item.desc}</p>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* CTA */}
            <section className="pb-24">
                <div className="max-w-3xl mx-auto px-6">
                    <div className="bg-gradient-to-br from-brand-600 to-purple-600 rounded-2xl p-8 sm:p-12 text-center">
                        <h2 className="text-2xl sm:text-3xl font-bold text-white mb-4">
                            Stop Losing Customers to Spam Labels
                        </h2>
                        <p className="text-white/80 mb-8 max-w-lg mx-auto">
                            Find out if your numbers are flagged. Get them fixed. Start getting answered again. 
                            Setup takes minutes, not weeks.
                        </p>
                        <div className="flex flex-col sm:flex-row items-center justify-center gap-4">
                            <Link
                                href={route('register')}
                                className="inline-flex items-center px-8 py-4 text-base font-semibold text-brand-600 bg-white rounded-lg hover:bg-gray-100 transition-colors"
                            >
                                Get Your Free Reputation Check
                            </Link>
                            <a
                                href="mailto:sales@brandcall.io"
                                className="inline-flex items-center px-6 py-4 text-base font-medium text-white border border-white/30 rounded-lg hover:bg-white/10 transition-colors"
                            >
                                Contact Sales
                            </a>
                        </div>
                    </div>
                </div>
            </section>
        </MarketingLayout>
    );
}
