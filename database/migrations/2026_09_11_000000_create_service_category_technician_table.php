<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_category_technician', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('technician_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['service_category_id', 'technician_id'], 'sc_technician_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_category_technician');
    }
};
