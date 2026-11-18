<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'organizations';

    protected $fillable = [
        'name',
        'description',
        'status',
        'user_id',
    ];

    // ===== روابط =====
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(OrganizationTransaction::class);
    }

    public function loans()
    {
        return $this->hasMany(OrganizationLoan::class);
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
        return $query->where('name', 'LIKE', "%{$search}%");
    }
}