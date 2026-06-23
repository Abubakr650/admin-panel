<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use App\Http\Requests\PharmacyBatch\StorePharmacyBatchRequest;
use App\Http\Requests\PharmacyBatch\UpdatePharmacyBatchRequest;
use App\Models\Pharmacy\PharmacyBatch;
use App\Models\Pharmacy\PharmacyItem;
use App\Models\Pharmacy\Supplier;
use App\Helpers\Idempotency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;

class PharmacyBatchController extends Controller
{
    public function index(Request $request)
    {
        $query = PharmacyBatch::with(['pharmacyItem', 'supplier']);

        // Search
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $safe = '%' . addcslashes($searchTerm, '%_\\') . '%';
                $q->where('batch_number', 'LIKE', $safe)
                  ->orWhereHas('pharmacyItem', fn($q2) => 
                      $q2->where('commercial_name', 'LIKE', $safe)
                         ->orWhere('qr_code', 'LIKE', $safe)
                  )
                  ->orWhereHas('supplier',     fn($q2) => $q2->where('name', 'LIKE', $safe));
            });
        }

        // Item filter (from item show page)
        if ($request->filled('item_id')) {
            $query->where('pharmacy_item_id', $request->input('item_id'));
        }

        // Expired filter — compare expiry_date against today
        if ($request->filled('expired') && in_array($request->input('expired'), ['expired', 'not_expired'])) {
            $today = Carbon::today();
            if ($request->input('expired') === 'expired') {
                $query->whereNotNull('expiry_date')
                      ->where('expiry_date', '<', $today);
            } else {
                $query->where(function ($q) use ($today) {
                    $q->whereNull('expiry_date')
                      ->orWhere('expiry_date', '>=', $today);
                });
            }
        }

        // Archived
        if ($request->input('archived') === 'true') {
            $query->onlyTrashed()->orderBy('deleted_at', 'desc');
        } else {
            $query->latest();
        }

        $items         = $query->paginate(10)->onEachSide(1)->withQueryString();
        $archivedCount = PharmacyBatch::onlyTrashed()->count();
        $totalCount    = PharmacyBatch::count();
        $expiredCount  = PharmacyBatch::where('expiry_date', '<', Carbon::today())->count();

        return view('pharmacy.batches.index', compact('items', 'expiredCount', 'archivedCount', 'totalCount'));
    }

    public function create()
    {
        $pharmacyItems = PharmacyItem::orderBy('commercial_name')->get();
        $suppliers     = Supplier::orderBy('name')->get();
        return view('pharmacy.batches.create', compact('pharmacyItems', 'suppliers'));
    }

    public function store(StorePharmacyBatchRequest $request)
    {
        $key = $request->input('idempotency_key');
        if (Idempotency::check($key)) {
            return redirect()->route('pharmacy.batches.index');
        }
        $data = $request->validated();
        $data['remaining_quantity'] = $data['quantity'];
        PharmacyBatch::create($data);
        return redirect()->route('pharmacy.batches.index')->with('success', 'Batch created successfully!');
    }

    public function show(string $id)
    {
        $item = PharmacyBatch::with(['pharmacyItem', 'supplier'])->findOrFail($id);
        return view('pharmacy.batches.show', compact('item'));
    }

    public function edit(string $id)
    {
        $item          = PharmacyBatch::findOrFail($id);
        $pharmacyItems = PharmacyItem::orderBy('commercial_name')->get();
        $suppliers     = Supplier::orderBy('name')->get();
        return view('pharmacy.batches.edit', compact('item', 'pharmacyItems', 'suppliers'));
    }

    public function update(UpdatePharmacyBatchRequest $request, string $id)
    {
        $key = $request->input('idempotency_key');
        if (Idempotency::check($key)) {
            return redirect()->route('pharmacy.batches.index');
        }
        PharmacyBatch::findOrFail($id)->update($request->validated());
        return redirect()->route('pharmacy.batches.show', $id)->with('success', 'Batch updated successfully!');
    }

    public function destroy(string $id)
    {
        $item = PharmacyBatch::withTrashed()->findOrFail($id);
        $item->delete();
        $previousUrl = URL::previous();
        $showRoute   = route('pharmacy.batches.show', $item->id);
        if (str_contains($previousUrl, $showRoute)) {
            return redirect()->route('pharmacy.batches.index')->with('success', 'Batch archived successfully!');
        }
        return redirect()->back()->with('success', 'Batch archived successfully!');
    }

    public function restore(string $id)
    {
        PharmacyBatch::withTrashed()->findOrFail($id)->restore();
        return redirect()->route('pharmacy.batches.index', ['archived' => 'true'])->with('success', 'Batch restored successfully!');
    }
}
