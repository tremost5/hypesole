<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shoe_care_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shoe_care_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_staff_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('service_name', 150);
            $table->decimal('service_price', 12, 2)->default(0);
            $table->string('shoe_brand', 100)->nullable();
            $table->string('shoe_model', 150)->nullable();
            $table->string('shoe_color', 80)->nullable();
            $table->string('shoe_size', 30)->nullable();
            $table->text('issue_description');
            $table->text('internal_notes')->nullable();
            $table->enum('status', ['received','inspection','queued','in_progress','quality_check','completed','ready_for_pickup','delivered','cancelled'])->default('received');
            $table->json('before_photos')->nullable();
            $table->json('after_photos')->nullable();
            $table->timestamps();
            $table->index(['shoe_care_order_id', 'status']);
        });

        // Preserve every existing single-shoe order as item number one.
        DB::table('shoe_care_orders')->orderBy('id')->chunk(200, function ($orders) {
            foreach ($orders as $order) {
                DB::table('shoe_care_order_items')->insert([
                    'shoe_care_order_id' => $order->id,
                    'service_id' => $order->service_id,
                    'assigned_staff_id' => $order->assigned_staff_id,
                    'service_name' => $order->service_name,
                    'service_price' => $order->service_price,
                    'shoe_brand' => $order->shoe_brand,
                    'shoe_model' => $order->shoe_model,
                    'shoe_color' => $order->shoe_color,
                    'shoe_size' => $order->shoe_size,
                    'issue_description' => $order->issue_description,
                    'internal_notes' => $order->internal_notes,
                    'status' => $order->status,
                    'before_photos' => $order->before_photos,
                    'after_photos' => $order->after_photos,
                    'created_at' => $order->created_at,
                    'updated_at' => $order->updated_at,
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shoe_care_order_items');
    }
};
