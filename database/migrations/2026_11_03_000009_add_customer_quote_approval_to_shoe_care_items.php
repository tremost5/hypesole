<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shoe_care_order_items', function (Blueprint $table) {
            $table->decimal('quoted_price', 12, 2)->nullable()->after('service_price');
            $table->text('inspection_notes')->nullable()->after('internal_notes');
            $table->enum('customer_approval', ['not_requested', 'pending', 'approved', 'rejected'])->default('not_requested')->after('inspection_notes');
            $table->timestamp('customer_approved_at')->nullable()->after('customer_approval');
        });
    }

    public function down(): void
    {
        Schema::table('shoe_care_order_items', function (Blueprint $table) {
            $table->dropColumn(['quoted_price', 'inspection_notes', 'customer_approval', 'customer_approved_at']);
        });
    }
};
