<?php

namespace App\Models\Billing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasAuditColumns;

class Invoice extends Model
{
    use HasFactory, HasUuids, SoftDeletes, HasAuditColumns;

    protected $table = 'invoices';

    protected $keyType = 'string';
    public $incrementing = false;

    // ─── Payment Status Constants ─────────────────────────────

    const STATUS_UNPAID  = 'unpaid';
    const STATUS_PARTIAL = 'partial';
    const STATUS_PAID    = 'paid';

    const PAYMENT_STATUSES = [
        self::STATUS_UNPAID,
        self::STATUS_PARTIAL,
        self::STATUS_PAID,
    ];

    // ─── Fillable ─────────────────────────────────────────────

    protected $fillable = [
        'invoice_number',
        'patient_id',
        'doctor_id',
        'total_amount',
        'discount_percent',
        'final_amount',
        'payment_status',
        'exchange_rate',
        'currency_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $dates = ['deleted_at'];

    protected function casts(): array
    {
        return [
            'deleted_at'       => 'datetime',
            'total_amount'     => 'decimal:2',
            'final_amount'     => 'decimal:2',
            'exchange_rate'    => 'decimal:6',
            'discount_percent' => 'integer',
        ];
    }

    // ─── Relationships ────────────────────────────────────────

    public function patient()
    {
        return $this->belongsTo(\App\Models\Clinic\Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(\App\Models\Clinic\Doctor::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    // ─── Scopes ───────────────────────────────────────────────

    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', self::STATUS_UNPAID);
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', self::STATUS_PAID);
    }

    // ─── Helpers ──────────────────────────────────────────────

    public function getTotalPaidAttribute(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->final_amount - $this->total_paid);
    }
}
