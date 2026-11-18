<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'module',
        'access',
        'item_ids',
    ];

    protected $casts = [
        'access' => 'boolean',
        'item_ids' => 'array',
    ];

    // ===== روابط =====
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ===== متدهای کمکی =====
    public function hasAccessToItem($itemId)
    {
        if ($this->access && $this->item_ids === null) {
            return true; // دسترسی به همه آیتم‌ها
        }

        if ($this->access && is_array($this->item_ids)) {
            return in_array($itemId, $this->item_ids);
        }

        return false;
    }

    public function getAllowedItemIds()
    {
        if ($this->access && $this->item_ids === null) {
            return 'all'; // همه آیتم‌ها
        }

        if ($this->access && is_array($this->item_ids)) {
            return $this->item_ids;
        }

        return [];
    }
}