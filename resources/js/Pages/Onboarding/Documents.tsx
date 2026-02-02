import { Head, useForm, router } from '@inertiajs/react';
import OnboardingLayout from '@/Layouts/OnboardingLayout';
import { useState } from 'react';

interface Document {
    id: number;
    type: string;
    name: string;
    original_filename: string;
    status: string;
    created_at: string;
    notes: string | null;
}

interface DocumentType {
    value: string;
    label: string;
}

interface Props {
    documents: Document[];
    documentTypes: DocumentType[];
}

export default function Documents({ documents, documentTypes }: Props) {
    const [showUploadModal, setShowUploadModal] = useState(false);
    
    const { data, setData, post, processing, errors, reset } = useForm({
        file: null as File | null,
        type: '',
        name: '',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('onboarding.documents.upload'), {
            forceFormData: true,
            onSuccess: () => {
                reset();
                setShowUploadModal(false);
            },
        });
    };

    const deleteDocument = (id: number) => {
        if (confirm('Are you sure you want to delete this document?')) {
            router.delete(route('onboarding.documents.delete', id));
        }
    };

    const submitKyc = () => {
        if (confirm('Submit your documents for KYC review? This action cannot be undone.')) {
            router.post(route('onboarding.kyc.submit'));
        }
    };

    const getStatusBadge = (status: string) => {
        const styles: Record<string, string> = {
            pending: 'bg-yellow-500/20 text-yellow-400',
            approved: 'bg-green-500/20 text-green-400',
            rejected: 'bg-red-500/20 text-red-400',
        };
        return styles[status] || styles.pending;
    };

    const getTypeLabel = (type: string) => {
        const found = documentTypes.find(t => t.value === type);
        return found?.label || type;
    };

    const pendingDocs = documents.filter(d => d.status === 'pending');
    const canSubmitKyc = pendingDocs.length > 0;

    return (
        <OnboardingLayout>
            <Head title="Documents" />

            <div className="max-w-4xl mx-auto">
                <div className="flex justify-between items-start mb-8">
                    <div>
                        <h1 className="text-2xl font-bold text-white mb-2">Documents</h1>
                        <p className="text-slate-400">
                            Upload verification documents for KYC approval.
                        </p>
                    </div>
                    <button 
                        onClick={() => setShowUploadModal(true)}
                        className="btn-primary"
                    >
                        <svg className="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
                        </svg>
                        Upload Document
                    </button>
                </div>

                {/* Required Documents Info */}
                <div className="card mb-8 bg-brand-600/5 border-brand-500/20">
                    <h3 className="font-semibold text-white mb-3">Required Documents</h3>
                    <p className="text-slate-400 text-sm mb-4">
                        To complete KYC verification, please upload the following:
                    </p>
                    
                    <div className="mb-4">
                        <h4 className="text-sm font-medium text-white mb-2">Business Verification (at least one)</h4>
                        <ul className="grid md:grid-cols-2 gap-2 text-sm text-slate-300">
                            <li className="flex items-center gap-2">
                                <svg className="w-4 h-4 text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Business License
                            </li>
                            <li className="flex items-center gap-2">
                                <svg className="w-4 h-4 text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Tax ID / EIN Document
                            </li>
                            <li className="flex items-center gap-2">
                                <svg className="w-4 h-4 text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Articles of Incorporation
                            </li>
                        </ul>
                    </div>
                    
                    <div className="mb-4">
                        <h4 className="text-sm font-medium text-white mb-2">Identity Verification (required)</h4>
                        <ul className="grid md:grid-cols-2 gap-2 text-sm text-slate-300">
                            <li className="flex items-center gap-2">
                                <svg className="w-4 h-4 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                Driver's License
                            </li>
                            <li className="flex items-center gap-2">
                                <svg className="w-4 h-4 text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Government ID (Passport, State ID)
                            </li>
                        </ul>
                    </div>
                    
                    <div>
                        <h4 className="text-sm font-medium text-white mb-2">Additional (if applicable)</h4>
                        <ul className="grid md:grid-cols-2 gap-2 text-sm text-slate-300">
                            <li className="flex items-center gap-2">
                                <svg className="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Letter of Authorization (LOA)
                            </li>
                            <li className="flex items-center gap-2">
                                <svg className="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                W-9 Form (US businesses)
                            </li>
                            <li className="flex items-center gap-2">
                                <svg className="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Utility Bill (address verification)
                            </li>
                        </ul>
                    </div>
                </div>

                {/* Documents List */}
                {documents.length > 0 ? (
                    <div className="card">
                        <h2 className="text-lg font-semibold text-white mb-6">Uploaded Documents</h2>
                        <div className="space-y-4">
                            {documents.map((doc) => (
                                <div key={doc.id} className="flex items-center gap-4 p-4 bg-slate-800/50 rounded-lg">
                                    <div className="w-10 h-10 rounded-lg bg-slate-700 flex items-center justify-center text-slate-400">
                                        <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <div className="flex-1 min-w-0">
                                        <p className="font-medium text-white truncate">{doc.name}</p>
                                        <p className="text-sm text-slate-400">
                                            {getTypeLabel(doc.type)} • {doc.original_filename}
                                        </p>
                                    </div>
                                    <span className={`px-2 py-1 rounded text-xs font-medium ${getStatusBadge(doc.status)}`}>
                                        {doc.status}
                                    </span>
                                    {doc.status === 'pending' && (
                                        <button
                                            onClick={() => deleteDocument(doc.id)}
                                            className="p-2 text-slate-400 hover:text-red-400 transition-colors"
                                            title="Delete"
                                        >
                                            <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    )}
                                </div>
                            ))}
                        </div>

                        {/* Submit KYC Button */}
                        {canSubmitKyc && (
                            <div className="mt-6 pt-6 border-t border-slate-700">
                                <button onClick={submitKyc} className="btn-primary w-full">
                                    Submit for KYC Review
                                </button>
                                <p className="text-sm text-slate-400 text-center mt-2">
                                    Review typically takes 1-2 business days
                                </p>
                            </div>
                        )}
                    </div>
                ) : (
                    <div className="card text-center py-12">
                        <div className="w-16 h-16 rounded-full bg-slate-800 flex items-center justify-center mx-auto mb-4">
                            <svg className="w-8 h-8 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 className="text-lg font-semibold text-white mb-2">No documents uploaded</h3>
                        <p className="text-slate-400 mb-6">Upload your first document to begin KYC verification.</p>
                        <button onClick={() => setShowUploadModal(true)} className="btn-primary">
                            Upload Document
                        </button>
                    </div>
                )}
            </div>

            {/* Upload Modal */}
            {showUploadModal && (
                <div className="fixed inset-0 bg-slate-900/80 flex items-center justify-center z-50 p-4">
                    <div className="card max-w-md w-full">
                        <div className="flex justify-between items-center mb-6">
                            <h2 className="text-lg font-semibold text-white">Upload Document</h2>
                            <button 
                                onClick={() => setShowUploadModal(false)}
                                className="text-slate-400 hover:text-white"
                            >
                                <svg className="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <form onSubmit={submit} className="space-y-6">
                            <div>
                                <label className="label">Document Type *</label>
                                <select
                                    value={data.type}
                                    onChange={e => setData('type', e.target.value)}
                                    className={`input ${errors.type ? 'input-error' : ''}`}
                                    required
                                >
                                    <option value="">Select type...</option>
                                    {documentTypes.map(type => (
                                        <option key={type.value} value={type.value}>{type.label}</option>
                                    ))}
                                </select>
                                {errors.type && <p className="error-text">{errors.type}</p>}
                            </div>

                            <div>
                                <label className="label">Document Name *</label>
                                <input
                                    type="text"
                                    value={data.name}
                                    onChange={e => setData('name', e.target.value)}
                                    placeholder="e.g., Business License 2024"
                                    className={`input ${errors.name ? 'input-error' : ''}`}
                                    required
                                />
                                {errors.name && <p className="error-text">{errors.name}</p>}
                            </div>

                            <div>
                                <label className="label">File *</label>
                                <input
                                    type="file"
                                    onChange={e => setData('file', e.target.files?.[0] || null)}
                                    accept=".pdf,.jpg,.jpeg,.png"
                                    className="w-full text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-brand-600 file:text-white file:font-medium hover:file:bg-brand-500"
                                    required
                                />
                                <p className="helper-text">PDF, JPG, or PNG. Max 10MB.</p>
                                {errors.file && <p className="error-text">{errors.file}</p>}
                            </div>

                            <div className="flex gap-3">
                                <button
                                    type="button"
                                    onClick={() => setShowUploadModal(false)}
                                    className="btn-secondary flex-1"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="btn-primary flex-1"
                                >
                                    {processing ? 'Uploading...' : 'Upload'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </OnboardingLayout>
    );
}
