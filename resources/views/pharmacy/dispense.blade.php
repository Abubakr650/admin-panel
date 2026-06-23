<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full gap-4">
            <div class="flex items-center gap-3 flex-1 min-w-0">
                <div class="shrink-0">
                    <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold">Pharmacy</p>
                    <h1 class="text-lg font-bold text-gray-800 leading-tight">Dispense / Sales</h1>
                </div>
                <div class="h-8 w-px bg-gray-200 shrink-0"></div>
                <div class="w-64">
                    <x-search-bar
                        action="{{ route('pharmacy.dispense') }}"
                        placeholder="Search name, QR, batch…"
                        :extraParams="array_filter(['category' => request('category'), 'form' => request('form')])"
                    />
                </div>

                {{-- Category Filter --}}
                @php $activeCat = request()->input('category', ''); @endphp
                <div class="flex items-center gap-1 bg-gray-100 rounded-xl p-1 shrink-0">
                    @foreach(['' => 'All', 'medicine' => 'Medicine', 'supplement' => 'Supplement', 'cosmetic' => 'Cosmetic', 'other' => 'Other'] as $val => $label)
                        @php
                            $isActive = $activeCat === $val;
                            $href = route('pharmacy.dispense', array_filter([
                                'search'   => request('search'),
                                'category' => $val ?: null,
                                'form'     => request('form'),
                            ]));
                        @endphp
                        <a href="{{ $href }}"
                           class="px-3 py-1 rounded-lg text-xs font-semibold transition-all duration-150
                                  {{ $isActive ? 'bg-white text-gray-800 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <button x-on:click="$dispatch('open-modal', 'scan-qr-dispense')"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-all duration-200 shadow-sm">
                    <x-icon name="qr-code" class="w-4 h-4 text-theme-from" />
                    Scan Mode
                </button>
            </div>
        </div>
    </x-slot>

    {{-- Scan QR Modal --}}
    <x-scan-qr-modal
        name="scan-qr-dispense"
        mode="search"
        :action="route('pharmacy.dispense')"
        :extraParams="array_filter(['category' => request('category'), 'form' => request('form')])"
    />

    <div class="flex gap-6" x-data="dispenseApp()">
        {{-- LEFT: Items Table --}}
        <div class="flex-1 min-w-0 space-y-4">
            {{-- Form Filter --}}
            @php $activeForm = request()->input('form', ''); @endphp
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Form:</span>
                @foreach(['' => 'All', 'tablet' => '💊 Tablet', 'capsule' => '🔵 Capsule', 'syrup' => '🧴 Syrup', 'cream' => '🧴 Cream', 'injection' => '💉 Injection', 'drops' => '💧 Drops'] as $val => $label)
                    @php
                        $isActive = $activeForm === $val;
                        $href = route('pharmacy.dispense', array_filter([
                            'search'   => request('search'),
                            'category' => request('category'),
                            'form'     => $val ?: null,
                        ]));
                    @endphp
                    <a href="{{ $href }}"
                       class="px-3 py-1.5 rounded-lg text-xs font-semibold border transition-all duration-150
                              {{ $isActive
                                  ? 'bg-theme-from/5 text-theme-from border-theme-from/20 shadow-sm'
                                  : 'bg-white text-gray-500 border-gray-200 hover:border-theme-from/30 hover:text-theme-from' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            {{-- Stats --}}
            <div class="relative overflow-hidden rounded-2xl bg-white border border-gray-100 shadow-sm p-4 flex items-center gap-4">
                <div class="flex-shrink-0 flex items-center justify-center w-11 h-11 rounded-xl bg-theme-from/10">
                    <x-icon name="document" class="w-5 h-5 text-theme-from" />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Available Batches</p>
                    <p class="text-2xl font-extrabold text-gray-800">{{ $totalAvailable }}</p>
                </div>
                <div class="absolute -right-3 -bottom-3 w-16 h-16 rounded-full bg-theme-from/10 opacity-60"></div>
            </div>

            {{-- Table --}}
            <div class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gray-50/70">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">#</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Medicine</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Batch</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Form</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Qty</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Price</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Expiry</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">QR</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($batches as $batch)
                            @php
                                $item = $batch->pharmacyItem;
                                $daysLeft = now()->diffInDays($batch->expiry_date, false);
                                $isNearExpiry = $daysLeft <= 30 && $daysLeft > 0;
                            @endphp
                            <tr class="group hover:bg-theme-from/10 transition-colors duration-150 cursor-pointer"
                                @click="showDetail('{{ $batch->id }}', {
                                    commercial_name: '{{ addslashes($item->commercial_name ?? '') }}',
                                    scientific_name: '{{ addslashes($item->scientific_name ?? '') }}',
                                    company_name: '{{ addslashes($item->company_name ?? '') }}',
                                    form: '{{ $item->form ?? '' }}',
                                    category: '{{ $item->category ?? '' }}',
                                    qr_code: '{{ $item->qr_code ?? '' }}',
                                    location: '{{ addslashes($item->location_in_pharmacy ?? '') }}',
                                    batch_number: '{{ $batch->batch_number }}',
                                    remaining: {{ $batch->remaining_quantity }},
                                    expiry: '{{ $batch->expiry_date?->format('M d, Y') ?? '—' }}',
                                    price: {{ $item->default_price ?? 0 }},
                                    days_left: {{ (int)$daysLeft }}
                                })">
                                <td class="px-4 py-3 font-semibold text-gray-300 text-sm">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3">
                                    <p class="text-sm font-bold text-gray-800">{{ $item->commercial_name ?? '—' }}</p>
                                    <p class="text-xs text-gray-400">{{ $item->scientific_name ?? '' }}</p>
                                </td>
                                <td class="px-4 py-3 text-sm font-mono text-gray-600">{{ $batch->batch_number }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 capitalize">{{ $item->form ?? '—' }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="font-bold text-gray-800 text-sm">{{ $batch->remaining_quantity }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-700">${{ number_format($item->default_price, 2) }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold border
                                        {{ $isNearExpiry ? 'bg-amber-50 text-amber-600 border-amber-200' : 'bg-theme-from/5 text-theme-from border-theme-from/20' }}">
                                        {{ $batch->expiry_date?->format('M d, Y') ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($item->qr_code)
                                        <span class="text-xs font-mono text-gray-400">{{ Str::limit($item->qr_code, 8) }}</span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <button type="button" @click.stop="addToCart('{{ $batch->id }}', '{{ addslashes($item->commercial_name) }}', {{ $item->default_price }}, {{ $batch->remaining_quantity }})"
                                        class="px-3 py-1.5 rounded-lg text-xs font-bold bg-gradient-to-r from-theme-from to-theme-to text-white hover:opacity-90 shadow-sm hover:shadow transition-all active:scale-95">
                                        + Add
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <x-message-empty colspan="9">No available batches found</x-message-empty>
                        @endforelse
                    </tbody>
                </table>

                @if($batches->hasPages())
                    <div class="px-5 py-3 border-t border-gray-100 bg-gray-50/50">
                        {{ $batches->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- RIGHT: Invoice Panel --}}
        <div class="w-[420px] shrink-0">
            <div class="sticky top-6 space-y-4">

                {{-- Detail Card (shows when a row is clicked) --}}
                <div x-show="selectedItem" x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                     class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-theme-from to-theme-to px-5 py-3 flex items-center justify-between">
                        <h3 class="text-white font-bold text-sm">Item Details</h3>
                        <button @click="selectedItem = null" class="text-white/70 hover:text-white"><x-icon name="x" class="w-4 h-4" /></button>
                    </div>
                    <div class="p-5 space-y-3 text-sm">
                        <div class="text-center" x-show="selectedItem?.qr_code">
                            <img :src="'https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=' + encodeURIComponent(selectedItem?.qr_code || '')"
                                 class="w-24 h-24 mx-auto rounded-xl border-2 border-dashed border-gray-100 p-1">
                            <p class="text-xs font-mono text-gray-400 mt-1" x-text="selectedItem?.qr_code"></p>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div><p class="text-xs text-gray-400 font-semibold">Commercial Name</p><p class="font-bold text-gray-800" x-text="selectedItem?.commercial_name"></p></div>
                            <div><p class="text-xs text-gray-400 font-semibold">Scientific Name</p><p class="font-bold text-gray-800" x-text="selectedItem?.scientific_name"></p></div>
                            <div><p class="text-xs text-gray-400 font-semibold">Company</p><p class="text-gray-700" x-text="selectedItem?.company_name"></p></div>
                            <div><p class="text-xs text-gray-400 font-semibold">Batch #</p><p class="font-mono text-gray-700" x-text="selectedItem?.batch_number"></p></div>
                            <div><p class="text-xs text-gray-400 font-semibold">Form</p><p class="capitalize text-gray-700" x-text="selectedItem?.form"></p></div>
                            <div><p class="text-xs text-gray-400 font-semibold">Category</p><p class="capitalize text-gray-700" x-text="selectedItem?.category"></p></div>
                            <div><p class="text-xs text-gray-400 font-semibold">Remaining Qty</p><p class="font-bold text-theme-from" x-text="selectedItem?.remaining"></p></div>
                            <div><p class="text-xs text-gray-400 font-semibold">Expiry Date</p>
                                <p class="font-semibold" :class="selectedItem?.days_left <= 30 ? 'text-amber-600' : 'text-gray-700'" x-text="selectedItem?.expiry"></p>
                            </div>
                            <div><p class="text-xs text-gray-400 font-semibold">Price</p><p class="font-bold text-gray-800" x-text="'$' + (selectedItem?.price || 0).toFixed(2)"></p></div>
                            <div><p class="text-xs text-gray-400 font-semibold">Location</p><p class="text-gray-700" x-text="selectedItem?.location || '—'"></p></div>
                        </div>
                    </div>
                </div>

                {{-- Invoice Form --}}
                <form action="{{ route('pharmacy.dispense.store') }}" method="POST"
                      class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    @csrf
                    <div class="bg-gradient-to-r from-theme-from to-theme-to px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-white text-base font-bold">Sale Invoice</h2>
                                <p class="text-sky-100 text-xs" x-text="cart.length + ' item(s) in cart'"></p>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 space-y-4">
                        {{-- Patient --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Patient (Optional)</label>
                            <select name="patient_id" x-model="patientId" @change="onPatientChange()"
                                class="w-full px-3 py-2 rounded-xl border border-gray-200 text-sm bg-gray-50 focus:ring-2 focus:ring-theme-from/40 focus:border-theme-from appearance-none cursor-pointer">
                                <option value="">— Walk-in Customer (Anonymous) —</option>
                                @foreach($patients as $patient)
                                    <option value="{{ $patient->id }}">{{ $patient->full_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Doctor --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Doctor (Optional)</label>
                            <select name="doctor_id"
                                class="w-full px-3 py-2 rounded-xl border border-gray-200 text-sm bg-gray-50 focus:ring-2 focus:ring-theme-from/40 focus:border-theme-from appearance-none cursor-pointer">
                                <option value="">— No Doctor / External Prescription —</option>
                                @foreach($doctors as $doctor)
                                    <option value="{{ $doctor->id }}">Dr. {{ $doctor->user->name ?? 'Unknown' }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Link to Existing Invoice (non-walkin only) --}}
                        <div x-show="!isWalkin && patientInvoices.length > 0"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="rounded-xl border border-amber-200 bg-amber-50 p-3 space-y-2">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <p class="text-xs font-bold text-amber-700">Patient has unpaid invoices</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <label class="flex items-center gap-1.5 cursor-pointer">
                                    <input type="radio" x-model="mode" value="new" class="accent-theme-from">
                                    <span class="text-xs font-semibold text-gray-600">New Invoice</span>
                                </label>
                                <label class="flex items-center gap-1.5 cursor-pointer">
                                    <input type="radio" x-model="mode" value="existing" class="accent-amber-500">
                                    <span class="text-xs font-semibold text-gray-600">Add to Existing</span>
                                </label>
                            </div>
                            <div x-show="mode === 'existing'">
                                <select x-model="selectedInvoiceId" name="invoice_id"
                                    class="w-full px-3 py-2 rounded-lg border border-amber-300 text-xs bg-white focus:ring-2 focus:ring-amber-400/40">
                                    <option value="">— Select Invoice —</option>
                                    <template x-for="inv in patientInvoices" :key="inv.id">
                                        <option :value="inv.id" x-text="'#' + inv.invoice_number + ' | Remaining: $' + inv.remaining.toFixed(2) + ' (' + inv.status + ')'"></option>
                                    </template>
                                </select>
                                <template x-if="selectedInvoice">
                                    <div class="mt-2 p-2 rounded-lg bg-white border border-amber-200 text-xs space-y-1">
                                        <div class="flex justify-between"><span class="text-gray-400">Invoice Total</span><span class="font-bold text-gray-700" x-text="'$' + selectedInvoice.total.toFixed(2)"></span></div>
                                        <div class="flex justify-between"><span class="text-gray-400">Already Paid</span><span class="font-semibold text-theme-from" x-text="'$' + selectedInvoice.paid.toFixed(2)"></span></div>
                                        <div class="flex justify-between"><span class="text-gray-400">Remaining</span><span class="font-bold text-amber-600" x-text="'$' + selectedInvoice.remaining.toFixed(2)"></span></div>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div x-show="!isWalkin && loadingInvoices" class="text-xs text-gray-400 flex items-center gap-1.5">
                            <svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            Loading invoices…
                        </div>

                        <input type="hidden" name="mode" :value="mode">
                        <input type="hidden" name="currency_id" value="{{ $currencies->first()?->id }}">

                        {{-- Cart Items --}}
                        <div class="border-t border-dashed border-gray-200 pt-3">
                            <label class="block text-xs font-semibold text-gray-500 mb-2">Cart Items</label>

                            <div x-show="cart.length === 0" class="text-center py-6 text-gray-400">
                                <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                                <p class="text-xs font-semibold">Cart is empty</p>
                                <p class="text-xs text-gray-300 mt-0.5">Click "+ Add" on items to add them</p>
                            </div>

                            <template x-for="(ci, idx) in cart" :key="idx">
                                <div class="flex items-center gap-2 mb-2 p-2.5 rounded-xl bg-gray-50 border border-gray-100">
                                    <input type="hidden" :name="'items['+idx+'][batch_id]'" :value="ci.batch_id">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-gray-800 truncate" x-text="ci.name"></p>
                                        <p class="text-xs text-gray-400" x-text="'$' + ci.price.toFixed(2) + ' × ' + ci.quantity"></p>
                                    </div>
                                    <input type="number" :name="'items['+idx+'][quantity]'" x-model.number="ci.quantity"
                                        min="1" :max="ci.maxQty" @input="recalc()"
                                        class="w-14 px-2 py-1 rounded-lg border border-gray-200 text-xs text-center bg-white focus:ring-2 focus:ring-theme-from/40">
                                    <input type="hidden" :name="'items['+idx+'][price]'" :value="ci.price">
                                    <span class="text-sm font-bold text-gray-800 w-16 text-right" x-text="'$' + (ci.price * ci.quantity).toFixed(2)"></span>
                                    <button type="button" @click="removeFromCart(idx)" class="p-1 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all">
                                        <x-icon name="x" class="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            </template>
                        </div>

                        {{-- Total --}}
                        <div class="rounded-xl bg-gradient-to-r from-theme-from/5 to-theme-to/5 border border-theme-from/20 px-4 py-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-sm font-semibold text-gray-600">Items Total</span>
                                    <p class="text-xs text-gray-400" x-text="cart.length + ' item(s)'"></p>
                                </div>
                                <span class="text-2xl font-extrabold text-theme-from" x-text="'$' + grandTotal.toFixed(2)"></span>
                            </div>
                            <template x-if="mode === 'existing' && selectedInvoice">
                                <div class="mt-2 pt-2 border-t border-theme-from/20 flex justify-between text-xs">
                                    <span class="text-gray-500">Invoice balance after this sale</span>
                                    <span class="font-bold text-amber-600" x-text="'$' + Math.max(0, selectedInvoice.remaining + grandTotal - amountPaid).toFixed(2)"></span>
                                </div>
                            </template>
                        </div>

                        {{-- Amount to Pay Now --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Amount to Pay Now</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-bold">$</span>
                                <input type="number" name="amount_paid" x-model.number="amountPaid"
                                    step="0.01" min="0" :max="maxPayable"
                                    class="w-full pl-7 pr-3 py-2 rounded-xl border border-gray-200 text-sm bg-gray-50 focus:ring-2 focus:ring-theme-from/40"
                                    placeholder="0.00 = record as unpaid">
                            </div>
                            <p class="text-xs text-gray-400 mt-1">
                                <span x-show="amountPaid <= 0">Leave 0 to record as unpaid / pay later</span>
                                <span x-show="amountPaid > 0 && amountPaid < grandTotal" class="text-amber-600">Partial payment — remaining will stay as balance</span>
                                <span x-show="amountPaid >= grandTotal && mode === 'new'" class="text-theme-from">Full payment ✓</span>
                            </p>
                        </div>

                        {{-- Payment Method --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1.5">Payment *</label>
                            <div class="grid grid-cols-3 gap-2">
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="payment_method" value="cash" class="peer sr-only" checked>
                                    <div class="px-3 py-2 rounded-xl border-2 border-gray-200 text-center text-xs font-semibold text-gray-600 peer-checked:border-theme-from peer-checked:bg-theme-from/5 peer-checked:text-theme-from transition-all">💵 Cash</div>
                                </label>
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="payment_method" value="card" class="peer sr-only">
                                    <div class="px-3 py-2 rounded-xl border-2 border-gray-200 text-center text-xs font-semibold text-gray-600 peer-checked:border-theme-from peer-checked:bg-theme-from/5 peer-checked:text-theme-from transition-all">💳 Card</div>
                                </label>
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="payment_method" value="bank_transfer" class="peer sr-only">
                                    <div class="px-3 py-2 rounded-xl border-2 border-gray-200 text-center text-xs font-semibold text-gray-600 peer-checked:border-theme-from peer-checked:bg-theme-from/5 peer-checked:text-theme-from transition-all">🏦 Transfer</div>
                                </label>
                            </div>
                        </div>

                        {{-- Notes --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Notes</label>
                            <textarea name="notes" rows="2" placeholder="Sale notes…"
                                class="w-full px-3 py-2 rounded-xl border border-gray-200 text-sm bg-gray-50 focus:ring-2 focus:ring-theme-from/40 resize-none"></textarea>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-3 pt-2 border-t border-dashed border-gray-200">
                            <button type="button" @click="clearCart()"
                                class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-all">
                                Clear
                            </button>
                            <button type="submit" :disabled="cart.length === 0"
                                class="flex-1 px-4 py-2.5 rounded-xl bg-gradient-to-r from-theme-from to-theme-to text-white text-sm font-bold shadow-lg  hover:shadow-xl hover:-translate-y-0.5 active:scale-95 transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:translate-y-0 disabled:hover:shadow-lg flex items-center justify-center gap-2">
                                <x-icon name="check-circle" class="w-4 h-4" />
                                Complete Sale
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function dispenseApp() {
            const walkinId = '';
            return {
                patientId: walkinId,
                cart: [],
                selectedItem: null,
                mode: 'new',
                patientInvoices: [],
                selectedInvoiceId: '',
                loadingInvoices: false,
                amountPaid: 0,

                get isWalkin() { return this.patientId === walkinId; },
                get grandTotal() { return this.cart.reduce((s, c) => s + (c.price * c.quantity), 0); },
                get selectedInvoice() { return this.patientInvoices.find(i => i.id === this.selectedInvoiceId) || null; },
                get maxPayable() {
                    if (this.mode === 'existing' && this.selectedInvoice) {
                        return this.selectedInvoice.remaining + this.grandTotal;
                    }
                    return this.grandTotal;
                },

                init() {
                    this.$watch('grandTotal', v => { if (this.amountPaid === 0 || this.amountPaid > this.maxPayable) this.amountPaid = v; });
                    this.$watch('mode', v => { if (v === 'new') { this.selectedInvoiceId = ''; this.amountPaid = this.grandTotal; } else { this.amountPaid = 0; } });
                    this.$watch('selectedInvoiceId', () => { this.amountPaid = 0; });
                },

                async onPatientChange() {
                    this.patientInvoices = [];
                    this.selectedInvoiceId = '';
                    this.mode = 'new';
                    if (this.isWalkin || !this.patientId) return;
                    this.loadingInvoices = true;
                    try {
                        const res = await fetch(`/api/patients/${this.patientId}/invoices`);
                        this.patientInvoices = await res.json();
                    } catch(e) { console.error(e); }
                    finally { this.loadingInvoices = false; }
                },

                showDetail(batchId, data) { this.selectedItem = data; },

                addToCart(batchId, name, price, maxQty) {
                    const existing = this.cart.find(c => c.batch_id === batchId);
                    if (existing) { if (existing.quantity < maxQty) existing.quantity++; return; }
                    this.cart.push({ batch_id: batchId, name, price: parseFloat(price), quantity: 1, maxQty });
                    if (this.mode === 'new') this.amountPaid = this.grandTotal;
                },
                removeFromCart(idx) {
                    this.cart.splice(idx, 1);
                    if (this.mode === 'new') this.amountPaid = this.grandTotal;
                },
                clearCart() { this.cart = []; this.amountPaid = 0; },
                recalc() {}
            };
        }
    </script>
</x-app-layout>
