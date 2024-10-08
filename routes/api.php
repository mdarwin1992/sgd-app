<?php

use App\Http\Controllers\auth\LoginController;
use App\Http\Controllers\auth\RegisterController;
use App\Http\Controllers\dashboard\correspondencetransfer\CorrespondenceTransferController;
use App\Http\Controllers\dashboard\department\DepartmentController;
use App\Http\Controllers\dashboard\documentsending\DocumentSendingController;
use App\Http\Controllers\dashboard\entity\EntityController;
use App\Http\Controllers\dashboard\office\OfficeController;
use App\Http\Controllers\dashboard\reception\ReceptionController;
use App\Http\Controllers\dashboard\requestresponse\RequestResponseController;
use App\Http\Controllers\reports\ReportsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('authenticate/register', [RegisterController::class, 'store']);
Route::post('authenticate/login', [LoginController::class, 'login']);
Route::get('/generate-ticket', [ReportsController::class, 'generatePDF']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('authenticate/logout', [LoginController::class, 'logout']);

    Route::get('dashboard/entities', [EntityController::class, 'index'])->name('api.entities.index');
    Route::post('dashboard/entity/store', [EntityController::class, 'store'])->name('api.entity.store');
    Route::get('dashboard/entity/show/{id}', [EntityController::class, 'show'])->name('api.entity.show');
    Route::patch('dashboard/entity/update/{id}', [EntityController::class, 'update'])->name('api.entity.update');
    Route::delete('dashboard/entity/destroy/{id}', [EntityController::class, 'destroy'])->name('api.entity.destroy');

    Route::get('dashboard/departments', [DepartmentController::class, 'index'])->name('api.departments.index');
    Route::post('dashboard/department/store', [DepartmentController::class, 'store'])->name('api.department.store');
    Route::get('dashboard/department/show/{id}', [DepartmentController::class, 'show'])->name('api.department.show');
    Route::patch('dashboard/department/update/{id}', [DepartmentController::class, 'update'])->name('api.department.update');
    Route::delete('dashboard/department/destroy/{id}', [DepartmentController::class, 'destroy'])->name('api.department.destroy');

    Route::get('dashboard/offices', [OfficeController::class, 'index'])->name('api.offices.index');
    Route::post('dashboard/office/store', [OfficeController::class, 'store'])->name('api.office.store');
    Route::get('dashboard/office/show/{id}', [OfficeController::class, 'show'])->name('api.office.show');
    Route::patch('dashboard/office/update/{id}', [OfficeController::class, 'update'])->name('api.office.update');
    Route::delete('dashboard/office/destroy/{id}', [OfficeController::class, 'destroy'])->name('api.office.destroy');


    Route::get('dashboard/reception', [ReceptionController::class, 'index'])->name('api.reception.index');
    Route::post('dashboard/reception/store', [ReceptionController::class, 'store'])->name('api.reception.store');
    Route::get('dashboard/reception/show/{id}', [ReceptionController::class, 'show'])->name('api.reception.show');
    Route::patch('dashboard/reception/update/{id}', [ReceptionController::class, 'update'])->name('api.reception.update');
    Route::delete('dashboard/reception/destroy/{id}', [ReceptionController::class, 'destroy'])->name('api.reception.destroy');

    Route::get('dashboard/correspondence-transfer', [CorrespondenceTransferController::class, 'index'])->name('api.correspondence.transfer.index');
    Route::post('dashboard/correspondence-transfer/store', [CorrespondenceTransferController::class, 'store'])->name('api.correspondence.transfer.store');
    Route::get('dashboard/correspondence-transfer/show/{id}', [CorrespondenceTransferController::class, 'show'])->name('api.correspondence.transfer.show');
    Route::patch('dashboard/correspondence-transfer/update/{id}', [CorrespondenceTransferController::class, 'update'])->name('api.correspondence.transfer.update');
    Route::delete('dashboard/correspondence-transfer/destroy/{id}', [CorrespondenceTransferController::class, 'destroy'])->name('api.correspondence.transfer.destroy');

    Route::get('dashboard/request-response', [RequestResponseController::class, 'index'])->name('api.request.response.index');
    Route::post('dashboard/request-response/store', [RequestResponseController::class, 'store'])->name('api.request.response.store');
    Route::get('dashboard/request-response/show/{id}', [RequestResponseController::class, 'show'])->name('api.request.response.show');
    Route::patch('dashboard/request-response/update/{id}', [RequestResponseController::class, 'update'])->name('api.request.response.update');
    Route::delete('dashboard/request-response/destroy/{id}', [RequestResponseController::class, 'destroy'])->name('api.request.response.destroy');

    Route::get('dashboard/document-sendings', [DocumentSendingController::class, 'index'])->name('api.document.sendings.index');
    Route::post('dashboard/document-sendings/store', [DocumentSendingController::class, 'store'])->name('api.document.sendings.index');
    Route::get('dashboard/document-sendings/{id}', [DocumentSendingController::class, 'show'])->name('api.document.sendings.index');
    Route::patch('dashboard/document-sendings/update/{id}', [DocumentSendingController::class, 'update'])->name('api.document.sendings.index');
    Route::delete('dashboard/document-sendings/destroy/{id}', [DocumentSendingController::class, 'destroy'])->name('api.document.sendings.index');

});
