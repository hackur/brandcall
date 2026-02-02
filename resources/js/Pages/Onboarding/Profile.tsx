import { Head, useForm } from '@inertiajs/react';
import OnboardingLayout from '@/Layouts/OnboardingLayout';

interface UserProfile {
    name: string;
    email: string;
    phone: string | null;
    company_name: string | null;
    company_website: string | null;
    company_phone: string | null;
    company_address: string | null;
    company_city: string | null;
    company_state: string | null;
    company_zip: string | null;
    industry: string | null;
    monthly_call_volume: string | null;
    use_case: string | null;
    current_provider: string | null;
    uses_stir_shaken: string | null;
}

interface Props {
    user: UserProfile;
}

export default function Profile({ user }: Props) {
    const { data, setData, post, processing, errors } = useForm({
        phone: user.phone || '',
        company_name: user.company_name || '',
        company_website: user.company_website || '',
        company_phone: user.company_phone || '',
        company_address: user.company_address || '',
        company_city: user.company_city || '',
        company_state: user.company_state || '',
        company_zip: user.company_zip || '',
        industry: user.industry || '',
        monthly_call_volume: user.monthly_call_volume || '',
        use_case: user.use_case || '',
        current_provider: user.current_provider || '',
        uses_stir_shaken: user.uses_stir_shaken || '',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('onboarding.profile.update'));
    };

    const industries = [
        'Healthcare / Medical',
        'Financial Services / Banking',
        'Insurance',
        'Real Estate',
        'Retail / E-commerce',
        'Telecommunications',
        'Technology / SaaS',
        'Education',
        'Government',
        'Non-profit',
        'Legal Services',
        'Automotive',
        'Travel / Hospitality',
        'Collections',
        'Other',
    ];

    const callVolumes = [
        'Under 1,000 calls/month',
        '1,000 - 10,000 calls/month',
        '10,000 - 100,000 calls/month',
        '100,000 - 1M calls/month',
        'Over 1M calls/month',
    ];

    const useCases = [
        'Customer service callbacks',
        'Appointment reminders',
        'Delivery notifications',
        'Sales outreach',
        'Account alerts / Fraud prevention',
        'Collections',
        'Political campaigns',
        'Surveys / Research',
        'Other',
    ];

    return (
        <OnboardingLayout>
            <Head title="Company Profile" />

            <div className="max-w-3xl mx-auto">
                <div className="mb-8">
                    <h1 className="text-2xl font-bold text-white mb-2">Company Profile</h1>
                    <p className="text-slate-400">
                        Complete your company information for KYC verification.
                    </p>
                </div>

                <form onSubmit={submit} className="space-y-8">
                    {/* Contact Information */}
                    <div className="card">
                        <h2 className="text-lg font-semibold text-white mb-6">Contact Information</h2>
                        <div className="grid md:grid-cols-2 gap-6">
                            <div>
                                <label className="label">Name</label>
                                <input type="text" value={user.name} disabled className="input bg-slate-900 opacity-60" />
                            </div>
                            <div>
                                <label className="label">Email</label>
                                <input type="email" value={user.email} disabled className="input bg-slate-900 opacity-60" />
                            </div>
                            <div className="md:col-span-2">
                                <label className="label">Phone Number</label>
                                <input
                                    type="tel"
                                    value={data.phone}
                                    onChange={e => setData('phone', e.target.value)}
                                    placeholder="+1 (555) 000-0000"
                                    className="input"
                                />
                                {errors.phone && <p className="error-text">{errors.phone}</p>}
                            </div>
                        </div>
                    </div>

                    {/* Company Information */}
                    <div className="card">
                        <h2 className="text-lg font-semibold text-white mb-6">Company Information</h2>
                        <div className="grid md:grid-cols-2 gap-6">
                            <div className="md:col-span-2">
                                <label className="label">Company Name *</label>
                                <input
                                    type="text"
                                    value={data.company_name}
                                    onChange={e => setData('company_name', e.target.value)}
                                    placeholder="Acme Corporation"
                                    className={`input ${errors.company_name ? 'input-error' : ''}`}
                                    required
                                />
                                {errors.company_name && <p className="error-text">{errors.company_name}</p>}
                            </div>
                            <div>
                                <label className="label">Website</label>
                                <input
                                    type="url"
                                    value={data.company_website}
                                    onChange={e => setData('company_website', e.target.value)}
                                    placeholder="https://example.com"
                                    className="input"
                                />
                                {errors.company_website && <p className="error-text">{errors.company_website}</p>}
                            </div>
                            <div>
                                <label className="label">Business Phone</label>
                                <input
                                    type="tel"
                                    value={data.company_phone}
                                    onChange={e => setData('company_phone', e.target.value)}
                                    placeholder="+1 (555) 000-0000"
                                    className="input"
                                />
                            </div>
                            <div className="md:col-span-2">
                                <label className="label">Street Address</label>
                                <input
                                    type="text"
                                    value={data.company_address}
                                    onChange={e => setData('company_address', e.target.value)}
                                    placeholder="123 Business Ave"
                                    className="input"
                                />
                            </div>
                            <div>
                                <label className="label">City</label>
                                <input
                                    type="text"
                                    value={data.company_city}
                                    onChange={e => setData('company_city', e.target.value)}
                                    className="input"
                                />
                            </div>
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label className="label">State</label>
                                    <input
                                        type="text"
                                        value={data.company_state}
                                        onChange={e => setData('company_state', e.target.value)}
                                        className="input"
                                    />
                                </div>
                                <div>
                                    <label className="label">ZIP</label>
                                    <input
                                        type="text"
                                        value={data.company_zip}
                                        onChange={e => setData('company_zip', e.target.value)}
                                        className="input"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Business Details */}
                    <div className="card">
                        <h2 className="text-lg font-semibold text-white mb-6">Business Details</h2>
                        <div className="grid md:grid-cols-2 gap-6">
                            <div>
                                <label className="label">Industry</label>
                                <select
                                    value={data.industry}
                                    onChange={e => setData('industry', e.target.value)}
                                    className="input"
                                >
                                    <option value="">Select your industry</option>
                                    {industries.map(industry => (
                                        <option key={industry} value={industry}>{industry}</option>
                                    ))}
                                </select>
                            </div>
                            <div>
                                <label className="label">Monthly Call Volume</label>
                                <select
                                    value={data.monthly_call_volume}
                                    onChange={e => setData('monthly_call_volume', e.target.value)}
                                    className="input"
                                >
                                    <option value="">Select volume</option>
                                    {callVolumes.map(volume => (
                                        <option key={volume} value={volume}>{volume}</option>
                                    ))}
                                </select>
                            </div>
                            <div>
                                <label className="label">Primary Use Case</label>
                                <select
                                    value={data.use_case}
                                    onChange={e => setData('use_case', e.target.value)}
                                    className="input"
                                >
                                    <option value="">Select use case</option>
                                    {useCases.map(useCase => (
                                        <option key={useCase} value={useCase}>{useCase}</option>
                                    ))}
                                </select>
                            </div>
                            <div>
                                <label className="label">Current Voice Provider</label>
                                <input
                                    type="text"
                                    value={data.current_provider}
                                    onChange={e => setData('current_provider', e.target.value)}
                                    placeholder="e.g., Twilio, RingCentral"
                                    className="input"
                                />
                            </div>
                            <div className="md:col-span-2">
                                <label className="label">Currently using STIR/SHAKEN?</label>
                                <div className="flex gap-6 mt-2">
                                    {[
                                        { value: 'yes', label: 'Yes' },
                                        { value: 'no', label: 'No' },
                                        { value: 'not_sure', label: 'Not sure' },
                                    ].map(option => (
                                        <label key={option.value} className="flex items-center gap-2 cursor-pointer">
                                            <input
                                                type="radio"
                                                name="uses_stir_shaken"
                                                value={option.value}
                                                checked={data.uses_stir_shaken === option.value}
                                                onChange={e => setData('uses_stir_shaken', e.target.value)}
                                                className="w-4 h-4 text-brand-600 bg-slate-800 border-slate-600 focus:ring-brand-500"
                                            />
                                            <span className="text-slate-300">{option.label}</span>
                                        </label>
                                    ))}
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Submit */}
                    <div className="flex justify-end">
                        <button type="submit" disabled={processing} className="btn-primary">
                            {processing ? 'Saving...' : 'Save Profile'}
                        </button>
                    </div>
                </form>
            </div>
        </OnboardingLayout>
    );
}
