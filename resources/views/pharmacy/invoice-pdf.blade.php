<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacy Invoice - {{ $invoice->invoice_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; -webkit-print-color-adjust: exact; print-color-adjust: exact; background-color: white;}
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; }
        .theme-text { color: #0077b6; }
        .theme-bg { background-color: #0077b6; color: white; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="flex justify-between items-start mb-8 border-b-2 border-gray-100 pb-6">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-800">Ibtisama Pharmacy</h1>
                <p class="text-sm text-gray-500 mt-1">Dental Clinic & Pharmacy</p>
                <p class="text-sm text-gray-500">123 Health Ave, Medical District</p>
                <p class="text-sm text-gray-500">Phone: (123) 456-7890</p>
            </div>
            <div class="text-right">
                <h2 class="text-2xl font-bold theme-text uppercase tracking-wider mb-2">Invoice</h2>
                <div class="text-sm text-gray-600 font-semibold mb-1">
                    Invoice #: <span class="text-gray-800">{{ $invoice->invoice_number }}</span>
                </div>
                <div class="text-sm text-gray-600 font-semibold mb-1">
                    Date: <span class="text-gray-800">{{ $invoice->created_at->format('M d, Y H:i') }}</span>
                </div>
                <div class="text-sm text-gray-600 font-semibold inline-block px-3 py-1 mt-2 rounded-full text-xs
                    {{ $invoice->payment_status === 'paid' ? 'bg-green-100 text-green-700' : ($invoice->payment_status === 'partial' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                    Status: {{ strtoupper($invoice->payment_status) }}
                </div>
            </div>
        </div>

        <div class="flex justify-between mb-8 gap-6">
            <div class="flex-1 bg-gray-50 p-4 rounded-xl border border-gray-100">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Billed To:</h3>
                <div class="font-bold text-gray-800 text-lg">{{ $invoice->patient ? $invoice->patient->full_name : 'Walk-in Customer' }}</div>
                @if($invoice->patient)
                    <div class="text-sm text-gray-600 mt-1">{{ $invoice->patient->phone ?? 'No Phone' }}</div>
                @endif
            </div>
            
            @if($invoice->doctor)
            <div class="flex-1 bg-gray-50 p-4 rounded-xl border border-gray-100">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Prescribed By:</h3>
                <div class="font-bold text-gray-800 text-lg">Dr. {{ $invoice->doctor->user->name ?? 'Unknown' }}</div>
                <div class="text-sm text-gray-600 mt-1">Dental Clinic</div>
            </div>
            @endif
        </div>

        <table class="w-full text-left border-collapse mb-8">
            <thead>
                <tr class="theme-bg">
                    <th class="py-3 px-4 rounded-tl-lg font-semibold text-sm">#</th>
                    <th class="py-3 px-4 font-semibold text-sm">Item / Medicine</th>
                    <th class="py-3 px-4 font-semibold text-sm text-center">Qty</th>
                    <th class="py-3 px-4 font-semibold text-sm text-right">Unit Price</th>
                    <th class="py-3 px-4 rounded-tr-lg font-semibold text-sm text-right">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($invoice->items as $item)
                    <tr class="text-sm text-gray-700">
                        <td class="py-3 px-4">{{ $loop->iteration }}</td>
                        <td class="py-3 px-4 font-semibold">
                            @if($item->treatment && $item->treatment->pharmacyBatch)
                                {{ $item->treatment->pharmacyBatch->pharmacyItem->commercial_name ?? 'Medicine' }}
                                <div class="text-xs text-gray-500 font-normal mt-0.5">Batch: {{ $item->treatment->pharmacyBatch->batch_number }}</div>
                            @else
                                {{ $item->description ?? 'Pharmacy Item' }}
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center font-bold">{{ $item->quantity }}</td>
                        <td class="py-3 px-4 text-right">${{ number_format($item->unit_price, 2) }}</td>
                        <td class="py-3 px-4 text-right font-bold">${{ number_format($item->total_price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="flex justify-end mt-4 border-t border-gray-200 pt-6">
            <div class="w-72 space-y-3">
                <div class="flex justify-between text-sm text-gray-600 font-semibold">
                    <span>Subtotal:</span>
                    <span>${{ number_format($invoice->total_amount, 2) }}</span>
                </div>
                @if($invoice->discount_percent > 0)
                <div class="flex justify-between text-sm text-red-500 font-semibold">
                    <span>Discount ({{ $invoice->discount_percent }}%):</span>
                    <span>-${{ number_format($invoice->total_amount * ($invoice->discount_percent / 100), 2) }}</span>
                </div>
                @endif
                <div class="flex justify-between text-lg font-bold text-gray-800 pt-2 border-t border-gray-200">
                    <span>Grand Total:</span>
                    <span class="theme-text">${{ number_format($invoice->final_amount, 2) }}</span>
                </div>
                
                <div class="flex justify-between text-sm text-green-600 font-semibold pt-2">
                    <span>Total Paid:</span>
                    <span>${{ number_format($invoice->total_paid, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm text-amber-600 font-bold pt-1">
                    <span>Balance Due:</span>
                    <span>${{ number_format($invoice->remaining_amount, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="mt-16 text-center text-xs text-gray-400 border-t border-gray-100 pt-4">
            <p>Thank you for choosing Ibtisama Pharmacy.</p>
            <p>For any inquiries regarding this invoice, please contact us at (123) 456-7890.</p>
            <p class="mt-2 font-mono text-[10px]">Generated by Ibtisama Clinic System</p>
        </div>
    </div>
</body>
</html>
