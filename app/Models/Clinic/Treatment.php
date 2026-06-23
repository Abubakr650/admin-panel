<?php

namespace App\Models\Clinic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasAuditColumns;

class Treatment extends Model
{
    use HasFactory, HasUuids, SoftDeletes, HasAuditColumns;

    protected $table = 'treatments';

    protected $keyType = 'string';
    public $incrementing = false;

    // ─── Type Constants ───────────────────────────────────────

    const TYPE_CONSULTATION       = 'consultation';
    const TYPE_FILLING            = 'filling';
    const TYPE_EXTRACTION         = 'extraction';
    const TYPE_CLEANING           = 'cleaning';
    const TYPE_COSMETIC           = 'cosmetic';
    const TYPE_RADIOLOGY          = 'radiology';
    const TYPE_ORTHODONTIC        = 'orthodontic_session';
    const TYPE_PHARMACY_DISPENSE  = 'pharmacy_dispense';
    const TYPE_OTHER              = 'other';

    // ─── Status Constants ─────────────────────────────────────

    const STATUS_DRAFT       = 'draft';
    const STATUS_PLANNED     = 'planned';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED   = 'completed';
    const STATUS_CANCELLED   = 'cancelled';

    // ─── Billing Status Constants ─────────────────────────────

    const BILLING_PENDING          = 'pending';
    const BILLING_PARTIALLY_BILLED = 'partially_billed';
    const BILLING_BILLED           = 'billed';
    const BILLING_CANCELLED        = 'cancelled';

    // ─── Fillable ─────────────────────────────────────────────

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'appointment_id',
        'service_id',
        'pharmacy_batch_id',
        'type',
        'quantity',
        'description',
        'price',
        'discount',
        'total',
        'status',
        'billing_status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $dates = ['deleted_at'];

    protected function casts(): array
    {
        return [
            'deleted_at' => 'datetime',
            'price'      => 'decimal:2',
            'discount'   => 'decimal:2',
            'total'      => 'decimal:2',
            'quantity'   => 'integer',
        ];
    }

    // ─── Relationships ────────────────────────────────────────

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function service()
    {
        return $this->belongsTo(\App\Models\Billing\Service::class);
    }

    public function pharmacyBatch()
    {
        return $this->belongsTo(\App\Models\Pharmacy\PharmacyBatch::class);
    }

    public function invoiceItems()
    {
        return $this->hasMany(\App\Models\Billing\InvoiceItem::class);
    }

    // ─── Scopes ───────────────────────────────────────────────

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopePendingBilling($query)
    {
        return $query->where('billing_status', self::BILLING_PENDING);
    }

    public function scopeForPatient($query, string $patientId)
    {
        return $query->where('patient_id', $patientId);
    }
}
