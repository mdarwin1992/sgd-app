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

Route::get('not-found', function () {
    return view('components.errors.not-found');
});

/** Rutas del dashboard */

Route::get('dashboard', function () {
    return view('components.dashboard.dashboard');
})->name('dashboard');

/** Rutas de la empresa */

Route::get('dashboard/empresa', function () {
    return view('components.business.index');
})->name('business.index');

Route::get('dashboard/empresa/crear', function () {
    return view('components.business.create');
})->name('business.create');

Route::get('dashboard/empresa/actualizar/{id}', function () {
    return view('components.business.update');
})->name('business.update');


/** Rutas del Configuracion - departamentos */

Route::get('dashboard/departamentos', function () {
    return view('components.departments.index');
})->name('departments.index');

Route::get('dashboard/departamento/crear', function () {
    return view('components.departments.create');
})->name('departments.create');

Route::get('dashboard/departamento/actualizar/{id}', function () {
    return view('components.departments.update');
})->name('departments.update');

/** ########################################################################################## */

/** Rutas del Configuracion - Oficinas */

Route::get('dashboard/oficinas', function () {
    return view('components.offices.index');
})->name('offices.index');

Route::get('dashboard/oficina/crear', function () {
    return view('components.offices.create');
})->name('offices.create');

Route::get('dashboard/oficina/actualizar/{id}', function () {
    return view('components.offices.update');
})->name('offices.update');

/** ########################################################################################## */

/** Rutas del Configuracion - tabla de retencion documental */
Route::get('/dashboard/tabla-de-retencion-documental', function () {
    return view('components.trd.create');
})->name('trd.create');
/** ########################################################################################## */

/** Rutas para la ventanilla unica - reception */
Route::get('/dashboard/ventanilla-unica/recepcion', function () {
    return view('components.reception.create');
})->name('reception.create');

/** Rutas para la ventanilla unica - transferir correspondencias recibidas */
Route::get('/dashboard/ventanilla-unica/transferir-correspondencias-recibidas', function () {
    return view('components.transfer.index');
})->name('transfer.index');

Route::get('/dashboard/ventanilla-unica/transferir-correspondencias/{id}', function () {
    return view('components.transfer.create');
})->name('transfer.create');

/** Rutas para la ventanilla unica - mi buzon */
Route::get('/dashboard/ventanilla-unica/mi-buzon', function () {
    return view('components.mailbox.index');
})->name('mailbox.index');

Route::get('/dashboard/ventanilla-unica/mi-buzon/responder/{id}', function () {
    return view('components.mailbox.create');
})->name('mailbox.update');

/** Rutas para la ventanilla unica - enviar */
Route::get('/dashboard/ventanilla-unica/enviar', function () {
    return view('components.sendings.create');
})->name('sendings.create');
/** ########################################################################################## */

/** Rutas para el archivo cental */
Route::get('/dashboard/archivo-central', function () {
    return view('components.centralfile.create');
})->name('centralfile.create');

Route::get('/dashboard/archivo-central/consultas', function () {
    return view('components.query.consultation');
})->name('query.consultation');

/** ########################################################################################## */

Route::get('/dashboard/archivo-historico', function () {
    return view('components.historicFile.index');
})->name('historic.file');

Route::get('/dashboard/archivo-historico/crear', function () {
    return view('components.historicFile.create');
})->name('historic.file');

/** ########################################################################################## */

Route::get('/dashboard/prestamos-documental', function () {
    return view('components.historicFile.index');
})->name('historic.file');

/** ########################################################################################## */

/** Rutas para los Reportes - ventanilla unica */
Route::get('/dashboard/ventanilla-unica/reportes', function () {
    return view('components.reports.singleWindow.singlewindow');
})->name('document.loans');

/** ########################################################################################## */

/** Usuarios */
Route::get('/dashboard/usuarios', function () {
    return view('components.users.index');
})->name('users.index');

/** Usuarios */
Route::get('/dashboard/usuario/crear', function () {
    return view('components.users.create');
})->name('users.create');

/** Usuarios */
Route::get('/dashboard/usuarios/actualizar/{id}', function () {
    return view('components.users.update');
})->name('users.update');

/** ########################################################################################## */

/** Rutas para los Reportes - ticket */
Route::get('/dashboard/ticket/{id}/{iten}', [ReportsController::class, 'generatePDF']);

/** Rutas para los Reportes - visualizacion del QR */
Route::get('/dashboard/ticket/qr/{id}/{iten}', function () {
    return view('viewer.qr');
});

/** Rutas para los Reportes - tabla de retencion documental */
Route::get('/reportes/tabla-de-retencion-documental/{id}', [ReportsController::class, 'generateTrdPDF']);

Route::get('/reportes/rotulos/{id}', [ReportsController::class, 'generateEtiquetaPDF']);
Route::get('/reportes/tiquete/{id}', [ReportsController::class, 'generateReceipt']);


/** ########################################################################################## */

/** Rutas para los Reportes - visualizacion de Documento PDF por correo para ventanilla unica */
Route::get('/dashboard/show-file/{id}/{item}', function ($id, $item) {
    return view('viewer.file', compact('id', 'item'));
})->name('dashboard.show-file');

/** Rutas para los Reportes - visualizacion de Documento PDF por correo para respuesta de docuementos */
Route::get('/dashboard/show-response/{id}/{item}', function ($id, $item) {
    return view('viewer.response', compact('id', 'item'));
})->name('dashboard.show-response');

/** ########################################################################################## */

// Ruta para prestamos documentales
Route::get('/dashboard/prestamos-documental', function () {
    return view('components.loans.index');
})->name('lending.index');

Route::get('/dashboard/prestamos-documental/archivo-central', function () {
    return view('components.loans.centralarchive');
})->name('lending.create');

Route::get('/dashboard/prestamos-documental/archivo-historico', function () {
    return view('components.loans.historicalarchive');
})->name('lending.create');

/** ########################################################################################## */

Route::get('/dashboard/access-roles', function () {
    return view('components.rolesandpermissions.index');
})->name('roles.index');
