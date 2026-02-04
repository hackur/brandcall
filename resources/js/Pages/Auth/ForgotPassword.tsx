import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import ThemeToggle from '@/Components/ThemeToggle';
import { Head, Link, useForm } from '@inertiajs/react';
import { FormEventHandler } from 'react';

export default function ForgotPassword({ status }: { status?: string }) {
    const { data, setData, post, processing, errors } = useForm({
        email: '',
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        post(route('password.email'));
    };

    return (
        <>
            <Head title="Reset Password - BrandCall" />
            
            <div className="relative min-h-screen bg-theme-primary transition-colors duration-300">
                {/* Subtle gradient accent */}
                <div className="absolute inset-0 overflow-hidden pointer-events-none">
                    <div className="absolute -left-40 -top-40 h-96 w-96 rounded-full bg-brand-600/5 blur-3xl" />
                    <div className="absolute -right-40 top-20 h-96 w-96 rounded-full bg-purple-600/5 blur-3xl" />
                </div>

                <div className="relative z-10 flex min-h-screen flex-col">
                    {/* Header - consistent with main site */}
                    <nav className="px-5 sm:px-6 py-3 sm:py-4 border-b border-theme-primary">
                        <div className="mx-auto flex max-w-7xl items-center justify-between">
                            <Link href="/" className="flex items-center gap-2 sm:gap-3">
                                <div className="flex h-8 w-8 sm:h-10 sm:w-10 items-center justify-center rounded-lg bg-brand-600">
                                    <svg className="h-4 w-4 sm:h-5 sm:w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                </div>
                                <span className="text-lg sm:text-xl font-semibold text-theme-primary">BrandCall</span>
                            </Link>

                            <div className="flex items-center gap-2 sm:gap-4">
                                <ThemeToggle />
                                <Link 
                                    href={route('login')} 
                                    className="text-sm text-theme-tertiary hover:text-theme-primary transition-colors"
                                >
                                    <span className="hidden sm:inline">Remember your password? </span>
                                    <span className="text-brand-500">Sign in</span>
                                </Link>
                            </div>
                        </div>
                    </nav>

                    {/* Main Content */}
                    <div className="flex flex-1 items-center justify-center px-6 py-12">
                        <div className="w-full max-w-md">
                            {/* Form Card */}
                            <div className="card p-8">
                                <div className="mb-6">
                                    <h2 className="text-2xl font-bold text-theme-primary">Reset your password</h2>
                                    <p className="mt-2 text-theme-muted">
                                        Forgot your password? No problem. Enter your email address and we'll send you a password reset link.
                                    </p>
                                </div>

                                {status && (
                                    <div className="mb-4 rounded-lg border border-green-500/20 bg-green-500/10 p-3 text-sm font-medium text-green-500">
                                        {status}
                                    </div>
                                )}

                                <form onSubmit={submit} className="space-y-6">
                                    <div>
                                        <InputLabel htmlFor="email" value="Email" className="text-theme-secondary" />
                                        <TextInput
                                            id="email"
                                            type="email"
                                            name="email"
                                            value={data.email}
                                            className="input mt-1"
                                            isFocused={true}
                                            onChange={(e) => setData('email', e.target.value)}
                                        />
                                        <InputError message={errors.email} className="mt-2" />
                                    </div>

                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="btn-primary w-full"
                                    >
                                        {processing ? (
                                            <>
                                                <svg className="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                                    <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                                                    <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                                                </svg>
                                                Sending...
                                            </>
                                        ) : (
                                            'Send Reset Link'
                                        )}
                                    </button>
                                </form>

                                {/* Back to login link */}
                                <div className="mt-6 text-center">
                                    <Link
                                        href={route('login')}
                                        className="inline-flex items-center gap-2 text-sm text-theme-muted hover:text-theme-primary transition-colors"
                                    >
                                        <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                                        </svg>
                                        Back to sign in
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
