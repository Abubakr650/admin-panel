<?php

namespace App\Models\Billing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Currency extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'currencies'; // Table name in the database

    protected $keyType = "string";
    public $incrementing = false;


    protected $fillable = [
        'name',
        'code',
    ];

    protected $dates = ['deleted_at'];

    protected function casts(): array
    {
        return [
            'deleted_at' => 'datetime',
        ];
    }

    public function ratesFrom()
    {
        return $this->hasMany(ExchangeRate::class, 'from_currency_id');
    }

    public function ratesTo()
    {
        return $this->hasMany(ExchangeRate::class, 'to_currency_id');
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
