<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\ProtectsRelations;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasUuids, SoftDeletes, ProtectsRelations;

    protected array $protectedRelations = ['doctor'];

    protected $keyType = "string";
    public $incrementing = false;

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::deleted(function (User $user) {
            // Only delete file if it's a permanent delete (forceDelete)
            // or if the model doesn't use SoftDeletes.
            // In our case, we want to keep it if it's just archived.
            if ($user->isForceDeleting()) {
                if ($user->image) {
                    app(\App\Services\FileStorageService::class)->delete($user->image);
                }
            }
        });
    }


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'full_name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'gender',
        'birth_date',
        'image',
    ];

    protected $dates = ['deleted_at', 'birth_date'];

    /**
     * Get the user's image URL.
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->image ? Storage::disk(config('filesystems.default', 's3'))->url($this->image) : null,
        );
    }


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'deleted_at' => 'datetime',
            'birth_date' => 'date',
        ];
    }

    public function doctor()
    {
        return $this->hasOne(\App\Models\Clinic\Doctor::class);
    }
}
