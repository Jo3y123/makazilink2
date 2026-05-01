<?php
 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TenantPortalController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\LeaseController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\WaterReadingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\MpesaController;
use App\Http\Controllers\PropertyReportController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProfitLossController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\RenewalController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\DepositController;
 
// Auth routes (guests only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});
 
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');
 
Route::get('/', fn () => redirect()->route('login'));
 
// Public renewal routes — no auth required
Route::prefix('renew')->name('renew.')->group(function () {
    Route::get('/',              [RenewalController::class, 'index'])->name('index');
    Route::post('/instructions', [RenewalController::class, 'instructions'])->name('instructions');
    Route::post('/verify',       [RenewalController::class, 'verify'])->name('verify');
});
 
// Subscription expired page — auth required but no role check
Route::middleware('auth')->get('/subscription/expired', [SubscriptionController::class, 'expired'])->name('subscription.expired');
 
// Staff Dashboard
Route::middleware(['auth', 'role:admin,agent,accountant,caretaker'])->group(function () {
 
    // Subscription admin panel
    Route::middleware('role:admin')->prefix('subscription')->name('subscription.')->group(function () {
        Route::get('/',          [SubscriptionController::class, 'index'])->name('index');
        Route::post('/',         [SubscriptionController::class, 'store'])->name('store');
        Route::post('/activate', [SubscriptionController::class, 'activate'])->name('activate');
        Route::post('/suspend',  [SubscriptionController::class, 'suspend'])->name('suspend');
    });
 
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
 
    // Chatbot
    Route::post('/chatbot', [ChatbotController::class, 'ask'])->name('chatbot.ask');
 
    Route::middleware('role:admin,agent')->prefix('properties')->name('properties.')->group(function () {
        Route::get('/',                  [PropertyController::class, 'index'])->name('index');
        Route::get('/create',            [PropertyController::class, 'create'])->name('create');
        Route::post('/',                 [PropertyController::class, 'store'])->name('store');
        Route::get('/{property}/edit',   [PropertyController::class, 'edit'])->name('edit');
        Route::put('/{property}',        [PropertyController::class, 'update'])->name('update');
        Route::delete('/{property}',     [PropertyController::class, 'destroy'])->name('destroy');
        Route::get('/{property}/water',  [PropertyController::class, 'sharedWater'])->name('water');
        Route::post('/{property}/water', [PropertyController::class, 'applySharedWater'])->name('water.apply');
    });
 
    Route::middleware('role:admin,agent,caretaker')->prefix('units')->name('units.')->group(function () {
        Route::get('/',            [UnitController::class, 'index'])->name('index');
        Route::post('/',           [UnitController::class, 'store'])->name('store');
        Route::get('/{unit}/edit', [UnitController::class, 'edit'])->name('edit');
        Route::put('/{unit}',      [UnitController::class, 'update'])->name('update');
        Route::delete('/{unit}',   [UnitController::class, 'destroy'])->name('destroy');
    });
 
    Route::middleware('role:admin,agent,caretaker')->prefix('tenants')->name('tenants.')->group(function () {
        Route::get('/',                   [TenantController::class, 'index'])->name('index');
        Route::get('/create',             [TenantController::class, 'create'])->name('create');
        Route::post('/',                  [TenantController::class, 'store'])->name('store');
        Route::get('/{tenant}/edit',      [TenantController::class, 'edit'])->name('edit');
        Route::put('/{tenant}',           [TenantController::class, 'update'])->name('update');
        Route::delete('/{tenant}',        [TenantController::class, 'destroy'])->name('destroy');
        Route::get('/{tenant}/statement', [TenantController::class, 'statement'])->name('statement');
    });
 
    Route::middleware('role:admin,agent')->prefix('leases')->name('leases.')->group(function () {
        Route::get('/',             [LeaseController::class, 'index'])->name('index');
        Route::get('/create',       [LeaseController::class, 'create'])->name('create');
        Route::post('/',            [LeaseController::class, 'store'])->name('store');
        Route::get('/{lease}',      [LeaseController::class, 'show'])->name('show');
        Route::get('/{lease}/edit', [LeaseController::class, 'edit'])->name('edit');
        Route::put('/{lease}',      [LeaseController::class, 'update'])->name('update');
        Route::delete('/{lease}',   [LeaseController::class, 'destroy'])->name('destroy');
    });
 
    Route::middleware('role:admin,accountant')->prefix('payments')->name('payments.')->group(function () {
        Route::get('/',                   [PaymentController::class, 'index'])->name('index');
        Route::get('/create',             [PaymentController::class, 'create'])->name('create');
        Route::get('/bulk-whatsapp',      [PaymentController::class, 'bulkWhatsapp'])->name('bulk.whatsapp');
        Route::post('/',                  [PaymentController::class, 'store'])->name('store');
        Route::get('/{payment}',          [PaymentController::class, 'show'])->name('show');
        Route::get('/{payment}/pdf',      [PaymentController::class, 'pdf'])->name('pdf');
        Route::get('/{payment}/whatsapp', [PaymentController::class, 'whatsapp'])->name('whatsapp');
        Route::delete('/{payment}',       [PaymentController::class, 'destroy'])->name('destroy');
    });
 
    Route::middleware('role:admin,accountant')->prefix('invoices')->name('invoices.')->group(function () {
    Route::get('/',               [InvoiceController::class, 'index'])->name('index');
    Route::get('/create',         [InvoiceController::class, 'create'])->name('create');
    Route::post('/',              [InvoiceController::class, 'store'])->name('store');
    Route::post('/bulk-generate', [InvoiceController::class, 'bulkGenerate'])->name('bulk');
    Route::delete('/mass-destroy',[InvoiceController::class, 'massDestroy'])->name('mass-destroy');
    Route::get('/{invoice}',      [InvoiceController::class, 'show'])->name('show');
    Route::get('/{invoice}/edit', [InvoiceController::class, 'edit'])->name('edit');
    Route::get('/{invoice}/pdf',  [InvoiceController::class, 'pdf'])->name('pdf');
    Route::put('/{invoice}',      [InvoiceController::class, 'update'])->name('update');
    Route::delete('/{invoice}',   [InvoiceController::class, 'destroy'])->name('destroy');
});
 
    Route::middleware('role:admin,caretaker')->prefix('maintenance')->name('maintenance.')->group(function () {
        Route::get('/',                   [MaintenanceController::class, 'index'])->name('index');
        Route::get('/create',             [MaintenanceController::class, 'create'])->name('create');
        Route::post('/',                  [MaintenanceController::class, 'store'])->name('store');
        Route::get('/{maintenance}',      [MaintenanceController::class, 'show'])->name('show');
        Route::get('/{maintenance}/edit', [MaintenanceController::class, 'edit'])->name('edit');
        Route::put('/{maintenance}',      [MaintenanceController::class, 'update'])->name('update');
        Route::delete('/{maintenance}',   [MaintenanceController::class, 'destroy'])->name('destroy');
    });
 
    Route::middleware('role:admin,caretaker,accountant')->prefix('water')->name('water.')->group(function () {
        Route::get('/',           [WaterReadingController::class, 'index'])->name('index');
        Route::post('/',          [WaterReadingController::class, 'store'])->name('store');
        Route::delete('/{water}', [WaterReadingController::class, 'destroy'])->name('destroy');
    });
 
    // Messages — Admin and Caretaker
    Route::middleware('role:admin,caretaker')->prefix('messages')->name('messages.')->group(function () {
        Route::get('/',                [MessageController::class, 'index'])->name('index');
        Route::get('/{tenant}',        [MessageController::class, 'show'])->name('show');
        Route::post('/{tenant}/reply', [MessageController::class, 'reply'])->name('reply');
    });
 
    // Messages unread count
    Route::get('/messages/unread', [MessageController::class, 'unreadCount'])->name('messages.unread');
 
    // Deposits
    Route::middleware('role:admin,accountant')->prefix('deposits')->name('deposits.')->group(function () {
        Route::get('/',                [DepositController::class, 'index'])->name('index');
        Route::post('/',               [DepositController::class, 'store'])->name('store');
        Route::get('/{deposit}/edit',  [DepositController::class, 'edit'])->name('edit');
        Route::put('/{deposit}',       [DepositController::class, 'update'])->name('update');
        Route::delete('/{deposit}',    [DepositController::class, 'destroy'])->name('destroy');
    });

    // Salaries
    Route::middleware('role:admin,accountant')->prefix('salaries')->name('salaries.')->group(function () {
        Route::get('/',              [SalaryController::class, 'index'])->name('index');
        Route::post('/',             [SalaryController::class, 'store'])->name('store');
        Route::delete('/{salary}',   [SalaryController::class, 'destroy'])->name('destroy');
    });

    // Excel Exports
    Route::middleware('role:admin,accountant')->prefix('export')->name('export.')->group(function () {
        Route::get('/payments',    [ExportController::class, 'payments'])->name('payments');
        Route::get('/invoices',    [ExportController::class, 'invoices'])->name('invoices');
        Route::get('/tenants',     [ExportController::class, 'tenants'])->name('tenants');
        Route::get('/profit-loss', [ExportController::class, 'profitLoss'])->name('profit-loss');
        Route::get('/salaries',    [ExportController::class, 'salaries'])->name('salaries');
    });

    Route::middleware('role:admin,accountant')->prefix('reports')->name('reports.')->group(function () {
        Route::get('/',                          [ReportController::class, 'index'])->name('index');
        Route::get('/properties',                [PropertyReportController::class, 'index'])->name('properties');
        Route::get('/properties/{property}',     [PropertyReportController::class, 'show'])->name('property.show');
        Route::get('/properties/{property}/pdf', [PropertyReportController::class, 'pdf'])->name('property.pdf');
        Route::get('/profit-loss',               [ProfitLossController::class, 'index'])->name('profit-loss');
        Route::get('/profit-loss/pdf',           [ProfitLossController::class, 'pdf'])->name('profit-loss.pdf');
    });
 
    Route::middleware('role:admin')->prefix('settings')->name('settings.')->group(function () {
        Route::get('/',                  [SettingsController::class, 'index'])->name('index');
        Route::post('/',                 [SettingsController::class, 'update'])->name('update');
        Route::get('/users',             [UserController::class, 'index'])->name('users');
        Route::post('/users',            [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}',      [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}',   [UserController::class, 'destroy'])->name('users.destroy');
    });
 
});
 
