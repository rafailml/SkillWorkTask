<?php

use App\Http\Controllers\v1\Auth\ChangePasswordController;
use App\Http\Controllers\v1\Auth\ForgottenPasswordController;
use App\Http\Controllers\v1\Auth\LoginController;
use App\Http\Controllers\v1\Auth\RegisterController;
use App\Http\Controllers\v1\CompaniesResultsController;
use App\Http\Controllers\v1\CompanyResultController;
use App\Http\Controllers\v1\CompanyResultJobStatusController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| v1 API Routes
|--------------------------------------------------------------------------
|
*/

Route::post('/login', LoginController::class)->name('v1.login');
Route::post('/sign-in', RegisterController::class)->name('v1.register');
Route::post('/forgotten-password', ForgottenPasswordController::class)->name('v1.forgotten-password');
Route::post('/change-password', ChangePasswordController::class)->name('v1.change-password');

Route::middleware(['api', 'api_token'])->group(function () {

    Route::post('/company', CompanyResultController::class)->name('v1.company-inquiry');

    // Return all results for user
    Route::get('/companies-results', CompaniesResultsController::class)->name('v1.company-results');

    // Return status of job
    Route::get('/company-result-status', CompanyResultJobStatusController::class)->name('v1.company-result-status');
});
