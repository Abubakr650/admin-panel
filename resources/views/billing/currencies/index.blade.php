<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-theme-from/10 text-theme-from shrink-0">
                    <x-icon name="currency-dollar" class="w-5 h-5" />
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold">Billing</p>
                    <h1 class="text-xl font-bold text-gray-800">Currencies</h1>
                </div>
            </div>
            <a href="{{ route('currencies.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-theme-from to-theme-to text-white text-sm font-medium rounded-xl hover:opacity-90 transition shadow-md">
                <x-icon name="plus" class="w-4 h-4" />
                New Currency
            </a>
        </div>
    </x-slot>

    <div class="p-6">
        {{-- Currency Converter Tool --}}
        <div class="mb-6 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <x-icon name="currency-dollar" class="w-5 h-5 text-theme-from" />
                Currency Converter
            </h3>
            <div x-data="{
                amount: 1,
                fromCurrency: '{{ $allCurrencies->first()->id ?? '' }}',
                toCurrency: '{{ $allCurrencies->skip(1)->first()->id ?? ($allCurrencies->first()->id ?? '') }}',
                result: null,
                loading: false,
                error: null,
                convert() {
                    if(!this.amount || !this.fromCurrency || !this.toCurrency) return;
                    this.loading = true;
                    this.result = null;
                    this.error = null;
                    fetch('{{ route('currencies.convert') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            amount: this.amount,
                            from_currency_id: this.fromCurrency,
                            to_currency_id: this.toCurrency
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.success) {
                            this.result = data.converted_amount;
                        } else {
                            this.error = data.message;
                        }
                    })
                    .catch(err => {
                        this.error = 'Failed to fetch exchange rate.';
                    })
                    .finally(() => {
                        this.loading = false;
                    });
                }
            }" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-1.5">Amount</label>
                    <input type="number" step="0.01" x-model="amount" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-theme-from focus:ring-2 focus:ring-theme-from/40 transition">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-1.5">From</label>
                    <select x-model="fromCurrency" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-theme-from focus:ring-2 focus:ring-theme-from/40 bg-gray-50 transition">
                        @foreach($allCurrencies as $c)
                            <option value="{{ $c->id }}">{{ $c->code }} - {{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-1.5">To</label>
                    <select x-model="toCurrency" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-theme-from focus:ring-2 focus:ring-theme-from/40 bg-gray-50 transition">
                        @foreach($allCurrencies as $c)
                            <option value="{{ $c->id }}">{{ $c->code }} - {{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button @click="convert" class="w-full h-[46px] bg-gradient-to-r from-theme-from to-theme-to hover:opacity-90 text-white font-bold rounded-xl flex items-center justify-center gap-2 transition-all shadow-md">
                        <span x-show="!loading">Convert</span>
                        <svg x-show="loading" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                    </button>
                </div>
                
                <div class="col-span-1 md:col-span-4" x-show="result !== null || error !== null" x-transition x-cloak>
                    <div x-show="result !== null" class="mt-2 p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-8 h-8 rounded-full bg-emerald-100 text-emerald-600">
                                <x-icon name="check-circle" class="w-5 h-5" />
                            </div>
                            <div>
                                <p class="text-sm text-emerald-700 font-medium">Conversion Result</p>
                                <p class="text-2xl font-black text-emerald-800" x-text="new Intl.NumberFormat('en-US', { style: 'decimal', maximumFractionDigits: 2 }).format(result)"></p>
                            </div>
                        </div>
                    </div>
                    <div x-show="error !== null" class="mt-2 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center gap-2 text-sm font-medium">
                        <x-icon name="x-circle" class="w-5 h-5 text-red-500 shrink-0" />
                        <span x-text="error"></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">#</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Code</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($currencies as $currency)
                        <tr class="hover:bg-gray-50/50 transition duration-150">
                            <td class="px-6 py-4 text-sm text-gray-400">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4 text-sm font-bold text-gray-800">{{ $currency->name }}</td>
                            <td class="px-6 py-4 text-sm">
                                <span class="inline-flex px-2.5 py-1 bg-theme-from/10 text-theme-from rounded-lg font-mono text-xs font-bold">{{ $currency->code }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('currencies.show', $currency) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-sky-50 text-sky-600 hover:bg-sky-100 transition" title="View">
                                        <x-icon name="eye" class="w-4 h-4" />
                                    </a>
                                    <a href="{{ route('currencies.edit', $currency) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 transition" title="Edit">
                                        <x-icon name="edit" class="w-4 h-4" />
                                    </a>
                                    <form action="{{ route('currencies.destroy', $currency) }}" method="POST" onsubmit="return confirm('Delete this currency?')" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition" title="Delete">
                                            <x-icon name="trash" class="w-4 h-4" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                        <x-icon name="currency-dollar" class="w-8 h-8 text-gray-400" />
                                    </div>
                                    <p class="text-gray-500 font-medium">No currencies found.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($currencies->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                    {{ $currencies->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
