<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\SupportTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class OnboardingController extends Controller
{
    /**
     * Show the onboarding dashboard.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('Onboarding/Dashboard', [
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'email_verified' => $user->isEmailVerified(),
                'company_name' => $user->company_name,
                'status' => $user->status,
                'kyc_submitted' => $user->hasCompletedKyc(),
                'onboarding_progress' => $user->getOnboardingProgress(),
            ],
            'documents' => $user->documents()->latest()->get()->map(fn ($doc) => $this->formatDocument($doc)),
            'tickets' => $user->supportTickets()->latest()->limit(5)->get(),
        ]);
    }

    /**
     * Show the company profile form.
     */
    public function profile(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('Onboarding/Profile', [
            'user' => $user->only([
                'name',
                'email',
                'phone',
                'company_name',
                'company_website',
                'company_phone',
                'company_address',
                'company_city',
                'company_state',
                'company_zip',
                'industry',
                'monthly_call_volume',
                'use_case',
                'current_provider',
                'uses_stir_shaken',
            ]),
        ]);
    }

    /**
     * Update the company profile.
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'phone' => 'nullable|string|max:20',
            'company_name' => 'required|string|max:255',
            'company_website' => 'nullable|url|max:255',
            'company_phone' => 'nullable|string|max:20',
            'company_address' => 'nullable|string|max:255',
            'company_city' => 'nullable|string|max:100',
            'company_state' => 'nullable|string|max:100',
            'company_zip' => 'nullable|string|max:20',
            'industry' => 'nullable|string|max:100',
            'monthly_call_volume' => 'nullable|string|max:50',
            'use_case' => 'nullable|string|max:100',
            'current_provider' => 'nullable|string|max:100',
            'uses_stir_shaken' => 'nullable|string|in:yes,no,not_sure',
        ]);

        $request->user()->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Show the documents page.
     */
    public function documents(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('Onboarding/Documents', [
            'documents' => $user->documents()->latest()->get()->map(fn ($doc) => $this->formatDocument($doc)),
            'documentTypes' => Document::getAllTypes(),
        ]);
    }

    /**
     * Format document for frontend with metadata.
     */
    protected function formatDocument(Document $document): array
    {
        $metadata = $document->getMetadata();

        return [
            'id' => $document->id,
            'type' => $document->type,
            'type_label' => Document::getTypeLabel($document->type),
            'name' => $document->name,
            'original_filename' => $document->original_filename,
            'status' => $document->status,
            'notes' => $document->notes,
            // File metadata
            'mime_type' => $metadata['mime_type'],
            'size' => $metadata['size'],
            'size_formatted' => $metadata['size_formatted'],
            'extension' => $metadata['extension'],
            'is_image' => $metadata['is_image'],
            'is_pdf' => $metadata['is_pdf'],
            // URLs
            'thumbnail_url' => $document->getThumbnailUrl(),
            'preview_url' => $document->getPreviewUrl(),
            'download_url' => $document->getDownloadUrl(),
            // Timestamps
            'uploaded_at' => $document->created_at?->toISOString(),
            'uploaded_at_formatted' => $document->created_at?->format('M j, Y g:i A'),
            'modified_at' => $document->updated_at?->toISOString(),
            'modified_at_formatted' => $document->updated_at?->format('M j, Y g:i A'),
            'last_viewed_at' => $document->last_viewed_at?->toISOString(),
            'last_viewed_at_formatted' => $document->last_viewed_at?->format('M j, Y g:i A'),
            'reviewed_at' => $document->reviewed_at?->toISOString(),
            'reviewed_at_formatted' => $document->reviewed_at?->format('M j, Y g:i A'),
        ];
    }

    /**
     * Upload a document with type-specific validation.
     */
    public function uploadDocument(Request $request): RedirectResponse
    {
        $validTypes = implode(',', [
            Document::TYPE_BUSINESS_LICENSE,
            Document::TYPE_TAX_ID,
            Document::TYPE_LOA,
            Document::TYPE_DRIVERS_LICENSE,
            Document::TYPE_GOVERNMENT_ID,
            Document::TYPE_ARTICLES_OF_INCORPORATION,
            Document::TYPE_UTILITY_BILL,
            Document::TYPE_W9,
            Document::TYPE_KYC,
            Document::TYPE_OTHER,
        ]);

        // First validate type and name
        $request->validate([
            'type' => "required|string|in:{$validTypes}",
            'name' => 'required|string|max:255',
        ]);

        $type = $request->input('type');

        // Get type-specific allowed extensions
        $extensions = Document::getAllowedExtensionsForType($type);
        $mimes = implode(',', $extensions);

        // Validate file with type-specific rules
        $request->validate([
            'file' => "required|file|mimes:{$mimes}|max:10240",
        ], [
            'file.mimes' => "This document type only accepts: " . strtoupper(implode(', ', $extensions)) . " files.",
        ]);

        $file = $request->file('file');

        // Create document record
        $document = $request->user()->documents()->create([
            'type' => $type,
            'name' => $request->input('name'),
            'original_filename' => $file->getClientOriginalName(),
            'path' => '', // Will be updated by media library
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'status' => 'pending',
        ]);

        // Add file to media library
        $document->addMedia($file)
            ->usingFileName($document->id . '_' . time() . '.' . $file->getClientOriginalExtension())
            ->toMediaCollection('document');

        // Update path with media library path
        $media = $document->getFirstMedia('document');
        if ($media) {
            $document->update(['path' => $media->getPath()]);
        }

        return back()->with('success', 'Document uploaded successfully.');
    }

    /**
     * View/preview a document (marks as viewed).
     */
    public function viewDocument(Request $request, Document $document): RedirectResponse
    {
        if ($document->user_id !== $request->user()->id) {
            abort(403);
        }

        $document->markAsViewed();

        $url = $document->getPreviewUrl() ?? $document->getDownloadUrl();

        return redirect($url);
    }

    /**
     * Delete a document.
     */
    public function deleteDocument(Request $request, Document $document): RedirectResponse
    {
        if ($document->user_id !== $request->user()->id) {
            abort(403);
        }

        if ($document->status !== 'pending') {
            return back()->with('error', 'Cannot delete a reviewed document.');
        }

        // Delete media files
        $document->clearMediaCollection('document');

        // Delete legacy file if exists
        if ($document->path && Storage::disk('private')->exists($document->path)) {
            Storage::disk('private')->delete($document->path);
        }

        $document->delete();

        return back()->with('success', 'Document deleted successfully.');
    }

    /**
     * Submit KYC for review.
     */
    public function submitKyc(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Check if user has uploaded required documents
        $hasKycDocs = $user->documents()
            ->whereIn('type', ['business_license', 'tax_id', 'kyc', 'drivers_license', 'government_id'])
            ->exists();

        if (!$hasKycDocs) {
            return back()->with('error', 'Please upload at least one verification document.');
        }

        $user->update([
            'kyc_submitted_at' => now(),
            'status' => 'verified',
        ]);

        return back()->with('success', 'KYC submitted for review. We\'ll notify you once approved.');
    }

    /**
     * Show the support tickets page.
     */
    public function tickets(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('Onboarding/Tickets', [
            'tickets' => $user->supportTickets()
                ->with('replies.user:id,name')
                ->latest()
                ->get(),
            'categories' => [
                ['value' => 'general', 'label' => 'General Inquiry'],
                ['value' => 'billing', 'label' => 'Billing & Payments'],
                ['value' => 'technical', 'label' => 'Technical Support'],
                ['value' => 'kyc', 'label' => 'KYC / Verification'],
                ['value' => 'feature', 'label' => 'Feature Request'],
            ],
        ]);
    }

    /**
     * Create a support ticket.
     */
    public function createTicket(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'category' => 'required|string|in:general,billing,technical,kyc,feature',
            'priority' => 'nullable|string|in:low,medium,high',
        ]);

        $request->user()->supportTickets()->create([
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'priority' => $validated['priority'] ?? 'medium',
        ]);

        return back()->with('success', 'Support ticket created. We\'ll respond within 24 hours.');
    }

    /**
     * Reply to a support ticket.
     */
    public function replyToTicket(Request $request, SupportTicket $ticket): RedirectResponse
    {
        if ($ticket->user_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $ticket->replies()->create([
            'user_id' => $request->user()->id,
            'message' => $validated['message'],
        ]);

        // Reopen ticket if it was closed
        if ($ticket->isClosed()) {
            $ticket->update(['status' => 'open']);
        }

        return back()->with('success', 'Reply sent.');
    }

    /**
     * Show the settings page.
     */
    public function settings(Request $request): Response
    {
        return Inertia::render('Onboarding/Settings', [
            'user' => $request->user()->only(['name', 'email']),
        ]);
    }

    /**
     * Show the documentation page.
     */
    public function documentation(): Response
    {
        return Inertia::render('Onboarding/Documentation');
    }
}
