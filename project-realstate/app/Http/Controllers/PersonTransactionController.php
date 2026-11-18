<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\PersonTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class PersonTransactionController extends Controller
{
    // ===== لیست تراکنش‌های یک شخص =====
    public function index(Person $person, Request $request)
    {
        $this->checkAccess($person);

        $search = $request->get('search');
        $type = $request->get('type');

        $query = $person->transactions();

        if ($search) {
            $query->search($search);
        }

        if ($type && in_array($type, ['income', 'expense'])) {
            $query->where('type', $type);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')->paginate(20);

        $totalIncome = $person->totalIncome();
        $totalExpense = $person->totalExpense();
        $profit = $person->profit();

        return view('persons.transactions.index', compact('person', 'transactions', 'totalIncome', 'totalExpense', 'profit', 'type', 'search'));
    }

    // ===== فرم ثبت تراکنش جدید =====
    public function create(Person $person)
    {
        $this->checkAccess($person);
        return view('persons.transactions.create', compact('person'));
    }

    // ===== ذخیره تراکنش جدید =====
    public function store(Request $request, Person $person)
    {
        $this->checkAccess($person);

        $request->validate([
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:1',
            'transaction_date' => 'required|string|regex:/^\d{4}\/\d{2}\/\d{2}$/',
            'from_account' => 'nullable|string|max:255',
            'to_account' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'receipt_image' => 'nullable|image|max:2048',
        ]);

        $data = $request->only([
            'type',
            'amount',
            'transaction_date',
            'from_account',
            'to_account',
            'description',
        ]);

        $data['person_id'] = $person->id;
        $data['user_id'] = Auth::id();

        if ($request->hasFile('receipt_image')) {
            $file = $request->file('receipt_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/receipts'), $filename);
            $data['receipt_image'] = 'uploads/receipts/' . $filename;
        }

        PersonTransaction::create($data);

        $typeText = $request->type === 'income' ? 'ورودی' : 'خروجی';
        return redirect()->route('persons.transactions.index', $person)
                        ->with('success', "تراکنش {$typeText} با موفقیت ثبت شد.");
    }

    // ===== فرم ویرایش تراکنش =====
    public function edit(Person $person, PersonTransaction $transaction)
    {
        $this->checkAccess($person);
        return view('persons.transactions.edit', compact('person', 'transaction'));
    }

    // ===== به‌روزرسانی تراکنش =====
    public function update(Request $request, Person $person, PersonTransaction $transaction)
    {
        $this->checkAccess($person);

        $request->validate([
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:1',
            'transaction_date' => 'required|string|regex:/^\d{4}\/\d{2}\/\d{2}$/',
            'from_account' => 'nullable|string|max:255',
            'to_account' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'receipt_image' => 'nullable|image|max:2048',
        ]);

        $data = $request->only([
            'type',
            'amount',
            'transaction_date',
            'from_account',
            'to_account',
            'description',
        ]);

        if ($request->hasFile('receipt_image')) {
            if ($transaction->receipt_image && file_exists(public_path($transaction->receipt_image))) {
                unlink(public_path($transaction->receipt_image));
            }
            
            $file = $request->file('receipt_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/receipts'), $filename);
            $data['receipt_image'] = 'uploads/receipts/' . $filename;
        }

        $transaction->update($data);

        return redirect()->route('persons.transactions.index', $person)
                        ->with('success', 'تراکنش با موفقیت ویرایش شد.');
    }

    // ===== حذف تراکنش =====
    public function destroy(Person $person, PersonTransaction $transaction)
    {
        $this->checkAccess($person);

        if ($transaction->receipt_image && file_exists(public_path($transaction->receipt_image))) {
            unlink(public_path($transaction->receipt_image));
        }

        $transaction->delete();

        return redirect()->route('persons.transactions.index', $person)
                        ->with('success', 'تراکنش با موفقیت حذف شد.');
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