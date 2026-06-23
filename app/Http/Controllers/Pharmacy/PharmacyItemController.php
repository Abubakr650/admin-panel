<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use App\Http\Requests\PharmacyItem\StorePharmacyItemRequest;
use App\Http\Requests\PharmacyItem\UpdatePharmacyItemRequest;
use App\Models\Pharmacy\PharmacyItem;
use App\Helpers\Idempotency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class PharmacyItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PharmacyItem::query();

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('commercial_name', 'LIKE', '%' . addcslashes($searchTerm, '%_\\') . '%')
                  ->orWhere('scientific_name', 'LIKE', '%' . addcslashes($searchTerm, '%_\\') . '%')
                  ->orWhere('company_name',    'LIKE', '%' . addcslashes($searchTerm, '%_\\') . '%')
                  ->orWhere('category',        'LIKE', '%' . addcslashes($searchTerm, '%_\\') . '%')
                  ->orWhere('qr_code',         'LIKE', '%' . addcslashes($searchTerm, '%_\\') . '%');
            });
        }

        // Form filter
        if ($request->filled('form')) {
            $query->where('form', $request->input('form'));
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        // Archived filter
        if ($request->input('archived') == 'true') {
            $query->onlyTrashed()->orderBy('deleted_at', 'desc');
        } else {
            $query->latest();
        }

        $items          = $query->paginate(10)->onEachSide(1)->withQueryString();
        $archivedCount  = PharmacyItem::onlyTrashed()->count();
        $totalCount     = PharmacyItem::count();

        // Distinct values for filter dropdowns
        $formOptions     = PharmacyItem::whereNotNull('form')->distinct()->orderBy('form')->pluck('form');
        $categoryOptions = PharmacyItem::whereNotNull('category')->distinct()->orderBy('category')->pluck('category');

        return view('pharmacy.items.index', compact('items', 'archivedCount', 'totalCount', 'formOptions', 'categoryOptions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pharmacy.items.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePharmacyItemRequest $request)
    {
        // Idempotency check
        $key = $request->input('idempotency_key');
        if (Idempotency::check($key)) {
            return redirect()->route('pharmacy.items.index');
        }

        PharmacyItem::create($request->validated());

        return redirect()->route('pharmacy.items.index')->with('success', 'Item created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $item = PharmacyItem::findOrFail($id);
        $activeTab = strtolower(trim($request->input('tab', 'batches'))) ?: 'batches';

        $counts = [
            'batches' => $item->pharmacyBatches()->count(),
        ];

        // Fetch paginated batches for the active tab with optional search
        $batches = $item->pharmacyBatches()
            ->with('supplier')
            ->when($request->input('search'), function ($q, $search) {
                $safe = '%' . addcslashes($search, '%_\\') . '%';
                $q->where('batch_number', 'LIKE', $safe)
                  ->orWhereHas('supplier', fn($s) => $s->where('name', 'LIKE', $safe));
            })
            ->latest()
            ->paginate(8, ['*'], 'batches_page')
            ->withQueryString();

        return view('pharmacy.items.show', compact('item', 'activeTab', 'counts', 'batches'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $item = PharmacyItem::findOrFail($id);
        return view('pharmacy.items.edit', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePharmacyItemRequest $request, string $id)
    {
        // Idempotency check
        $key = $request->input('idempotency_key');
        if (Idempotency::check($key)) {
            return redirect()->route('pharmacy.items.index');
        }

        PharmacyItem::findOrFail($id)->update($request->validated());

        return redirect()->route('pharmacy.items.show', $id)->with('success', 'Item updated successfully!');
    }

    /**
     * Archive (soft delete) the specified resource.
     */
    public function destroy(string $id)
    {
        $item = PharmacyItem::withTrashed()->findOrFail($id);

        if ($item->hasProtectedRelations()) {
            return back()->with('error', 'Cannot archive this item because it has associated batches.');
        }

        $item->delete();

        $previousUrl = URL::previous();
        $showRoute   = route('pharmacy.items.show', $item->id);
        if (str_contains($previousUrl, $showRoute)) {
            return redirect()->route('pharmacy.items.index')->with('success', 'Item archived successfully!');
        }

        return redirect()->back()->with('success', 'Item archived successfully!');
    }

    /**
     * Restore the specified resource from archive.
     */
    public function restore(string $id)
    {
        PharmacyItem::withTrashed()->findOrFail($id)->restore();

        return redirect()->route('pharmacy.items.index', ['archived' => 'true'])
            ->with('success', 'Item restored successfully!');
    }
}
