<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class StoreOrderStatusLog extends Model {
    protected $fillable=['store_order_id','user_id','from_status','to_status','note'];
    public function order(): BelongsTo { return $this->belongsTo(StoreOrder::class,'store_order_id'); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
