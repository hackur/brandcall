import { Head, Link } from '@inertiajs/react';
import MarketingLayout from '@/Layouts/MarketingLayout';

interface Benefit {
    title: string;
    desc: string;
}

interface Stat {
    value: string;
    label: string;
}

interface IndustryPageProps {
    seo: {
        title: string;
        description: string;
        keywords: string;
        canonical: string;
    };
    badge: string;
    headline: string;
    headlineAccent: string;
    subheadline: string;
    stats: Stat[];
    problemTitle: string;
    problemDesc: string;
    problems: string[];
    benefits: Benefit[];
    useCases: string[];
    ctaTitle: string;
    ctaDesc: string;
}

export default function IndustryPage({ page }: { page: IndustryPageProps }) {
    return (
        <MarketingLayout title={page.seo.title}>
            <Head>
                <title>{page.seo.title}</title>
                <meta name="description" content={page.seo.description} />
                <meta name="keywords" content={page.seo.keywords} />
                <meta property="og:title" content={page.seo.title} />
                <meta property="og:description" content={page.seo.description} />
                <meta property="og:type" content="website" />
                <link rel="canonical" href={page.seo.canonical} />
            </Head>

            {/* Hero */}
            <section className="py-16 sm:py-24">
                <div className="max-w-4xl mx-auto px-6 text-center">
                    <span className="inline-flex items-center px-3 py-1 text-xs font-medium text-brand-600 dark:text-brand-400 bg-brand-600/10 rounded-full border border-brand-600/20 mb-6">
                        {page.badge}
                    </span>
                    <h1 className="text-4xl sm:text-5xl lg:text-6xl font-bold text-theme-primary mb-6">
                        {page.headline}{' '}
                        <span className="bg-gradient-to-r from-brand-400 via-purple-400 to-brand-400 bg-clip-text text-transparent">
                            {page.headlineAccent}
                        </span>
                    </h1>
                    <p className="text-lg sm:text-xl text-theme-secondary max-w-2xl mx-auto mb-10 leading-relaxed">
                        {page.subheadline}
                    </p>
                    <div className="flex flex-col sm:flex-row items-center justify-center gap-4 mb-12">
                        <Link
                            href={route('register')}
                            className="inline-flex items-center px-8 py-4 text-base font-semibold text-white bg-brand-600 rounded-lg hover:bg-brand-500 transition-colors"
                        >
                            Get Started Free
                        </Link>
                        <a
                            href="mailto:sales@brandcall.io"
                            className="inline-flex items-center px-6 py-4 text-base font-medium text-theme-primary border border-theme-primary rounded-lg hover:bg-theme-tertiary transition-colors"
                        >
                            Talk to Sales
                        </a>
                    </div>
                    <div className="grid grid-cols-2 lg:grid-cols-4 gap-6 pt-8 border-t border-theme-primary">
                        {page.stats.map((stat) => (
                            <div key={stat.label}>
                                <div className="text-2xl sm:text-3xl font-bold text-brand-500 mb-1">{stat.value}</div>
                                <div className="text-sm text-theme-muted">{stat.label}</div>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* The Problem */}
            <section className="pb-16 sm:pb-24">
                <div className="max-w-4xl mx-auto px-6">
                    <h2 className="text-3xl font-bold text-theme-primary mb-4">{page.problemTitle}</h2>
                    <p className="text-lg text-theme-secondary mb-8">{page.problemDesc}</p>
                    <div className="space-y-3">
                        {page.problems.map((problem) => (
                            <div key={problem} className="flex gap-3 items-start p-4 bg-red-500/5 border border-red-500/10 rounded-xl">
                                <span className="text-red-500 flex-shrink-0 mt-0.5">✗</span>
                                <p className="text-theme-secondary">{problem}</p>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* Benefits */}
            <section className="pb-16 sm:pb-24 bg-theme-secondary border-y border-theme-primary py-16 sm:py-24">
                <div className="max-w-5xl mx-auto px-6">
                    <h2 className="text-3xl font-bold text-theme-primary mb-4 text-center">How BrandCall Helps</h2>
                    <p className="text-lg text-theme-secondary text-center max-w-2xl mx-auto mb-12">
                        Purpose-built features that solve real problems for your industry.
                    </p>
                    <div className="grid sm:grid-cols-2 gap-6">
                        {page.benefits.map((benefit) => (
                            <div key={benefit.title} className="bg-theme-primary border border-theme-primary rounded-xl p-6">
                                <h3 className="text-lg font-semibold text-theme-primary mb-2">{benefit.title}</h3>
                                <p className="text-sm text-theme-secondary leading-relaxed">{benefit.desc}</p>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* Use Cases */}
            <section className="py-16 sm:py-24">
                <div className="max-w-4xl mx-auto px-6">
                    <h2 className="text-3xl font-bold text-theme-primary mb-8">Common Use Cases</h2>
                    <div className="grid sm:grid-cols-2 gap-4">
                        {page.useCases.map((uc) => (
                            <div key={uc} className="flex gap-3 items-center p-4 bg-theme-secondary border border-theme-primary rounded-xl">
                                <span className="text-green-500 flex-shrink-0">✓</span>
                                <p className="text-theme-secondary">{uc}</p>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* CTA */}
            <section className="pb-24">
                <div className="max-w-3xl mx-auto px-6">
                    <div className="bg-brand-600/10 border border-brand-600/20 rounded-2xl p-8 sm:p-12 text-center">
                        <h2 className="text-2xl font-bold text-theme-primary mb-3">{page.ctaTitle}</h2>
                        <p className="text-theme-secondary mb-6">{page.ctaDesc}</p>
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
