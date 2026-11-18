<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HouseRent extends Model
{
    use HasFactory;

    protected $fillable = [
        'house_id',
        'user_id',
        'type',
        'monthly_amount',
        'start_date',
        'end_date',
        'total_months',
        'status',
    ];

    // ===== روابط =====
    public function house()
    {
        return $this->belongsTo(House::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function installments()
    {
        return $this->hasMany(HouseRentInstallment::class, 'rent_id'); // ← اصلاح شد
    }

    // ===== متدهای کمکی =====
    public function isRentOut()
    {
        return $this->type === 'rent_out';
    }

    public function isRentIn()
    {
        return $this->type === 'rent_in';
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
        return $this->isRentOut() ? 'income' : 'expense';
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