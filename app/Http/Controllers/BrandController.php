<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $tenant = $request->user()->tenant;
        app()->instance('current_tenant', $tenant);

        $brands = Brand::with('phoneNumbers')
            ->latest()
            ->paginate(10);

        return Inertia::render('Brands/Index', [
            'brands' => $brands,
        ]);
    }

    public function create()
    {
        return Inertia::render('Brands/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'display_name' => 'nullable|string|max:32',
            'call_reason' => 'nullable|string|max:100',
            'logo' => 'nullable|image|max:2048',
        ]);

        $tenant = $request->user()->tenant;
        app()->instance('current_tenant', $tenant);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('brands/logos', 'public');
        }

        $brand = Brand::create([
            'tenant_id' => $tenant->id,
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'display_name' => $validated['display_name'] ?? Str::limit($validated['name'], 32, ''),
            'call_reason' => $validated['call_reason'],
            'logo_path' => $logoPath,
            'status' => 'draft',
        ]);

        return redirect()->route('brands.show', $brand)
            ->with('success', 'Brand created successfully!');
    }

    public function show(Request $request, Brand $brand)
    {
        $tenant = $request->user()->tenant;
        app()->instance('current_tenant', $tenant);

        $brand->load('phoneNumbers');

        return Inertia::render('Brands/Show', [
            'brand' => $brand,
        ]);
    }

    public function edit(Request $request, Brand $brand)
    {
        $tenant = $request->user()->tenant;
        app()->instance('current_tenant', $tenant);

        return Inertia::render('Brands/Edit', [
            'brand' => $brand,
        ]);
    }

    public function update(Request $request, Brand $brand)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'display_name' => 'nullable|string|max:32',
            'call_reason' => 'nullable|string|max:100',
            'logo' => 'nullable|image|max:2048',
        ]);

        $tenant = $request->user()->tenant;
        app()->instance('current_tenant', $tenant);

        if ($request->hasFile('logo')) {
            $validated['logo_path'] = $request->file('logo')->store('brands/logos', 'public');
        }

        $brand->update($validated);

        return redirect()->route('brands.show', $brand)
            ->with('success', 'Brand updated successfully!');
    }

    public function destroy(Request $request, Brand $brand)
    {
        $tenant = $request->user()->tenant;
        app()->instance('current_tenant', $tenant);

        $brand->delete();

        return redirect()->route('brands.index')
            ->with('success', 'Brand deleted successfully!');
    }
}
