import { Head, Link, useForm } from '@inertiajs/react';
import { FormEventHandler } from 'react';

export default function VerifyEmail({ status }: { status?: string }) {
    const { post, processing } = useForm({});

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(route('verification.send'));
    };

    return (
        <>
            <Head title="Verify Email" />

            <div className="min-h-screen bg-slate-950 flex flex-col items-center justify-center p-6">
                {/* Logo */}
                <div className="flex items-center gap-3 mb-8">
                    <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-brand shadow-brand">
                        <svg className="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                    </div>
                    <span className="text-2xl font-bold text-white font-heading">BrandCall</span>
                </div>

                {/* Card */}
                <div className="card max-w-md w-full text-center">
                    {/* Email Icon */}
                    <div className="w-16 h-16 rounded-full bg-brand-600/20 flex items-center justify-center mx-auto mb-6">
                        <svg className="w-8 h-8 text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>

                    <h1 className="text-xl font-bold text-white mb-2">Verify Your Email</h1>
                    
                    <p className="text-slate-400 mb-6">
                        We've sent a verification link to your email address. 
                        Click the link to verify your account and unlock full access.
                    </p>

                    {status === 'verification-link-sent' && (
                        <div className="mb-6 p-4 rounded-lg bg-green-500/10 border border-green-500/20">
                            <p className="text-sm text-green-400">
                                ✓ A new verification link has been sent to your email!
                            </p>
                        </div>
                    )}

                    <form onSubmit={submit} className="space-y-4">
                        <button
                            type="submit"
                            disabled={processing}
                            className="btn-primary w-full"
                        >
                            {processing ? 'Sending...' : 'Resend Verification Email'}
                        </button>

                        <div className="flex items-center justify-center gap-4 pt-2">
                            <Link
                                href={route('onboarding.index')}
                                className="text-sm text-brand-400 hover:text-brand-300 transition-colors"
                            >
                                Continue to Dashboard
                            </Link>
                            <span className="text-slate-600">•</span>
                            <Link
                                href={route('logout')}
                                method="post"
                                as="button"
                                className="text-sm text-slate-400 hover:text-white transition-colors"
                            >
                                Log Out
                            </Link>
                        </div>
                    </form>
                </div>

                {/* Help text */}
                <p className="mt-6 text-sm text-slate-500 text-center max-w-sm">
                    Didn't receive the email? Check your spam folder or contact{' '}
                    <a href="mailto:support@brandcall.io" className="text-brand-400 hover:text-brand-300">
                        support@brandcall.io
                    </a>
                </p>
            </div>
        </>
    );
}
