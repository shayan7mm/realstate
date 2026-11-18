<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\CarTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class CarTransactionController extends Controller
{
    // ===== لیست تراکنش‌های یک ماشین =====
    public function index(Car $car, Request $request)
    {
        $this->checkAccess($car);

        $search = $request->get('search');
        $type = $request->get('type');

        $query = $car->transactions();

        if ($search) {
            $query->search($search);
        }

        if ($type && in_array($type, ['income', 'expense'])) {
            $query->where('type', $type);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')->paginate(20);

        $totalIncome = $car->totalIncome();
        $totalExpense = $car->totalExpense();
        $profit = $car->profit();

        return view('cars.transactions.index', compact('car', 'transactions', 'totalIncome', 'totalExpense', 'profit', 'type', 'search'));
    }

    // ===== فرم ثبت تراکنش جدید =====
    public function create(Car $car)
    {
        $this->checkAccess($car);
        return view('cars.transactions.create', compact('car'));
    }

    // ===== ذخیره تراکنش جدید =====
    public function store(Request $request, Car $car)
    {
        $this->checkAccess($car);

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
            'from_account',
            'to_account',
            'description',
        ]);

        // ذخیره تاریخ به صورت مستقیم (همان string که کاربر وارد کرده)
        $data['transaction_date'] = $request->transaction_date;
        $data['car_id'] = $car->id;
        $data['user_id'] = Auth::id();

        // آپلود تصویر فیش
        if ($request->hasFile('receipt_image')) {
            $file = $request->file('receipt_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/receipts'), $filename);
            $data['receipt_image'] = 'uploads/receipts/' . $filename;
        }

        CarTransaction::create($data);

        $typeText = $request->type === 'income' ? 'ورودی' : 'خروجی';
        return redirect()->route('cars.transactions.index', $car)
                        ->with('success', "تراکنش {$typeText} با موفقیت ثبت شد.");
    }

    // ===== نمایش جزئیات تراکنش =====
    public function show(Car $car, CarTransaction $transaction)
    {
        $this->checkAccess($car);
        return view('cars.transactions.show', compact('car', 'transaction'));
    }

    // ===== فرم ویرایش تراکنش =====
    public function edit(Car $car, CarTransaction $transaction)
    {
        $this->checkAccess($car);
        return view('cars.transactions.edit', compact('car', 'transaction'));
    }

    // ===== به‌روزرسانی تراکنش =====
    public function update(Request $request, Car $car, CarTransaction $transaction)
    {
        $this->checkAccess($car);

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
            'from_account',
            'to_account',
            'description',
        ]);

        // ذخیره تاریخ به صورت مستقیم
        $data['transaction_date'] = $request->transaction_date;

        // آپلود تصویر جدید
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

        return redirect()->route('cars.transactions.index', $car)
                        ->with('success', 'تراکنش با موفقیت ویرایش شد.');
    }

    // ===== حذف تراکنش =====
    public function destroy(Car $car, CarTransaction $transaction)
    {
        $this->checkAccess($car);

        // حذف تصویر
        if ($transaction->receipt_image && file_exists(public_path($transaction->receipt_image))) {
            unlink(public_path($transaction->receipt_image));
        }

        $transaction->delete();

        return redirect()->route('cars.transactions.index', $car)
                        ->with('success', 'تراکنش با موفقیت حذف شد.');
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