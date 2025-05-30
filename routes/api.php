<?php

use App\Http\Controllers\auth\LoginController;
use App\Http\Controllers\auth\RegisterController;
use App\Http\Controllers\dashboard\centralarchive\CentralArchiveController;
use App\Http\Controllers\dashboard\consultation\ConsultationController;
use App\Http\Controllers\dashboard\correspondencetransfer\CorrespondenceTransferController;
use App\Http\Controllers\dashboard\department\DepartmentController;
use App\Http\Controllers\dashboard\documentloan\DocumentLoanController;
use App\Http\Controllers\dashboard\documentsending\DocumentSendingController;
use App\Http\Controllers\dashboard\entity\EntityController;
use App\Http\Controllers\dashboard\historicfile\HistoricFileController;
use App\Http\Controllers\dashboard\mailbox\MailboxController;
use App\Http\Controllers\dashboard\office\OfficeController;
use App\Http\Controllers\dashboard\reception\ReceptionController;
use App\Http\Controllers\dashboard\report\ReportController;
use App\Http\Controllers\dashboard\retenciondocumental\RetencionDocumentalController;
use App\Http\Controllers\dashboard\series\SeriesController;
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
use Spatie\Permission\Contracts\Role;

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

// Authentication routes
Route::post('authenticate/register', [RegisterController::class, 'store']);
Route::post('authenticate/login', [LoginController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('authenticate/logout', [LoginController::class, 'logout']);
    Route::post('/refresh-token', [LoginController::class, 'refreshToken']);
});

