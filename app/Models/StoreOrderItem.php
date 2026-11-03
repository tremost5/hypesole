<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class StoreOrderItem extends Model {
    public $timestamps=false;
    protected $fillable=['store_order_id','product_id','product_name','sku','product_type','size','unit_price','quantity','line_total'];
    protected function casts(): array { return ['unit_price'=>'decimal:2','line_total'=>'decimal:2']; }
    public function order(): BelongsTo { return $this->belongsTo(StoreOrder::class,'store_order_id'); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
}
