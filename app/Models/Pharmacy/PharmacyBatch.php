<?php

namespace App\Models\Pharmacy;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class PharmacyBatch extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'pharmacy_batches';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'pharmacy_item_id',
        'batch_number',
        'quantity',
        'remaining_quantity',
        'production_date',
        'expiry_date',
        'supplier_id',
    ];

    protected $dates = ['deleted_at', 'production_date', 'expiry_date'];

    protected function casts(): array
    {
        return [
            'deleted_at'      => 'datetime',
            'production_date' => 'date',
            'expiry_date'     => 'date',
        ];
    }

    public function pharmacyItem()
    {
        return $this->belongsTo(PharmacyItem::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function treatments()
    {
        return $this->hasMany(\App\Models\Clinic\Treatment::class);
    }

    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', now());
    }

    public function scopeAvailable($query)
    {
        return $query->where('remaining_quantity', '>', 0)
                     ->where('expiry_date', '>=', now());
    }
}
