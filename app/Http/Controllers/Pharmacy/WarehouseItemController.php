<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use App\Models\Pharmacy\WarehouseItem;
use App\Models\Pharmacy\Supplier;
use App\Http\Requests\Pharmacy\StoreWarehouseItemRequest;
use App\Http\Requests\Pharmacy\UpdateWarehouseItemRequest;
use App\Helpers\Idempotency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;

class WarehouseItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = WarehouseItem::with('supplier');

        // Search
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function($q) use ($searchTerm) {
                $safeSearch = '%' . addcslashes($searchTerm, '%_\\') . '%';
                $q->where('name', 'LIKE', $safeSearch)
                  ->orWhere('company_name', 'LIKE', $safeSearch)
                  ->orWhere('qr_code', 'LIKE', $safeSearch);
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        // Supplier filter (from supplier show page)
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->input('supplier_id'));
        }

        // Archived filter
        if ($request->input('archived') == 'true') {
            $query->onlyTrashed()->orderBy('deleted_at', 'desc');
        } else {
            $query->latest();
        }

        $items = $query->paginate(12)->onEachSide(1)->withQueryString();
        
        // Stats
        $archivedCount = WarehouseItem::onlyTrashed()->count();
        $totalCount    = WarehouseItem::count();
        $expiredCount  = WarehouseItem::where('expiry_date', '<', Carbon::today())->count();

        // Distinct values for filter dropdowns
        $categoryOptions = WarehouseItem::whereNotNull('category')->distinct()->orderBy('category')->pluck('category');

        return view('pharmacy.warehouse.index', compact('items', 'archivedCount', 'totalCount', 'expiredCount', 'categoryOptions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        return view('pharmacy.warehouse.create', compact('suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWarehouseItemRequest $request)
    {
        $key = $request->input('idempotency_key');
        if (Idempotency::check($key)) {
            return redirect()->route('pharmacy.warehouse.index');
        }

        WarehouseItem::create($request->validated());

        return redirect()->route('pharmacy.warehouse.index')->with('success', 'Item added to warehouse successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $item = WarehouseItem::with('supplier')->findOrFail($id);
        return view('pharmacy.warehouse.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $item = WarehouseItem::findOrFail($id);
        $suppliers = Supplier::orderBy('name')->get();
        return view('pharmacy.warehouse.edit', compact('item', 'suppliers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWarehouseItemRequest $request, string $id)
    {
        $key = $request->input('idempotency_key');
        if (Idempotency::check($key)) {
            return redirect()->route('pharmacy.warehouse.index');
        }

        WarehouseItem::findOrFail($id)->update($request->validated());

        return redirect()->route('pharmacy.warehouse.show', $id)->with('success', 'Item updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = WarehouseItem::withTrashed()->findOrFail($id);
        $item->delete();

        $previousUrl = URL::previous();
        $showRoute = route('pharmacy.warehouse.show', $item->id);

        if (str_contains($previousUrl, $showRoute)) {
            return redirect()->route('pharmacy.warehouse.index')->with('success', 'Item archived successfully.');
        }

        return redirect()->back()->with('success', 'Item archived successfully.');
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore(string $id)
    {
        WarehouseItem::withTrashed()->findOrFail($id)->restore();

        return redirect()->route('pharmacy.warehouse.index', ['archived' => 'true'])->with('success', 'Item restored successfully.');
    }
}
