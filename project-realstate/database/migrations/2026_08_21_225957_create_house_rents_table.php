<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('house_rents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('house_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['rent_out', 'rent_in']); // اجاره داده / اجاره گرفته
            $table->decimal('monthly_amount', 15, 0);
            $table->string('start_date'); // تاریخ شروع (شمسی)
            $table->string('end_date')->nullable(); // تاریخ پایان (شمسی)
            $table->integer('total_months')->default(12);
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('house_rents');
    }
};