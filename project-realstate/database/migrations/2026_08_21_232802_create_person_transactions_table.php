<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('person_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('person_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('type', ['income', 'expense']);
            $table->decimal('amount', 15, 0);
            $table->string('transaction_date');
            $table->string('from_account')->nullable();
            $table->string('to_account')->nullable();
            $table->string('receipt_image')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // اضافه کردن foreign key ها به صورت جداگانه
            $table->foreign('person_id')->references('id')->on('persons')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('person_transactions');
    }
};