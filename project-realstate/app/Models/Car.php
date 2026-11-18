<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Car extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'owner_name',
        'owner_phone',
        'description',
        'status',
        'user_id',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    // ===== روابط =====
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(CarTransaction::class);
    }

    // ===== متدهای کمکی =====
    public function totalIncome()
    {
        return $this->transactions()->where('type', 'income')->sum('amount');
    }

    public function totalExpense()
    {
        return $this->transactions()->where('type', 'expense')->sum('amount');
    }

    public function profit()
    {
        return $this->totalIncome() - $this->totalExpense();
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isArchived()
    {
        return $this->status === 'archived';
    }

    // ===== اسکوپ‌ها =====
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('owner_name', 'LIKE', "%{$search}%")
              ->orWhere('owner_phone', 'LIKE', "%{$search}%");
        });
    }
}