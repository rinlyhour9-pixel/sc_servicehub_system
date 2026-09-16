<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_operations', function (Blueprint $table) {
            $table->decimal('opening_cash', 12, 2)->default(0)->after('opened_at');
            $table->decimal('expected_cash', 12, 2)->nullable()->after('closed_at');
            $table->decimal('actual_cash', 12, 2)->nullable()->after('expected_cash');
            $table->decimal('cash_difference', 12, 2)->nullable()->after('actual_cash');
        });
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id(); $table->foreignId('daily_operation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type'); $table->string('payment_method')->default('cash');
            $table->decimal('amount', 12, 2); $table->string('description')->nullable(); $table->timestamps();
            $table->index(['type', 'payment_method']);
        });
        Schema::create('business_settings', function (Blueprint $table) {
            $table->id(); $table->string('business_name')->default('ServiceHub'); $table->string('logo_path')->nullable(); $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('wallet_transactions'); Schema::dropIfExists('business_settings'); }
};
