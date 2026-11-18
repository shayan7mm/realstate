<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganizationLoan extends Model
{
    use HasFactory;

    protected $table = 'organization_loans';

    protected $fillable = [
        'organization_id',
        'user_id',
        'type',
        'total_amount',
        'monthly_amount',
        'start_date',
        'end_date',
        'total_months',
        'status',
    ];

    // ===== روابط =====
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function installments()
    {
        return $this->hasMany(OrganizationLoanInstallment::class, 'loan_id');
    }

    // ===== متدهای کمکی =====
    public function isLoanGiven()
    {
        return $this->type === 'loan_given';
    }

    public function isLoanTaken()
    {
        return $this->type === 'loan_taken';
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function getTransactionType()
    {
        return $this->isLoanGiven() ? 'income' : 'expense';
    }

    public function getPaidCount()
    {
        return $this->installments()->where('is_paid', true)->count();
    }

    public function getUnpaidCount()
    {
        return $this->installments()->where('is_paid', false)->count();
    }

    public function getTotalPaidAmount()
    {
        return $this->installments()->where('is_paid', true)->sum('amount');
    }

    public function getTotalUnpaidAmount()
    {
        return $this->installments()->where('is_paid', false)->sum('amount');
    }
}