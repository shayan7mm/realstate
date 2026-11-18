<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PersonController extends Controller
{
    // ===== لیست اشخاص =====
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search');
        $status = $request->get('status', 'active');

        $query = Person::query();

        if (!$user->isMaster()) {
            $personIds = $user->getPersonIds('persons');
            if (empty($personIds)) {
                return view('persons.index', ['persons' => collect([]), 'status' => $status, 'search' => $search]);
            }
            $query->whereIn('id', $personIds);
        }

        if ($status === 'active') {
            $query->active();
        } elseif ($status === 'archived') {
            $query->archived();
        }

        if ($search) {
            $query->search($search);
        }

        $persons = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('persons.index', compact('persons', 'status', 'search'));
    }

    // ===== فرم ثبت شخص جدید =====
    public function create()
    {
        return view('persons.create');
    }

    // ===== ذخیره شخص جدید =====
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $person = Person::create([
            'name' => $request->name,
            'description' => $request->description,
            'user_id' => Auth::id(),
            'status' => 'active',
        ]);

        return redirect()->route('persons.index')->with('success', 'شخص با موفقیت ثبت شد.');
    }

    // ===== فرم ویرایش شخص =====
    public function edit(Person $person)
    {
        $this->checkAccess($person);
        return view('persons.edit', compact('person'));
    }

    // ===== به‌روزرسانی شخص =====
    public function update(Request $request, Person $person)
    {
        $this->checkAccess($person);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,archived',
        ]);

        $person->update([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status ?? $person->status,
        ]);

        return redirect()->route('persons.index')->with('success', 'شخص با موفقیت ویرایش شد.');
    }

    // ===== حذف (آرشیو) شخص =====
    public function destroy(Person $person)
    {
        $this->checkAccess($person);

        if ($person->isActive()) {
            $person->update(['status' => 'archived']);
            return redirect()->route('persons.index')->with('success', 'شخص با موفقیت آرشیو شد.');
        } else {
            $person->forceDelete();
            return redirect()->route('persons.index')->with('success', 'شخص با موفقیت حذف شد.');
        }
    }

    // ===== بازیابی از آرشیو =====
    public function restore($id)
    {
        $person = Person::withTrashed()->findOrFail($id);
        $this->checkAccess($person);
        
        $person->restore();
        $person->update(['status' => 'active']);

        return redirect()->route('persons.index')->with('success', 'شخص با موفقیت بازیابی شد.');
    }

    // ===== بررسی دسترسی =====
    private function checkAccess($person)
    {
        $user = Auth::user();
        if (!$user->isMaster() && !$user->canAccessPerson($person->id)) {
            abort(403, 'شما دسترسی به این شخص را ندارید.');
        }
    }
}