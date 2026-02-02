import { Head } from '@inertiajs/react';
import OnboardingLayout from '@/Layouts/OnboardingLayout';

export default function Documentation() {
    const guides = [
        {
            title: 'Getting Started',
            description: 'Learn the basics of BrandCall and set up your first branded caller ID.',
            icon: (
                <svg className="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            ),
            articles: [
                { title: 'What is Branded Caller ID?', href: '#' },
                { title: 'Account Setup Guide', href: '#' },
                { title: 'Understanding STIR/SHAKEN', href: '#' },
                { title: 'KYC Requirements', href: '#' },
            ],
        },
        {
            title: 'API Integration',
            description: 'Integrate BrandCall into your existing telephony infrastructure.',
            icon: (
                <svg className="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                </svg>
            ),
            articles: [
                { title: 'API Authentication', href: '#' },
                { title: 'Making Your First API Call', href: '#' },
                { title: 'Webhooks & Callbacks', href: '#' },
                { title: 'Rate Limits & Best Practices', href: '#' },
            ],
        },
        {
            title: 'Brand Management',
            description: 'Configure and manage your branded calling identities.',
            icon: (
                <svg className="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                </svg>
            ),
            articles: [
                { title: 'Creating a Brand', href: '#' },
                { title: 'Logo Requirements', href: '#' },
                { title: 'Call Categories & Reasons', href: '#' },
                { title: 'Number Registration', href: '#' },
            ],
        },
        {
            title: 'Compliance',
            description: 'Stay compliant with FCC regulations and industry standards.',
            icon: (
                <svg className="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            ),
            articles: [
                { title: 'STIR/SHAKEN Compliance', href: '#' },
                { title: 'TCPA Guidelines', href: '#' },
                { title: 'FCC Requirements', href: '#' },
                { title: 'Attestation Levels Explained', href: '#' },
            ],
        },
    ];

    const faqs = [
        {
            question: 'How long does KYC verification take?',
            answer: 'KYC verification typically takes 1-2 business days. We review all submitted documents to ensure compliance with telecommunications regulations.',
        },
        {
            question: 'What documents do I need for verification?',
            answer: 'You\'ll need to provide at least one of the following: Business License, Tax ID/EIN document, or a Letter of Authorization (LOA) for your phone numbers.',
        },
        {
            question: 'How does STIR/SHAKEN work?',
            answer: 'STIR/SHAKEN is an industry standard that verifies caller ID information. When you make calls through BrandCall, we sign your calls with full attestation, proving your identity to receiving carriers.',
        },
        {
            question: 'Can I use my existing phone numbers?',
            answer: 'Yes! You can register your existing business phone numbers with BrandCall. You\'ll need to verify ownership through LOA documentation.',
        },
        {
            question: 'What carriers support branded calling?',
            answer: 'Major carriers including AT&T, T-Mobile, and Verizon support Rich Call Data. Coverage continues to expand as more carriers adopt the standard.',
        },
    ];

    return (
        <OnboardingLayout>
            <Head title="Documentation" />

            <div className="max-w-4xl mx-auto">
                <div className="mb-10">
                    <h1 className="text-2xl font-bold text-white mb-2">Documentation</h1>
                    <p className="text-slate-400">
                        Everything you need to know about BrandCall and branded caller ID.
                    </p>
                </div>

                {/* Quick Links */}
                <div className="grid md:grid-cols-2 gap-6 mb-12">
                    {guides.map((guide) => (
                        <div key={guide.title} className="card">
                            <div className="flex items-start gap-4 mb-4">
                                <div className="w-12 h-12 rounded-xl bg-brand-600/10 text-brand-400 flex items-center justify-center">
                                    {guide.icon}
                                </div>
                                <div>
                                    <h2 className="text-lg font-semibold text-white">{guide.title}</h2>
                                    <p className="text-sm text-slate-400">{guide.description}</p>
                                </div>
                            </div>
                            <ul className="space-y-2">
                                {guide.articles.map((article) => (
                                    <li key={article.title}>
                                        <a 
                                            href={article.href}
                                            className="flex items-center gap-2 text-sm text-slate-300 hover:text-brand-400 transition-colors"
                                        >
                                            <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                                            </svg>
                                            {article.title}
                                        </a>
                                    </li>
                                ))}
                            </ul>
                        </div>
                    ))}
                </div>

                {/* FAQs */}
                <div className="card">
                    <h2 className="text-lg font-semibold text-white mb-6">Frequently Asked Questions</h2>
                    <div className="space-y-6">
                        {faqs.map((faq, index) => (
                            <div key={index} className="pb-6 border-b border-slate-700 last:border-b-0 last:pb-0">
                                <h3 className="font-medium text-white mb-2">{faq.question}</h3>
                                <p className="text-slate-400 text-sm">{faq.answer}</p>
                            </div>
                        ))}
                    </div>
                </div>

                {/* Contact Support */}
                <div className="mt-8 text-center">
                    <p className="text-slate-400 mb-4">Can't find what you're looking for?</p>
                    <a href={route('onboarding.tickets')} className="btn-primary">
                        Contact Support
                    </a>
                </div>
            </div>
        </OnboardingLayout>
    );
}
