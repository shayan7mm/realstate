<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Hekmatinasser\Verta\Verta;

class TodoController extends Controller
{
    // ===== لیست کارها =====
    public function index(Request $request)
    {
        if (!Auth::user()->isMaster()) {
            abort(403, 'شما دسترسی به این بخش را ندارید.');
        }

        $search = $request->get('search');
        $filter = $request->get('filter', 'all');

        $query = Todo::where('user_id', Auth::id());

        if ($search) {
            $query->search($search);
        }

        if ($filter === 'pending') {
            $query->pending();
        } elseif ($filter === 'completed') {
            $query->completed();
        }

        $todos = $query->orderBy('created_at', 'desc')->paginate(20);

        $totalPending = Todo::where('user_id', Auth::id())->pending()->count();
        $totalCompleted = Todo::where('user_id', Auth::id())->completed()->count();
        $totalAll = Todo::where('user_id', Auth::id())->count();

        return view('master.todos.index', compact('todos', 'totalPending', 'totalCompleted', 'totalAll', 'filter', 'search'));
    }

    public function create()
    {
        if (!Auth::user()->isMaster()) {
            abort(403, 'شما دسترسی به این بخش را ندارید.');
        }

        return view('master.todos.create');
    }

    // ===== ذخیره کار جدید (ساده شده) =====
    public function store(Request $request)
    {
        if (!Auth::user()->isMaster()) {
            abort(403, 'شما دسترسی به این بخش را ندارید.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|string|max:50', // فقط string ساده
        ]);

        $data = [
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'is_completed' => false,
        ];

        if ($request->due_date && $request->due_date !== '') {
            $data['due_date'] = $request->due_date;
        }

        Todo::create($data);

        return redirect()->route('master.todos.index')->with('success', 'کار با موفقیت ثبت شد.');
    }

    public function edit(Todo $todo)
    {
        if (!Auth::user()->isMaster()) {
            abort(403, 'شما دسترسی به این بخش را ندارید.');
        }

        $this->checkAccess($todo);
        return view('master.todos.edit', compact('todo'));
    }

    // ===== به‌روزرسانی کار (ساده شده) =====
    public function update(Request $request, Todo $todo)
    {
        if (!Auth::user()->isMaster()) {
            abort(403, 'شما دسترسی به این بخش را ندارید.');
        }

        $this->checkAccess($todo);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|string|max:50', // فقط string ساده
        ]);

        $data = [
            'title' => $request->title,
            'description' => $request->description,
        ];

        if ($request->due_date && $request->due_date !== '') {
            $data['due_date'] = $request->due_date;
        } else {
            $data['due_date'] = null;
        }

        $todo->update($data);

        return redirect()->route('master.todos.index')->with('success', 'کار با موفقیت ویرایش شد.');
    }

    public function destroy(Todo $todo)
    {
        if (!Auth::user()->isMaster()) {
            abort(403, 'شما دسترسی به این بخش را ندارید.');
        }

        $this->checkAccess($todo);
        $todo->delete();

        return redirect()->route('master.todos.index')->with('success', 'کار با موفقیت حذف شد.');
    }

    public function toggleStatus(Todo $todo)
    {
        if (!Auth::user()->isMaster()) {
            abort(403, 'شما دسترسی به این بخش را ندارید.');
        }

        $this->checkAccess($todo);

        $todo->is_completed = !$todo->is_completed;
        $todo->completed_at = $todo->is_completed ? now() : null;
        $todo->save();

        $status = $todo->is_completed ? 'انجام شد' : 'برگشت به لیست کارها';
        return redirect()->route('master.todos.index')->with('success', "وضعیت کار به «{$status}» تغییر یافت.");
    }

    private function checkAccess($todo)
    {
        if ($todo->user_id !== Auth::id()) {
            abort(403, 'شما دسترسی به این کار را ندارید.');
        }
    }
}