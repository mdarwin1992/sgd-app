<?php

use App\Http\Controllers\reports\ReportsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('login', function () {
    return view('components.auth.login');
});

Route::get('forbidden', function () {
    return view('components.errors.forbidden');
});

Route::get('dashboard', function () {
    return view('components.dashboard.dashboard');
});

Route::get('/dashboard/enviar', function () {
    return view('components.sendings.create');
});

Route::get('dashboard/departamentos', function () {
    return view('components.departments.index');
});

Route::get('dashboard/departamento/crear', function () {
    return view('components.departments.create');
});

Route::get('dashboard/departamento/actualizar/{id}', function () {
    return view('components.departments.update');
});

Route::get('dashboard/oficinas', function () {
    return view('components.offices.index');
});

Route::get('dashboard/oficina/crear', function () {
    return view('components.offices.create');
});

Route::get('dashboard/oficina/actualizar/{id}', function () {
    return view('components.offices.update');
});

Route::get('/dashboard/recepcion', function () {
    return view('components.reception.create');
});

Route::get('/dashboard/transferir-correspondencias-recibidas', function () {
    return view('components.transfer.index');
});

Route::get('/dashboard/transferir-correspondencias/{id}', function () {
    return view('components.transfer.create');
});

Route::get('/dashboard/mi-buzon', function () {
    return view('components.mailbox.index');
});

Route::get('/dashboard/mi-buzon/responder/{id}', function () {
    return view('components.mailbox.create');
});

Route::get('/dashboard/enviar', function () {
    return view('components.sendings.create');
});

Route::get('/dashboard/ticket/qr/{id}/{iten}', function () {
    return view('viewer.qr');
});

Route::get('/dashboard/reportes', function () {
    return view('reports.reports.index');
});


Route::get('/dashboard/show-file/{id}/{item}', function ($id, $item) {
    return view('viewer.file', compact('id', 'item'));
})->name('dashboard.show-file');


Route::get('/dashboard/show-response/{id}/{item}', function ($id, $item) {
    return view('viewer.response', compact('id', 'item'));
})->name('dashboard.show-response');

Route::get('/dashboard/ticket/{id}/{iten}', [ReportsController::class, 'generatePDF']);

