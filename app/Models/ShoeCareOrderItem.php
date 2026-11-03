<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShoeCareOrderItem extends Model
{
    protected $fillable = [
        'shoe_care_order_id', 'service_id', 'assigned_staff_id', 'service_name',
        'service_price', 'quoted_price', 'inspection_notes', 'customer_approval', 'customer_approved_at', 'shoe_brand', 'shoe_model', 'shoe_color', 'shoe_size',
        'issue_description', 'internal_notes', 'status', 'before_photos', 'after_photos',
    ];

    protected function casts(): array
    {
        return ['service_price' => 'decimal:2', 'quoted_price' => 'decimal:2', 'customer_approved_at' => 'datetime', 'before_photos' => 'array', 'after_photos' => 'array'];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(ShoeCareOrder::class, 'shoe_care_order_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function assignedStaff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_staff_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return [
            'received' => 'Diterima', 'inspection' => 'Pemeriksaan', 'queued' => 'Menunggu antrean',
            'in_progress' => 'Sedang dikerjakan', 'quality_check' => 'Pemeriksaan akhir',
            'completed' => 'Selesai dikerjakan', 'ready_for_pickup' => 'Siap diambil',
            'delivered' => 'Sudah diserahkan', 'cancelled' => 'Dibatalkan',
        ][$this->status] ?? ucfirst(str_replace('_', ' ', $this->status));
    }
}
