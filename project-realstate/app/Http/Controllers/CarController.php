<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\CarTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CarController extends Controller
{
    // ===== لیست ماشین‌ها =====
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search');
        $status = $request->get('status', 'active');

        $query = Car::query();

        // فیلتر بر اساس دسترسی
        if (!$user->isMaster()) {
            $carIds = $user->getCarIds('cars');
            if (empty($carIds)) {
                return view('cars.index', ['cars' => collect([]), 'status' => $status, 'search' => $search]);
            }
            $query->whereIn('id', $carIds);
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

        $cars = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('cars.index', compact('cars', 'status', 'search'));
    }

    // ===== فرم ثبت ماشین جدید =====
    public function create()
    {
        return view('cars.create');
    }

    // ===== ذخیره ماشین جدید =====
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'owner_phone' => 'required|string|max:20',
            'description' => 'nullable|string',
        ]);

        $car = Car::create([
            'name' => $request->name,
            'owner_name' => $request->owner_name,
            'owner_phone' => $request->owner_phone,
            'description' => $request->description,
            'user_id' => Auth::id(),
            'status' => 'active',
        ]);

        return redirect()->route('cars.index')->with('success', 'ماشین با موفقیت ثبت شد.');
    }

    // ===== نمایش جزئیات ماشین =====
    public function show(Car $car)
    {
        $this->checkAccess($car);
        return view('cars.show', compact('car'));
    }

    // ===== فرم ویرایش ماشین =====
    public function edit(Car $car)
    {
        $this->checkAccess($car);
        return view('cars.edit', compact('car'));
    }

    // ===== به‌روزرسانی ماشین =====
    public function update(Request $request, Car $car)
    {
        $this->checkAccess($car);

        $request->validate([
            'name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'owner_phone' => 'required|string|max:20',
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,archived',
        ]);

        $car->update([
            'name' => $request->name,
            'owner_name' => $request->owner_name,
            'owner_phone' => $request->owner_phone,
            'description' => $request->description,
            'status' => $request->status ?? $car->status,
        ]);

        return redirect()->route('cars.index')->with('success', 'ماشین با موفقیت ویرایش شد.');
    }

    // ===== حذف (آرشیو) ماشین =====
    public function destroy(Car $car)
    {
        $this->checkAccess($car);

        if ($car->isActive()) {
            $car->update(['status' => 'archived']);
            return redirect()->route('cars.index')->with('success', 'ماشین با موفقیت آرشیو شد.');
        } else {
            $car->forceDelete();
            return redirect()->route('cars.index')->with('success', 'ماشین با موفقیت حذف شد.');
        }
    }

    // ===== بازیابی از آرشیو =====
    public function restore($id)
    {
        $car = Car::withTrashed()->findOrFail($id);
        $this->checkAccess($car);
        
        $car->restore();
        $car->update(['status' => 'active']);

        return redirect()->route('cars.index')->with('success', 'ماشین با موفقیت بازیابی شد.');
    }

    // ===== تغییر وضعیت =====
    public function toggleStatus(Car $car)
    {
        $this->checkAccess($car);

        $newStatus = $car->isActive() ? 'archived' : 'active';
        $car->update(['status' => $newStatus]);

        $message = $car->isActive() ? 'ماشین فعال شد.' : 'ماشین آرشیو شد.';
        return redirect()->route('cars.index')->with('success', $message);
    }

    // ===== بررسی دسترسی =====
    private function checkAccess($car)
    {
        $user = Auth::user();
        if (!$user->isMaster() && !$user->canAccessCar($car->id)) {
            abort(403, 'شما دسترسی به این ماشین را ندارید.');
        }
    }
}