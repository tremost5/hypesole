<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShoeCareOrder extends Model {
    protected $fillable = ['order_number','customer_id','service_id','received_by_user_id','assigned_staff_id','customer_name','customer_phone','service_name','service_price','shoe_brand','shoe_model','shoe_color','shoe_size','issue_description','customer_notes','internal_notes','status','before_photos','after_photos','received_at','estimated_completion_at','completed_at','delivered_at'];
    protected function casts(): array {
        return ['service_price'=>'decimal:2','before_photos'=>'array','after_photos'=>'array','received_at'=>'datetime','estimated_completion_at'=>'datetime','completed_at'=>'datetime','delivered_at'=>'datetime'];
    }
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function service(): BelongsTo { return $this->belongsTo(Service::class); }
    public function receivedBy(): BelongsTo { return $this->belongsTo(User::class, 'received_by_user_id'); }
    public function assignedStaff(): BelongsTo { return $this->belongsTo(User::class, 'assigned_staff_id'); }
    public function statusLogs(): HasMany { return $this->hasMany(ShoeCareOrderStatusLog::class)->latest(); }
    public function items(): HasMany { return $this->hasMany(ShoeCareOrderItem::class)->orderBy('id'); }
    public function shipments(): HasMany { return $this->hasMany(ShoeCareShipment::class)->with('events')->latest(); }
    public function getStatusLabelAttribute(): string {
        return ['received'=>'Diterima','inspection'=>'Pemeriksaan','queued'=>'Menunggu antrean','in_progress'=>'Sedang dikerjakan','quality_check'=>'Pemeriksaan akhir','completed'=>'Selesai dikerjakan','ready_for_pickup'=>'Siap diambil','delivered'=>'Sudah diserahkan','cancelled'=>'Dibatalkan'][$this->status] ?? ucfirst(str_replace('_',' ',$this->status));
    }
}
