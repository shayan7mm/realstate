<?php

namespace App\Http\Controllers;

use App\Models\House;
use App\Models\HouseTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class HouseTransactionController extends Controller
{
    // ===== لیست تراکنش‌های یک منزل =====
    public function index(House $house, Request $request)
    {
        $this->checkAccess($house);

        $search = $request->get('search');
        $type = $request->get('type');

        $query = $house->transactions();

        if ($search) {
            $query->search($search);
        }

        if ($type && in_array($type, ['income', 'expense'])) {
            $query->where('type', $type);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')->paginate(20);

        $totalIncome = $house->totalIncome();
        $totalExpense = $house->totalExpense();
        $profit = $house->profit();

        return view('houses.transactions.index', compact('house', 'transactions', 'totalIncome', 'totalExpense', 'profit', 'type', 'search'));
    }

    // ===== فرم ثبت تراکنش جدید =====
    public function create(House $house)
    {
        $this->checkAccess($house);
        return view('houses.transactions.create', compact('house'));
    }

    // ===== ذخیره تراکنش جدید =====
    public function store(Request $request, House $house)
    {
        $this->checkAccess($house);

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

        $data['house_id'] = $house->id;
        $data['user_id'] = Auth::id();

        if ($request->hasFile('receipt_image')) {
            $file = $request->file('receipt_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/receipts'), $filename);
            $data['receipt_image'] = 'uploads/receipts/' . $filename;
        }

        HouseTransaction::create($data);

        $typeText = $request->type === 'income' ? 'ورودی' : 'خروجی';
        return redirect()->route('houses.transactions.index', $house)
                        ->with('success', "تراکنش {$typeText} با موفقیت ثبت شد.");
    }

    // ===== فرم ویرایش تراکنش =====
    public function edit(House $house, HouseTransaction $transaction)
    {
        $this->checkAccess($house);
        return view('houses.transactions.edit', compact('house', 'transaction'));
    }

    // ===== به‌روزرسانی تراکنش =====
    public function update(Request $request, House $house, HouseTransaction $transaction)
    {
        $this->checkAccess($house);

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

        return redirect()->route('houses.transactions.index', $house)
                        ->with('success', 'تراکنش با موفقیت ویرایش شد.');
    }

    // ===== حذف تراکنش =====
    public function destroy(House $house, HouseTransaction $transaction)
    {
        $this->checkAccess($house);

        if ($transaction->receipt_image && file_exists(public_path($transaction->receipt_image))) {
            unlink(public_path($transaction->receipt_image));
        }

        $transaction->delete();

        return redirect()->route('houses.transactions.index', $house)
                        ->with('success', 'تراکنش با موفقیت حذف شد.');
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