<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // متدهای کمکی
    public function isMaster(): bool
    {
        return $this->role === 'master';
    }

    public function isAccountant(): bool
    {
        return $this->role === 'accountant';
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

     public function cars()
    {
        return $this->hasMany(Car::class);
    }

    public function transactions()
    {
        return $this->hasMany(CarTransaction::class);
    }

    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }

    // ===== متدهای دسترسی =====
    public function hasModuleAccess($module)
    {
        // مستر به همه چیز دسترسی دارد
        if ($this->isMaster()) {
            return true;
        }

        $permission = $this->permissions()->where('module', $module)->first();
        return $permission && $permission->access;
    }

    public function getCarIds($module = 'cars')
    {
        // مستر همه ماشین‌ها را می‌بیند
        if ($this->isMaster()) {
            return Car::pluck('id')->toArray();
        }

        $permission = $this->permissions()->where('module', $module)->first();
        
        if (!$permission || !$permission->access) {
            return [];
        }

        if ($permission->item_ids === null) {
            // دسترسی به همه ماشین‌ها
            return Car::pluck('id')->toArray();
        }

        return $permission->item_ids;
    }

    public function canAccessCar($carId)
    {
        // مستر به همه ماشین‌ها دسترسی دارد
        if ($this->isMaster()) {
            return true;
        }

        $carIds = $this->getCarIds('cars');
        return in_array($carId, $carIds);
    }


  

public function getHouseIds($module = 'houses')
{
    if ($this->isMaster()) {
        return House::pluck('id')->toArray();
    }

    $permission = $this->permissions()->where('module', $module)->first();
    
    if (!$permission || !$permission->access) {
        return [];
    }

    if ($permission->item_ids === null) {
        return House::pluck('id')->toArray();
    }

    return $permission->item_ids;
}

public function canAccessHouse($houseId)
{
    if ($this->isMaster()) {
        return true;
    }

    $houseIds = $this->getHouseIds('houses');
    return in_array($houseId, $houseIds);
}


// ===== متدهای دسترسی اشخاص =====
public function getPersonIds($module = 'persons')
{
    if ($this->isMaster()) {
        return Person::pluck('id')->toArray();
    }

    $permission = $this->permissions()->where('module', $module)->first();
    
    if (!$permission || !$permission->access) {
        return [];
    }

    if ($permission->item_ids === null) {
        return Person::pluck('id')->toArray();
    }

    return $permission->item_ids;
}

public function canAccessPerson($personId)
{
    if ($this->isMaster()) {
        return true;
    }

    $personIds = $this->getPersonIds('persons');
    return in_array($personId, $personIds);
}


// ===== متدهای دسترسی ارگان‌ها =====
public function getOrganizationIds($module = 'organizations')
{
    if ($this->isMaster()) {
        return Organization::pluck('id')->toArray();
    }

    $permission = $this->permissions()->where('module', $module)->first();
    
    if (!$permission || !$permission->access) {
        return [];
    }

    if ($permission->item_ids === null) {
        return Organization::pluck('id')->toArray();
    }

    return $permission->item_ids;
}

public function canAccessOrganization($organizationId)
{
    if ($this->isMaster()) {
        return true;
    }

    $organizationIds = $this->getOrganizationIds('organizations');
    return in_array($organizationId, $organizationIds);
}

}