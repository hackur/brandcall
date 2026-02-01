import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm, Link } from '@inertiajs/react';
import { PageProps } from '@/types';
import { FormEventHandler } from 'react';

export default function BrandsCreate({ auth }: PageProps) {
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        display_name: '',
        call_reason: '',
        logo: null as File | null,
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(route('brands.store'));
    };

    return (
        <AuthenticatedLayout
            header={
                <div className="flex items-center gap-4">
                    <Link
                        href={route('brands.index')}
                        className="rounded-lg p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700"
                    >
                        <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                    <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                        Create Brand
                    </h2>
                </div>
            }
        >
            <Head title="Create Brand" />

            <div className="py-12">
                <div className="mx-auto max-w-2xl sm:px-6 lg:px-8">
                    <form onSubmit={submit} className="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                        <div className="space-y-6">
                            {/* Brand Name */}
                            <div>
                                <label htmlFor="name" className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Brand Name *
                                </label>
                                <input
                                    type="text"
                                    id="name"
                                    value={data.name}
                                    onChange={(e) => setData('name', e.target.value)}
                                    className="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                    placeholder="Health Insurance Florida"
                                    required
                                />
                                {errors.name && (
                                    <p className="mt-1 text-sm text-red-600 dark:text-red-400">{errors.name}</p>
                                )}
                                <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    This is how your brand will appear in our system.
                                </p>
                            </div>

                            {/* Display Name */}
                            <div>
                                <label htmlFor="display_name" className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Display Name (CNAM)
                                </label>
                                <input
                                    type="text"
                                    id="display_name"
                                    value={data.display_name}
                                    onChange={(e) => setData('display_name', e.target.value)}
                                    maxLength={32}
                                    className="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                    placeholder="FL Health Ins"
                                />
                                {errors.display_name && (
                                    <p className="mt-1 text-sm text-red-600 dark:text-red-400">{errors.display_name}</p>
                                )}
                                <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Max 32 characters. This appears on the recipient's phone.
                                </p>
                            </div>

                            {/* Call Reason */}
                            <div>
                                <label htmlFor="call_reason" className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Default Call Reason
                                </label>
                                <input
                                    type="text"
                                    id="call_reason"
                                    value={data.call_reason}
                                    onChange={(e) => setData('call_reason', e.target.value)}
                                    maxLength={100}
                                    className="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                    placeholder="Appointment Reminder"
                                />
                                {errors.call_reason && (
                                    <p className="mt-1 text-sm text-red-600 dark:text-red-400">{errors.call_reason}</p>
                                )}
                                <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Displayed as part of Rich Call Data on supported devices.
                                </p>
                            </div>

                            {/* Logo Upload */}
                            <div>
                                <label htmlFor="logo" className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Brand Logo
                                </label>
                                <div className="mt-1 flex items-center gap-4">
                                    <div className="flex h-20 w-20 items-center justify-center rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600">
                                        {data.logo ? (
                                            <img
                                                src={URL.createObjectURL(data.logo)}
                                                alt="Preview"
                                                className="h-16 w-16 object-contain"
                                            />
                                        ) : (
                                            <svg className="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        )}
                                    </div>
                                    <input
                                        type="file"
                                        id="logo"
                                        accept="image/*"
                                        onChange={(e) => setData('logo', e.target.files?.[0] || null)}
                                        className="block text-sm text-gray-500 file:mr-4 file:rounded-lg file:border-0 file:bg-purple-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-purple-700 hover:file:bg-purple-100 dark:text-gray-400"
                                    />
                                </div>
                                {errors.logo && (
                                    <p className="mt-1 text-sm text-red-600 dark:text-red-400">{errors.logo}</p>
                                )}
                                <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    PNG or JPG, max 2MB. Recommended: 512x512px.
                                </p>
                            </div>
                        </div>

                        {/* Submit */}
                        <div className="mt-8 flex items-center justify-end gap-4">
                            <Link
                                href={route('brands.index')}
                                className="rounded-lg px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                disabled={processing}
                                className="rounded-lg bg-purple-500 px-4 py-2 text-sm font-medium text-white hover:bg-purple-600 disabled:opacity-50"
                            >
                                {processing ? 'Creating...' : 'Create Brand'}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