// QR Code generation
Route::get('dashboard/qr/{id}/{iten}', [ReportsController::class, 'generateQrCode']);

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {


    // Entity routes
    Route::get('dashboard/entities', [EntityController::class, 'index'])->name('api.entities.index');
    Route::post('dashboard/entity/store', [EntityController::class, 'store'])->name('api.entity.store');
    Route::get('dashboard/entity/show/{id}', [EntityController::class, 'show'])->name('api.entity.show');
    Route::patch('dashboard/entity/update/{id}', [EntityController::class, 'update'])->name('api.entity.update');
    Route::delete('dashboard/entity/destroy/{id}', [EntityController::class, 'destroy'])->name('api.entity.destroy');

    // Department routes
    Route::get('dashboard/departments', [DepartmentController::class, 'index'])->name('api.departments.index');
    Route::post('dashboard/department/store', [DepartmentController::class, 'store'])->name('api.department.store');
    Route::get('dashboard/department/show/{id}', [DepartmentController::class, 'show'])->name('api.department.show');
    Route::patch('dashboard/department/update/{id}', [DepartmentController::class, 'update'])->name('api.department.update');
    Route::delete('dashboard/department/destroy/{id}', [DepartmentController::class, 'destroy'])->name('api.department.destroy');

    // Office routes
    Route::get('dashboard/offices', [OfficeController::class, 'index'])->name('api.offices.index');
    Route::post('dashboard/office/store', [OfficeController::class, 'store'])->name('api.office.store');
    Route::get('dashboard/office/show/{id}', [OfficeController::class, 'show'])->name('api.office.show');
    Route::get('dashboard/office/show/offices/{id}', [OfficeController::class, 'getOffices'])->name('api.office.getOffices');
    Route::get('dashboard/departments/{id}/offices', [OfficeController::class, 'department'])->name('api.office.department');
    Route::get('dashboard/office/manager/{id}', [OfficeController::class, 'officeManager'])->name('api.office.manager');
    Route::patch('dashboard/office/update/{id}', [OfficeController::class, 'update'])->name('api.office.update');
    Route::delete('dashboard/office/destroy/{id}', [OfficeController::class, 'destroy'])->name('api.office.destroy');
    Route::get('dashboard/offices/{officeId}/series', [OfficeController::class, 'series'])->name('api.office.series');
    Route::get('dashboard/series/{seriesId}/subseries', [OfficeController::class, 'subseries'])->name('api.office.subseries');

    // User routes
    //Route::get('dashboard/users', [UserController::class, 'index'])->name('api.users.index');
    Route::get('dashboard/users/list', [UserController::class, 'list'])->name('api.users.list');

    Route::prefix('dashboard')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/{id}', [UserController::class, 'show']);
        Route::post('/users', [UserController::class, 'store']);
        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);

        // Rutas adicionales para roles y permisos
        Route::get('/roles', [UserController::class, 'getRoles']);
        Route::get('/permissions', [UserController::class, 'getPermissions']);
        Route::get('/permissions', [UserController::class, 'getPermissions']);
    });
    Route::get('/users/pesrmissions/{id}', [UserController::class, 'getUserPermissionsById']);
    Route::get('/dashboard/roles/{id}/has-permissions', [UserController::class, 'checkRoleHasPermissions']);

    // Reception routes
    Route::get('dashboard/reception', [ReceptionController::class, 'index'])->name('api.reception.index');
    Route::post('dashboard/reception/store', [ReceptionController::class, 'store'])->name('api.reception.store');
    Route::get('dashboard/reception/show/{id}', [ReceptionController::class, 'show'])->name('api.reception.show');
    Route::patch('dashboard/reception/update/{id}', [ReceptionController::class, 'update'])->name('api.reception.update');
    Route::delete('dashboard/reception/destroy/{id}', [ReceptionController::class, 'destroy'])->name('api.reception.destroy');
    Route::get('dashboard/reception/show/transferir-correspondencias', [ReceptionController::class, 'index'])->name('api.reception.index');


    // Correspondence Transfer routes
    Route::get('dashboard/correspondence-transfer', [CorrespondenceTransferController::class, 'index'])->name('api.correspondence.transfer.index');
    Route::post('dashboard/correspondence-transfer/store', [CorrespondenceTransferController::class, 'store'])->name('api.correspondence.transfer.store');
    Route::get('dashboard/correspondence-transfer/show/{id}', [CorrespondenceTransferController::class, 'show'])->name('api.correspondence.transfer.show');
    Route::patch('dashboard/correspondence-transfer/update/{id}', [CorrespondenceTransferController::class, 'update'])->name('api.correspondence.transfer.update');
    Route::delete('dashboard/correspondence-transfer/destroy/{id}', [CorrespondenceTransferController::class, 'destroy'])->name('api.correspondence.transfer.destroy');
    Route::get('/dashboard/correspondence-transfer/office/{id}', [CorrespondenceTransferController::class, 'getCorrespondenceTransfer']);
    Route::get('/dashboard/mailbox/office/{id}', [MailboxController::class, 'getMailbox']);

    // Mailbox routes 
    Route::get('dashboard/mailbox', [MailboxController::class, 'index'])->name('api.mailbox.index');
    Route::post('dashboard/mailbox/store', [MailboxController::class, 'store'])->name('api.mailbox.store');
    Route::get('dashboard/mailbox/show/{id}', [MailboxController::class, 'show'])->name('api.mailbox.show');
    Route::patch('dashboard/mailbox/update/{id}', [MailboxController::class, 'update'])->name('api.mailbox.update');
    Route::delete('dashboard/mailbox/destroy/{id}', [MailboxController::class, 'destroy'])->name('api.mailbox.destroy');

    // Document Sending routes
    Route::get('dashboard/document-sendings', [DocumentSendingController::class, 'index'])->name('api.document.sendings.index');
    Route::post('dashboard/document-sendings/store', [DocumentSendingController::class, 'store'])->name('api.document.sendings.store');
    Route::get('dashboard/document-sendings/{id}', [DocumentSendingController::class, 'show'])->name('api.document.sendings.show');
    Route::patch('dashboard/document-sendings/update/{id}', [DocumentSendingController::class, 'update'])->name('api.document.sendings.update');
    Route::delete('dashboard/document-sendings/destroy/{id}', [DocumentSendingController::class, 'destroy'])->name('api.document.sendings.destroy');

    // Retention Documental routes
    Route::get('dashboard/retencion-documental', [RetencionDocumentalController::class, 'index'])->name('api.retencion-documental.index');
    Route::post('dashboard/retencion-documental/store', [RetencionDocumentalController::class, 'store'])->name('api.retencion-documental.store');
    Route::get('dashboard/retencion-documental/{id}', [RetencionDocumentalController::class, 'show'])->name('api.retencion-documental.show');
    Route::patch('dashboard/retencion-documental/update/{id}', [RetencionDocumentalController::class, 'update'])->name('api.retencion-documental.update');
    Route::delete('dashboard/retencion-documental/destroy/{id}', [RetencionDocumentalController::class, 'destroy'])->name('api.retencion-documental.destroy');
    Route::get('dashboard/series/used-series', [RetencionDocumentalController::class, 'getUsedSeries']);

    // Series routes
    Route::post('dashboard/series/store', [SeriesController::class, 'store'])->name('api.series.store');
    Route::get('dashboard/series/{id}', [SeriesController::class, 'show'])->name('api.series.show');

    // Central Archive routes
    Route::get('dashboard/central-archive', [CentralArchiveController::class, 'index'])->name('api.central.archive.index');
    Route::post('dashboard/central-archive/store', [CentralArchiveController::class, 'store'])->name('api.central.archive.store');
    Route::get('dashboard/central-archive/{id}', [CentralArchiveController::class, 'show'])->name('api.central.archive.show');
    Route::patch('dashboard/central-archive/update/{id}', [CentralArchiveController::class, 'update'])->name('api.central.archive.update');
    Route::delete('dashboard/central-archive/destroy/{id}', [CentralArchiveController::class, 'destroy'])->name('api.central.archive.destroy');

    // Consultation routes
    Route::get('/dashboard/find-document/{id}', [ConsultationController::class, 'findDocument']);
    Route::get('/dashboard/search-by-box/{id}', [ConsultationController::class, 'searchByBox']);
    Route::get('/dashboard/search-by-serial/{id}', [ConsultationController::class, 'searchBySerial']);
    Route::get('/dashboard/search-by-year/{id}', [ConsultationController::class, 'searchByYear']);

    Route::get('/dashboard/historical-archive/search-by-year/{id}', [ConsultationController::class, 'searchByYearHistoric']);
    Route::get('/dashboard/historical-archive/search-by-box/{id}', [ConsultationController::class, 'searchByBoxHistoric']);
    Route::get('/dashboard/historical-archive/find-document/{id}', [ConsultationController::class, 'findDocumentHistoric']);
    Route::get('/dashboard/historical-archive/search-by-serial/{id}', [ConsultationController::class, 'searchBySerialHistoric']);

    // Central Archive routes
    Route::get('dashboard/historical-archive', [HistoricFileController::class, 'index'])->name('api.historical.archive.index');
    Route::post('dashboard/historical-archive/store', [HistoricFileController::class, 'store'])->name('api.historical.archive.store');
    Route::get('dashboard/historical-archive/{id}', [HistoricFileController::class, 'show'])->name('api.historical.archive.show');
    Route::patch('dashboard/historical-archive/update/{id}', [HistoricFileController::class, 'update'])->name('api.historical.archive.update');
    Route::delete('dashboard/historical-archive/destroy/{id}', [HistoricFileController::class, 'destroy'])->name('api.historical.archive.destroy');

    //Prestamos Documentales archivo central 
    Route::get('/dashboard/document-loan', [DocumentLoanController::class, 'index']);
    Route::post('/dashboard/document-loan', [DocumentLoanController::class, 'store']);
    Route::get('/dashboard/document-loan/{id}/central-archive', [DocumentLoanController::class, 'getLoanCentralArchive']);
    Route::post('/document-loans/return', [DocumentLoanController::class, 'returnDocument']);
    Route::get('/document-loans/order-number', [DocumentLoanController::class, 'getOrderNumber']);

    // Helper routes
    Route::get('/entity/{entityId}/counter/{code}', [HelpersController::class, 'showCounter']);
    Route::get('/entity/counters', [HelpersController::class, 'listCounters']);

    // File upload routes
    Route::post('/dashboard/upload', [FilesController::class, 'upload'])->name('files.upload');
    Route::post('/dashboard/upload/logo', [FilesController::class, 'logosFiles'])->name('files.logosFiles');
    Route::post('/dashboard/response/upload', [FilesController::class, 'responseFile'])->name('files.upload.responsefile');
    Route::post('/dashboard/central-archive/upload', [FilesController::class, 'CentralArchiveFile'])->name('files.upload.centralarchivefile');
    Route::post('/dashboard/historical-file/upload', [FilesController::class, 'HistoricalFile'])->name('files.upload.historicalarchive');

    Route::post('/single-window/reports/generate', [GeneralReportControllers::class, 'generateReport'])->name('single.window.reports.generate');
    Route::post('/single-window/reports/generate-pdf', [GeneralReportControllers::class, 'generatePdfReport'])->name('single.window.reports.pdf');
    Route::post('/reports/loans/generate', [GeneralReportControllers::class, 'generateReport']);
    Route::post('/reports/loans/generate-pdf', [GeneralReportControllers::class, 'generatePdfReport']);


    // Notification routes
    Route::get('/notifications/unread/{userId}', [NotificationController::class, 'getUnreadNotifications']);
    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead']);

    // Counter routes
    Route::get('/get-all-counters/{id}', [CounterController::class, 'getAllCounters']);
    Route::post('/serie/counters/{id}', [CounterController::class, 'incrementOrCreate']);

    // General Stats route
    Route::get('/document-statistics', [ReportsController::class, 'getDocumentStatistics']);
    Route::get('/reports/recent-activities', [ReportsController::class, 'getArchiveAndDocumentRecords']);
    Route::post('/reports/recent-activities-calendar', [ReportsController::class, 'getArchiveAndDocumentRecordsCalendar']);
    // Report routes
    Route::get('/reports/projections-vs-actuals', [ReportsController::class, 'getProjectionsVsActuals']);
    Route::get('/reports/document-process-timeline', [CalendarReportController::class, 'getDocumentProcessTimeline']);
});


Route::get('/dashboard/retention-report', [ReportsController::class, 'generateAllEtiquetaPDF']);
