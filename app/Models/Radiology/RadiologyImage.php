<?php

namespace App\Models\Radiology;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class RadiologyImage extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'radiology_images'; // Table name in the database

    protected $keyType = "string";
    public $incrementing = false;

    protected $fillable = [
        'radiology_id', 
        'image_path', 
        'ai_analysis',
    ];


    protected $dates = ['deleted_at'];

    protected function casts(): array
    {
        return [
            'deleted_at' => 'datetime',
        ];
    }

    public function radiology()
    {
        return $this->belongsTo(Radiology::class);
    }
}
