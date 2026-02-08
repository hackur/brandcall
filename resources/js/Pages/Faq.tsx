import { Head, Link } from '@inertiajs/react';
import MarketingLayout from '@/Layouts/MarketingLayout';
import { useState } from 'react';

interface FaqItem {
    question: string;
    answer: string;
}

const faqs: Record<string, FaqItem[]> = {
    'Getting Started': [
        {
            question: 'What is branded caller ID?',
            answer: 'Branded caller ID displays your verified business name, logo, and call reason on the recipient\'s phone screen when you make outbound calls. Instead of showing an unknown number, recipients see exactly who is calling and why — dramatically improving answer rates.',
        },
        {
            question: 'How does BrandCall work?',
            answer: 'BrandCall integrates with your existing phone system through our REST API. After completing our KYC verification process (typically same-day), you can start branding your outbound calls. We handle the STIR/SHAKEN authentication, carrier delivery, and reputation management automatically.',
        },
        {
            question: 'How long does setup take?',
            answer: 'Most businesses are up and running within minutes. Sign up, complete KYC verification (business license + ID), and integrate via our API. No partner intermediaries, no weeks-long onboarding — direct access from day one.',
        },
        {
            question: 'What do I need to get started?',
            answer: 'You\'ll need: (1) A valid business license or registration, (2) Government-issued ID for verification, (3) Proof of phone number ownership (LOA), and (4) Your existing phone system or call center software. Our team guides you through the entire process.',
        },
        {
            question: 'Is there a free trial?',
            answer: 'We offer a pilot program that lets you test branded calling on a limited number of phone numbers before committing to a full plan. Contact our sales team for details.',
        },
    ],
    'Technology & Compliance': [
        {
            question: 'What is STIR/SHAKEN and why does it matter?',
            answer: 'STIR/SHAKEN (Secure Telephone Identity Revisited / Signature-based Handling of Asserted Information Using toKENs) is an FCC-mandated framework for caller ID authentication. It uses cryptographic signatures to verify that calls come from legitimate sources. BrandCall ensures all your calls achieve the highest attestation level (Full Attestation A), building trust with carriers and recipients.',
        },
        {
            question: 'What is Rich Call Data (RCD)?',
            answer: 'Rich Call Data is the enhanced information delivered alongside your branded calls — including your business name, logo, and call reason. RCD is transmitted via STIR/SHAKEN protocols and displayed on supported mobile devices, giving recipients full context before they answer.',
        },
        {
            question: 'Is BrandCall TCPA compliant?',
            answer: 'BrandCall provides the technology for branded calling, but TCPA compliance is ultimately the responsibility of the caller. We provide tools to help — including consent tracking, DNC list integration, and time-of-day call restrictions — but you must ensure your calling practices comply with all applicable regulations.',
        },
        {
            question: 'Which carriers support branded caller ID?',
            answer: 'Branded caller ID is supported on all major U.S. carriers including AT&T, Verizon, T-Mobile, and their MVNOs. Display support varies by device — most modern smartphones (iPhone, Android) display the full branded information including name, logo, and call reason.',
        },
        {
            question: 'What attestation levels are there?',
            answer: 'STIR/SHAKEN defines three attestation levels: (A) Full Attestation — the carrier knows the customer and they\'re authorized to use the number; (B) Partial Attestation — the carrier knows the customer but not the specific number; (C) Gateway Attestation — the carrier doesn\'t know the originator. BrandCall ensures your calls achieve A-level attestation for maximum trust.',
        },
    ],
    'Pricing & Plans': [
        {
            question: 'How is BrandCall priced?',
            answer: 'BrandCall uses transparent, usage-based pricing. You pay per branded call delivered — no hidden fees, no carrier surcharges, no long-term contracts required. Volume discounts are available for high-volume callers. Visit our pricing page for current rates.',
        },
        {
            question: 'Do I pay for calls that aren\'t answered?',
            answer: 'You only pay when branded information is successfully delivered to the recipient\'s device. If the branding isn\'t displayed (due to carrier limitations or device incompatibility), you won\'t be charged for that call.',
        },
        {
            question: 'Are there any setup fees?',
            answer: 'No setup fees. The KYC verification process is included at no additional cost. You only start paying when you begin making branded calls.',
        },
    ],
    'Number Reputation & Spam': [
        {
            question: 'My numbers are showing as "Spam Likely" — can you fix that?',
            answer: 'Yes. Our remediation service actively works with carriers and analytics providers to remove spam/scam labels from your numbers. This typically takes 1-3 business days. We also provide ongoing monitoring to prevent future flagging and alert you immediately if any of your numbers get labeled.',
        },
        {
            question: 'What causes numbers to get spam-labeled?',
            answer: 'Common causes include: high call volume from a single number, aggressive predictive dialing, low answer rates, consumer complaints, and sudden changes in calling patterns. BrandCall helps you avoid these triggers with best-practice recommendations, rate limiting, and proactive reputation monitoring.',
        },
        {
            question: 'How does number reputation management work?',
            answer: 'We continuously monitor your phone numbers across all major carrier analytics engines. If a number shows signs of reputation degradation, we alert you and can automatically initiate remediation. Our dashboard shows real-time reputation scores, spam risk levels, and carrier delivery status for every number in your account.',
        },
        {
            question: 'Should I brand all my outbound calls?',
            answer: 'Not necessarily. We recommend branding high-value calls where recognition improves outcomes — appointment reminders, customer service callbacks, delivery notifications. For high-volume campaigns, strategic branding of a portion of calls (roughly 1 in 3) is more cost-effective and better preserves number reputation.',
        },
    ],
    'Integration & Technical': [
        {
            question: 'How do I integrate BrandCall with my phone system?',
            answer: 'BrandCall offers a REST API that integrates with any modern phone system, call center platform, or CPaaS provider. We provide SDKs for PHP, Node.js, and Python, along with a comprehensive Postman collection. Most integrations take less than a day.',
        },
        {
            question: 'Does BrandCall work with Twilio/Five9/other providers?',
            answer: 'Yes. BrandCall is platform-agnostic and works alongside any telephony provider. We layer branded calling on top of your existing infrastructure — no need to switch carriers or change your phone system.',
        },
        {
            question: 'Is there an API rate limit?',
            answer: 'API rate limits depend on your plan and trust level. New accounts start with conservative limits that increase as your account history builds. Enterprise customers receive custom rate limits based on their needs. All limits are clearly documented in your dashboard.',
        },
        {
            question: 'Do you provide webhooks?',
            answer: 'Yes. BrandCall sends real-time webhooks for call events including branding delivery confirmation, spam label alerts, reputation changes, and KYC status updates. Webhooks are configurable per-event type in your dashboard.',
        },
    ],
};

