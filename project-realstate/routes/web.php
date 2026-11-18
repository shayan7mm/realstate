<?php

use App\Http\Controllers\CarController;
use App\Http\Controllers\CarTransactionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HouseController;
use App\Http\Controllers\HouseRentController;
use App\Http\Controllers\HouseTransactionController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\OrganizationLoanController;
use App\Http\Controllers\OrganizationTransactionController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\PersonTransactionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ============================================
// صفحه اصلی
// ============================================
 Route::get('/', [HomeController::class, 'user'])->name('user');

// ============================================
// خروج از سیستم (GET و POST)
// ============================================
Route::get('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout.post');

// ============================================
// مسیرهای احراز هویت (Breeze)
// ============================================
require __DIR__.'/auth.php';

// ============================================
// مسیرهای محافظت شده با احراز هویت
// ============================================
Route::middleware(['auth', 'verified'])->group(function () {
    
    // ===== داشبورد =====
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ===== پروفایل کاربری =====
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    // ==========================================
    // 👥 مدیریت کاربران (فقط مستر)
    // ==========================================
    Route::middleware(['role:master'])->prefix('master')->name('master.')->group(function () {
        Route::get('/users', [UserManagementController::class, 'index'])->name('users');
        Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
        Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
        Route::post('/users/{id}/restore', [UserManagementController::class, 'restore'])->name('users.restore');
        Route::post('/users/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('users.toggle-status');
    });

   Route::middleware(['auth', 'role:master'])->prefix('permissions')->name('permissions.')->group(function () {
    Route::get('/user/{user}', [PermissionController::class, 'index'])->name('index');
    Route::get('/user/{user}/edit', [PermissionController::class, 'edit'])->name('edit');
    Route::put('/user/{user}', [PermissionController::class, 'update'])->name('update');
    Route::delete('/{permission}', [PermissionController::class, 'destroy'])->name('destroy');
});

    // ==========================================
    // 🚗 مدیریت ماشین‌ها
    // ==========================================
    Route::resource('cars', CarController::class);
    Route::post('/cars/{id}/restore', [CarController::class, 'restore'])->name('cars.restore');
    Route::post('/cars/{car}/toggle-status', [CarController::class, 'toggleStatus'])->name('cars.toggle-status');

    // ===== مدیریت تراکنش‌های ماشین =====
    Route::prefix('cars/{car}')->name('cars.')->group(function () {
        Route::get('/transactions', [CarTransactionController::class, 'index'])->name('transactions.index');
        Route::get('/transactions/create', [CarTransactionController::class, 'create'])->name('transactions.create');
        Route::post('/transactions', [CarTransactionController::class, 'store'])->name('transactions.store');
        Route::get('/transactions/{transaction}', [CarTransactionController::class, 'show'])->name('transactions.show');
        Route::get('/transactions/{transaction}/edit', [CarTransactionController::class, 'edit'])->name('transactions.edit');
        Route::put('/transactions/{transaction}', [CarTransactionController::class, 'update'])->name('transactions.update');
        Route::delete('/transactions/{transaction}', [CarTransactionController::class, 'destroy'])->name('transactions.destroy');
    });

    // ==========================================
    // 🏠 مدیریت منازل
    // ==========================================
    Route::resource('houses', HouseController::class);
    Route::post('/houses/{id}/restore', [HouseController::class, 'restore'])->name('houses.restore');
    Route::post('/houses/{house}/toggle-status', [HouseController::class, 'toggleStatus'])->name('houses.toggle-status');

    // ===== مدیریت تراکنش‌های منازل =====
    Route::prefix('houses/{house}')->name('houses.')->group(function () {
        Route::get('/transactions', [HouseTransactionController::class, 'index'])->name('transactions.index');
        Route::get('/transactions/create', [HouseTransactionController::class, 'create'])->name('transactions.create');
        Route::post('/transactions', [HouseTransactionController::class, 'store'])->name('transactions.store');
        Route::get('/transactions/{transaction}/edit', [HouseTransactionController::class, 'edit'])->name('transactions.edit');
        Route::put('/transactions/{transaction}', [HouseTransactionController::class, 'update'])->name('transactions.update');
        Route::delete('/transactions/{transaction}', [HouseTransactionController::class, 'destroy'])->name('transactions.destroy');
    });

    // ===== مدیریت اجاره منازل =====
    Route::prefix('houses/{house}')->name('houses.')->group(function () {
        Route::get('/rents', [HouseRentController::class, 'index'])->name('rents.index');
        Route::get('/rents/create', [HouseRentController::class, 'create'])->name('rents.create');
        Route::post('/rents', [HouseRentController::class, 'store'])->name('rents.store');
        Route::get('/rents/{rent}/edit', [HouseRentController::class, 'edit'])->name('rents.edit');
        Route::put('/rents/{rent}', [HouseRentController::class, 'update'])->name('rents.update');
        Route::delete('/rents/{rent}', [HouseRentController::class, 'destroy'])->name('rents.destroy');
        Route::post('/rents/{rent}/installments/{installment}/pay', [HouseRentController::class, 'payInstallment'])->name('rents.pay-installment');
        Route::post('/rents/{rent}/installments/{installment}/cancel', [HouseRentController::class, 'cancelInstallment'])->name('rents.cancel-installment');
    });

    // ==========================================
    // 👤 مدیریت اشخاص
    // ==========================================
    Route::resource('persons', PersonController::class);
    Route::post('/persons/{id}/restore', [PersonController::class, 'restore'])->name('persons.restore');
    Route::post('/persons/{person}/toggle-status', [PersonController::class, 'toggleStatus'])->name('persons.toggle-status');

    // ===== مدیریت تراکنش‌های اشخاص =====
    Route::prefix('persons/{person}')->name('persons.')->group(function () {
        Route::get('/transactions', [PersonTransactionController::class, 'index'])->name('transactions.index');
        Route::get('/transactions/create', [PersonTransactionController::class, 'create'])->name('transactions.create');
        Route::post('/transactions', [PersonTransactionController::class, 'store'])->name('transactions.store');
        Route::get('/transactions/{transaction}/edit', [PersonTransactionController::class, 'edit'])->name('transactions.edit');
        Route::put('/transactions/{transaction}', [PersonTransactionController::class, 'update'])->name('transactions.update');
        Route::delete('/transactions/{transaction}', [PersonTransactionController::class, 'destroy'])->name('transactions.destroy');
    });

    // ==========================================
    // 🏦 مدیریت ارگان‌ها
    // ==========================================
    Route::resource('organizations', OrganizationController::class);
    Route::post('/organizations/{id}/restore', [OrganizationController::class, 'restore'])->name('organizations.restore');
    Route::post('/organizations/{organization}/toggle-status', [OrganizationController::class, 'toggleStatus'])->name('organizations.toggle-status');

    // ===== مدیریت تراکنش‌های ارگان‌ها =====
    Route::prefix('organizations/{organization}')->name('organizations.')->group(function () {
        Route::get('/transactions', [OrganizationTransactionController::class, 'index'])->name('transactions.index');
        Route::get('/transactions/create', [OrganizationTransactionController::class, 'create'])->name('transactions.create');
        Route::post('/transactions', [OrganizationTransactionController::class, 'store'])->name('transactions.store');
        Route::get('/transactions/{transaction}/edit', [OrganizationTransactionController::class, 'edit'])->name('transactions.edit');
        Route::put('/transactions/{transaction}', [OrganizationTransactionController::class, 'update'])->name('transactions.update');
        Route::delete('/transactions/{transaction}', [OrganizationTransactionController::class, 'destroy'])->name('transactions.destroy');
    });

    // ===== مدیریت وام‌های ارگان‌ها =====
    Route::prefix('organizations/{organization}')->name('organizations.')->group(function () {
        Route::get('/loans', [OrganizationLoanController::class, 'index'])->name('loans.index');
        Route::get('/loans/create', [OrganizationLoanController::class, 'create'])->name('loans.create');
        Route::post('/loans', [OrganizationLoanController::class, 'store'])->name('loans.store');
        Route::get('/loans/{loan}/edit', [OrganizationLoanController::class, 'edit'])->name('loans.edit');
        Route::put('/loans/{loan}', [OrganizationLoanController::class, 'update'])->name('loans.update');
        Route::delete('/loans/{loan}', [OrganizationLoanController::class, 'destroy'])->name('loans.destroy');
        Route::post('/loans/{loan}/installments/{installment}/pay', [OrganizationLoanController::class, 'payInstallment'])->name('loans.pay-installment');
        Route::post('/loans/{loan}/installments/{installment}/cancel', [OrganizationLoanController::class, 'cancelInstallment'])->name('loans.cancel-installment');
    });

    // ==========================================
    // 💰 مدیریت هزینه‌های جانبی
    // ==========================================
    Route::resource('expenses', ExpenseController::class);

   


});
 // ==========================================
// 📋 مدیریت To-Do List (فقط مستر)
// ==========================================
Route::middleware(['auth', 'role:master'])->prefix('master/todos')->name('master.todos.')->group(function () {
    Route::get('/', [TodoController::class, 'index'])->name('index');
    Route::get('/create', [TodoController::class, 'create'])->name('create');
    Route::post('/', [TodoController::class, 'store'])->name('store');
    Route::get('/{todo}/edit', [TodoController::class, 'edit'])->name('edit');
    Route::put('/{todo}', [TodoController::class, 'update'])->name('update');
    Route::delete('/{todo}', [TodoController::class, 'destroy'])->name('destroy');
    Route::post('/{todo}/toggle', [TodoController::class, 'toggleStatus'])->name('toggle');
});