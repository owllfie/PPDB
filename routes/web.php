<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainCont;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VerifyEmailController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\InboxController;
use App\Http\Controllers\AdmissionController;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('index');
    }
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
    
    Route::get('/forgot-password', [\App\Http\Controllers\PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [\App\Http\Controllers\PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::post('/forgot-password/select', [\App\Http\Controllers\PasswordResetController::class, 'selectMethod'])->name('password.method.select');
    Route::get('/forgot-password/otp', [\App\Http\Controllers\PasswordResetController::class, 'showOTPForm'])->name('password.otp.form');
    Route::post('/forgot-password/otp', [\App\Http\Controllers\PasswordResetController::class, 'verifyOTP'])->name('password.otp.verify');
    Route::get('/reset-password/{token}', [\App\Http\Controllers\PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [\App\Http\Controllers\PasswordResetController::class, 'reset'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/verify-email', [VerifyEmailController::class, 'show'])->name('verify.email');
    Route::post('/verify-email', [VerifyEmailController::class, 'verify'])->name('verify.email.submit');
    Route::post('/verify-email/resend', [VerifyEmailController::class, 'resend'])->name('verify.email.resend');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::middleware(['auth', 'verified.email'])->group(function () {
    Route::get('/index', [MainCont::class, 'index'])->name('index');

    Route::middleware('permission:user.register')->group(function () {
        Route::get('/registration', [\App\Http\Controllers\RegistrationController::class, 'create'])->name('user.register.form');
        Route::post('/registration', [\App\Http\Controllers\RegistrationController::class, 'store'])->name('user.register.store');
        Route::get('/inbox', [InboxController::class, 'index'])->name('user.inbox');
        Route::get('/admission/test/{token}', [AdmissionController::class, 'showTest'])->name('user.test.show');
        Route::post('/admission/test/{token}', [AdmissionController::class, 'submitTest'])->name('user.test.submit');
        Route::get('/admission/reregister/{token}', [AdmissionController::class, 'showReRegistration'])->name('user.reregister.show');
        Route::post('/admission/reregister/{token}', [AdmissionController::class, 'submitReRegistration'])->name('user.reregister.submit');
    });

    // User Management
    Route::middleware('permission:admin.users')->group(function () {
        Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
        Route::post('/admin/users/{id}/reset-password', [AdminController::class, 'resetPassword'])->name('admin.users.reset');
        Route::post('/admin/users/{id}/change-role', [AdminController::class, 'changeRole'])->name('admin.users.role');
        Route::post('/admin/users/{id}/restore', [AdminController::class, 'restoreUser'])->name('admin.users.restore');
        Route::delete('/admin/users/{id}/force', [AdminController::class, 'forceDeleteUser'])->name('admin.users.force_delete');
        Route::post('/admin/history/{id}/revert', [AdminController::class, 'revertHistory'])->name('admin.history.revert');
        Route::delete('/admin/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    });

    // Registration Queue & Reports
    Route::middleware('permission:admin.queue')->group(function () {
        Route::get('/admin/queue', [AdminController::class, 'queue'])->name('admin.queue');
        Route::get('/admin/tests', [AdminController::class, 'tests'])->name('admin.tests');
        Route::post('/admin/queue/{id}/approve', [AdminController::class, 'approveRegistration'])->name('admin.queue.approve');
        Route::post('/admin/queue/{id}/reject', [AdminController::class, 'rejectRegistration'])->name('admin.queue.reject');
        Route::post('/admin/tests/{id}/pass', [AdminController::class, 'passTestCandidate'])->name('admin.tests.pass');
        Route::post('/admin/tests/{id}/uncertain', [AdminController::class, 'setTestCandidateUncertain'])->name('admin.tests.uncertain');
        Route::post('/admin/tests/{id}/fail', [AdminController::class, 'failTestCandidate'])->name('admin.tests.fail');
        Route::get('/admin/queue/{id}/document/{type}', [AdminController::class, 'viewRegistrationDocument'])
            ->whereIn('type', ['kk', 'ijazah', 'akta_lahir'])
            ->name('admin.queue.document');
    });

    Route::middleware('permission:admin.reports')->group(function () {
        Route::get('/admin/reports', [AdminController::class, 'reports'])->name('admin.reports');
        Route::get('/admin/reports/export', [AdminController::class, 'exportReports'])->name('admin.reports.export');
        Route::get('/admin/reports/pdf', [AdminController::class, 'exportPdf'])->name('admin.reports.pdf');
    });

    // Super Admin Features
    Route::middleware('permission:admin.access')->group(function () {
        Route::get('/admin/access', [AdminController::class, 'access'])->name('admin.access');
        Route::post('/admin/access', [AdminController::class, 'updateAccess'])->name('admin.access.update');
    });

    Route::middleware('permission:admin.logs')->group(function () {
        Route::get('/admin/logs', [AdminController::class, 'logs'])->name('admin.logs');
    });

    Route::middleware('permission:admin.settings')->group(function () {
        Route::get('/admin/settings', [SettingController::class, 'index'])->name('admin.settings');
        Route::post('/admin/settings', [SettingController::class, 'update'])->name('admin.settings.update');
    });

    Route::middleware('permission:admin.backup')->group(function () {
        Route::get('/admin/backup', [BackupController::class, 'download'])->name('admin.backup');
    });
});
