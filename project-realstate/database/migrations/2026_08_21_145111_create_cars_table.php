<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('name');                    // اسم ماشین - اجباری
            $table->string('owner_name');              // اسم صاحب ماشین - اجباری
            $table->string('owner_phone');             // شماره تماس صاحب ماشین - اجباری
            $table->text('description')->nullable();   // توضیحات - اختیاری
            $table->enum('status', ['active', 'archived'])->default('active');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};