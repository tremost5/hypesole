<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShoeCareShipmentEvent extends Model
{
    protected $fillable = ['shoe_care_shipment_id','user_id','status','description','proof_photo_path'];
    public function shipment(): BelongsTo { return $this->belongsTo(ShoeCareShipment::class, 'shoe_care_shipment_id'); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
