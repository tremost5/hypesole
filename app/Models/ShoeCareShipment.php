<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShoeCareShipment extends Model
{
    protected $fillable = [
        'shoe_care_order_id','type','method','courier_id','recipient_name','recipient_phone',
        'address','area','distance_km','shipping_fee','carrier','tracking_number','status',
        'notes','proof_photo_path','assigned_at','picked_up_at','delivered_at',
    ];

    protected function casts(): array
    {
        return [
            'distance_km' => 'decimal:2',
            'shipping_fee' => 'decimal:2',
            'assigned_at' => 'datetime',
            'picked_up_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo { return $this->belongsTo(ShoeCareOrder::class, 'shoe_care_order_id'); }
    public function courier(): BelongsTo { return $this->belongsTo(User::class, 'courier_id'); }
    public function events(): HasMany { return $this->hasMany(ShoeCareShipmentEvent::class)->latest(); }

    public function getStatusLabelAttribute(): string
    {
        return [
            'waiting_assignment'=>'Menunggu kurir','assigned'=>'Kurir ditugaskan',
            'picked_up'=>'Sudah dijemput','in_transit'=>'Dalam perjalanan',
            'delivered'=>'Terkirim','failed'=>'Pengiriman gagal','cancelled'=>'Dibatalkan',
        ][$this->status] ?? $this->status;
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->type === 'pickup' ? 'Pickup ke workshop' : 'Delivery ke pelanggan';
    }
}
