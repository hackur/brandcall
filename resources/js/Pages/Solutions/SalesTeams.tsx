import IndustryPage from './IndustryPage';

export default function SalesTeams() {
    return (
        <IndustryPage
            page={{
                seo: {
                    title: 'Branded Caller ID for Sales Teams | BrandCall',
                    description: 'Prospects answer branded calls 48% more often. Stop getting ignored and start closing deals with verified business caller ID for sales teams.',
                    keywords: 'sales caller id, branded calling sales, sales answer rates, outbound sales caller id',
                    canonical: 'https://brandcall.io/solutions/sales-teams',
                },
                badge: 'Sales Teams',
                headline: 'Branded Caller ID for',
                headlineAccent: 'Sales Teams',
                subheadline: 'Prospects are 48% more likely to answer a branded call. Stop getting sent to voicemail and start having conversations that close deals.',
                stats: [
                    { value: '48%+', label: 'More prospects answer' },
                    { value: '95%', label: 'Ignore unknown numbers' },
                    { value: '2.5x', label: 'More conversations per day' },
                    { value: 'Minutes', label: 'To go live' },
                ],
                problemTitle: 'Why Prospects Don\'t Answer',
                problemDesc: '95% of people ignore calls from unknown numbers. Your sales reps are dialing all day — but most prospects never hear the pitch.',
                problems: [
                    'Prospects see an unknown number and let it ring to voicemail',
                    'Your number shows as "Spam Likely" after a few days of outbound calling',
                    'Reps waste hours leaving voicemails that never get returned',
                    'Cold outreach conversion rates keep dropping year over year',
                    'Competitors with branded calling are getting the conversations you\'re missing',
                ],
                benefits: [
                    { title: 'Instant Recognition', desc: 'Your company name and logo appear on the prospect\'s phone before they answer. First impressions start before "hello."' },
                    { title: 'Call Reason Display', desc: 'Show "Following up on your demo request" or "Scheduled call" — giving prospects a reason to pick up right now.' },
                    { title: 'Never Show as Spam', desc: 'A-level STIR/SHAKEN attestation plus active reputation monitoring ensures your numbers stay clean.' },
                    { title: 'CRM Integration', desc: 'Works with Salesforce, HubSpot, Outreach, and any dialer. Brand calls automatically based on campaign or lead source.' },
                    { title: 'Per-Rep Analytics', desc: 'See which reps have the highest branded answer rates. Identify coaching opportunities and optimize dial strategies.' },
                    { title: 'Territory Number Management', desc: 'Assign local numbers per territory, all branded with your company identity. Local presence + brand trust = maximum answer rates.' },
                ],
                useCases: [
                    'Cold outbound prospecting',
                    'Demo follow-up calls',
                    'Inbound lead response',
                    'Renewal and upsell calls',
                    'Account management check-ins',
                    'SDR/BDR dial sessions',
                    'Conference follow-up outreach',
                    'Trial-to-paid conversion calls',
                ],
                ctaTitle: 'Start More Conversations. Close More Deals.',
                ctaDesc: 'Your competitors are already using branded caller ID. Don\'t let another prospect ignore your call.',
            }}
        />
    );
}
