<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrganizationController extends Controller
{
    // ===== لیست ارگان‌ها =====
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search');
        $status = $request->get('status', 'active');

        $query = Organization::query();

        // فیلتر بر اساس دسترسی (اگر مستر نباشد)
        if (!$user->isMaster()) {
            $organizationIds = $user->getOrganizationIds('organizations');
            if (empty($organizationIds)) {
                // برگرداندن paginate خالی به جای Collection
                $organizations = Organization::whereIn('id', [])->paginate(20);
                return view('organizations.index', compact('organizations', 'status', 'search'));
            }
            $query->whereIn('id', $organizationIds);
        }

        // فیلتر بر اساس وضعیت
        if ($status === 'active') {
            $query->active();
        } elseif ($status === 'archived') {
            $query->archived();
        }

        // جستجو
        if ($search) {
            $query->search($search);
        }

        // مرتب‌سازی و صفحه‌بندی
        $organizations = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('organizations.index', compact('organizations', 'status', 'search'));
    }

    // ===== فرم ثبت ارگان جدید =====
    public function create()
    {
        return view('organizations.create');
    }

    // ===== ذخیره ارگان جدید =====
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $organization = Organization::create([
            'name' => $request->name,
            'description' => $request->description,
            'user_id' => Auth::id(),
            'status' => 'active',
        ]);

        return redirect()->route('organizations.index')->with('success', 'ارگان با موفقیت ثبت شد.');
    }

    // ===== فرم ویرایش ارگان =====
    public function edit(Organization $organization)
    {
        $this->checkAccess($organization);
        return view('organizations.edit', compact('organization'));
    }

    // ===== به‌روزرسانی ارگان =====
    public function update(Request $request, Organization $organization)
    {
        $this->checkAccess($organization);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,archived',
        ]);

        $organization->update([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status ?? $organization->status,
        ]);

        return redirect()->route('organizations.index')->with('success', 'ارگان با موفقیت ویرایش شد.');
    }

    // ===== حذف (آرشیو) ارگان =====
    public function destroy(Organization $organization)
    {
        $this->checkAccess($organization);

        if ($organization->isActive()) {
            $organization->update(['status' => 'archived']);
            return redirect()->route('organizations.index')->with('success', 'ارگان با موفقیت آرشیو شد.');
        } else {
            // حذف فیزیکی
            $organization->forceDelete();
            return redirect()->route('organizations.index')->with('success', 'ارگان با موفقیت حذف شد.');
        }
    }

    // ===== بازیابی از آرشیو =====
    public function restore($id)
    {
        $organization = Organization::withTrashed()->findOrFail($id);
        $this->checkAccess($organization);
        
        $organization->restore();
        $organization->update(['status' => 'active']);

        return redirect()->route('organizations.index')->with('success', 'ارگان با موفقیت بازیابی شد.');
    }

    // ===== تغییر وضعیت =====
    public function toggleStatus(Organization $organization)
    {
        $this->checkAccess($organization);

        $newStatus = $organization->isActive() ? 'archived' : 'active';
        $organization->update(['status' => $newStatus]);

        $message = $organization->isActive() ? 'ارگان فعال شد.' : 'ارگان آرشیو شد.';
        return redirect()->route('organizations.index')->with('success', $message);
    }

    // ===== بررسی دسترسی =====
    private function checkAccess($organization)
    {
        $user = Auth::user();
        if (!$user->isMaster() && !$user->canAccessOrganization($organization->id)) {
            abort(403, 'شما دسترسی به این ارگان را ندارید.');
        }
    }
}