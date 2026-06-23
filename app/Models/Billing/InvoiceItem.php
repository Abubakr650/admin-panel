<?php

namespace App\Models\Billing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceItem extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'invoice_items';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'invoice_id',
        'treatment_id',
        'quantity',
        'unit_price',
        'total_price',
    ];

    protected $dates = ['deleted_at'];

    protected function casts(): array
    {
        return [
            'deleted_at'  => 'datetime',
            'unit_price'  => 'decimal:2',
            'total_price' => 'decimal:2',
            'quantity'    => 'integer',
        ];
    }

    // ─── Relationships ────────────────────────────────────────

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function treatment()
    {
        return $this->belongsTo(\App\Models\Clinic\Treatment::class);
    }
}
