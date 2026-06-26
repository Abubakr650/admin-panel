@extends('layouts.print-receipt')

@section('title', 'Receipt #' . $invoice->invoice_number)
@section('receipt_title', 'IBTISAMA PHARMACY')

@section('content')
<div style="margin: 8px 0; font-size: 12px;">
    <table style="width:100%;">
        <tr><td>Rcpt:</td><td class="text-right font-bold">{{ $invoice->invoice_number }}</td></tr>
        <tr><td>Date:</td><td class="text-right">{{ $invoice->created_at->format('Y-m-d H:i') }}</td></tr>
        <tr><td>Cashier:</td><td class="text-right">{{ auth()->user()->name ?? 'System' }}</td></tr>
        <tr><td>Patient:</td><td class="text-right">{{ $invoice->patient ? Str::limit($invoice->patient->full_name, 15) : 'Walk-in' }}</td></tr>
    </table>
</div>

<div class="divider"></div>

<table>
    <thead>
        <tr>
            <th style="width: 50%;">Item</th>
            <th style="width: 15%; text-align: center;">Qty</th>
            <th style="width: 35%; text-align: right;">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoice->items as $item)
        <tr>
            <td style="vertical-align: top;">
                <span class="item-name">
                    @if($item->treatment && $item->treatment->pharmacyBatch)
                        {{ $item->treatment->pharmacyBatch->pharmacyItem->commercial_name ?? 'Item' }}
                    @else
                        {{ $item->description ?? 'Item' }}
                    @endif
                </span>
                <span style="font-size: 10px; color: #555;">${{ number_format($item->unit_price, 2) }}</span>
            </td>
            <td class="text-center" style="vertical-align: top;">{{ $item->quantity }}</td>
            <td class="text-right font-bold" style="vertical-align: top;">${{ number_format($item->total_price, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="divider"></div>

<table style="font-size: 12px;">
    <tr>
        <td>Subtotal:</td>
        <td class="text-right">${{ number_format($invoice->total_amount, 2) }}</td>
    </tr>
    @if($invoice->discount_percent > 0)
    <tr>
        <td>Discount ({{ $invoice->discount_percent }}%):</td>
        <td class="text-right">-${{ number_format($invoice->total_amount * ($invoice->discount_percent / 100), 2) }}</td>
    </tr>
    @endif
    <tr class="font-bold" style="font-size: 16px;">
        <td style="padding-top: 8px;">TOTAL:</td>
        <td class="text-right" style="padding-top: 8px;">${{ number_format($invoice->final_amount, 2) }}</td>
    </tr>
</table>

<div class="divider" style="margin-top: 12px;"></div>

<table style="font-size: 12px;">
    <tr>
        <td>Paid:</td>
        <td class="text-right">${{ number_format($invoice->total_paid, 2) }}</td>
    </tr>
    @if($invoice->remaining_amount > 0)
    <tr>
        <td>Balance Due:</td>
        <td class="text-right font-bold">${{ number_format($invoice->remaining_amount, 2) }}</td>
    </tr>
    @endif
</table>
@endsection
