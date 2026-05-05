<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostulantController;
use App\Http\Controllers\ExaminerController;
use App\Http\Controllers\SubCommissionController;
use App\Http\Controllers\PresidentController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Authentification (Public)
|--------------------------------------------------------------------------
*/
Route::get('/', fn() => redirect()->route('login'));
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Routes protégées par Auth
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    /*
    |----------------------------------------------------------------------
    | Postulant
    |----------------------------------------------------------------------
    */
    Route::middleware(['role:postulant'])->prefix('postulant')->name('postulant.')->group(function () {
        Route::get('/dashboard', [PostulantController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile',   [PostulantController::class, 'profile'])->name('profile');
        Route::post('/profile',  [PostulantController::class, 'updateProfile'])->name('profile.update');
        Route::get('/requests',  [PostulantController::class, 'showRequests'])->name('requests');
        Route::post('/requests', [PostulantController::class, 'submitRequest'])->name('requests.submit');
    });

    /*
    |----------------------------------------------------------------------
    | Examinateur
    |----------------------------------------------------------------------
    */
    Route::middleware(['role:examiner'])->prefix('examiner')->name('examiner.')->group(function () {
        Route::get('/dashboard',         [ExaminerController::class, 'dashboard'])->name('dashboard');
        Route::get('/notifications',     [ExaminerController::class, 'notifications'])->name('notifications');
        Route::get('/review/{id}',       [ExaminerController::class, 'showReview'])->name('review');
        Route::post('/review/{id}',      [ExaminerController::class, 'submitReview'])->name('review.submit');
    });

    /*
    |----------------------------------------------------------------------
    | Sous-Commission (Président de sous-commission)
    |----------------------------------------------------------------------
    */
    Route::middleware(['role:president-sub'])->prefix('subcommission')->name('subcommission.')->group(function () {
        Route::get('/dashboard',              [SubCommissionController::class, 'dashboard'])->name('dashboard');
        Route::get('/requests',               [SubCommissionController::class, 'requests'])->name('requests');
        Route::get('/assign/{id}',            [SubCommissionController::class, 'assign'])->name('assign');
        Route::post('/assign/{id}',           [SubCommissionController::class, 'doAssign'])->name('assign.post');
        Route::get('/reviews',                [SubCommissionController::class, 'reviews'])->name('reviews');
        Route::get('/consolidate/{id}',       [SubCommissionController::class, 'consolidate'])->name('consolidate');
        Route::post('/consolidate/{id}',      [SubCommissionController::class, 'doConsolidate'])->name('consolidate.post');
        Route::get('/transmit',               [SubCommissionController::class, 'transmit'])->name('transmit');
        Route::post('/transmit/{id}',         [SubCommissionController::class, 'doTransmit'])->name('transmit.post');
    });

    /*
    |----------------------------------------------------------------------
    | Président du Conseil
    |----------------------------------------------------------------------
    */
    Route::middleware(['role:president-council'])->prefix('president')->name('president.')->group(function () {
        Route::get('/dashboard',          [PresidentController::class, 'dashboard'])->name('dashboard');
        Route::get('/dossiers',           [PresidentController::class, 'dossiers'])->name('dossiers');
        Route::get('/dossier/{id}',       [PresidentController::class, 'dossierDetail'])->name('dossier.detail');
        Route::post('/dossier/{id}/decide', [PresidentController::class, 'decide'])->name('dossier.decide');
        Route::get('/commissions',        [PresidentController::class, 'commissions'])->name('commissions');
    });

    /*
    |----------------------------------------------------------------------
    | Administrateur
    |----------------------------------------------------------------------
    */
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users',              [AdminController::class, 'users'])->name('users');
        Route::post('/users',             [AdminController::class, 'createUser'])->name('users.create');
        Route::delete('/users/{id}',      [AdminController::class, 'deleteUser'])->name('users.delete');
        Route::get('/commissions',        [AdminController::class, 'commissions'])->name('commissions');
        Route::post('/commissions',       [AdminController::class, 'createCommission'])->name('commissions.create');
        Route::get('/personnel',          [AdminController::class, 'personnelManagement'])->name('personnel');
    });

});