function FaqAccordion({ item }: { item: FaqItem }) {
    const [open, setOpen] = useState(false);

    return (
        <div className="border border-theme-primary rounded-xl overflow-hidden">
            <button
                onClick={() => setOpen(!open)}
                className="w-full flex items-center justify-between px-6 py-5 text-left hover:bg-theme-tertiary/50 transition-colors"
            >
                <span className="text-base font-medium text-theme-primary pr-4">{item.question}</span>
                <svg
                    className={`w-5 h-5 text-theme-muted flex-shrink-0 transition-transform duration-200 ${open ? 'rotate-180' : ''}`}
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            {open && (
                <div className="px-6 pb-5 text-sm text-theme-secondary leading-relaxed">
                    {item.answer}
                </div>
            )}
        </div>
    );
}

export default function Faq() {
    return (
        <MarketingLayout>
            <Head title="FAQ - Frequently Asked Questions" />

            {/* Hero */}
            <section className="py-16 sm:py-24">
                <div className="max-w-4xl mx-auto px-6 text-center">
                    <h1 className="text-4xl sm:text-5xl font-bold text-theme-primary mb-4">
                        Frequently Asked Questions
                    </h1>
                    <p className="text-lg text-theme-secondary max-w-2xl mx-auto">
                        Everything you need to know about branded caller ID, STIR/SHAKEN compliance, 
                        and how BrandCall helps your business connect with customers.
                    </p>
                </div>
            </section>

            {/* FAQ Sections */}
            <section className="pb-24">
                <div className="max-w-3xl mx-auto px-6 space-y-12">
                    {Object.entries(faqs).map(([category, items]) => (
                        <div key={category}>
                            <h2 className="text-2xl font-bold text-theme-primary mb-6">{category}</h2>
                            <div className="space-y-3">
                                {items.map((item, i) => (
                                    <FaqAccordion key={i} item={item} />
                                ))}
                            </div>
                        </div>
                    ))}
                </div>
            </section>

            {/* CTA */}
            <section className="pb-24">
                <div className="max-w-3xl mx-auto px-6">
                    <div className="bg-brand-600/10 border border-brand-600/20 rounded-2xl p-8 sm:p-12 text-center">
                        <h2 className="text-2xl font-bold text-theme-primary mb-3">
                            Still have questions?
                        </h2>
                        <p className="text-theme-secondary mb-6">
                            Our team is ready to help you get started with branded caller ID.
                        </p>
                        <div className="flex flex-col sm:flex-row items-center justify-center gap-4">
                            <Link
                                href={route('register')}
                                className="inline-flex items-center px-6 py-3 text-sm font-medium text-white bg-brand-600 rounded-lg hover:bg-brand-500 transition-colors"
                            >
                                Get Started Free
                            </Link>
                            <a
                                href="mailto:support@brandcall.io"
                                className="inline-flex items-center px-6 py-3 text-sm font-medium text-theme-primary border border-theme-primary rounded-lg hover:bg-theme-tertiary transition-colors"
                            >
                                Contact Support
                            </a>
                        </div>
                    </div>
                </div>
            </section>
        </MarketingLayout>
    );
}
