import { Head, Link } from '@inertiajs/react';
import MarketingLayout from '@/Layouts/MarketingLayout';

export default function StirShakenExplained() {
    return (
        <MarketingLayout title="STIR/SHAKEN Explained">
            <Head>
                <title>STIR/SHAKEN Explained: Plain-Language Guide to Call Authentication</title>
                <meta name="description" content="STIR/SHAKEN is the FCC-mandated call authentication framework that verifies caller identity. Learn what it means for your business in plain English." />
                <meta name="keywords" content="stir shaken explained, what is stir shaken, call authentication, fcc stir shaken, caller id verification" />
                <meta property="og:title" content="STIR/SHAKEN Explained in Plain English | BrandCall" />
                <meta property="og:description" content="The FCC requires STIR/SHAKEN for all voice calls. Here's what it means for your business — no jargon." />
                <meta property="og:type" content="article" />
                <link rel="canonical" href="https://brandcall.io/stir-shaken-explained" />
            </Head>

            {/* Hero */}
            <section className="py-16 sm:py-24">
                <div className="max-w-4xl mx-auto px-6 text-center">
                    <span className="inline-flex items-center px-3 py-1 text-xs font-medium text-brand-600 dark:text-brand-400 bg-brand-600/10 rounded-full border border-brand-600/20 mb-6">
                        Plain-Language Guide
                    </span>
                    <h1 className="text-4xl sm:text-5xl lg:text-6xl font-bold text-theme-primary mb-6">
                        STIR/SHAKEN <span className="bg-gradient-to-r from-brand-400 via-purple-400 to-brand-400 bg-clip-text text-transparent">Explained</span>
                    </h1>
                    <p className="text-lg sm:text-xl text-theme-secondary max-w-2xl mx-auto leading-relaxed">
                        The FCC-mandated call authentication framework — explained without the jargon. 
                        What it is, why it matters, and what your business needs to do.
                    </p>
                </div>
            </section>

            {/* Content */}
            <section className="pb-24">
                <div className="max-w-4xl mx-auto px-6">

                    {/* What is it */}
                    <div className="mb-16">
                        <h2 className="text-3xl font-bold text-theme-primary mb-6">What Is STIR/SHAKEN?</h2>
                        <p className="text-lg text-theme-secondary mb-6 leading-relaxed">
                            <strong className="text-theme-primary">STIR/SHAKEN</strong> is a set of technical standards that lets phone carriers verify 
                            that a caller is who they claim to be. Think of it like the padlock icon on a website — it proves the connection is legitimate.
                        </p>
                        <div className="bg-theme-secondary border border-theme-primary rounded-xl p-6 mb-6">
                            <p className="text-sm text-theme-muted mb-2">What the acronyms stand for:</p>
                            <ul className="space-y-2 text-sm text-theme-secondary">
                                <li><strong className="text-theme-primary">STIR</strong> = Secure Telephone Identity Revisited (the protocol)</li>
                                <li><strong className="text-theme-primary">SHAKEN</strong> = Signature-based Handling of Asserted Information Using toKENs (how carriers implement it)</li>
                            </ul>
                        </div>
                        <p className="text-lg text-theme-secondary leading-relaxed">
                            In plain English: when you make a call, your carrier digitally "signs" it — like a wax seal on a letter. 
                            The receiving carrier checks that seal and can tell if the call is genuine or if someone is spoofing your number.
                        </p>
                    </div>

                    {/* Why the FCC mandated it */}
                    <div className="mb-16">
                        <h2 className="text-3xl font-bold text-theme-primary mb-6">Why Did the FCC Mandate It?</h2>
                        <p className="text-lg text-theme-secondary mb-6 leading-relaxed">
                            Robocalls are the #1 consumer complaint to the FCC. In 2024, Americans received over <strong className="text-theme-primary">50 billion robocalls</strong>. 
                            Most use "spoofed" caller IDs — they fake a local number to trick you into answering.
                        </p>
                        <p className="text-lg text-theme-secondary mb-8 leading-relaxed">
                            STIR/SHAKEN makes spoofing much harder. Since June 2021, the FCC requires all U.S. carriers to implement it. 
                            Calls without proper authentication are increasingly blocked or labeled as spam.
                        </p>
                        <div className="bg-red-500/5 border border-red-500/20 rounded-xl p-6">
                            <p className="text-sm font-semibold text-red-500 mb-2">⚠ The bottom line for businesses</p>
                            <p className="text-theme-secondary">
                                If your outbound calls don't have proper STIR/SHAKEN attestation, carriers will treat them 
                                as suspicious. That means spam labels, lower answer rates, and potentially blocked calls.
                            </p>
                        </div>
                    </div>

                    {/* The 3 Levels */}
                    <div className="mb-16">
                        <h2 className="text-3xl font-bold text-theme-primary mb-6">The Three Attestation Levels</h2>
                        <p className="text-lg text-theme-secondary mb-8 leading-relaxed">
                            Not all STIR/SHAKEN verification is equal. There are three levels, and the difference matters:
                        </p>
                        <div className="space-y-4">
                            <div className="border-2 border-green-500/30 bg-green-500/5 rounded-xl p-6">
                                <div className="flex items-center gap-3 mb-3">
                                    <span className="text-xl font-bold text-green-500">A</span>
                                    <h3 className="text-lg font-semibold text-theme-primary">Full Attestation</h3>
                                    <span className="text-xs font-medium text-green-500 bg-green-500/10 px-2 py-0.5 rounded-full">Best — what you want</span>
                                </div>
                                <p className="text-theme-secondary">
                                    The carrier knows exactly who is making the call <strong>and</strong> they're authorized to use that specific phone number. 
                                    This is the gold standard. Carriers trust these calls the most.
                                </p>
                            </div>
                            <div className="border border-yellow-500/30 bg-yellow-500/5 rounded-xl p-6">
                                <div className="flex items-center gap-3 mb-3">
                                    <span className="text-xl font-bold text-yellow-500">B</span>
                                    <h3 className="text-lg font-semibold text-theme-primary">Partial Attestation</h3>
                                    <span className="text-xs font-medium text-yellow-500 bg-yellow-500/10 px-2 py-0.5 rounded-full">Acceptable</span>
                                </div>
                                <p className="text-theme-secondary">
                                    The carrier knows the customer but can't verify they're authorized to use that specific number. 
                                    Common with some VoIP setups. Less trusted than A-level.
                                </p>
                            </div>
                            <div className="border border-red-500/30 bg-red-500/5 rounded-xl p-6">
                                <div className="flex items-center gap-3 mb-3">
                                    <span className="text-xl font-bold text-red-500">C</span>
                                    <h3 className="text-lg font-semibold text-theme-primary">Gateway Attestation</h3>
                                    <span className="text-xs font-medium text-red-500 bg-red-500/10 px-2 py-0.5 rounded-full">Risky</span>
                                </div>
                                <p className="text-theme-secondary">
                                    The carrier is just passing the call through and can't verify the caller at all. 
                                    Common with international calls and legacy systems. Carriers treat these with the most suspicion.
                                </p>
                            </div>
                        </div>
                    </div>

                    {/* How it works visually */}
                    <div className="mb-16">
                        <h2 className="text-3xl font-bold text-theme-primary mb-6">How It Works (Step by Step)</h2>
                        <div className="space-y-6">
                            {[
                                { step: '1', title: 'You make a call', desc: 'Your phone system initiates an outbound call to a customer.' },
                                { step: '2', title: 'Your carrier signs it', desc: 'Your carrier creates a digital certificate (a "passport" for the call) that includes your identity and attestation level.' },
                                { step: '3', title: 'The call travels the network', desc: 'The signed call moves through potentially several carriers on its way to the recipient.' },
                                { step: '4', title: 'The receiving carrier verifies', desc: 'The recipient\'s carrier checks the digital signature. Is it valid? What attestation level? Has it been tampered with?' },
                                { step: '5', title: 'The recipient sees the result', desc: 'Based on verification, the call is displayed normally, shows a "Verified" badge, or gets flagged as "Spam Likely."' },
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

                    {/* What this means for your business */}
                    <div className="mb-16">
                        <h2 className="text-3xl font-bold text-theme-primary mb-6">What This Means for Your Business</h2>
                        <div className="grid sm:grid-cols-2 gap-6">
                            {[
                                { title: 'Your calls need A-level attestation', desc: 'Without it, carriers increasingly flag or block your calls. BrandCall ensures every call gets full attestation.' },
                                { title: 'STIR/SHAKEN alone isn\'t enough', desc: 'Authentication prevents spoofing, but it doesn\'t tell recipients who you are. You need branded caller ID on top of STIR/SHAKEN.' },
                                { title: 'Non-compliance is costly', desc: 'The FCC can fine carriers (and by extension, their customers) for non-compliance. Fines start at $10,000 per violation.' },
                                { title: 'It\'s a competitive advantage', desc: 'Businesses with proper authentication and branding see 48%+ higher answer rates than those without.' },
                            ].map((item) => (
                                <div key={item.title} className="bg-theme-secondary border border-theme-primary rounded-xl p-6">
                                    <h3 className="text-lg font-semibold text-theme-primary mb-2">{item.title}</h3>
                                    <p className="text-sm text-theme-secondary leading-relaxed">{item.desc}</p>
                                </div>
                            ))}
                        </div>
                    </div>

                    {/* STIR/SHAKEN + Branded Caller ID */}
                    <div className="mb-16">
                        <h2 className="text-3xl font-bold text-theme-primary mb-6">STIR/SHAKEN + Branded Caller ID = Full Trust</h2>
                        <div className="overflow-x-auto">
                            <table className="w-full text-sm">
                                <thead>
                                    <tr className="border-b border-theme-primary">
                                        <th className="text-left py-3 pr-4 text-theme-primary font-semibold"></th>
                                        <th className="text-left py-3 px-4 text-theme-muted font-semibold">STIR/SHAKEN Only</th>
                                        <th className="text-left py-3 pl-4 text-brand-500 font-semibold">STIR/SHAKEN + BrandCall</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-theme-primary">
                                    {[
                                        ['Call authentication', '✓', '✓'],
                                        ['Spoofing prevention', '✓', '✓'],
                                        ['Business name display', '✗', '✓'],
                                        ['Company logo', '✗', '✓'],
                                        ['Call reason', '✗', '✓'],
                                        ['Spam label prevention', 'Partial', '✓ Active monitoring'],
                                        ['Reputation management', '✗', '✓'],
                                    ].map(([feature, shaken, brandcall]) => (
                                        <tr key={feature}>
                                            <td className="py-3 pr-4 text-theme-primary font-medium">{feature}</td>
                                            <td className="py-3 px-4 text-theme-muted">{shaken}</td>
                                            <td className="py-3 pl-4 text-theme-secondary">{brandcall}</td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {/* Key Dates */}
                    <div className="mb-16">
                        <h2 className="text-3xl font-bold text-theme-primary mb-6">Key STIR/SHAKEN Dates</h2>
                        <div className="space-y-4">
                            {[
                                { date: 'June 30, 2021', event: 'FCC deadline for large carriers to implement STIR/SHAKEN' },
                                { date: 'June 30, 2023', event: 'Extended deadline for small carriers and VoIP providers' },
                                { date: 'Ongoing', event: 'FCC continues to strengthen enforcement and expand requirements' },
                            ].map((item) => (
                                <div key={item.date} className="flex gap-4 items-start">
                                    <div className="flex-shrink-0 text-sm font-mono font-semibold text-brand-500 w-36">{item.date}</div>
                                    <p className="text-theme-secondary">{item.event}</p>
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
                            Get STIR/SHAKEN Compliant — and Go Beyond
                        </h2>
                        <p className="text-theme-secondary mb-6">
                            BrandCall handles STIR/SHAKEN compliance automatically, then adds branded caller ID 
                            on top for maximum answer rates.
                        </p>
                        <div className="flex flex-col sm:flex-row items-center justify-center gap-4">
                            <Link
                                href={route('register')}
                                className="inline-flex items-center px-6 py-3 text-sm font-medium text-white bg-brand-600 rounded-lg hover:bg-brand-500 transition-colors"
                            >
                                Get Started Free
                            </Link>
                            <Link
                                href="/what-is-branded-caller-id"
                                className="inline-flex items-center px-6 py-3 text-sm font-medium text-theme-primary border border-theme-primary rounded-lg hover:bg-theme-tertiary transition-colors"
                            >
                                Learn About Branded Caller ID
                            </Link>
                        </div>
                    </div>
                </div>
            </section>
        </MarketingLayout>
    );
}
