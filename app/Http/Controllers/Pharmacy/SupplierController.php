<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use App\Http\Requests\Supplier\StoreSupplierRequest;
use App\Http\Requests\Supplier\UpdateSupplierRequest;
use App\Models\Pharmacy\Supplier;
use App\Helpers\Idempotency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::query();

        // Search
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $safeSearch = '%' . addcslashes($searchTerm, '%_\\') . '%';
                $q->where('name',    'LIKE', $safeSearch)
                  ->orWhere('phone',  'LIKE', $safeSearch)
                  ->orWhere('email',  'LIKE', $safeSearch)
                  ->orWhere('country','LIKE', $safeSearch);
            });
        }

        // Archived
        if ($request->input('archived') == 'true') {
            $query->onlyTrashed()->orderBy('deleted_at', 'desc');
        } else {
            $query->latest();
        }

        $items         = $query->paginate(10)->onEachSide(1)->withQueryString();
        $archivedCount = Supplier::onlyTrashed()->count();
        $totalCount    = Supplier::count();

        return view('pharmacy.suppliers.index', compact('items', 'archivedCount', 'totalCount'));
    }

    public function create()
    {
        return view('pharmacy.suppliers.create');
    }

    public function store(StoreSupplierRequest $request)
    {
        $key = $request->input('idempotency_key');
        if (Idempotency::check($key)) {
            return redirect()->route('pharmacy.suppliers.index');
        }
        Supplier::create($request->validated());
        return redirect()->route('pharmacy.suppliers.index')->with('success', 'Supplier created successfully!');
    }

    public function show(Request $request, string $id)
    {
        $item = Supplier::findOrFail($id);
        $activeTab = strtolower(trim($request->input('tab', 'batches'))) ?: 'batches';

        $counts = [
            'batches'   => $item->pharmacyBatches()->count(),
            'warehouse' => $item->warehouseItems()->count(),
        ];

        $batches = $activeTab === 'batches'
            ? $item->pharmacyBatches()
                ->with('pharmacyItem')
                ->when($request->input('search'), function ($q, $search) {
                    $safe = '%' . addcslashes($search, '%_\\') . '%';
                    $q->where('batch_number', 'LIKE', $safe)
                      ->orWhereHas('pharmacyItem', fn($s) => $s->where('commercial_name', 'LIKE', $safe));
                })
                ->latest()
                ->paginate(8, ['*'], 'batches_page')
                ->withQueryString()
            : new \Illuminate\Pagination\LengthAwarePaginator([], 0, 8);

        $warehouseItems = $activeTab === 'warehouse'
            ? $item->warehouseItems()
                ->when($request->input('search'), function ($q, $search) {
                    $safe = '%' . addcslashes($search, '%_\\') . '%';
                    $q->where('name', 'LIKE', $safe)
                      ->orWhere('company_name', 'LIKE', $safe);
                })
                ->latest()
                ->paginate(8, ['*'], 'warehouse_page')
                ->withQueryString()
            : new \Illuminate\Pagination\LengthAwarePaginator([], 0, 8);

        return view('pharmacy.suppliers.show', compact('item', 'activeTab', 'counts', 'batches', 'warehouseItems'));
    }

    public function edit(string $id)
    {
        $item = Supplier::findOrFail($id);
        return view('pharmacy.suppliers.edit', compact('item'));
    }

    public function update(UpdateSupplierRequest $request, string $id)
    {
        $key = $request->input('idempotency_key');
        if (Idempotency::check($key)) {
            return redirect()->route('pharmacy.suppliers.index');
        }
        Supplier::findOrFail($id)->update($request->validated());
        return redirect()->route('pharmacy.suppliers.show', $id)->with('success', 'Supplier updated successfully!');
    }

    public function destroy(string $id)
    {
        $item = Supplier::withTrashed()->findOrFail($id);

        if ($item->hasProtectedRelations()) {
            return back()->with('error', 'Cannot archive this supplier because it has associated batches or warehouse items.');
        }

        $item->delete();
        $previousUrl = URL::previous();
        $showRoute   = route('pharmacy.suppliers.show', $item->id);
        if (str_contains($previousUrl, $showRoute)) {
            return redirect()->route('pharmacy.suppliers.index')->with('success', 'Supplier archived successfully!');
        }
        return redirect()->back()->with('success', 'Supplier archived successfully!');
    }

    public function restore(string $id)
    {
        Supplier::withTrashed()->findOrFail($id)->restore();
        return redirect()->route('pharmacy.suppliers.index', ['archived' => 'true'])->with('success', 'Supplier restored successfully!');
    }
}
