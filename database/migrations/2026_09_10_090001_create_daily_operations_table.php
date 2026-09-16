<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_operations', function (Blueprint $table) {
            $table->id();
            $table->date('business_date')->unique();
            $table->foreignId('opened_by')->constrained('users');
            $table->timestamp('opened_at');
            $table->text('opening_notes')->nullable();
            $table->foreignId('closed_by')->nullable()->constrained('users');
            $table->timestamp('closed_at')->nullable();
            $table->text('closing_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_operations');
    }
};
