<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\OrganizationLoan;
use App\Models\OrganizationLoanInstallment;
use App\Models\OrganizationTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Hekmatinasser\Verta\Verta;

class OrganizationLoanController extends Controller
{
    public function index(Organization $organization)
    {
        $this->checkAccess($organization);
        
        $loans = $organization->loans()->orderBy('created_at', 'desc')->paginate(20);
        
        return view('organizations.loans.index', compact('organization', 'loans'));
    }

    public function create(Organization $organization)
    {
        $this->checkAccess($organization);
        return view('organizations.loans.create', compact('organization'));
    }

    public function store(Request $request, Organization $organization)
    {
        $this->checkAccess($organization);

        $request->validate([
            'type' => 'required|in:loan_given,loan_taken',
            'total_amount' => 'required|numeric|min:1',
            'monthly_amount' => 'required|numeric|min:1',
            'start_date' => 'required|string|regex:/^\d{4}\/\d{2}\/\d{2}$/',
            'total_months' => 'required|integer|min:1|max:60',
        ]);

        $startDate = Verta::parseFormat('Y/m/d', $request->start_date);
        $endDate = $startDate->addMonths($request->total_months - 1);

        $loan = OrganizationLoan::create([
            'organization_id' => $organization->id,
            'user_id' => Auth::id(),
            'type' => $request->type,
            'total_amount' => $request->total_amount,
            'monthly_amount' => $request->monthly_amount,
            'start_date' => $request->start_date,
            'end_date' => $endDate->format('Y/m/d'),
            'total_months' => $request->total_months,
            'status' => 'active',
        ]);

        $this->generateInstallments($loan);

        $typeText = $request->type === 'loan_given' ? 'وام داده شده' : 'وام گرفته شده';
        return redirect()->route('organizations.loans.index', $organization)
                        ->with('success', "تسهیلات با موفقیت ثبت شد. ({$typeText})");
    }

    private function generateInstallments(OrganizationLoan $loan)
    {
        $startDate = Verta::parseFormat('Y/m/d', $loan->start_date);
        $amount = $loan->monthly_amount;

        for ($i = 1; $i <= $loan->total_months; $i++) {
            $dueDate = $startDate->addMonths($i - 1)->format('Y/m/d');
            
            OrganizationLoanInstallment::create([
                'loan_id' => $loan->id,
                'installment_number' => $i,
                'due_date' => $dueDate,
                'amount' => $amount,
                'is_paid' => false,
            ]);
        }
    }

    public function edit(Organization $organization, OrganizationLoan $loan)
    {
        $this->checkAccess($organization);
        return view('organizations.loans.edit', compact('organization', 'loan'));
    }

    public function update(Request $request, Organization $organization, OrganizationLoan $loan)
    {
        $this->checkAccess($organization);

        $request->validate([
            'type' => 'required|in:loan_given,loan_taken',
            'total_amount' => 'required|numeric|min:1',
            'monthly_amount' => 'required|numeric|min:1',
            'start_date' => 'required|string|regex:/^\d{4}\/\d{2}\/\d{2}$/',
            'total_months' => 'required|integer|min:1|max:60',
            'status' => 'nullable|in:active,completed,cancelled',
        ]);

        $startDate = Verta::parseFormat('Y/m/d', $request->start_date);
        $endDate = $startDate->addMonths($request->total_months - 1);

        $loan->update([
            'type' => $request->type,
            'total_amount' => $request->total_amount,
            'monthly_amount' => $request->monthly_amount,
            'start_date' => $request->start_date,
            'end_date' => $endDate->format('Y/m/d'),
            'total_months' => $request->total_months,
            'status' => $request->status ?? $loan->status,
        ]);

        $loan->installments()->delete();
        $this->generateInstallments($loan);

        return redirect()->route('organizations.loans.index', $organization)
                        ->with('success', 'تسهیلات با موفقیت ویرایش شد.');
    }

    public function destroy(Organization $organization, OrganizationLoan $loan)
    {
        $this->checkAccess($organization);
        
        $loan->installments()->delete();
        $loan->delete();

        return redirect()->route('organizations.loans.index', $organization)
                        ->with('success', 'تسهیلات با موفقیت حذف شد.');
    }

    public function payInstallment(Request $request, Organization $organization, OrganizationLoan $loan, OrganizationLoanInstallment $installment)
    {
        $this->checkAccess($organization);

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

        $transactionData = [
            'organization_id' => $organization->id,
            'user_id' => Auth::id(),
            'type' => $loan->getTransactionType(),
            'amount' => $installment->amount,
            'transaction_date' => $request->paid_date,
            'from_account' => $request->from_account,
            'to_account' => $request->to_account,
            'description' => $request->description ?? "قسط شماره {$installment->installment_number} - وام {$organization->name}",
        ];

        if ($request->hasFile('receipt_image')) {
            $file = $request->file('receipt_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/receipts'), $filename);
            $transactionData['receipt_image'] = 'uploads/receipts/' . $filename;
        }

        $transaction = OrganizationTransaction::create($transactionData);

        $installment->update([
            'is_paid' => true,
            'paid_date' => $request->paid_date,
            'transaction_id' => $transaction->id,
        ]);

        $typeText = $loan->isLoanGiven() ? 'ورودی' : 'خروجی';
        return redirect()->route('organizations.loans.index', $organization)
                        ->with('success', "قسط شماره {$installment->installment_number} با موفقیت پرداخت شد و به {$typeText} اضافه شد.");
    }

    public function cancelInstallment(Organization $organization, OrganizationLoan $loan, OrganizationLoanInstallment $installment)
    {
        $this->checkAccess($organization);

        if (!$installment->is_paid) {
            return back()->with('error', 'این قسط پرداخت نشده است.');
        }

        if ($installment->transaction_id) {
            $transaction = OrganizationTransaction::find($installment->transaction_id);
            if ($transaction) {
                if ($transaction->receipt_image && file_exists(public_path($transaction->receipt_image))) {
                    unlink(public_path($transaction->receipt_image));
                }
                $transaction->delete();
            }
        }

        $installment->update([
            'is_paid' => false,
            'paid_date' => null,
            'transaction_id' => null,
        ]);

        return redirect()->route('organizations.loans.index', $organization)
                        ->with('success', "قسط شماره {$installment->installment_number} لغو شد.");
    }

    private function checkAccess($organization)
    {
        $user = Auth::user();
        if (!$user->isMaster() && !$user->canAccessOrganization($organization->id)) {
            abort(403, 'شما دسترسی به این ارگان را ندارید.');
        }
    }
}