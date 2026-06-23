<?php

namespace App\Models\Billing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasAuditColumns;

class Payment extends Model
{
    use HasFactory, HasUuids, SoftDeletes, HasAuditColumns;

    protected $table = 'payments';

    protected $keyType = 'string';
    public $incrementing = false;

    // ─── Payment Method Constants ─────────────────────────────

    const METHOD_CASH          = 'cash';
    const METHOD_CARD          = 'card';
    const METHOD_BANK_TRANSFER = 'bank_transfer';
    const METHOD_OTHER         = 'other';

    const PAYMENT_METHODS = [
        self::METHOD_CASH,
        self::METHOD_CARD,
        self::METHOD_BANK_TRANSFER,
        self::METHOD_OTHER,
    ];

    // ─── Fillable ─────────────────────────────────────────────

    protected $fillable = [
        'invoice_id',
        'amount',
        'payment_method',
        'paid_at',
        'exchange_rate',
        'currency_id',
        'notes',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $dates = ['deleted_at', 'paid_at'];

    protected function casts(): array
    {
        return [
            'deleted_at'    => 'datetime',
            'paid_at'       => 'datetime',
            'amount'        => 'decimal:2',
            'exchange_rate'  => 'decimal:6',
        ];
    }

    // ─── Relationships ────────────────────────────────────────

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
