<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrganizationTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'organization_transactions';

    protected $fillable = [
        'organization_id',
        'user_id',
        'type',
        'amount',
        'transaction_date',
        'from_account',
        'to_account',
        'receipt_image',
        'description',
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

    public function installment()
    {
        return $this->hasOne(OrganizationLoanInstallment::class, 'transaction_id');
    }

    // ===== متدهای کمکی =====
    public function isIncome()
    {
        return $this->type === 'income';
    }

    public function isExpense()
    {
        return $this->type === 'expense';
    }

    // ===== اسکوپ‌ها =====
    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('amount', 'LIKE', "%{$search}%")
              ->orWhere('description', 'LIKE', "%{$search}%")
              ->orWhere('from_account', 'LIKE', "%{$search}%")
              ->orWhere('to_account', 'LIKE', "%{$search}%");
        });
    }
}