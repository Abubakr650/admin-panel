<?php

namespace App\Models\Pharmacy;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class WarehouseItem extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'warehouse_items'; // Table name in the database

    protected $keyType = "string";
    public $incrementing = false;

    protected $fillable = [
        'name', 
        'company_name',
        'type', 
        'quantity', 
        'supplier_id',
        'production_date',
        'expiry_date',
        'category',             
        'qr_code',              
        'location_in_warehouse', 
        'notes',
    ];

    protected $dates = ['deleted_at', 'production_date', 'expiry_date'];

    protected function casts(): array
    {
        return [
            'deleted_at' => 'datetime',
            'production_date' => 'date',
            'expiry_date' => 'date',
        ];
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