// M-Pesa
Route::middleware(['auth', 'role:admin,accountant'])->prefix('mpesa')->name('mpesa.')->group(function () {
    Route::get('/push/{invoice}', [MpesaController::class, 'showPush'])->name('push');
    Route::post('/push',          [MpesaController::class, 'sendPush'])->name('send');
    Route::get('/status',         [MpesaController::class, 'status'])->name('status');
    Route::get('/query',          [MpesaController::class, 'query'])->name('query');
});
 
// M-Pesa Callback (no auth — called by Safaricom)
Route::post('/mpesa/callback', [MpesaController::class, 'callback'])->name('mpesa.callback');
 
// Tenant Portal
Route::middleware(['auth', 'role:tenant'])->prefix('portal')->name('tenant.')->group(function () {
    Route::get('/',              [TenantPortalController::class, 'index'])->name('portal');
    Route::get('/messages',      [MessageController::class, 'tenantInbox'])->name('messages');
    Route::post('/messages',     [MessageController::class, 'tenantSend'])->name('messages.send');
    Route::post('/pay',          [MpesaController::class, 'tenantPay'])->name('pay');
    Route::get('/pay/status',    [MpesaController::class, 'tenantPayStatus'])->name('pay.status');
    Route::post('/maintenance',  [TenantPortalController::class, 'submitMaintenance'])->name('maintenance.store');
});
 