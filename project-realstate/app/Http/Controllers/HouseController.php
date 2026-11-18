<?php

namespace App\Http\Controllers;

use App\Models\House;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HouseController extends Controller
{
    // ===== لیست منازل =====
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search');
        $status = $request->get('status', 'active');

        $query = House::query();

        if (!$user->isMaster()) {
            $houseIds = $user->getHouseIds('houses');
            if (empty($houseIds)) {
                return view('houses.index', ['houses' => collect([]), 'status' => $status, 'search' => $search]);
            }
            $query->whereIn('id', $houseIds);
        }

        if ($status === 'active') {
            $query->active();
        } elseif ($status === 'archived') {
            $query->archived();
        }

        if ($search) {
            $query->search($search);
        }

        $houses = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('houses.index', compact('houses', 'status', 'search'));
    }

    // ===== فرم ثبت منزل جدید =====
    public function create()
    {
        return view('houses.create');
    }

    // ===== ذخیره منزل جدید =====
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'owner_phone' => 'required|string|max:20',
            'address' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $house = House::create([
            'name' => $request->name,
            'owner_name' => $request->owner_name,
            'owner_phone' => $request->owner_phone,
            'address' => $request->address,
            'description' => $request->description,
            'user_id' => Auth::id(),
            'status' => 'active',
        ]);

        return redirect()->route('houses.index')->with('success', 'منزل با موفقیت ثبت شد.');
    }

    // ===== فرم ویرایش منزل =====
    public function edit(House $house)
    {
        $this->checkAccess($house);
        return view('houses.edit', compact('house'));
    }

    // ===== به‌روزرسانی منزل =====
    public function update(Request $request, House $house)
    {
        $this->checkAccess($house);

        $request->validate([
            'name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'owner_phone' => 'required|string|max:20',
            'address' => 'required|string',
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,archived',
        ]);

        $house->update([
            'name' => $request->name,
            'owner_name' => $request->owner_name,
            'owner_phone' => $request->owner_phone,
            'address' => $request->address,
            'description' => $request->description,
            'status' => $request->status ?? $house->status,
        ]);

        return redirect()->route('houses.index')->with('success', 'منزل با موفقیت ویرایش شد.');
    }

    // ===== حذف (آرشیو) منزل =====
    public function destroy(House $house)
    {
        $this->checkAccess($house);

        if ($house->isActive()) {
            $house->update(['status' => 'archived']);
            return redirect()->route('houses.index')->with('success', 'منزل با موفقیت آرشیو شد.');
        } else {
            $house->forceDelete();
            return redirect()->route('houses.index')->with('success', 'منزل با موفقیت حذف شد.');
        }
    }

    // ===== بازیابی از آرشیو =====
    public function restore($id)
    {
        $house = House::withTrashed()->findOrFail($id);
        $this->checkAccess($house);
        
        $house->restore();
        $house->update(['status' => 'active']);

        return redirect()->route('houses.index')->with('success', 'منزل با موفقیت بازیابی شد.');
    }

    // ===== بررسی دسترسی =====
    private function checkAccess($house)
    {
        $user = Auth::user();
        if (!$user->isMaster() && !$user->canAccessHouse($house->id)) {
            abort(403, 'شما دسترسی به این منزل را ندارید.');
        }
    }
}