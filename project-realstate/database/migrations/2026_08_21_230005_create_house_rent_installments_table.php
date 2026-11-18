<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('house_rent_installments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rent_id');
            $table->integer('installment_number');
            $table->string('due_date');
            $table->decimal('amount', 15, 0);
            $table->boolean('is_paid')->default(false);
            $table->string('paid_date')->nullable();
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->timestamps();

            // اضافه کردن foreign key ها به صورت جداگانه
            $table->foreign('rent_id')->references('id')->on('house_rents')->onDelete('cascade');
            $table->foreign('transaction_id')->references('id')->on('house_transactions')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('house_rent_installments');
    }
};