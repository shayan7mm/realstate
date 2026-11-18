<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganizationLoanInstallment extends Model
{
    use HasFactory;

    protected $table = 'organization_loan_installments';

    protected $fillable = [
        'loan_id',
        'installment_number',
        'due_date',
        'amount',
        'is_paid',
        'paid_date',
        'transaction_id',
    ];

    protected $casts = [
        'is_paid' => 'boolean',
    ];

    // ===== روابط =====
    public function loan()
    {
        return $this->belongsTo(OrganizationLoan::class, 'loan_id');
    }

    public function transaction()
    {
        return $this->belongsTo(OrganizationTransaction::class, 'transaction_id');
    }
}