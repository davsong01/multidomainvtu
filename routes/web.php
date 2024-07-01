<?php

use App\Mail\EmailMessages;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APIController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KycDataController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EmailApiController;
use App\Http\Controllers\EmailLogController;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\BillerLogController;
use App\Http\Controllers\BlackListController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VariationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CustomerLevelController;
use App\Http\Controllers\PaymentGatewayController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ReservedAccountNumberController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\PaymentProcessors\SquadController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


// Route::get('payment-callback/{provider_id?}', [PaymentController::class, 'analyzePaymentResponse'])->name('payment-callback');
Route::post('log-p-callback/{provider}', [PaymentController::class, 'dumpCallback'])->name('log.payment.response'); // fixed
Route::get('cron/analyze-callback', [PaymentController::class, 'analyzeCallbackResponse'])->name('callback.analyze'); // fixed
Route::get('cron/sendemails', [Controller::class, 'cronSendEmails']); // fixed

Route::get('/', function () {
    return view('mainlandingpages.index');
})->name('landing.main');

Route::group(['prefix' => '{merchant}'], function ($merchant) {
    Route::middleware(['default-domain-merchant-check'])->group(function ($merchant) {
        Route::get('test', function () {
            return view('emails.empty_template');
        });
        
        Route::get('/', [MerchantController::class, 'index'])->name('welcome');

        Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('login', [LoginController::class, 'store'])->name('login.action');
        
        // Auth routes
        Route::middleware([])->group(function ($merchant) {
            Route::get('register', [RegisterController::class, 'create'])->name('register');
            Route::post('register', [RegisterController::class, 'store']);
            Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
            Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email')->middleware('throttle:1,1');
            Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
            Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
        });

        Route::get('verify/{token}', [VerifyEmailController::class, 'confirmEmailVerification'])
        ->middleware(['throttle:6,1'])
        ->name('verification.verify');

        Route::middleware(['auth'])->group(function ($merchant) {
            Route::get('email-verify', [VerifyEmailController::class, 'store'])->name('verification.notice');
            // Email verification routes
            Route::post('email/verification-notification', [VerifyEmailController::class, 'store'])->middleware('throttle:6,1')->name('verification.send');
        });

        Route::middleware(['auth', 'verified'])->group(function ($merchant) {
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
            Route::get('/create-transaction-pin', [DashboardController::class, 'createTransactionPin'])->name('customer.create.pin');
            Route::post('/create-transaction-pin', [DashboardController::class, 'processCreateTransactionPin'])->name('customer.process.create.pin');
            Route::post('logout', [LoginController::class, 'logout'])->name('logout');

            Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
            Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
            Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
            Route::get('/get-keys', [ProfileController::class, 'generateKeys'])->name('profile.keys');

            Route::middleware(['tpin'])->group(function () {
                Route::middleware('reserved_account')->group(function () {
                });
                Route::get('/reset-transaction-pin', [DashboardController::class, 'resetTransactionPin'])->name('customer.reset.pin');
                Route::post('/process-transaction-pin-reset', [DashboardController::class, 'processResetTransactionPin'])->name('process.transaction.pin.reset');
                Route::get('/confirm_reset_pin', [DashboardController::class, 'resetPin2']);
                Route::post('reset_pin_final', [DashboardController::class, 'finalProcessPin'])->name('final.pin.reset');
                // Route::post('change-pin', [HomeController::class, 'processResetPin'])->name('pin.process.reset');
                Route::get('customer/{slug}', [TransactionController::class, 'showProductsPage'])->name('open.transaction.page');
                Route::get('airtime-to-cash', [TransactionController::class, 'airtimeToCash'])->name('airtime-to-cash');
                Route::get('wallet-to-bank/{slug}', [TransactionController::class, 'walletToBank'])->name('wallet-to-bank');
                
                Route::get('customer-get-variations', [VariationController::class, 'getCustomerVariations'])->name('get.customer.variations');
            
                Route::middleware(['kyc'])->group(function () {
                    Route::post('customer-initialize-transaction', [TransactionController::class, 'initializeTransaction'])->name('initialize.transaction');
                    Route::get('customer-transactions', [TransactionController::class, 'customerTransactionHistory'])->name('customer.transaction.history');
                    
                    Route::post('customer-verify', [TransactionController::class, 'verify'])->name('verify.unique.element');
                    Route::get('customer-transaction_status/{transaction_id}', [TransactionController::class, 'transactionStatus'])->name('transaction.status');
                    
                    Route::get('customer-transaction-report', [TransactionController::class, 'showTransactionReportPage'])->name('customer.transaction.report');
                    Route::get('customer-load-wallet', [DashboardController::class, 'showLoadWalletPge'])->name('customer.load.wallet');
                    Route::get('customer-level-upgrade', [DashboardController::class, 'showUpgradeForm'])->name('customer.level.upgrade');
                    Route::post('process-customer-load-wallet', [PaymentController::class, 'redirectToUrl'])->name('process-customer-load-wallet');
                    Route::post('level-upgrade', [DashboardController::class, 'upgradeAccount'])->name('customer.level.upgrade.process');
                    Route::get('download-transaction-receipt/{transaction_id}', [TransactionController::class, 'transactionReceipt'])->name('transaction.receipt.download');
                    
                    Route::get('downlines/process/withdrawal', [DashboardController::class, 'downlinesWithdrawal'])->name('downlines.withdraw');
                    Route::post('downlines/withdraw', [DashboardController::class, 'processWithdrawal'])->name('process.withdrawal');
                    Route::get('downlines/{id?}', [DashboardController::class, 'downlines'])->name('downlines');
                    Route::get('alldownlines', [DashboardController::class, 'allDownlines'])->name('alldownlines');
                    Route::get('api-settings', [DashboardController::class, 'apiSettings'])->name('api.settings');
                    
                });
                Route::get('customer-update-kyc-info', [DashboardController::class, 'updateKycInfo'])->name('update.kyc.details');
                Route::post('customer-update-kyc-info', [DashboardController::class, 'processUpdateKycInfo'])->name('update.kyc.details.process');
                Route::get('get-lga-by-statename/{state}', [KycDataController::class, 'getLgaByStateName'])->name('kyc-get-lga-by-state');
                Route::post('customer-get-discount', [TransactionController::class, 'getCustomerDiscount'])->name('get.customer.discount');
            
                // Route::post('transaction-confirm/{provider}/{reference?}', [PaymentController::class, 'logPaymentResponse'])->name('log.payment.response');
            });
            
            // Admin
            Route::middleware(['admin', 'adminRoute'])->prefix('admin')->group(function () {
                Route::get('pull-product', [ProductController::class, 'pullProducts'])->name('product.pull');
                Route::get('repull-product', [ProductController::class, 'rePullProducts'])->name('product.repull');
                Route::get('duplicate-product/{product}', [ProductController::class, 'duplicateProduct'])->name('duplicate.product');
                Route::get('api-balance/{api}', [APIController::class, 'getBalance'])->name('api.balance');
                
                Route::resource('api', APIController::class);
                Route::resource('category', CategoryController::class);
                Route::resource('customer-blacklist', BlackListController::class);
                Route::resource('announcement', AnnouncementController::class);
                Route::resource('customerlevel', CustomerLevelController::class);
                Route::resource('billerlog', BillerLogController::class);
                Route::resource('role', RoleController::class);
                Route::resource('permission', RolePermissionController::class);
                Route::resource('paymentgateway', PaymentGatewayController::class);
                Route::resource('product', ProductController::class);

                Route::get('pull-categories', [CategoryController::class, 'pullCategories'])->name('category.pull');
                Route::get('repull-categories', [CategoryController::class, 'pullCategories'])->name('category.repull');
                Route::get('emails/send/{count?}', [EmailLogController::class, 'sendMail'])->name('emails.send');
                Route::get('emails/fire-mail/{log}', [EmailLogController::class, 'send'])->name('emails-send');
                Route::get('emails/pending', [EmailLogController::class, 'pending'])->name('emails.pending');
                Route::get('emails/resend/{id}', [EmailLogController::class, 'resend'])->name('emails.resend');
                Route::patch('emails/update/{id}', [EmailLogController::class, 'update'])->name('emails.update');
                Route::get('emails/destroy/{id}', [EmailLogController::class, 'destroy'])->name('emails.destroy');
                Route::get('emails/clear', [EmailLogController::class, 'sweep'])->name('emails.sweep');
                Route::get('emails', [EmailLogController::class, 'index'])->name('emails.index');
                Route::get('black-list-status', [BlackListController::class, 'status'])->name('black.list.status');
            
                Route::get('setup-emails', [EmailApiController::class, 'edit'])->name('email.setup');
                Route::post('setup-emails', [EmailApiController::class, 'update'])->name('email.setup.update');
            
                // transactions route
                Route::get('transactions', [TransactionController::class, 'transView'])->name('admin.trans');
                Route::get('wallet-transactions', [TransactionController::class, 'walletTransView'])->name('admin.walletlog');
                Route::get('admin-wallet-funding-log', [TransactionController::class, 'walletFundingLogView'])->name('admin.walletfundinglog');
                Route::get('admin-airtime-2-cash-log', [TransactionController::class, 'airtimeToCashTransactions'])->name('admin.airtime.2.cash.log');
                
                Route::get('admin-earninglog', [TransactionController::class, 'walletEarningView'])->name('admin.earninglog');
                Route::get('credit-customer', [TransactionController::class, 'creditCustomerPage'])->name('admin.credit.customer');
                Route::get('debit-customer', [TransactionController::class, 'debitCustomerPage'])->name('admin.debit.customer');
                Route::post('process-credit-debit', [TransactionController::class, 'processCreditDebit'])->name('admin.process.credit.debit');
                Route::get('admin-kyc', [KycDataController::class, 'adminKycIndex'])->name('admin.kyc');
                Route::get('admin-reserved-account', [ReservedAccountNumberController::class, 'index'])->name('admin.reserved.accounts');
                Route::get('account-transactions/{account}', [ReservedAccountNumberController::class, 'show'])->name('account.transactions');
                Route::get('admin-callback-analysis', [PaymentController::class, 'callBackAnalysis'])->name('callback.analysis');
                Route::get('admin-callback-analysis-reset/{callback}', [PaymentController::class, 'resetCallBackResponse'])->name('callback.reset');
                Route::get('admin-callback-error-logs', [SquadController::class, 'getCallbackLogs'])->name('callback-error-logs');
                
                Route::get('reserved-account-delete/{account}', [ReservedAccountNumberController::class, 'delete'])->name('reserved_account.delete');
                
                Route::get('single-transaction-view/{transaction}', [TransactionController::class, 'singleTransactionView'])->name('admin.single.transaction.view');
                Route::get('query-wallet/{transactionlog?}', [TransactionController::class, 'queryWallet'])->name('admin.query.wallet');
                Route::get('requery-transaction/{transactionlog?}', [TransactionController::class, 'requery'])->name('admin.requery.transaction');
                
                Route::post('change-transaction-method/{transaction}', [TransactionController::class, 'changeTransactionMethod'])->name('admin.changetransactionmethod');
                
                Route::post('verify-bank-details', [TransactionController::class, 'verifyBankDetails'])->name('admin.verify.bank.details');
                
                Route::get('customers/{status?}', [CustomerController::class, 'customers'])->name('customers');
                Route::get('customers-active/{status}', [CustomerController::class, 'customers'])->name('customers.active');
                Route::get('customers-suspended/{status}', [CustomerController::class, 'customers'])->name('customers.suspended');
                Route::get('customers-unverified', [CustomerController::class, 'unverifiedCustomers'])->name('customers.unverified');
                Route::get('customers-verify/{customer}', [CustomerController::class, 'verifyCustomer'])->name('customer.verify');
                
                Route::get('customer/edit/{id}', [CustomerController::class, 'singleCustomer'])->name('customers.edit');
                Route::post('customer/update/{id}', [CustomerController::class, 'updateCustomer'])->name('customers.update');
                
                Route::get('pull-variations/{product}', [VariationController::class, 'pullVariations'])->name('variations.pull');
                Route::post('update-variations/{product}', [VariationController::class, 'updateVariations'])->name('variations.update');
                Route::post('manual-variations-add/{product}', [VariationController::class, 'addManualVariations'])->name('manual.variations.add');
                Route::get('delete-variations/{variation}', [VariationController::class, 'deleteVariations'])->name('variation.delete');

                Route::post('create-reserved-account/{customer}', [CustomerController::class, 'addReservedAccounts'])->name('create.reserved.account');
                Route::get('admin-download-transaction-receipt/{transaction_id}', [TransactionController::class, 'transactionReceipt'])->name('admin.transaction.receipt.download');
                
                Route::controller(AdminController::class)->group(function () {
                    Route::get('admins', 'index')->name('admins');
                    Route::get('admin/new', 'create')->name('newAdmin');
                    Route::post('admin/save', 'store')->name('adminSave');
                    Route::get('admin/view', 'view')->name('viewAdmin');
                    Route::post('admin/update', 'update')->name('updateAdmin');
                    Route::get('verify-biller', 'verifyBiller')->name('admin.verifybiller');
                    Route::post('verify-post', 'verifyPost')->name('admin.verify.post');
                });
                    
                Route::get('settings-update', [SettingsController::class, 'edit'])->name('settings.edit');
                Route::post('settings-update', [SettingsController::class, 'update'])->name('settings.update');
            
                Route::get('verify-transaction/{reference}/{provider_id?}', [PaymentController::class, 'verifyPayment'])->name('transaction.verify');
            
                Route::post('transaction-pin-reset/{user}', [CustomerController::class, 'resetTransactionPin'])->name('admin.transaction.pin.reset');
                Route::post('password-reset/{user}', [CustomerController::class, 'resetPassword'])->name('admin.password.reset');
                Route::post('customer-update-kyc/{customer}', [CustomerController::class, 'processCustomerUpdateKycInfo'])->name('admin.customer.update.kyc');
                Route::get('customer-approve-kyc/{customer}', [CustomerController::class, 'approveCustomerKyc'])->name('admin.customer.approve.kyc');
                Route::get('customer-decline-kyc/{customer}', [CustomerController::class, 'declineCustomerKyc'])->name('admin.customer.decline.kyc');
            });
        });
    });
});

Route::middleware(['custom-domain-merchant-check'])->group(function () {   
    Route::get('hello', function () {
        return "Welcome to the default site!";
    }); 
    // Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

});