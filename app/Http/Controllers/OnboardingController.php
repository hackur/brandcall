<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\SupportTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
            'documents' => $user->documents()->latest()->get(),
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
            'documents' => $user->documents()->latest()->get(),
            'documentTypes' => Document::getAllTypes(),
        ]);
    }

    /**
     * Upload a document.
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

        $validated = $request->validate([
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max
            'type' => "required|string|in:{$validTypes}",
            'name' => 'required|string|max:255',
        ]);

        $file = $request->file('file');
        $path = $file->store('documents/' . $request->user()->id, 'private');

        $request->user()->documents()->create([
            'type' => $validated['type'],
            'name' => $validated['name'],
            'original_filename' => $file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'status' => 'pending',
        ]);

        return back()->with('success', 'Document uploaded successfully.');
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

        Storage::disk('private')->delete($document->path);
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
            ->whereIn('type', ['business_license', 'tax_id', 'kyc'])
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
