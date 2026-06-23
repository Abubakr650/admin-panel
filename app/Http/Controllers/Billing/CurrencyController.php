<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $currencies = \App\Models\Billing\Currency::paginate(10);
        $allCurrencies = \App\Models\Billing\Currency::all();
        return view('billing.currencies.index', compact('currencies', 'allCurrencies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $currencies = \App\Models\Billing\Currency::all();
        return view('billing.currencies.create', compact('currencies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'code'             => 'required|string|max:10|unique:currencies,code',
            'base_currency_id' => 'nullable|exists:currencies,id',
            'exchange_rate'    => 'nullable|numeric|min:0.000001',
        ]);
        
        $currency = \App\Models\Billing\Currency::create([
            'name' => $validated['name'],
            'code' => $validated['code'],
        ]);
        
        if (!empty($validated['base_currency_id']) && !empty($validated['exchange_rate'])) {
            \App\Models\Billing\ExchangeRate::create([
                'from_currency_id' => $validated['base_currency_id'],
                'to_currency_id'   => $currency->id,
                'rate'             => $validated['exchange_rate']
            ]);
            \App\Models\Billing\ExchangeRate::create([
                'from_currency_id' => $currency->id,
                'to_currency_id'   => $validated['base_currency_id'],
                'rate'             => 1 / $validated['exchange_rate']
            ]);
        }
        
        return redirect()->route('currencies.index')->with('success', 'Currency created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $currency = \App\Models\Billing\Currency::findOrFail($id);
        return view('billing.currencies.show', compact('currency'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $currency = \App\Models\Billing\Currency::findOrFail($id);
        $currencies = \App\Models\Billing\Currency::where('id', '!=', $id)->get();
        $latestRate = \App\Models\Billing\ExchangeRate::where('to_currency_id', $id)->latest()->first();

        return view('billing.currencies.edit', compact('currency', 'currencies', 'latestRate'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $currency = \App\Models\Billing\Currency::findOrFail($id);
        
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'code'             => 'required|string|max:10|unique:currencies,code,'.$currency->id,
            'base_currency_id' => 'nullable|exists:currencies,id',
            'exchange_rate'    => 'nullable|numeric|min:0.000001',
        ]);
        
        $currency->update([
            'name' => $validated['name'],
            'code' => $validated['code'],
        ]);
        
        if (!empty($validated['base_currency_id']) && !empty($validated['exchange_rate'])) {
            \App\Models\Billing\ExchangeRate::create([
                'from_currency_id' => $validated['base_currency_id'],
                'to_currency_id'   => $currency->id,
                'rate'             => $validated['exchange_rate']
            ]);
            \App\Models\Billing\ExchangeRate::create([
                'from_currency_id' => $currency->id,
                'to_currency_id'   => $validated['base_currency_id'],
                'rate'             => 1 / $validated['exchange_rate']
            ]);
        }
        
        return redirect()->route('currencies.index')->with('success', 'Currency updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $currency = \App\Models\Billing\Currency::findOrFail($id);
        $currency->delete();
        
        return redirect()->route('currencies.index')->with('success', 'Currency deleted successfully.');
    }
    
    /**
     * Convert an amount between currencies.
     */
    public function convert(Request $request)
    {
        $request->validate([
            'amount'           => 'required|numeric|min:0',
            'from_currency_id' => 'required|exists:currencies,id',
            'to_currency_id'   => 'required|exists:currencies,id',
        ]);

        try {
            $converted = \App\Services\CurrencyService::convert(
                $request->amount,
                $request->from_currency_id,
                $request->to_currency_id
            );
            return response()->json([
                'success' => true,
                'converted_amount' => $converted
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
