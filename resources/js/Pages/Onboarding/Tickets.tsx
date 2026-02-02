import { Head, useForm } from '@inertiajs/react';
import OnboardingLayout from '@/Layouts/OnboardingLayout';
import { useState } from 'react';

interface Reply {
    id: number;
    message: string;
    user: { id: number; name: string };
    created_at: string;
}

interface Ticket {
    id: number;
    ticket_number: string;
    subject: string;
    description: string;
    category: string;
    priority: string;
    status: string;
    created_at: string;
    replies: Reply[];
}

interface Category {
    value: string;
    label: string;
}

interface Props {
    tickets: Ticket[];
    categories: Category[];
}

export default function Tickets({ tickets, categories }: Props) {
    const [showCreateModal, setShowCreateModal] = useState(false);
    const [selectedTicket, setSelectedTicket] = useState<Ticket | null>(null);

    const createForm = useForm({
        subject: '',
        description: '',
        category: 'general',
        priority: 'medium',
    });

    const replyForm = useForm({
        message: '',
    });

    const submitCreate = (e: React.FormEvent) => {
        e.preventDefault();
        createForm.post(route('onboarding.tickets.create'), {
            onSuccess: () => {
                createForm.reset();
                setShowCreateModal(false);
            },
        });
    };

    const submitReply = (e: React.FormEvent) => {
        e.preventDefault();
        if (!selectedTicket) return;
        
        replyForm.post(route('onboarding.tickets.reply', selectedTicket.id), {
            onSuccess: () => {
                replyForm.reset();
            },
        });
    };

    const getStatusBadge = (status: string) => {
        const styles: Record<string, string> = {
            open: 'bg-green-500/20 text-green-400',
            in_progress: 'bg-blue-500/20 text-blue-400',
            waiting: 'bg-yellow-500/20 text-yellow-400',
            resolved: 'bg-slate-500/20 text-slate-400',
            closed: 'bg-slate-600/20 text-slate-500',
        };
        return styles[status] || styles.open;
    };

    const getPriorityBadge = (priority: string) => {
        const styles: Record<string, string> = {
            low: 'bg-slate-500/20 text-slate-400',
            medium: 'bg-blue-500/20 text-blue-400',
            high: 'bg-orange-500/20 text-orange-400',
            urgent: 'bg-red-500/20 text-red-400',
        };
        return styles[priority] || styles.medium;
    };

    const getCategoryLabel = (category: string) => {
        const found = categories.find(c => c.value === category);
        return found?.label || category;
    };

    const formatDate = (dateString: string) => {
        return new Date(dateString).toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        });
    };

    return (
        <OnboardingLayout>
            <Head title="Support" />

            <div className="max-w-4xl mx-auto">
                <div className="flex justify-between items-start mb-8">
                    <div>
                        <h1 className="text-2xl font-bold text-white mb-2">Support</h1>
                        <p className="text-slate-400">
                            Get help from our team. We typically respond within 24 hours.
                        </p>
                    </div>
                    <button 
                        onClick={() => setShowCreateModal(true)}
                        className="btn-primary"
                    >
                        <svg className="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
                        </svg>
                        New Ticket
                    </button>
                </div>

                <div className="grid lg:grid-cols-5 gap-6">
                    {/* Tickets List */}
                    <div className="lg:col-span-2">
                        <div className="card">
                            <h2 className="text-lg font-semibold text-white mb-4">Your Tickets</h2>
                            {tickets.length > 0 ? (
                                <div className="space-y-2">
                                    {tickets.map((ticket) => (
                                        <button
                                            key={ticket.id}
                                            onClick={() => setSelectedTicket(ticket)}
                                            className={`w-full text-left p-4 rounded-lg border transition-colors ${
                                                selectedTicket?.id === ticket.id
                                                    ? 'bg-brand-600/10 border-brand-500/30'
                                                    : 'bg-slate-800/50 border-slate-700/50 hover:bg-slate-800'
                                            }`}
                                        >
                                            <div className="flex items-start justify-between gap-2 mb-2">
                                                <p className="font-medium text-white truncate">{ticket.subject}</p>
                                                <span className={`px-2 py-0.5 rounded text-xs font-medium whitespace-nowrap ${getStatusBadge(ticket.status)}`}>
                                                    {ticket.status.replace('_', ' ')}
                                                </span>
                                            </div>
                                            <p className="text-sm text-slate-400">{ticket.ticket_number}</p>
                                        </button>
                                    ))}
                                </div>
                            ) : (
                                <p className="text-slate-400 text-center py-8">No tickets yet</p>
                            )}
                        </div>
                    </div>

                    {/* Ticket Detail */}
                    <div className="lg:col-span-3">
                        {selectedTicket ? (
                            <div className="card">
                                <div className="border-b border-slate-700 pb-4 mb-4">
                                    <div className="flex items-start justify-between gap-4">
                                        <div>
                                            <p className="text-sm text-slate-400 mb-1">{selectedTicket.ticket_number}</p>
                                            <h2 className="text-lg font-semibold text-white">{selectedTicket.subject}</h2>
                                        </div>
                                        <div className="flex gap-2">
                                            <span className={`px-2 py-1 rounded text-xs font-medium ${getPriorityBadge(selectedTicket.priority)}`}>
                                                {selectedTicket.priority}
                                            </span>
                                            <span className={`px-2 py-1 rounded text-xs font-medium ${getStatusBadge(selectedTicket.status)}`}>
                                                {selectedTicket.status.replace('_', ' ')}
                                            </span>
                                        </div>
                                    </div>
                                    <p className="text-sm text-slate-400 mt-2">
                                        {getCategoryLabel(selectedTicket.category)} • Created {formatDate(selectedTicket.created_at)}
                                    </p>
                                </div>

                                {/* Messages */}
                                <div className="space-y-4 max-h-96 overflow-y-auto mb-4">
                                    {/* Original message */}
                                    <div className="p-4 bg-slate-800/50 rounded-lg">
                                        <p className="text-sm text-slate-400 mb-2">You</p>
                                        <p className="text-slate-200 whitespace-pre-wrap">{selectedTicket.description}</p>
                                    </div>

                                    {/* Replies */}
                                    {selectedTicket.replies.map((reply) => (
                                        <div 
                                            key={reply.id} 
                                            className={`p-4 rounded-lg ${
                                                reply.user.name === 'Support' 
                                                    ? 'bg-brand-600/10 border border-brand-500/20' 
                                                    : 'bg-slate-800/50'
                                            }`}
                                        >
                                            <p className="text-sm text-slate-400 mb-2">{reply.user.name}</p>
                                            <p className="text-slate-200 whitespace-pre-wrap">{reply.message}</p>
                                            <p className="text-xs text-slate-500 mt-2">{formatDate(reply.created_at)}</p>
                                        </div>
                                    ))}
                                </div>

                                {/* Reply Form */}
                                {!['resolved', 'closed'].includes(selectedTicket.status) && (
                                    <form onSubmit={submitReply} className="border-t border-slate-700 pt-4">
                                        <textarea
                                            value={replyForm.data.message}
                                            onChange={e => replyForm.setData('message', e.target.value)}
                                            placeholder="Type your reply..."
                                            rows={3}
                                            className="input mb-3"
                                            required
                                        />
                                        <button
                                            type="submit"
                                            disabled={replyForm.processing}
                                            className="btn-primary"
                                        >
                                            {replyForm.processing ? 'Sending...' : 'Send Reply'}
                                        </button>
                                    </form>
                                )}
                            </div>
                        ) : (
                            <div className="card text-center py-12">
                                <div className="w-16 h-16 rounded-full bg-slate-800 flex items-center justify-center mx-auto mb-4">
                                    <svg className="w-8 h-8 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                </div>
                                <h3 className="text-lg font-semibold text-white mb-2">Select a ticket</h3>
                                <p className="text-slate-400">Choose a ticket from the list to view details</p>
                            </div>
                        )}
                    </div>
                </div>
            </div>

            {/* Create Ticket Modal */}
            {showCreateModal && (
                <div className="fixed inset-0 bg-slate-900/80 flex items-center justify-center z-50 p-4">
                    <div className="card max-w-lg w-full">
                        <div className="flex justify-between items-center mb-6">
                            <h2 className="text-lg font-semibold text-white">New Support Ticket</h2>
                            <button 
                                onClick={() => setShowCreateModal(false)}
                                className="text-slate-400 hover:text-white"
                            >
                                <svg className="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <form onSubmit={submitCreate} className="space-y-6">
                            <div>
                                <label className="label">Subject *</label>
                                <input
                                    type="text"
                                    value={createForm.data.subject}
                                    onChange={e => createForm.setData('subject', e.target.value)}
                                    placeholder="Brief description of your issue"
                                    className={`input ${createForm.errors.subject ? 'input-error' : ''}`}
                                    required
                                />
                                {createForm.errors.subject && <p className="error-text">{createForm.errors.subject}</p>}
                            </div>

                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label className="label">Category</label>
                                    <select
                                        value={createForm.data.category}
                                        onChange={e => createForm.setData('category', e.target.value)}
                                        className="input"
                                    >
                                        {categories.map(cat => (
                                            <option key={cat.value} value={cat.value}>{cat.label}</option>
                                        ))}
                                    </select>
                                </div>
                                <div>
                                    <label className="label">Priority</label>
                                    <select
                                        value={createForm.data.priority}
                                        onChange={e => createForm.setData('priority', e.target.value)}
                                        className="input"
                                    >
                                        <option value="low">Low</option>
                                        <option value="medium">Medium</option>
                                        <option value="high">High</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label className="label">Description *</label>
                                <textarea
                                    value={createForm.data.description}
                                    onChange={e => createForm.setData('description', e.target.value)}
                                    placeholder="Describe your issue in detail..."
                                    rows={5}
                                    className={`input ${createForm.errors.description ? 'input-error' : ''}`}
                                    required
                                />
                                {createForm.errors.description && <p className="error-text">{createForm.errors.description}</p>}
                            </div>

                            <div className="flex gap-3">
                                <button
                                    type="button"
                                    onClick={() => setShowCreateModal(false)}
                                    className="btn-secondary flex-1"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    disabled={createForm.processing}
                                    className="btn-primary flex-1"
                                >
                                    {createForm.processing ? 'Creating...' : 'Create Ticket'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </OnboardingLayout>
    );
}
