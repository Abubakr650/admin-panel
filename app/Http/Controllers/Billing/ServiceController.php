<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\Billing\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $isArchived = $request->input('archived') === 'true';
        $query = Service::query();

        if ($isArchived) {
            $query->onlyTrashed()->orderBy('deleted_at', 'desc');
        } else {
            if ($request->filled('search')) {
                $term = $request->input('search');
                $query->where(function ($q) use ($term) {
                    $q->where('name', 'LIKE', '%' . $term . '%')
                      ->orWhere('code', 'LIKE', '%' . $term . '%');
                });
            }
            $query->orderBy('name');
        }

        $services = $query->paginate(15)->onEachSide(1)->withQueryString();
        $archivedCount = Service::onlyTrashed()->count();

        return view('billing.services.index', compact('services', 'archivedCount'));
    }

    public function create()
    {
        return view('billing.services.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'code'             => 'nullable|string|max:50|unique:services,code',
            'default_price'    => 'required|numeric|min:0',
            'duration_minutes' => 'nullable|integer|min:0',
            'is_active'        => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        Service::create($validated);

        return redirect()->route('services.index')
            ->with('success', 'Service "' . $validated['name'] . '" created successfully!');
    }

    public function show(string $id)
    {
        $service = Service::withTrashed()->findOrFail($id);
        return view('billing.services.show', compact('service'));
    }

    public function edit(string $id)
    {
        $service = Service::findOrFail($id);
        return view('billing.services.edit', compact('service'));
    }

    public function update(Request $request, string $id)
    {
        $service = Service::findOrFail($id);

        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'code'             => 'nullable|string|max:50|unique:services,code,' . $service->id,
            'default_price'    => 'required|numeric|min:0',
            'duration_minutes' => 'nullable|integer|min:0',
            'is_active'        => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $service->update($validated);

        return redirect()->route('services.show', $service->id)
            ->with('success', 'Service updated successfully!');
    }

    public function destroy(string $id)
    {
        $service = Service::withTrashed()->findOrFail($id);
        $service->delete();

        $previousUrl = URL::previous();
        if (str_contains($previousUrl, route('services.show', $service->id))) {
            return redirect()->route('services.index')->with('success', 'Service archived!');
        }
        return redirect()->back()->with('success', 'Service archived!');
    }

    public function restore(string $id)
    {
        $service = Service::withTrashed()->findOrFail($id);
        $service->restore();
        return redirect()->route('services.index', ['archived' => 'true'])
            ->with('success', 'Service restored successfully!');
    }
}
