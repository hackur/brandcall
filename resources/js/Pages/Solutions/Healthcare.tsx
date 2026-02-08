import IndustryPage from './IndustryPage';

export default function Healthcare() {
    return (
        <IndustryPage
            page={{
                seo: {
                    title: 'Branded Caller ID for Healthcare | HIPAA-Compliant | BrandCall',
                    description: 'Patients answer when they see their provider\'s name. HIPAA-compliant branded caller ID for hospitals, clinics, telehealth, and healthcare systems.',
                    keywords: 'healthcare caller id, HIPAA branded calling, hospital caller id, patient outreach caller id, telehealth caller id',
                    canonical: 'https://brandcall.io/solutions/healthcare',
                },
                badge: 'Healthcare',
                headline: 'Branded Caller ID for',
                headlineAccent: 'Healthcare',
                subheadline: 'Patients answer when they see their doctor\'s name. HIPAA-compliant branded calling for appointment reminders, test results, and telehealth follow-ups.',
                stats: [
                    { value: '48%+', label: 'Higher patient answer rates' },
                    { value: '30%', label: 'Reduction in no-shows' },
                    { value: 'HIPAA', label: 'Compliant platform' },
                    { value: 'Minutes', label: 'Setup time' },
                ],
                problemTitle: 'Patients Aren\'t Answering',
                problemDesc: 'Missed calls mean missed appointments, delayed care, and lost revenue. When your clinic\'s number shows as unknown, patients don\'t pick up.',
                problems: [
                    'Appointment reminder calls go unanswered — leading to costly no-shows',
                    'Critical test results can\'t be delivered by phone when patients ignore unknown numbers',
                    'Telehealth follow-ups require multiple call attempts to connect',
                    'Patient satisfaction scores drop when they can\'t identify who\'s calling',
                    'Staff waste hours on redials and voicemails instead of patient care',
                ],
                benefits: [
                    { title: 'HIPAA-Compliant Branding', desc: 'Display your practice name and call reason without exposing PHI. "Appointment Reminder" is visible; patient details are not.' },
                    { title: 'Reduce No-Shows', desc: 'Patients who see "Dr. Smith\'s Office — Appointment Reminder" answer 48% more often. Fewer no-shows means more revenue.' },
                    { title: 'Multi-Location Support', desc: 'Brand calls from each clinic, hospital, or practice location with the correct facility name and logo.' },
                    { title: 'EHR Integration', desc: 'Works with Epic, Cerner, Athena, and other EHR/PM systems. Trigger branded calls from appointment workflows.' },
                    { title: 'Verified Provider Identity', desc: 'A-level STIR/SHAKEN attestation plus "Verified" badges build patient trust and compliance.' },
                    { title: 'Reputation Protection', desc: 'Healthcare numbers get flagged too. We monitor and protect your numbers so patients always see your name, not a spam warning.' },
                ],
                useCases: [
                    'Appointment reminders & confirmations',
                    'Lab result notifications',
                    'Telehealth follow-up calls',
                    'Prescription refill reminders',
                    'Post-discharge check-ins',
                    'Insurance verification calls',
                    'Patient satisfaction surveys',
                    'Billing & payment reminders',
                ],
                ctaTitle: 'Improve Patient Outcomes with Branded Calling',
                ctaDesc: 'When patients answer, care happens. Set up HIPAA-compliant branded caller ID in minutes.',
            }}
        />
    );
}
