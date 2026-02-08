import IndustryPage from './IndustryPage';

export default function CallCenters() {
    return (
        <IndustryPage
            page={{
                seo: {
                    title: 'Branded Caller ID for Call Centers & BPOs | BrandCall',
                    description: 'Increase contact rates, reduce cost per contact, and protect number reputation. Branded caller ID built for high-volume call centers and BPOs.',
                    keywords: 'call center caller id, branded calling contact center, BPO caller id, call center answer rates',
                    canonical: 'https://brandcall.io/solutions/call-centers',
                },
                badge: 'Call Centers & BPOs',
                headline: 'Branded Caller ID for',
                headlineAccent: 'Call Centers',
                subheadline: 'Higher contact rates, lower cost per dial, and zero number burn. Branded calling built for high-volume operations that can\'t afford to be ignored.',
                stats: [
                    { value: '48%+', label: 'Answer rate improvement' },
                    { value: '3x', label: 'Fewer dial attempts needed' },
                    { value: 'Minutes', label: 'Setup time, not weeks' },
                    { value: '0', label: 'Numbers burned per month' },
                ],
                problemTitle: 'The Call Center Dilemma',
                problemDesc: 'Your agents are making hundreds of calls per day. But if your numbers show as "Spam Likely," most of those dials are wasted.',
                problems: [
                    'Numbers get flagged as spam within days of high-volume dialing',
                    'Constantly rotating numbers is expensive and disrupts campaign tracking',
                    'Low contact rates inflate cost per acquisition and kill agent morale',
                    'Clients demand higher connect rates but spam labels make it impossible',
                    'No visibility into which numbers are flagged across carrier networks',
                ],
                benefits: [
                    { title: 'Number Reputation Protection', desc: 'Real-time monitoring across all carrier analytics engines. Get alerts before a number gets flagged — not after.' },
                    { title: 'White-Label Branding', desc: 'Display your client\'s brand on outbound calls. Perfect for BPOs managing multiple campaigns and brands simultaneously.' },
                    { title: 'Bulk Number Management', desc: 'Manage hundreds of numbers from a single dashboard. See reputation scores, spam risk, and carrier status at a glance.' },
                    { title: 'A-Level STIR/SHAKEN', desc: 'Every call gets full attestation automatically. Carriers trust your calls, and recipients see verified business information.' },
                    { title: 'Campaign-Level Analytics', desc: 'Track answer rates, branding delivery, and reputation metrics per campaign, agent, or number pool.' },
                    { title: 'API-First Integration', desc: 'REST API integrates with Five9, Genesys, NICE, Twilio, and any modern dialer in hours, not weeks.' },
                ],
                useCases: [
                    'Outbound sales campaigns',
                    'Collections & debt recovery',
                    'Customer service callbacks',
                    'Survey & research calls',
                    'Appointment setting',
                    'Lead qualification calls',
                    'White-label client campaigns',
                    'Political & advocacy outreach',
                ],
                ctaTitle: 'Stop Burning Numbers. Start Connecting.',
                ctaDesc: 'See how branded caller ID can transform your call center\'s connect rates. Setup takes minutes.',
            }}
        />
    );
}
