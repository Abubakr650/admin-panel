<?php

namespace App\Models\Pharmacy;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\ProtectsRelations;

class Supplier extends Model
{
    use HasFactory, HasUuids, SoftDeletes, ProtectsRelations;

    protected array $protectedRelations = ['pharmacyBatches', 'warehouseItems'];

    protected $table = 'suppliers'; // Table name in the database

    protected $keyType = "string";
    public $incrementing = false;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'country',
        'notes',
    ];

    protected $dates = ['deleted_at'];

    protected function casts(): array
    {
        return [
            'deleted_at' => 'datetime',
        ];
    }


    public function pharmacyBatches()
    {
        return $this->hasMany(PharmacyBatch::class);
    }

    public function warehouseItems()
    {
        return $this->hasMany(WarehouseItem::class);
    }
}
