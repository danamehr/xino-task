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
        Schema::create('invoices', function (Blueprint $table) {
            $table->ulid('id');
            $table->string('reference')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('plan_id')->constrained();
            $table->unsignedTinyInteger('type'); // \App\Modules\Invoice\Enums\InvoiceType::class
            $table->unsignedTinyInteger('status'); // \App\Modules\Invoice\Enums\InvoiceStatus::class
            $table->decimal('amount', 5); // For information. Maybe plan's price change in the future.
            $table->unsignedSmallInteger('duration_days'); // For information. Maybe plan's duration days change in the future.
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
