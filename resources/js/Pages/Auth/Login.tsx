import Checkbox from '@/Components/Checkbox';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import { Head, Link, useForm } from '@inertiajs/react';
import { FormEventHandler } from 'react';

export default function Login({
    status,
    canResetPassword,
}: {
    status?: string;
    canResetPassword: boolean;
}) {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: '',
        password: '',
        remember: false as boolean,
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        post(route('login'), {
            onFinish: () => reset('password'),
        });
    };

    return (
        <>
            <Head title="Sign In - BrandCall" />
            
            <div className="relative min-h-screen overflow-hidden">
                {/* Animated Background */}
                <div className="absolute inset-0 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950" />
                <div className="absolute inset-0 overflow-hidden">
                    <div className="animate-blob absolute -left-40 -top-40 h-96 w-96 rounded-full bg-purple-600/10 mix-blend-screen blur-3xl filter" />
                    <div className="animate-blob animation-delay-2000 absolute -right-40 top-20 h-96 w-96 rounded-full bg-indigo-600/10 mix-blend-screen blur-3xl filter" />
                </div>

                <div className="relative z-10 flex min-h-screen flex-col">
                    {/* Header */}
                    <nav className="px-6 py-4">
                        <div className="mx-auto flex max-w-7xl items-center justify-between">
                            <Link href="/" className="flex items-center gap-2">
                                <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-purple-500 to-indigo-600">
                                    <svg className="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                </div>
                                <span className="text-xl font-bold text-white">BrandCall</span>
                            </Link>
                            <Link href={route('register')} className="text-sm text-gray-400 hover:text-white">
                                Don't have an account? <span className="text-purple-400">Get started</span>
                            </Link>
                        </div>
                    </nav>

                    {/* Main Content */}
                    <div className="flex flex-1 items-center justify-center px-6 py-12">
                        <div className="w-full max-w-md">
                            {/* Form Card */}
                            <div className="rounded-2xl border border-gray-800 bg-gray-900/80 p-8 backdrop-blur">
                                <div className="mb-6">
                                    <h2 className="text-2xl font-bold text-white">Welcome back</h2>
                                    <p className="mt-1 text-gray-400">Sign in to your BrandCall account</p>
                                </div>

                                {status && (
                                    <div className="mb-4 rounded-lg border border-green-500/20 bg-green-500/10 p-3 text-sm font-medium text-green-400">
                                        {status}
                                    </div>
                                )}

                                <form onSubmit={submit} className="space-y-6">
                                    <div>
                                        <InputLabel htmlFor="email" value="Email" className="text-gray-300" />
                                        <TextInput
                                            id="email"
                                            type="email"
                                            name="email"
                                            value={data.email}
                                            className="mt-1 block w-full border-gray-700 bg-gray-800 text-white focus:border-purple-500 focus:ring-purple-500"
                                            autoComplete="username"
                                            isFocused={true}
                                            onChange={(e) => setData('email', e.target.value)}
                                        />
                                        <InputError message={errors.email} className="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel htmlFor="password" value="Password" className="text-gray-300" />
                                        <TextInput
                                            id="password"
                                            type="password"
                                            name="password"
                                            value={data.password}
                                            className="mt-1 block w-full border-gray-700 bg-gray-800 text-white focus:border-purple-500 focus:ring-purple-500"
                                            autoComplete="current-password"
                                            onChange={(e) => setData('password', e.target.value)}
                                        />
                                        <InputError message={errors.password} className="mt-2" />
                                    </div>

                                    <div className="flex items-center justify-between">
                                        <label className="flex items-center">
                                            <Checkbox
                                                name="remember"
                                                checked={data.remember}
                                                onChange={(e) =>
                                                    setData(
                                                        'remember',
                                                        (e.target.checked || false) as false,
                                                    )
                                                }
                                                className="border-gray-600 bg-gray-800 text-purple-500 focus:ring-purple-500 focus:ring-offset-gray-900"
                                            />
                                            <span className="ms-2 text-sm text-gray-400">
                                                Remember me
                                            </span>
                                        </label>

                                        {canResetPassword && (
                                            <Link
                                                href={route('password.request')}
                                                className="text-sm text-purple-400 hover:text-purple-300"
                                            >
                                                Forgot password?
                                            </Link>
                                        )}
                                    </div>

                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-purple-500 to-indigo-600 px-6 py-2.5 font-medium text-white shadow-lg shadow-purple-500/25 transition-all hover:shadow-purple-500/40 disabled:cursor-not-allowed disabled:opacity-50"
                                    >
                                        {processing ? (
                                            <>
                                                <svg className="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                                    <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                                                    <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                                                </svg>
                                                Signing in...
                                            </>
                                        ) : (
                                            'Sign in'
                                        )}
                                    </button>
                                </form>
                            </div>

                            {/* Mobile register link */}
                            <div className="mt-6 text-center sm:hidden">
                                <Link href={route('register')} className="text-sm text-gray-400 hover:text-white">
                                    Don't have an account? <span className="text-purple-400">Get started</span>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                {/* CSS for animations */}
                <style>{`
                    @keyframes blob {
                        0%, 100% { transform: translate(0, 0) scale(1); }
                        25% { transform: translate(20px, -30px) scale(1.1); }
                        50% { transform: translate(-20px, 20px) scale(0.9); }
                        75% { transform: translate(30px, 10px) scale(1.05); }
                    }
                    .animate-blob {
                        animation: blob 15s infinite;
                    }
                    .animation-delay-2000 {
                        animation-delay: 2s;
                    }
                `}</style>
            </div>
        </>
    );
}
