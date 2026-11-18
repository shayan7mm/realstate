<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserManagementController extends Controller
{
    /**
     * نمایش لیست تمام حسابداران
     */
    public function index()
    {
        $users = User::where('role', 'accountant')
                    ->orderBy('created_at', 'desc')
                    ->paginate(15);
        
        return view('master.users', compact('users'));
    }

    /**
     * نمایش فرم ثبت‌نام حسابدار جدید
     */
    public function create()
    {
        // فقط مستر می‌تواند به این صفحه دسترسی داشته باشد
        if (!Auth::check() || !Auth::user()->isMaster()) {
            abort(403, 'شما دسترسی به این بخش را ندارید.');
        }
        
        return view('master.create-user');
    }

    /**
     * ذخیره حسابدار جدید در دیتابیس
     */
    public function store(Request $request)
    {
        // فقط مستر می‌تواند ثبت‌نام کند
        if (!Auth::check() || !Auth::user()->isMaster()) {
            abort(403, 'شما دسترسی به این بخش را ندارید.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'accountant',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        return redirect()->route('master.users')
                        ->with('success', "حسابدار «{$user->name}» با موفقیت ایجاد شد.");
    }

    /**
     * نمایش فرم ویرایش حسابدار
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        
        // جلوگیری از ویرایش مستر
        if ($user->isMaster()) {
            abort(403, 'شما نمی‌توانید اطلاعات مستر را ویرایش کنید.');
        }
        
        return view('master.edit-user', compact('user'));
    }

    /**
     * به‌روزرسانی اطلاعات حسابدار
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        // جلوگیری از ویرایش مستر
        if ($user->isMaster()) {
            abort(403, 'شما نمی‌توانید اطلاعات مستر را ویرایش کنید.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['required', 'string', 'max:20', Rule::unique('users')->ignore($user->id)],
            'is_active' => 'required|boolean',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = $request->only(['name', 'email', 'phone', 'is_active']);
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('master.users')
                        ->with('success', "اطلاعات حسابدار «{$user->name}» با موفقیت به‌روزرسانی شد.");
    }

    /**
     * حذف حسابدار
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // جلوگیری از حذف مستر
        if ($user->isMaster()) {
            abort(403, 'شما نمی‌توانید مستر را حذف کنید.');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('master.users')
                        ->with('success', "حسابدار «{$userName}» با موفقیت حذف شد.");
    }

    /**
     * نمایش لیست حسابداران حذف شده (سطل زباله)
     */
    public function trashed()
    {
        $users = User::where('role', 'accountant')
                    ->onlyTrashed()
                    ->orderBy('deleted_at', 'desc')
                    ->paginate(15);
        
        return view('master.trashed-users', compact('users'));
    }

    /**
     * بازیابی حسابدار حذف شده
     */
    public function restore($id)
    {
        $user = User::where('role', 'accountant')
                    ->withTrashed()
                    ->findOrFail($id);
        
        $user->restore();

        return redirect()->route('master.users')
                        ->with('success', "حسابدار «{$user->name}» با موفقیت بازیابی شد.");
    }

    /**
     * حذف دائمی حسابدار
     */
    public function forceDelete($id)
    {
        $user = User::where('role', 'accountant')
                    ->withTrashed()
                    ->findOrFail($id);
        
        $userName = $user->name;
        $user->forceDelete();

        return redirect()->route('master.users')
                        ->with('success', "حسابدار «{$userName}» به طور دائمی حذف شد.");
    }

    /**
     * تغییر وضعیت فعال/غیرفعال حسابدار
     */
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        
        // جلوگیری از تغییر وضعیت مستر
        if ($user->isMaster()) {
            abort(403, 'شما نمی‌توانید وضعیت مستر را تغییر دهید.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'فعال' : 'غیرفعال';
        return redirect()->route('master.users')
                        ->with('success', "وضعیت حسابدار «{$user->name}» به «{$status}» تغییر یافت.");
    }

    /**
     * جستجوی حسابداران
     */
    public function search(Request $request)
    {
        $search = $request->get('search');
        
        $users = User::where('role', 'accountant')
                    ->where(function($query) use ($search) {
                        $query->where('name', 'LIKE', "%{$search}%")
                              ->orWhere('email', 'LIKE', "%{$search}%")
                              ->orWhere('phone', 'LIKE', "%{$search}%");
                    })
                    ->orderBy('created_at', 'desc')
                    ->paginate(15);
        
        return view('master.users', compact('users'));
    }

    /**
     * نمایش اطلاعات یک حسابدار (جزئیات)
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->isMaster()) {
            abort(403, 'شما نمی‌توانید اطلاعات مستر را مشاهده کنید.');
        }
        
        return view('master.show-user', compact('user'));
    }
}