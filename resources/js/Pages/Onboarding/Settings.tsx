import { Head, useForm, Link } from '@inertiajs/react';
import OnboardingLayout from '@/Layouts/OnboardingLayout';

interface Props {
    user: {
        name: string;
        email: string;
    };
}

export default function Settings({ user }: Props) {
    const updateProfileForm = useForm({
        name: user.name,
        email: user.email,
    });

    const updatePasswordForm = useForm({
        current_password: '',
        password: '',
        password_confirmation: '',
    });

    const deleteForm = useForm({});

    const updateProfile = (e: React.FormEvent) => {
        e.preventDefault();
        updateProfileForm.patch(route('profile.update'));
    };

    const updatePassword = (e: React.FormEvent) => {
        e.preventDefault();
        updatePasswordForm.put(route('password.update'), {
            onSuccess: () => updatePasswordForm.reset(),
        });
    };

    const deleteAccount = (e: React.FormEvent) => {
        e.preventDefault();
        if (confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
            deleteForm.delete(route('profile.destroy'));
        }
    };

    return (
        <OnboardingLayout>
            <Head title="Settings" />

            <div className="max-w-2xl mx-auto">
                <div className="mb-10">
                    <h1 className="text-2xl font-bold text-white mb-2">Account Settings</h1>
                    <p className="text-slate-400">
                        Manage your account preferences and security.
                    </p>
                </div>

                {/* Profile Information */}
                <div className="card mb-8">
                    <h2 className="text-lg font-semibold text-white mb-6">Profile Information</h2>
                    <form onSubmit={updateProfile} className="space-y-6">
                        <div>
                            <label className="label">Name</label>
                            <input
                                type="text"
                                value={updateProfileForm.data.name}
                                onChange={e => updateProfileForm.setData('name', e.target.value)}
                                className={`input ${updateProfileForm.errors.name ? 'input-error' : ''}`}
                                required
                            />
                            {updateProfileForm.errors.name && (
                                <p className="error-text">{updateProfileForm.errors.name}</p>
                            )}
                        </div>

                        <div>
                            <label className="label">Email</label>
                            <input
                                type="email"
                                value={updateProfileForm.data.email}
                                onChange={e => updateProfileForm.setData('email', e.target.value)}
                                className={`input ${updateProfileForm.errors.email ? 'input-error' : ''}`}
                                required
                            />
                            {updateProfileForm.errors.email && (
                                <p className="error-text">{updateProfileForm.errors.email}</p>
                            )}
                            <p className="helper-text">
                                Changing your email will require re-verification.
                            </p>
                        </div>

                        <div className="flex justify-end">
                            <button
                                type="submit"
                                disabled={updateProfileForm.processing}
                                className="btn-primary"
                            >
                                {updateProfileForm.processing ? 'Saving...' : 'Save Changes'}
                            </button>
                        </div>
                    </form>
                </div>

                {/* Update Password */}
                <div className="card mb-8">
                    <h2 className="text-lg font-semibold text-white mb-6">Update Password</h2>
                    <form onSubmit={updatePassword} className="space-y-6">
                        <div>
                            <label className="label">Current Password</label>
                            <input
                                type="password"
                                value={updatePasswordForm.data.current_password}
                                onChange={e => updatePasswordForm.setData('current_password', e.target.value)}
                                className={`input ${updatePasswordForm.errors.current_password ? 'input-error' : ''}`}
                                required
                            />
                            {updatePasswordForm.errors.current_password && (
                                <p className="error-text">{updatePasswordForm.errors.current_password}</p>
                            )}
                        </div>

                        <div>
                            <label className="label">New Password</label>
                            <input
                                type="password"
                                value={updatePasswordForm.data.password}
                                onChange={e => updatePasswordForm.setData('password', e.target.value)}
                                className={`input ${updatePasswordForm.errors.password ? 'input-error' : ''}`}
                                required
                            />
                            {updatePasswordForm.errors.password && (
                                <p className="error-text">{updatePasswordForm.errors.password}</p>
                            )}
                        </div>

                        <div>
                            <label className="label">Confirm New Password</label>
                            <input
                                type="password"
                                value={updatePasswordForm.data.password_confirmation}
                                onChange={e => updatePasswordForm.setData('password_confirmation', e.target.value)}
                                className={`input ${updatePasswordForm.errors.password_confirmation ? 'input-error' : ''}`}
                                required
                            />
                            {updatePasswordForm.errors.password_confirmation && (
                                <p className="error-text">{updatePasswordForm.errors.password_confirmation}</p>
                            )}
                        </div>

                        <div className="flex justify-end">
                            <button
                                type="submit"
                                disabled={updatePasswordForm.processing}
                                className="btn-primary"
                            >
                                {updatePasswordForm.processing ? 'Updating...' : 'Update Password'}
                            </button>
                        </div>
                    </form>
                </div>

                {/* Email Verification */}
                <div className="card mb-8">
                    <h2 className="text-lg font-semibold text-white mb-4">Email Verification</h2>
                    <p className="text-slate-400 mb-4">
                        If you haven't received the verification email, you can request a new one.
                    </p>
                    <Link
                        href={route('verification.send')}
                        method="post"
                        as="button"
                        className="btn-secondary"
                    >
                        Resend Verification Email
                    </Link>
                </div>

                {/* Danger Zone */}
                <div className="card border-red-500/20">
                    <h2 className="text-lg font-semibold text-red-400 mb-4">Danger Zone</h2>
                    <p className="text-slate-400 mb-4">
                        Once you delete your account, all of your data will be permanently removed.
                        This action cannot be undone.
                    </p>
                    <form onSubmit={deleteAccount}>
                        <button
                            type="submit"
                            disabled={deleteForm.processing}
                            className="px-4 py-2 bg-red-600/20 text-red-400 border border-red-500/30 rounded-lg hover:bg-red-600/30 transition-colors"
                        >
                            {deleteForm.processing ? 'Deleting...' : 'Delete Account'}
                        </button>
                    </form>
                </div>
            </div>
        </OnboardingLayout>
    );
}
