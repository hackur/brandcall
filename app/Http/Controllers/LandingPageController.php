<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\LandingPage;
use App\Models\LandingPageLead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LandingPageController extends Controller
{
    /**
     * Display the landing page.
     */
    public function show(string $slug): Response
    {
        $page = LandingPage::findBySlug($slug);

        if (! $page) {
            abort(404);
        }

        // Record view
        $page->recordView();

        return Inertia::render('LandingPage/Show', [
            'page' => [
                'id' => $page->id,
                'slug' => $page->slug,
                'name' => $page->name,
                'headline' => $page->headline,
                'subheadline' => $page->subheadline,
                'description' => $page->description,
                'cta_text' => $page->cta_text,
                'cta_url' => $page->cta_url,
                'hero_image' => $page->hero_image ? asset('storage/' . $page->hero_image) : null,
                'hero_video_url' => $page->hero_video_url,
                'features' => $page->features ?? [],
                'testimonials' => $page->testimonials ?? [],
                'show_pricing' => $page->show_pricing,
                'pricing_headline' => $page->pricing_headline,
                'show_contact_form' => $page->show_contact_form,
                'form_headline' => $page->form_headline,
                'form_fields' => $page->getFormFieldsWithDefaults(),
                'layout_preset' => $page->layout_preset,
                'color_scheme' => $page->color_scheme,
                'colors' => $page->colors,
                'logo_url' => $page->logo_url ? asset('storage/' . $page->logo_url) : null,
                'meta_title' => $page->meta_title ?? $page->headline,
                'meta_description' => $page->meta_description ?? $page->description,
                'og_image' => $page->og_image ? asset('storage/' . $page->og_image) : null,
            ],
        ]);
    }

    /**
     * Handle lead form submission.
     */
    public function submitLead(Request $request, string $slug): JsonResponse
    {
        $page = LandingPage::findBySlug($slug);

        if (! $page) {
            return response()->json(['error' => 'Page not found'], 404);
        }

        // Validate based on form fields
        $rules = ['email' => 'required|email'];

        foreach ($page->getFormFieldsWithDefaults() as $field) {
            if ($field['required'] ?? false) {
                $fieldName = $field['name'];
                if ($fieldName !== 'email') {
                    $rules[$fieldName] = 'required';
                }
            }
        }

        $validated = $request->validate($rules);

        // Create lead
        $lead = LandingPageLead::create([
            'landing_page_id' => $page->id,
            'name' => $request->input('name'),
            'email' => $validated['email'],
            'phone' => $request->input('phone'),
            'company' => $request->input('company'),
            'message' => $request->input('message'),
            'custom_fields' => $request->except(['name', 'email', 'phone', 'company', 'message']),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referrer' => $request->header('referer'),
            'utm_source' => $request->input('utm_source') ?? $page->utm_source,
            'utm_medium' => $request->input('utm_medium') ?? $page->utm_medium,
            'utm_campaign' => $request->input('utm_campaign') ?? $page->utm_campaign,
        ]);

        // Record conversion
        $page->recordConversion();

        return response()->json([
            'success' => true,
            'message' => 'Thank you! We\'ll be in touch soon.',
            'lead_id' => $lead->id,
        ]);
    }
}
