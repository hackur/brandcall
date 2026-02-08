import IndustryPage from './IndustryPage';

export default function FinancialServices() {
    return (
        <IndustryPage
            page={{
                seo: {
                    title: 'Branded Caller ID for Financial Services | BrandCall',
                    description: 'Build trust with verified calls for fraud alerts, collections, and customer service. Branded caller ID for banks, credit unions, and financial institutions.',
                    keywords: 'financial services caller id, bank branded calling, collections caller id, fraud alert caller id',
                    canonical: 'https://brandcall.io/solutions/financial-services',
                },
                badge: 'Financial Services',
                headline: 'Branded Caller ID for',
                headlineAccent: 'Financial Services',
                subheadline: 'Customers trust calls from their bank when they see a verified name. Reduce fraud exposure, improve collections, and deliver critical alerts that actually get answered.',
                stats: [
                    { value: '48%+', label: 'Higher answer rates' },
                    { value: '60%', label: 'Faster fraud alert response' },
                    { value: '$14B', label: 'Lost to phone fraud yearly' },
                    { value: 'Minutes', label: 'To go live' },
                ],
                problemTitle: 'When Customers Don\'t Answer the Bank',
                problemDesc: 'Fraud alerts, payment reminders, and account notifications are time-sensitive. When customers ignore calls from unknown numbers, the consequences are real.',
                problems: [
                    'Fraud alerts go unanswered — allowing unauthorized transactions to continue',
                    'Collections calls get ignored, extending recovery timelines and increasing write-offs',
                    'Customers can\'t distinguish your legitimate calls from phishing attempts',
                    'Regulatory requirements (Reg E, TILA) demand timely customer notification',
                    'Account holders report your numbers as spam, damaging reputation across all carriers',
                ],
                benefits: [
                    { title: 'Verified Institution Identity', desc: 'Display your bank or credit union name with a verified badge. Customers know it\'s really you, not a phishing attempt.' },
                    { title: 'Fraud Alert Delivery', desc: '"Fraud Alert — Verify Transaction" as the call reason ensures customers answer immediately when it matters most.' },
                    { title: 'Collections Improvement', desc: 'Branded calls with your institution\'s name get answered 48% more often. Faster right-party contact means faster resolution.' },
                    { title: 'Regulatory Compliance', desc: 'Meet Reg E, TILA, and FDCPA notification requirements with verified, documented outbound calls.' },
                    { title: 'Multi-Branch Branding', desc: 'Brand calls from each branch or department with the appropriate name. Corporate, retail, mortgage — each with its own identity.' },
                    { title: 'Core System Integration', desc: 'REST API connects with FIS, Fiserv, Jack Henry, and modern banking platforms. Trigger branded calls from existing workflows.' },
                ],
                useCases: [
                    'Fraud and suspicious activity alerts',
                    'Collections & payment reminders',
                    'Account notification calls',
                    'Loan application updates',
                    'Wire transfer verifications',
                    'Account onboarding calls',
                    'Investment advisor check-ins',
                    'Insurance claim status updates',
                ],
                ctaTitle: 'Build Trust with Every Call',
                ctaDesc: 'Your customers need to know it\'s really their bank calling. Set up verified branded calling in minutes.',
            }}
        />
    );
}
