<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\OrganizationTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class OrganizationTransactionController extends Controller
{
    public function index(Organization $organization, Request $request)
    {
        $this->checkAccess($organization);

        $search = $request->get('search');
        $type = $request->get('type');

        $query = $organization->transactions();

        if ($search) {
            $query->search($search);
        }

        if ($type && in_array($type, ['income', 'expense'])) {
            $query->where('type', $type);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')->paginate(20);

        $totalIncome = $organization->totalIncome();
        $totalExpense = $organization->totalExpense();
        $profit = $organization->profit();

        return view('organizations.transactions.index', compact('organization', 'transactions', 'totalIncome', 'totalExpense', 'profit', 'type', 'search'));
    }

    public function create(Organization $organization)
    {
        $this->checkAccess($organization);
        return view('organizations.transactions.create', compact('organization'));
    }

    public function store(Request $request, Organization $organization)
    {
        $this->checkAccess($organization);

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

        $data['organization_id'] = $organization->id;
        $data['user_id'] = Auth::id();

        if ($request->hasFile('receipt_image')) {
            $file = $request->file('receipt_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/receipts'), $filename);
            $data['receipt_image'] = 'uploads/receipts/' . $filename;
        }

        OrganizationTransaction::create($data);

        $typeText = $request->type === 'income' ? 'ورودی' : 'خروجی';
        return redirect()->route('organizations.transactions.index', $organization)
                        ->with('success', "تراکنش {$typeText} با موفقیت ثبت شد.");
    }

    public function edit(Organization $organization, OrganizationTransaction $transaction)
    {
        $this->checkAccess($organization);
        return view('organizations.transactions.edit', compact('organization', 'transaction'));
    }

    public function update(Request $request, Organization $organization, OrganizationTransaction $transaction)
    {
        $this->checkAccess($organization);

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

        return redirect()->route('organizations.transactions.index', $organization)
                        ->with('success', 'تراکنش با موفقیت ویرایش شد.');
    }

    public function destroy(Organization $organization, OrganizationTransaction $transaction)
    {
        $this->checkAccess($organization);

        if ($transaction->receipt_image && file_exists(public_path($transaction->receipt_image))) {
            unlink(public_path($transaction->receipt_image));
        }

        $transaction->delete();

        return redirect()->route('organizations.transactions.index', $organization)
                        ->with('success', 'تراکنش با موفقیت حذف شد.');
    }

    private function checkAccess($organization)
    {
        $user = Auth::user();
        if (!$user->isMaster() && !$user->canAccessOrganization($organization->id)) {
            abort(403, 'شما دسترسی به این ارگان را ندارید.');
        }
    }
}