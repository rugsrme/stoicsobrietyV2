<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->nullable()->constrained('books')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('source')->default('manual')->comment('manual|amazon|barnes-noble|other-retailer|direct');
            $table->string('external_reference')->nullable()->comment('e.g. Amazon order ID, Stripe session ID');
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedInteger('amount_cents')->nullable();
            $table->string('currency', 3)->default('usd');
            $table->string('status')->default('pending')->comment('pending|paid|fulfilled|cancelled|refunded');
            $table->string('delivery_method')->default('external')->comment('external|digital|physical');
            $table->string('delivery_status')->default('not_required')->comment('not_required|pending|in_progress|delivered');
            $table->string('tracking_number')->nullable();
            $table->text('shipping_address')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('ordered_at')->nullable();
            $table->timestamp('fulfilled_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
