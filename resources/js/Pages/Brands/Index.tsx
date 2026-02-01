import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { PageProps } from '@/types';

interface Brand {
    id: number;
    name: string;
    slug: string;
    display_name: string | null;
    logo_path: string | null;
    status: string;
    api_key: string;
    created_at: string;
    phone_numbers: Array<{ id: number; phone_number: string }>;
}

interface BrandsIndexProps extends PageProps {
    brands: {
        data: Brand[];
        current_page: number;
        last_page: number;
    };
}

export default function BrandsIndex({ auth, brands }: BrandsIndexProps) {
    const getStatusBadge = (status: string) => {
        const styles: Record<string, string> = {
            active: 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-400',
            pending_vetting: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-400',
            draft: 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-400',
            suspended: 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-400',
        };
        return styles[status] || styles.draft;
    };

    return (
        <AuthenticatedLayout
            header={
                <div className="flex items-center justify-between">
                    <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                        Brands
                    </h2>
                    <Link
                        href={route('brands.create')}
                        className="rounded-lg bg-purple-500 px-4 py-2 text-sm font-medium text-white hover:bg-purple-600"
                    >
                        Create Brand
                    </Link>
                </div>
            }
        >
            <Head title="Brands" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
                        {brands.data.length > 0 ? (
                            <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead className="bg-gray-50 dark:bg-gray-900">
                                    <tr>
                                        <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                            Brand
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                            API Endpoint
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                            Phone Numbers
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                            Status
                                        </th>
                                        <th className="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-200 dark:divide-gray-700">
                                    {brands.data.map((brand) => (
                                        <tr key={brand.id}>
                                            <td className="whitespace-nowrap px-6 py-4">
                                                <div className="flex items-center">
                                                    <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-700">
                                                        {brand.logo_path ? (
                                                            <img src={`/storage/${brand.logo_path}`} alt={brand.name} className="h-8 w-8 object-contain" />
                                                        ) : (
                                                            <span className="text-lg font-semibold text-gray-600 dark:text-gray-400">
                                                                {brand.name.charAt(0)}
                                                            </span>
                                                        )}
                                                    </div>
                                                    <div className="ml-4">
                                                        <div className="font-medium text-gray-900 dark:text-white">{brand.name}</div>
                                                        <div className="text-sm text-gray-500 dark:text-gray-400">{brand.display_name}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="whitespace-nowrap px-6 py-4">
                                                <code className="rounded bg-gray-100 px-2 py-1 text-xs dark:bg-gray-700">
                                                    /api/v1/brands/{brand.slug}/calls
                                                </code>
                                            </td>
                                            <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                {brand.phone_numbers?.length || 0} numbers
                                            </td>
                                            <td className="whitespace-nowrap px-6 py-4">
                                                <span className={`rounded-full px-2 py-1 text-xs font-medium ${getStatusBadge(brand.status)}`}>
                                                    {brand.status.replace('_', ' ')}
                                                </span>
                                            </td>
                                            <td className="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                                <Link
                                                    href={route('brands.show', brand.id)}
                                                    className="text-purple-600 hover:text-purple-900 dark:text-purple-400"
                                                >
                                                    View
                                                </Link>
                                                <Link
                                                    href={route('brands.edit', brand.id)}
                                                    className="ml-4 text-gray-600 hover:text-gray-900 dark:text-gray-400"
                                                >
                                                    Edit
                                                </Link>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        ) : (
                            <div className="p-12 text-center">
                                <svg className="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <h3 className="mt-4 text-lg font-medium text-gray-900 dark:text-white">No brands yet</h3>
                                <p className="mt-2 text-sm text-gray-500 dark:text-gray-400">Get started by creating your first branded caller ID.</p>
                                <Link
                                    href={route('brands.create')}
                                    className="mt-4 inline-block rounded-lg bg-purple-500 px-4 py-2 text-sm font-medium text-white hover:bg-purple-600"
                                >
                                    Create Your First Brand
                                </Link>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
