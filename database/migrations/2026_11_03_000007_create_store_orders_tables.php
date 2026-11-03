<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('store_orders', function(Blueprint $table) {
            $table->id();
            $table->string('order_number',40)->unique();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->foreignId('customer_address_id')->nullable()->constrained('customer_addresses')->nullOnDelete();
            $table->string('recipient_name',100);
            $table->string('recipient_phone',24);
            $table->text('shipping_address');
            $table->text('shipping_notes')->nullable();
            $table->decimal('subtotal',14,2);
            $table->decimal('shipping_cost',14,2)->default(0);
            $table->decimal('total',14,2);
            $table->enum('payment_method',['cash','transfer'])->default('transfer');
            $table->enum('payment_status',['unpaid','paid','refunded'])->default('unpaid');
            $table->enum('status',['pending_confirmation','confirmed','processing','ready','completed','cancelled'])->default('pending_confirmation');
            $table->text('customer_notes')->nullable();
            $table->timestamp('stock_deducted_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            $table->index(['customer_id','created_at']);
            $table->index(['status','payment_status']);
        });
        Schema::create('store_order_items', function(Blueprint $table) {
            $table->id();
            $table->foreignId('store_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name',180);
            $table->string('sku',80);
            $table->string('product_type',20);
            $table->string('size',30)->nullable();
            $table->decimal('unit_price',14,2);
            $table->unsignedInteger('quantity');
            $table->decimal('line_total',14,2);
        });
        Schema::create('store_order_status_logs', function(Blueprint $table) {
            $table->id();
            $table->foreignId('store_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('from_status',40)->nullable();
            $table->string('to_status',40);
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index(['store_order_id','created_at']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('store_order_status_logs');
        Schema::dropIfExists('store_order_items');
        Schema::dropIfExists('store_orders');
    }
};
