<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('shoe_care_shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shoe_care_order_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['pickup', 'delivery']);
            $table->enum('method', ['internal', 'external'])->default('internal');
            $table->foreignId('courier_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('recipient_name', 120);
            $table->string('recipient_phone', 24);
            $table->text('address');
            $table->string('area', 120)->nullable();
            $table->decimal('distance_km', 8, 2)->nullable();
            $table->decimal('shipping_fee', 12, 2)->default(0);
            $table->string('carrier', 100)->nullable();
            $table->string('tracking_number', 120)->nullable();
            $table->enum('status', ['waiting_assignment','assigned','picked_up','in_transit','delivered','failed','cancelled'])->default('waiting_assignment');
            $table->text('notes')->nullable();
            $table->string('proof_photo_path')->nullable();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
            $table->index(['type','status']);
            $table->index(['courier_id','status']);
            $table->index('tracking_number');
        });

        Schema::create('shoe_care_shipment_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shoe_care_shipment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status', 40);
            $table->text('description')->nullable();
            $table->string('proof_photo_path')->nullable();
            $table->timestamps();
            $table->index(['shoe_care_shipment_id','created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shoe_care_shipment_events');
        Schema::dropIfExists('shoe_care_shipments');
    }
};
