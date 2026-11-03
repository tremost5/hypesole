<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class StoreOrder extends Model {
    protected $fillable=['order_number','customer_id','customer_address_id','recipient_name','recipient_phone','shipping_address','shipping_notes','subtotal','shipping_cost','total','payment_method','payment_status','status','customer_notes','confirmed_at','completed_at','cancelled_at'];
    protected function casts(): array { return ['subtotal'=>'decimal:2','shipping_cost'=>'decimal:2','total'=>'decimal:2','confirmed_at'=>'datetime','completed_at'=>'datetime','cancelled_at'=>'datetime']; }
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function address(): BelongsTo { return $this->belongsTo(CustomerAddress::class,'customer_address_id'); }
    public function items(): HasMany { return $this->hasMany(StoreOrderItem::class); }
    public function statusLogs(): HasMany { return $this->hasMany(StoreOrderStatusLog::class)->latest(); }
}
