<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class ExpenseController extends Controller
{
    // ===== لیست هزینه‌ها =====
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search');
        $type = $request->get('type');

        $query = Expense::query();

        // فقط هزینه‌های کاربر خودش را ببیند (مگر مستر)
        if (!$user->isMaster()) {
            $query->where('user_id', $user->id);
        }

        if ($search) {
            $query->search($search);
        }

        if ($type && in_array($type, ['income', 'expense'])) {
            $query->where('type', $type);
        }

        $expenses = $query->orderBy('transaction_date', 'desc')->paginate(20);

        // محاسبه جمع کل
        $totalIncome = $query->clone()->where('type', 'income')->sum('amount');
        $totalExpense = $query->clone()->where('type', 'expense')->sum('amount');
        $balance = $totalIncome - $totalExpense;

        return view('expenses.index', compact('expenses', 'totalIncome', 'totalExpense', 'balance', 'type', 'search'));
    }

    // ===== فرم ثبت هزینه جدید =====
    public function create()
    {
        return view('expenses.create');
    }

    // ===== ذخیره هزینه جدید =====
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:1',
            'transaction_date' => 'required|string|regex:/^\d{4}\/\d{2}\/\d{2}$/',
            'from_account' => 'nullable|string|max:255',
            'to_account' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'receipt_image' => 'nullable|image|max:2048',
        ]);

        $data = $request->only([
            'title',
            'type',
            'amount',
            'transaction_date',
            'from_account',
            'to_account',
            'description',
        ]);

        $data['user_id'] = Auth::id();

        if ($request->hasFile('receipt_image')) {
            $file = $request->file('receipt_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/receipts'), $filename);
            $data['receipt_image'] = 'uploads/receipts/' . $filename;
        }

        Expense::create($data);

        $typeText = $request->type === 'income' ? 'درآمد' : 'هزینه';
        return redirect()->route('expenses.index')
                        ->with('success', "{$typeText} با موفقیت ثبت شد.");
    }

    // ===== فرم ویرایش هزینه =====
    public function edit(Expense $expense)
    {
        $this->checkAccess($expense);
        return view('expenses.edit', compact('expense'));
    }

    // ===== به‌روزرسانی هزینه =====
    public function update(Request $request, Expense $expense)
    {
        $this->checkAccess($expense);

        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:1',
            'transaction_date' => 'required|string|regex:/^\d{4}\/\d{2}\/\d{2}$/',
            'from_account' => 'nullable|string|max:255',
            'to_account' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'receipt_image' => 'nullable|image|max:2048',
        ]);

        $data = $request->only([
            'title',
            'type',
            'amount',
            'transaction_date',
            'from_account',
            'to_account',
            'description',
        ]);

        if ($request->hasFile('receipt_image')) {
            if ($expense->receipt_image && file_exists(public_path($expense->receipt_image))) {
                unlink(public_path($expense->receipt_image));
            }
            
            $file = $request->file('receipt_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/receipts'), $filename);
            $data['receipt_image'] = 'uploads/receipts/' . $filename;
        }

        $expense->update($data);

        return redirect()->route('expenses.index')
                        ->with('success', 'با موفقیت ویرایش شد.');
    }

    // ===== حذف هزینه =====
    public function destroy(Expense $expense)
    {
        $this->checkAccess($expense);

        if ($expense->receipt_image && file_exists(public_path($expense->receipt_image))) {
            unlink(public_path($expense->receipt_image));
        }

        $expense->delete();

        return redirect()->route('expenses.index')
                        ->with('success', 'با موفقیت حذف شد.');
    }

    // ===== بررسی دسترسی =====
    private function checkAccess($expense)
    {
        $user = Auth::user();
        if (!$user->isMaster() && $expense->user_id !== $user->id) {
            abort(403, 'شما دسترسی به این آیتم را ندارید.');
        }
    }
}