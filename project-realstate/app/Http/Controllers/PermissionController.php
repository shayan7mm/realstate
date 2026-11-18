<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Permission;
use App\Models\Car;
use App\Models\House;
use App\Models\Person;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    // ===== بررسی دسترسی =====
    private function checkMaster()
    {
        $user = Auth::user();
        
        if (!$user || !$user->isMaster()) {
            abort(403, 'شما دسترسی به این بخش را ندارید.');
        }
    }

    // ===== لیست دسترسی‌های یک کاربر =====
    public function index(User $user)
    {
        $this->checkMaster(); // ← بررسی دسترسی

        if ($user->isMaster()) {
            abort(403, 'شما نمی‌توانید دسترسی‌های مستر را ویرایش کنید.');
        }

        $permissions = $user->permissions;
        $modules = $this->getModules();

        return view('permissions.index', compact('user', 'permissions', 'modules'));
    }

    // ===== فرم تنظیم دسترسی =====
    public function edit(User $user)
    {
        $this->checkMaster(); // ← بررسی دسترسی

        if ($user->isMaster()) {
            abort(403, 'شما نمی‌توانید دسترسی‌های مستر را ویرایش کنید.');
        }

        $permissions = $user->permissions;
        $modules = $this->getModules();

        $items = [
            'cars' => Car::where('user_id', $user->id)->orWhere('user_id', Auth::id())->get(['id', 'name']),
            'houses' => House::where('user_id', $user->id)->orWhere('user_id', Auth::id())->get(['id', 'name']),
            'persons' => Person::where('user_id', $user->id)->orWhere('user_id', Auth::id())->get(['id', 'name']),
            'organizations' => Organization::where('user_id', $user->id)->orWhere('user_id', Auth::id())->get(['id', 'name']),
            'expenses' => collect([]),
        ];

        return view('permissions.edit', compact('user', 'permissions', 'modules', 'items'));
    }

    // ===== ذخیره دسترسی‌ها =====
    public function update(Request $request, User $user)
    {
        $this->checkMaster(); // ← بررسی دسترسی

        if ($user->isMaster()) {
            abort(403, 'شما نمی‌توانید دسترسی‌های مستر را ویرایش کنید.');
        }

        $request->validate([
            'permissions' => 'required|array',
            'permissions.*.module' => 'required|string',
            'permissions.*.access' => 'required|boolean',
            'permissions.*.item_ids' => 'nullable|array',
        ]);

        $user->permissions()->delete();

        foreach ($request->permissions as $perm) {
            $data = [
                'user_id' => $user->id,
                'module' => $perm['module'],
                'access' => $perm['access'],
            ];

            if (isset($perm['item_ids']) && is_array($perm['item_ids']) && count($perm['item_ids']) > 0) {
                $data['item_ids'] = $perm['item_ids'];
            } else {
                $data['item_ids'] = null;
            }

            Permission::create($data);
        }

        return redirect()->route('permissions.index', $user)
                        ->with('success', 'دسترسی‌ها با موفقیت به‌روزرسانی شد.');
    }

    // ===== حذف یک دسترسی =====
    public function destroy(Permission $permission)
    {
        $this->checkMaster(); // ← بررسی دسترسی

        $user = $permission->user;

        if ($user->isMaster()) {
            abort(403, 'شما نمی‌توانید دسترسی‌های مستر را ویرایش کنید.');
        }

        $permission->delete();

        return back()->with('success', 'دسترسی با موفقیت حذف شد.');
    }

    // ===== لیست ماژول‌های موجود =====
    private function getModules()
    {
        return [
            'cars' => '🚗 ماشین‌ها',
            'houses' => '🏠 منازل',
            'persons' => '👤 اشخاص',
            'organizations' => '🏦 ارگان‌ها',
            'expenses' => '💰 هزینه‌های جانبی',
        ];
    }
}