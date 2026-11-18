<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organization_loan_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained('organization_loans')->onDelete('cascade');
            $table->integer('installment_number');
            $table->string('due_date');
            $table->decimal('amount', 15, 0);
            $table->boolean('is_paid')->default(false);
            $table->string('paid_date')->nullable();
            $table->foreignId('transaction_id')->nullable()->constrained('organization_transactions')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organization_loan_installments');
    }
};