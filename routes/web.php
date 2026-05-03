<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () { return view('auth.login'); })->name('login');

Route::get('/dashboard', function () {
    return view('postulant.dashboard');
})->name('dashboard');

// Postulant routes
Route::get('/postulant/dashboard', function () { return view('postulant.dashboard'); });
Route::get('/postulant/profile',   function () { return view('postulant.profile'); });
Route::get('/postulant/requests',  function () { return view('postulant.requests'); });

// Other role routes
Route::get('/examiner/dashboard', function () { return view('examiner.dashboard'); });
Route::get('/examiner/notifications', function () { return view('examiner.notifications'); });
Route::get('/examiner/review/{id}', function ($id) { return view('examiner.review', ['id' => $id]); });
Route::get('/subcommission/dashboard', function () { return view('subcommission.dashboard'); });
Route::get('/subcommission/requests', function () { return view('subcommission.requests'); });
Route::get('/subcommission/assign/{id}', function ($id) { return view('subcommission.assign', ['id' => $id]); });
Route::get('/subcommission/reviews', function () { return view('subcommission.reviews'); });
Route::get('/subcommission/consolidate/{id}', function ($id) { return view('subcommission.consolidate', ['id' => $id]); });
Route::get('/subcommission/transmit', function () { return view('subcommission.transmit'); });
Route::get('/president/dashboard', function () { return view('president.dashboard'); });
Route::get('/president/dossiers', function () { return view('president.dossiers'); });
Route::get('/president/dossier/{id}', function ($id) { return view('president.dossier-detail', ['id' => $id]); });
Route::get('/president/commissions', function () { return view('president.commissions'); });
Route::get('/admin/users', function () { return view('admin.users'); });
Route::get('/admin/commissions', function () { return view('admin.subcommissions'); });
