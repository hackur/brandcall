import { Head, useForm, router } from '@inertiajs/react';
import OnboardingLayout from '@/Layouts/OnboardingLayout';
import { useState } from 'react';

interface Document {
    id: number;
    type: string;
    type_label: string;
    name: string;
    original_filename: string;
    status: string;
    notes: string | null;
    // File metadata
    mime_type: string;
    size: number;
    size_formatted: string;
    extension: string;
    is_image: boolean;
    is_pdf: boolean;
    // URLs
    thumbnail_url: string | null;
    preview_url: string | null;
    download_url: string;
    // Timestamps
    uploaded_at: string;
    uploaded_at_formatted: string;
    modified_at: string;
    modified_at_formatted: string;
    last_viewed_at: string | null;
    last_viewed_at_formatted: string | null;
    reviewed_at: string | null;
    reviewed_at_formatted: string | null;
}

interface DocumentType {
    value: string;
    label: string;
    extensions: string[];
}

interface Props {
    documents: Document[];
    documentTypes: DocumentType[];
}

export default function Documents({ documents, documentTypes }: Props) {
    const [showUploadModal, setShowUploadModal] = useState(false);
    const [previewDoc, setPreviewDoc] = useState<Document | null>(null);
    const [detailDoc, setDetailDoc] = useState<Document | null>(null);
    
    const { data, setData, post, processing, errors, reset } = useForm({
        file: null as File | null,
        type: '',
        name: '',
    });

    const selectedType = documentTypes.find(t => t.value === data.type);
    const acceptedExtensions = selectedType?.extensions.map(e => `.${e}`).join(',') || '.pdf,.jpg,.jpeg,.png';

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

    const getFileIcon = (doc: Document) => {
        if (doc.is_pdf) {
            return (
                <div className="w-full h-full bg-red-500/20 flex items-center justify-center">
                    <svg className="w-8 h-8 text-red-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 2l5 5h-5V4zM8.5 13h1c.55 0 1 .45 1 1v3c0 .55-.45 1-1 1h-1c-.55 0-1-.45-1-1v-3c0-.55.45-1 1-1zm3 0h1.5c.28 0 .5.22.5.5s-.22.5-.5.5H12v1h.5c.28 0 .5.22.5.5s-.22.5-.5.5H12v1.5c0 .28-.22.5-.5.5s-.5-.22-.5-.5V13z"/>
                    </svg>
                </div>
            );
        }
        if (doc.thumbnail_url) {
            return <img src={doc.thumbnail_url} alt={doc.name} className="w-full h-full object-cover" />;
        }
        return (
            <div className="w-full h-full bg-slate-700 flex items-center justify-center">
                <svg className="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
        );
    };

    const pendingDocs = documents.filter(d => d.status === 'pending');
    const canSubmitKyc = pendingDocs.length > 0;

    return (
        <OnboardingLayout>
            <Head title="Documents" />

            <div className="max-w-5xl mx-auto">
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
                    
                    <div className="grid md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <h4 className="font-medium text-white mb-2">Business Verification</h4>
                            <ul className="space-y-1 text-slate-300">
                                <li>• Business License</li>
                                <li>• Tax ID / EIN</li>
                                <li>• Articles of Incorporation</li>
                            </ul>
                        </div>
                        <div>
                            <h4 className="font-medium text-white mb-2">Identity Verification</h4>
                            <ul className="space-y-1 text-slate-300">
                                <li>• Driver's License</li>
                                <li>• Passport / State ID</li>
                            </ul>
                        </div>
                        <div>
                            <h4 className="font-medium text-white mb-2">Additional (if needed)</h4>
                            <ul className="space-y-1 text-slate-300">
                                <li>• Letter of Authorization</li>
                                <li>• W-9 Form</li>
                                <li>• Utility Bill</li>
                            </ul>
                        </div>
                    </div>
                </div>

                {/* Documents Grid */}
                {documents.length > 0 ? (
                    <div className="card">
                        <h2 className="text-lg font-semibold text-white mb-6">Uploaded Documents ({documents.length})</h2>
                        
                        <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                            {documents.map((doc) => (
                                <div 
                                    key={doc.id} 
                                    className="bg-slate-800/50 rounded-lg overflow-hidden border border-slate-700 hover:border-slate-600 transition-colors"
                                >
                                    {/* Thumbnail / Preview */}
                                    <div 
                                        className="aspect-[4/3] relative cursor-pointer group"
                                        onClick={() => setPreviewDoc(doc)}
                                    >
                                        {getFileIcon(doc)}
                                        <div className="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                            <span className="text-white text-sm">Click to preview</span>
                                        </div>
                                        {/* Status badge */}
                                        <span className={`absolute top-2 right-2 px-2 py-1 rounded text-xs font-medium ${getStatusBadge(doc.status)}`}>
                                            {doc.status}
                                        </span>
                                    </div>

                                    {/* Info */}
                                    <div className="p-4">
                                        <h3 className="font-medium text-white truncate" title={doc.name}>{doc.name}</h3>
                                        <p className="text-sm text-slate-400 truncate">{doc.type_label}</p>
                                        
                                        {/* Metadata */}
                                        <div className="mt-3 pt-3 border-t border-slate-700 text-xs text-slate-500 space-y-1">
                                            <div className="flex justify-between">
                                                <span>Size:</span>
                                                <span>{doc.size_formatted}</span>
                                            </div>
                                            <div className="flex justify-between">
                                                <span>Type:</span>
                                                <span className="uppercase">{doc.extension}</span>
                                            </div>
                                            <div className="flex justify-between">
                                                <span>Uploaded:</span>
                                                <span>{doc.uploaded_at_formatted}</span>
                                            </div>
                                            {doc.last_viewed_at_formatted && (
                                                <div className="flex justify-between">
                                                    <span>Last viewed:</span>
                                                    <span>{doc.last_viewed_at_formatted}</span>
                                                </div>
                                            )}
                                        </div>

                                        {/* Actions */}
                                        <div className="mt-3 flex gap-2">
                                            <button
                                                onClick={() => setDetailDoc(doc)}
                                                className="flex-1 px-3 py-1.5 bg-slate-700 hover:bg-slate-600 rounded text-sm text-white transition-colors"
                                            >
                                                Details
                                            </button>
                                            <a
                                                href={doc.download_url}
                                                download
                                                className="px-3 py-1.5 bg-slate-700 hover:bg-slate-600 rounded text-sm text-white transition-colors"
                                                title="Download"
                                            >
                                                <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                </svg>
                                            </a>
                                            {doc.status === 'pending' && (
                                                <button
                                                    onClick={() => deleteDocument(doc.id)}
                                                    className="px-3 py-1.5 bg-red-500/20 hover:bg-red-500/30 rounded text-sm text-red-400 transition-colors"
                                                    title="Delete"
                                                >
                                                    <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            )}
                                        </div>
                                    </div>
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
                                {selectedType && (
                                    <p className="helper-text">
                                        Accepted formats: {selectedType.extensions.map(e => e.toUpperCase()).join(', ')}
                                    </p>
                                )}
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
                                    accept={acceptedExtensions}
                                    className="w-full text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-brand-600 file:text-white file:font-medium hover:file:bg-brand-500"
                                    required
                                />
                                <p className="helper-text">Max file size: 10MB</p>
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

            {/* Preview Modal */}
            {previewDoc && (
                <div className="fixed inset-0 bg-slate-900/90 flex items-center justify-center z-50 p-4" onClick={() => setPreviewDoc(null)}>
                    <div className="max-w-4xl max-h-[90vh] w-full" onClick={e => e.stopPropagation()}>
                        <div className="flex justify-between items-center mb-4">
                            <h3 className="text-white font-medium">{previewDoc.name}</h3>
                            <button onClick={() => setPreviewDoc(null)} className="text-slate-400 hover:text-white">
                                <svg className="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        
                        <div className="bg-slate-800 rounded-lg overflow-hidden">
                            {previewDoc.is_pdf ? (
                                <iframe 
                                    src={previewDoc.preview_url || previewDoc.download_url} 
                                    className="w-full h-[70vh]"
                                    title={previewDoc.name}
                                />
                            ) : previewDoc.is_image ? (
                                <img 
                                    src={previewDoc.preview_url || previewDoc.download_url} 
                                    alt={previewDoc.name}
                                    className="max-w-full max-h-[70vh] mx-auto"
                                />
                            ) : (
                                <div className="p-8 text-center text-slate-400">
                                    <p>Preview not available for this file type.</p>
                                    <a href={previewDoc.download_url} download className="btn-primary mt-4 inline-block">
                                        Download File
                                    </a>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            )}

            {/* Detail Modal */}
            {detailDoc && (
                <div className="fixed inset-0 bg-slate-900/80 flex items-center justify-center z-50 p-4" onClick={() => setDetailDoc(null)}>
                    <div className="card max-w-lg w-full" onClick={e => e.stopPropagation()}>
                        <div className="flex justify-between items-center mb-6">
                            <h2 className="text-lg font-semibold text-white">Document Details</h2>
                            <button onClick={() => setDetailDoc(null)} className="text-slate-400 hover:text-white">
                                <svg className="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div className="space-y-4">
                            <div>
                                <label className="text-xs text-slate-500 uppercase tracking-wider">Name</label>
                                <p className="text-white">{detailDoc.name}</p>
                            </div>

                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label className="text-xs text-slate-500 uppercase tracking-wider">Type</label>
                                    <p className="text-white">{detailDoc.type_label}</p>
                                </div>
                                <div>
                                    <label className="text-xs text-slate-500 uppercase tracking-wider">Status</label>
                                    <p><span className={`px-2 py-1 rounded text-xs font-medium ${getStatusBadge(detailDoc.status)}`}>{detailDoc.status}</span></p>
                                </div>
                            </div>

                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label className="text-xs text-slate-500 uppercase tracking-wider">File Name</label>
                                    <p className="text-white text-sm truncate">{detailDoc.original_filename}</p>
                                </div>
                                <div>
                                    <label className="text-xs text-slate-500 uppercase tracking-wider">File Size</label>
                                    <p className="text-white">{detailDoc.size_formatted}</p>
                                </div>
                            </div>

                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label className="text-xs text-slate-500 uppercase tracking-wider">MIME Type</label>
                                    <p className="text-white text-sm">{detailDoc.mime_type}</p>
                                </div>
                                <div>
                                    <label className="text-xs text-slate-500 uppercase tracking-wider">Extension</label>
                                    <p className="text-white uppercase">{detailDoc.extension}</p>
                                </div>
                            </div>

                            <div className="pt-4 border-t border-slate-700">
                                <h4 className="text-sm font-medium text-white mb-3">Timestamps</h4>
                                <div className="space-y-2 text-sm">
                                    <div className="flex justify-between">
                                        <span className="text-slate-400">Uploaded:</span>
                                        <span className="text-white">{detailDoc.uploaded_at_formatted}</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-slate-400">Last Modified:</span>
                                        <span className="text-white">{detailDoc.modified_at_formatted}</span>
                                    </div>
                                    {detailDoc.last_viewed_at_formatted && (
                                        <div className="flex justify-between">
                                            <span className="text-slate-400">Last Viewed:</span>
                                            <span className="text-white">{detailDoc.last_viewed_at_formatted}</span>
                                        </div>
                                    )}
                                    {detailDoc.reviewed_at_formatted && (
                                        <div className="flex justify-between">
                                            <span className="text-slate-400">Reviewed:</span>
                                            <span className="text-white">{detailDoc.reviewed_at_formatted}</span>
                                        </div>
                                    )}
                                </div>
                            </div>

                            {detailDoc.notes && (
                                <div className="pt-4 border-t border-slate-700">
                                    <label className="text-xs text-slate-500 uppercase tracking-wider">Review Notes</label>
                                    <p className="text-white mt-1">{detailDoc.notes}</p>
                                </div>
                            )}

                            <div className="flex gap-3 pt-4">
                                <a href={detailDoc.download_url} download className="btn-primary flex-1 text-center">
                                    Download
                                </a>
                                <button onClick={() => { setDetailDoc(null); setPreviewDoc(detailDoc); }} className="btn-secondary flex-1">
                                    Preview
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </OnboardingLayout>
    );
}
