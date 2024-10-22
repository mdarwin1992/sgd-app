<?php

use App\Http\Controllers\auth\LoginController;
use App\Http\Controllers\auth\RegisterController;
use App\Http\Controllers\dashboard\correspondencetransfer\CorrespondenceTransferController;
use App\Http\Controllers\dashboard\department\DepartmentController;
use App\Http\Controllers\dashboard\documentsending\DocumentSendingController;
use App\Http\Controllers\dashboard\entity\EntityController;
use App\Http\Controllers\dashboard\mailbox\MailboxController;
use App\Http\Controllers\dashboard\office\OfficeController;
use App\Http\Controllers\dashboard\reception\ReceptionController;
use App\Http\Controllers\dashboard\report\ReportController;
use App\Http\Controllers\dashboard\retenciondocumental\RetencionDocumentalController;
use App\Http\Controllers\dashboard\user\UserController;
use App\Http\Controllers\helpers\CounterController;
use App\Http\Controllers\helpers\FilesController;
use App\Http\Controllers\helpers\HelpersController;
use App\Http\Controllers\notificaciones\NotificationController;
use App\Http\Controllers\reports\CalendarReportController;
use App\Http\Controllers\reports\GeneralReportControllers;
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

Route::get('dashboard/qr/{id}/{iten}', [ReportsController::class, 'generateQrCode']);

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
    Route::get('dashboard/departments/{id}/offices', [OfficeController::class, 'department'])->name('api.office.department');
    Route::get('dashboard/office/manager/{id}', [OfficeController::class, 'officeManager'])->name('api.office.manager');
    Route::patch('dashboard/office/update/{id}', [OfficeController::class, 'update'])->name('api.office.update');
    Route::delete('dashboard/office/destroy/{id}', [OfficeController::class, 'destroy'])->name('api.office.destroy');

    Route::get('dashboard/users', [UserController::class, 'index'])->name('api.users.index');
    Route::get('dashboard/users/list', [UserController::class, 'list'])->name('api.users.list');


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

    Route::get('/dashboard/correspondence-transfer/office', [CorrespondenceTransferController::class, 'getCorrespondenceTransfer']);
    Route::get('/dashboard/mailbox/office', [MailboxController::class, 'getMailbox']);

    Route::get('dashboard/mailbox', [MailboxController::class, 'index'])->name('api.mailbox.index');
    Route::post('dashboard/mailbox/store', [MailboxController::class, 'store'])->name('api.mailbox.store');
    Route::get('dashboard/mailbox/show/{id}', [MailboxController::class, 'show'])->name('api.mailbox.show');
    Route::patch('dashboard/mailbox/update/{id}', [MailboxController::class, 'update'])->name('api.mailbox.update');
    Route::delete('dashboard/mailbox/destroy/{id}', [MailboxController::class, 'destroy'])->name('api.mailbox.destroy');

    Route::get('dashboard/document-sendings', [DocumentSendingController::class, 'index'])->name('api.document.sendings.index');
    Route::post('dashboard/document-sendings/store', [DocumentSendingController::class, 'store'])->name('api.document.sendings.store');
    Route::get('dashboard/document-sendings/{id}', [DocumentSendingController::class, 'show'])->name('api.document.sendings.show');
    Route::patch('dashboard/document-sendings/update/{id}', [DocumentSendingController::class, 'update'])->name('api.document.sendings.update');
    Route::delete('dashboard/document-sendings/destroy/{id}', [DocumentSendingController::class, 'destroy'])->name('api.document.sendings.destroy');

    Route::get('dashboard/retencion-documental', [RetencionDocumentalController::class, 'index'])->name('api.retencion-documental.index');
    Route::post('dashboard/retencion-documental/store', [RetencionDocumentalController::class, 'store'])->name('api.retencion-documental.store');
    Route::get('dashboard/retencion-documental/{id}', [RetencionDocumentalController::class, 'show'])->name('api.retencion-documental.show');
    Route::patch('dashboard/retencion-documental/update/{id}', [RetencionDocumentalController::class, 'update'])->name('api.retencion-documental.update');
    Route::delete('dashboard/retencion-documental/destroy/{id}', [RetencionDocumentalController::class, 'destroy'])->name('api.retencion-documental.destroy');
    Route::post('dashboard/series/store/series', [RetencionDocumentalController::class, 'series'])->name('api.retencion-documental.series');
    Route::get('dashboard/series/{id}', [RetencionDocumentalController::class, 'loadSeries'])->name('api.retencion-documental.series');

});
Route::post('/dashboard/upload', [FilesController::class, 'upload'])->name('files.upload');
Route::post('/dashboard/response/upload', [FilesController::class, 'responseFile'])->name('files.upload');

Route::get('/entity/{entityId}/counter', [HelpersController::class, 'showCounter']);
Route::get('/entity/counters', [HelpersController::class, 'listCounters']);

Route::get('/reports/projections-vs-actuals', [ReportsController::class, 'getProjectionsVsActuals']);

Route::get('/reports/document-process-timeline', [CalendarReportController::class, 'getDocumentProcessTimeline']);
Route::post('/reports/generate', [GeneralReportControllers::class, 'generateReport']);

// New routes for report generation
Route::get('/reports/generate', [GeneralReportControllers::class, 'generatePDF']);
Route::get('/departments', [GeneralReportControllers::class, 'getDepartments']);
Route::get('/offices', [GeneralReportControllers::class, 'getOffices']);

Route::post('/reports/generate-pdf', [GeneralReportControllers::class, 'generatePdfReport']);

Route::get('/notifications/{id}', [NotificationController::class, 'getUnreadNotifications']);
Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);

Route::get('/get-all-counters/{id}', [CounterController::class, 'getAllCounters']);
Route::post('/serie/counters/{id}', [CounterController::class, 'incrementOrCreate']);
