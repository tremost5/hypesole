<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('shoe_care_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 40)->unique();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('received_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_staff_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('customer_name', 120);
            $table->string('customer_phone', 24);
            $table->string('service_name', 150);
            $table->decimal('service_price', 12, 2)->default(0);
            $table->string('shoe_brand', 100)->nullable();
            $table->string('shoe_model', 150)->nullable();
            $table->string('shoe_color', 80)->nullable();
            $table->string('shoe_size', 30)->nullable();
            $table->text('issue_description');
            $table->text('customer_notes')->nullable();
            $table->text('internal_notes')->nullable();
            $table->enum('status', ['received','inspection','queued','in_progress','quality_check','completed','ready_for_pickup','delivered','cancelled'])->default('received');
            $table->json('before_photos')->nullable();
            $table->json('after_photos')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamp('estimated_completion_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'created_at']);
            $table->index(['customer_id', 'created_at']);
            $table->index(['assigned_staff_id', 'status']);
        });
        Schema::create('shoe_care_order_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shoe_care_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('from_status', 40)->nullable();
            $table->string('to_status', 40);
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index(['shoe_care_order_id', 'created_at']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('shoe_care_order_status_logs');
        Schema::dropIfExists('shoe_care_orders');
    }
};
