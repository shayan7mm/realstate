<?php

namespace App\Http\Controllers;

use App\Models\House;
use App\Models\HouseRent;
use App\Models\HouseRentInstallment;
use App\Models\HouseTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Hekmatinasser\Verta\Verta;

class HouseRentController extends Controller
{
    // ===== لیست اجاره‌های یک منزل =====
    public function index(House $house)
    {
        $this->checkAccess($house);
        
        $rents = $house->rents()->orderBy('created_at', 'desc')->paginate(20);
        
        return view('houses.rents.index', compact('house', 'rents'));
    }

    // ===== فرم ثبت اجاره جدید =====
    public function create(House $house)
    {
        $this->checkAccess($house);
        return view('houses.rents.create', compact('house'));
    }

    // ===== ذخیره اجاره جدید =====
    public function store(Request $request, House $house)
    {
        $this->checkAccess($house);

        $request->validate([
            'type' => 'required|in:rent_out,rent_in',
            'monthly_amount' => 'required|numeric|min:1',
            'start_date' => 'required|string|regex:/^\d{4}\/\d{2}\/\d{2}$/',
            'total_months' => 'required|integer|min:1|max:60',
        ]);

        // محاسبه تاریخ پایان
        $startDate = Verta::parseFormat('Y/m/d', $request->start_date);
        $endDate = $startDate->addMonths($request->total_months - 1);

        $rent = HouseRent::create([
            'house_id' => $house->id,
            'user_id' => Auth::id(),
            'type' => $request->type,
            'monthly_amount' => $request->monthly_amount,
            'start_date' => $request->start_date,
            'end_date' => $endDate->format('Y/m/d'),
            'total_months' => $request->total_months,
            'status' => 'active',
        ]);

        // تولید خودکار اقساط
        $this->generateInstallments($rent);

        $typeText = $request->type === 'rent_out' ? 'اجاره داده شده' : 'اجاره گرفته شده';
        return redirect()->route('houses.rents.index', $house)
                        ->with('success', "اجاره با موفقیت ثبت شد. ({$typeText})");
    }

    // ===== تولید اقساط =====
    private function generateInstallments(HouseRent $rent)
    {
        $startDate = Verta::parseFormat('Y/m/d', $rent->start_date);
        $amount = $rent->monthly_amount;

        for ($i = 1; $i <= $rent->total_months; $i++) {
            // تاریخ هر قسط
            $dueDate = $startDate->addMonths($i - 1)->format('Y/m/d');
            
            HouseRentInstallment::create([
                'rent_id' => $rent->id,
                'installment_number' => $i,
                'due_date' => $dueDate,
                'amount' => $amount,
                'is_paid' => false,
            ]);
        }
    }

    // ===== فرم ویرایش اجاره =====
    public function edit(House $house, HouseRent $rent)
    {
        $this->checkAccess($house);
        return view('houses.rents.edit', compact('house', 'rent'));
    }

    // ===== به‌روزرسانی اجاره =====
    public function update(Request $request, House $house, HouseRent $rent)
    {
        $this->checkAccess($house);

        $request->validate([
            'type' => 'required|in:rent_out,rent_in',
            'monthly_amount' => 'required|numeric|min:1',
            'start_date' => 'required|string|regex:/^\d{4}\/\d{2}\/\d{2}$/',
            'total_months' => 'required|integer|min:1|max:60',
            'status' => 'nullable|in:active,completed,cancelled',
        ]);

        // محاسبه تاریخ پایان
        $startDate = Verta::parseFormat('Y/m/d', $request->start_date);
        $endDate = $startDate->addMonths($request->total_months - 1);

        $rent->update([
            'type' => $request->type,
            'monthly_amount' => $request->monthly_amount,
            'start_date' => $request->start_date,
            'end_date' => $endDate->format('Y/m/d'),
            'total_months' => $request->total_months,
            'status' => $request->status ?? $rent->status,
        ]);

        // حذف اقساط قبلی و تولید مجدد
        $rent->installments()->delete();
        $this->generateInstallments($rent);

        return redirect()->route('houses.rents.index', $house)
                        ->with('success', 'اجاره با موفقیت ویرایش شد.');
    }

    // ===== حذف اجاره =====
    public function destroy(House $house, HouseRent $rent)
    {
        $this->checkAccess($house);
        
        // حذف اقساط
        $rent->installments()->delete();
        $rent->delete();

        return redirect()->route('houses.rents.index', $house)
                        ->with('success', 'اجاره با موفقیت حذف شد.');
    }

    // ===== پرداخت قسط =====
    public function payInstallment(Request $request, House $house, HouseRent $rent, HouseRentInstallment $installment)
    {
        $this->checkAccess($house);

        if ($installment->is_paid) {
            return back()->with('error', 'این قسط قبلاً پرداخت شده است.');
        }

        $request->validate([
            'paid_date' => 'required|string|regex:/^\d{4}\/\d{2}\/\d{2}$/',
            'from_account' => 'nullable|string|max:255',
            'to_account' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'receipt_image' => 'nullable|image|max:2048',
        ]);

        // ایجاد تراکنش
        $transactionData = [
            'house_id' => $house->id,
            'user_id' => Auth::id(),
            'type' => $rent->getTransactionType(), // income یا expense
            'amount' => $installment->amount,
            'transaction_date' => $request->paid_date,
            'from_account' => $request->from_account,
            'to_account' => $request->to_account,
            'description' => $request->description ?? "قسط شماره {$installment->installment_number} - اجاره {$house->name}",
        ];

        // آپلود تصویر
        if ($request->hasFile('receipt_image')) {
            $file = $request->file('receipt_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/receipts'), $filename);
            $transactionData['receipt_image'] = 'uploads/receipts/' . $filename;
        }

        $transaction = HouseTransaction::create($transactionData);

        // بروزرسانی قسط
        $installment->update([
            'is_paid' => true,
            'paid_date' => $request->paid_date,
            'transaction_id' => $transaction->id,
        ]);

        $typeText = $rent->isRentOut() ? 'ورودی' : 'خروجی';
        return redirect()->route('houses.rents.index', $house)
                        ->with('success', "قسط شماره {$installment->installment_number} با موفقیت پرداخت شد و به {$typeText} اضافه شد.");
    }

    // ===== لغو پرداخت قسط =====
    public function cancelInstallment(House $house, HouseRent $rent, HouseRentInstallment $installment)
    {
        $this->checkAccess($house);

        if (!$installment->is_paid) {
            return back()->with('error', 'این قسط پرداخت نشده است.');
        }

        // حذف تراکنش مرتبط
        if ($installment->transaction_id) {
            $transaction = HouseTransaction::find($installment->transaction_id);
            if ($transaction) {
                // حذف تصویر
                if ($transaction->receipt_image && file_exists(public_path($transaction->receipt_image))) {
                    unlink(public_path($transaction->receipt_image));
                }
                $transaction->delete();
            }
        }

        // بروزرسانی قسط
        $installment->update([
            'is_paid' => false,
            'paid_date' => null,
            'transaction_id' => null,
        ]);

        return redirect()->route('houses.rents.index', $house)
                        ->with('success', "قسط شماره {$installment->installment_number} لغو شد.");
    }

    // ===== تولید مجدد اقساط =====
    public function generateInstallmentsManually(House $house, HouseRent $rent)
    {
        $this->checkAccess($house);

        // حذف اقساط قبلی
        $rent->installments()->delete();
        
        // تولید مجدد
        $this->generateInstallments($rent);

        return redirect()->route('houses.rents.index', $house)
                        ->with('success', 'اقساط با موفقیت تولید شد.');
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