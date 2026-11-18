<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HouseRentInstallment extends Model
{
    use HasFactory;

    protected $fillable = [
        'rent_id',
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
    public function rent()
    {
        return $this->belongsTo(HouseRent::class, 'rent_id'); // ← اصلاح شد
    }

    public function transaction()
    {
        return $this->belongsTo(HouseTransaction::class, 'transaction_id');
    }
}