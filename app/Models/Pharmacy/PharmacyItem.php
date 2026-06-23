<?php

namespace App\Models\Pharmacy;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\ProtectsRelations;

class PharmacyItem extends Model
{
    use HasFactory, HasUuids, SoftDeletes, ProtectsRelations;

    protected array $protectedRelations = ['pharmacyBatches'];

    protected $table = 'pharmacy_items';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'commercial_name',
        'scientific_name',
        'company_name',
        'form',
        'category',
        'default_price',
        'qr_code',
        'location_in_pharmacy',
        'notes',
    ];

    protected $dates = ['deleted_at'];

    protected function casts(): array
    {
        return [
            'deleted_at'    => 'datetime',
            'default_price' => 'decimal:2',
        ];
    }

    public function pharmacyBatches()
    {
        return $this->hasMany(PharmacyBatch::class);
    }
}
