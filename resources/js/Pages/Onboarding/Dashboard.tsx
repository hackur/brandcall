import { Head, Link, usePage, router } from '@inertiajs/react';
import OnboardingLayout from '@/Layouts/OnboardingLayout';
import { useEffect, useState } from 'react';

interface User {
    name: string;
    email: string;
    email_verified: boolean;
    company_name: string | null;
    status: string;
    kyc_submitted: boolean;
    onboarding_progress: number;
}

interface Document {
    id: number;
    name: string;
    type: string;
    status: string;
    created_at: string;
}

interface Ticket {
    id: number;
    ticket_number: string;
    subject: string;
    status: string;
    created_at: string;
}

interface Props {
    user: User;
    documents: Document[];
    tickets: Ticket[];
}

export default function Dashboard({ user, documents, tickets }: Props) {
    const [showVerifiedBanner, setShowVerifiedBanner] = useState(false);

    useEffect(() => {
        // Check for ?verified=1 query param
        const params = new URLSearchParams(window.location.search);
        if (params.get('verified') === '1') {
            setShowVerifiedBanner(true);
            // Remove the query param from URL
            window.history.replaceState({}, '', window.location.pathname);
            // Auto-hide after 5 seconds
            setTimeout(() => setShowVerifiedBanner(false), 5000);
        }
    }, []);

    const steps = [
        {
            title: 'Verify Email',
            description: 'Confirm your email address',
            completed: user.email_verified,
            href: user.email_verified ? null : '/email/verification-notification',
            action: user.email_verified ? null : 'Resend Email',
        },
        {
            title: 'Complete Profile',
            description: 'Add your company information',
            completed: !!user.company_name,
            href: route('onboarding.profile'),
            action: 'Edit Profile',
        },
        {
            title: 'Upload Documents',
            description: 'Provide verification documents',
            completed: documents.length > 0,
            href: route('onboarding.documents'),
            action: 'Upload Documents',
        },
        {
            title: 'Submit for Review',
            description: 'Submit KYC for approval',
            completed: user.kyc_submitted,
            href: route('onboarding.documents'),
            action: user.kyc_submitted ? null : 'Submit KYC',
        },
    ];

    const getStatusBadge = (status: string) => {
        const styles: Record<string, string> = {
            pending: 'bg-yellow-500/20 text-yellow-400',
            verified: 'bg-blue-500/20 text-blue-400',
            approved: 'bg-green-500/20 text-green-400',
            rejected: 'bg-red-500/20 text-red-400',
        };
        return styles[status] || styles.pending;
    };

    return (
        <OnboardingLayout>
            <Head title="Onboarding" />

            <div className="max-w-4xl mx-auto">
                {/* Email Verified Success Banner */}
                {showVerifiedBanner && (
                    <div className="mb-6 p-4 rounded-lg bg-green-500/10 border border-green-500/20 flex items-center gap-3">
                        <div className="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center flex-shrink-0">
                            <svg className="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div className="flex-1">
                            <p className="font-medium text-green-400">Email Verified!</p>
                            <p className="text-sm text-green-400/70">Your email address has been successfully verified.</p>
                        </div>
                        <button 
                            onClick={() => setShowVerifiedBanner(false)}
                            className="text-green-400 hover:text-green-300"
                        >
                            <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                )}

                {/* Welcome Header */}
                <div className="text-center mb-10">
                    <h1 className="text-3xl font-bold text-white mb-2">
                        Welcome, {user.name.split(' ')[0]}!
                    </h1>
                    <p className="text-slate-400">
                        Complete these steps to activate your account
                    </p>
                </div>

                {/* Progress Bar */}
                <div className="mb-8">
                    <div className="flex justify-between text-sm text-slate-400 mb-2">
                        <span>Onboarding Progress</span>
                        <span>{user.onboarding_progress}% Complete</span>
                    </div>
                    <div className="h-2 bg-slate-800 rounded-full overflow-hidden">
                        <div 
                            className="h-full bg-gradient-brand transition-all duration-500"
                            style={{ width: `${user.onboarding_progress}%` }}
                        />
                    </div>
                </div>

                {/* Status Badge */}
                <div className="flex justify-center mb-8">
                    <span className={`px-4 py-1.5 rounded-full text-sm font-medium ${getStatusBadge(user.status)}`}>
                        Account Status: {user.status.charAt(0).toUpperCase() + user.status.slice(1)}
                    </span>
                </div>

                {/* Onboarding Steps */}
                <div className="card mb-8">
                    <h2 className="text-lg font-semibold text-white mb-6">Setup Checklist</h2>
                    <div className="space-y-4">
                        {steps.map((step, index) => (
                            <div 
                                key={index}
                                className={`flex items-center gap-4 p-4 rounded-lg border ${
                                    step.completed 
                                        ? 'bg-green-500/5 border-green-500/20' 
                                        : 'bg-slate-800/50 border-slate-700/50'
                                }`}
                            >
                                <div className={`flex items-center justify-center w-8 h-8 rounded-full ${
                                    step.completed 
                                        ? 'bg-green-500 text-white' 
                                        : 'bg-slate-700 text-slate-400'
                                }`}>
                                    {step.completed ? (
                                        <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                                        </svg>
                                    ) : (
                                        <span>{index + 1}</span>
                                    )}
                                </div>
                                <div className="flex-1">
                                    <h3 className={`font-medium ${step.completed ? 'text-green-400' : 'text-white'}`}>
                                        {step.title}
                                    </h3>
                                    <p className="text-sm text-slate-400">{step.description}</p>
                                </div>
                                {step.href && step.action && (
                                    <Link 
                                        href={step.href}
                                        className="btn-secondary btn-sm"
                                    >
                                        {step.action}
                                    </Link>
                                )}
                            </div>
                        ))}
                    </div>
                </div>

                {/* Quick Links */}
                <div className="grid md:grid-cols-3 gap-4 mb-8">
                    <Link href={route('onboarding.documents')} className="card-hover text-center p-6">
                        <div className="w-12 h-12 rounded-xl bg-brand-600/10 text-brand-400 mx-auto mb-3 flex items-center justify-center">
                            <svg className="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 className="font-medium text-white mb-1">Documents</h3>
                        <p className="text-sm text-slate-400">{documents.length} uploaded</p>
                    </Link>

                    <Link href={route('onboarding.tickets')} className="card-hover text-center p-6">
                        <div className="w-12 h-12 rounded-xl bg-brand-600/10 text-brand-400 mx-auto mb-3 flex items-center justify-center">
                            <svg className="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <h3 className="font-medium text-white mb-1">Support</h3>
                        <p className="text-sm text-slate-400">{tickets.length} tickets</p>
                    </Link>

                    <Link href={route('onboarding.documentation')} className="card-hover text-center p-6">
                        <div className="w-12 h-12 rounded-xl bg-brand-600/10 text-brand-400 mx-auto mb-3 flex items-center justify-center">
                            <svg className="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <h3 className="font-medium text-white mb-1">Documentation</h3>
                        <p className="text-sm text-slate-400">Get started guide</p>
                    </Link>
                </div>

                {/* Recent Tickets */}
                {tickets.length > 0 && (
                    <div className="card">
                        <div className="flex justify-between items-center mb-4">
                            <h2 className="text-lg font-semibold text-white">Recent Tickets</h2>
                            <Link href={route('onboarding.tickets')} className="text-sm text-brand-400 hover:text-brand-300">
                                View all →
                            </Link>
                        </div>
                        <div className="space-y-3">
                            {tickets.slice(0, 3).map((ticket) => (
                                <div key={ticket.id} className="flex items-center justify-between p-3 bg-slate-800/50 rounded-lg">
                                    <div>
                                        <p className="text-white font-medium">{ticket.subject}</p>
                                        <p className="text-sm text-slate-400">{ticket.ticket_number}</p>
                                    </div>
                                    <span className={`px-2 py-1 rounded text-xs font-medium ${
                                        ticket.status === 'open' ? 'bg-green-500/20 text-green-400' :
                                        ticket.status === 'in_progress' ? 'bg-blue-500/20 text-blue-400' :
                                        'bg-slate-500/20 text-slate-400'
                                    }`}>
                                        {ticket.status.replace('_', ' ')}
                                    </span>
                                </div>
                            ))}
                        </div>
                    </div>
                )}
            </div>
        </OnboardingLayout>
    );
}
