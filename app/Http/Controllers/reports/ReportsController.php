<?php

namespace App\Http\Controllers\reports;

use Exception;
use Carbon\Carbon;
use App\Models\Document;
use Barryvdh\DomPDF\PDF;
use App\Models\Reception;
use App\Models\Retention;
use Endroid\QrCode\QrCode;
use App\Models\DocumentLoan;
use App\Models\HistoricFile;
use Illuminate\Http\Request;
use App\Models\CentralArchive;
use App\Models\DocumentStatus;
use App\Models\DocumentSending;
use App\Models\RequestResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Endroid\QrCode\Writer\PngWriter;
use App\Models\CorrespondenceTransfer;
use App\Http\Controllers\helpers\HelpersController;

class ReportsController extends Controller
{
    private function parseDate($dateString)
    {
        // Decodificar la URL
        $decodedDate = urldecode($dateString);

        // Parsear la fecha
        $carbonDate = Carbon::parse($decodedDate);

        // Ajustar la zona horaria si es necesario
        // $carbonDate->setTimezone('America/Bogota');  // Descomenta y ajusta si necesitas una zona horaria específica

        return $carbonDate->format('Y-m-d H:i:s');
    }

    public function getProjectionsVsActuals(): JsonResponse
    {
        try {
            $year = request('year', date('Y'));

            $projections = Document::selectRaw('MONTH(DATE(received_date)) as month, COUNT(*) as count')
                ->whereRaw('YEAR(DATE(received_date)) = ?', [$year])
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('count', 'month')
                ->all();

            $statusCounts = Document::selectRaw('MONTH(DATE(document.received_date)) as month, document_status.status, COUNT(*) as count')
                ->join('document_status', 'document.id', '=', 'document_status.document_id')
                ->whereRaw('YEAR(DATE(document.received_date)) = ?', [$year])
                ->groupBy('month', 'document_status.status')
                ->orderBy('month')
                ->get()
                ->groupBy('month')
                ->map(function ($group) {
                    return $group->pluck('count', 'status');
                });

            $months = [
                1 => 'Ene',
                2 => 'Feb',
                3 => 'Mar',
                4 => 'Abr',
                5 => 'May',
                6 => 'Jun',
                7 => 'Jul',
                8 => 'Ago',
                9 => 'Sep',
                10 => 'Oct',
                11 => 'Nov',
                12 => 'Dic'
            ];

            $data = [];
            foreach ($months as $monthNum => $monthName) {
                $data[] = [
                    'month' => $monthName,
                    'projections' => $projections[$monthNum] ?? 0,
                    'recibida' => $statusCounts[$monthNum]['RECIBIDA'] ?? 0,
                    'procesando' => $statusCounts[$monthNum]['PROCESANDO'] ?? 0,
                    'contestado' => $statusCounts[$monthNum]['CONTESTADO'] ?? 0,
                    'archivado' => $statusCounts[$monthNum]['ARCHIVADO'] ?? 0,
                ];
            }

            return response()->json([
                'data' => $data,
                'year' => $year
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'An error occurred while processing the request.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function generatePDF($id, $iten)
    {
        // Consultar el documento por el código de referencia
        $document = Document::where('reference_code', $id)->first();

        // Verificar si el documento existe
        if (!$document) {
            return response()->json(['message' => 'Documento no encontrado'], 404);
        }

        // Generar el QR code
        $qrCode = QrCode::create('http://127.0.0.1:8000/dashboard/dashboard/ticket/qr/' . $id . '/' . $id)
            ->setSize(100)
            ->setMargin(0);
        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        // Guardar la imagen del QR code temporalmente
        $qrImagePath = storage_path('app/public/qr_' . $id . '.png');
        $result->saveToFile($qrImagePath);

        // Preparar los datos para el PDF
        $data = [
            'document' => $document,
            'entity' => HelpersController::getEntityData($iten)->name,
            'nit' => HelpersController::getEntityData($iten)->nit,
            'qrCode' => $qrImagePath // Agregar la ruta de la imagen QR
        ];

        // Generar el PDF utilizando una vista
        $pdf = PDF::loadView('reports.ticket.ticket', $data);

        // Ajustar el tamaño del papel (ejemplo: 80mm x 200mm)
        $pdf->setPaper([0, 0, 450, 180]); // Ancho y largo en puntos

        // Generar el PDF
        $pdfContent = $pdf->output();

        // Eliminar la imagen temporal del QR code
        unlink($qrImagePath);

        // Mostrar el PDF en el navegador
        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="document_' . $id . '.pdf"'
        ]);
    }

    public function getDocumentStatistics()
    {
        // Conteos básicos usando Eloquent
        $documentCount = Document::count();

        // Conteo de tareas por tipo
        $receptionCount = Reception::distinct('document_id')->count();
        $transferCount = CorrespondenceTransfer::distinct('document_id')->count();
        $responseCount = RequestResponse::distinct('document_id')->count();

        // Estadísticas de préstamos de documentos
        $loansCount = DocumentLoan::count();
        $activeLoansCount = DocumentLoan::where('state', 1)->count();
        $returnedLoansCount = DocumentLoan::where('state', 0)->count();

        // Calcular total de tareas (incluyendo préstamos como una tarea más)
        $totalTaskCount = $receptionCount + $transferCount + $responseCount + $loansCount;

        // Obtener conteos de estado
        $statusCounts = DocumentStatus::selectRaw('
        SUM(CASE WHEN status = "RECIBIDA" THEN 1 ELSE 0 END) as received_count,
        SUM(CASE WHEN status = "PROCESANDO" THEN 1 ELSE 0 END) as processing_count,
        SUM(CASE WHEN status = "CONTESTADO" THEN 1 ELSE 0 END) as completed_count')
            ->join('document', 'document.id', '=', 'document_status.document_id')
            ->first();

        // Calcular tasa de productividad (considerando préstamos devueltos como tareas completadas)
        $completedTasks = $statusCounts->completed_count + $returnedLoansCount;
        $productivityRate = $totalTaskCount > 0
            ? ($completedTasks / $totalTaskCount) * 100
            : 0;

        return response()->json([
            'totalDocuments' => $documentCount,
            'receptionCount' => $receptionCount,
            'transferCount' => $transferCount,
            'responseCount' => $responseCount,
            'totalLoans' => $loansCount,
            'activeLoans' => $activeLoansCount,
            'returnedLoans' => $returnedLoansCount,
            'totalTasks' => $totalTaskCount,
            'receivedDocuments' => $statusCounts->received_count,
            'processingTransfers' => $statusCounts->processing_count,
            'completedResponses' => $statusCounts->completed_count,
            'productivityPercentage' => round($productivityRate, 2)
        ]);
    }

    public function generateTrdPDF($entityId)
    {
        // Get entity information
        $entity = DB::table('entity')
            ->where('id', $entityId)
            ->first();

        // Get offices with department information
        $offices = DB::table('office')
            ->join('department', 'office.department_id', '=', 'department.id')
            ->join('users', 'office.user_id', '=', 'users.id')
            ->where('department.entity_id', $entityId)
            ->select('office.*', 'department.name as department_name', 'users.name as user_name')
            ->get();

        $officesData = [];

        foreach ($offices as $office) {
            // Get series for each office
            $series = DB::table('series')
                ->join('series_entity', 'series.series_entity_id', '=', 'series_entity.id')
                ->leftJoin('retention', 'series.id', '=', 'retention.series_id')
                ->leftJoin('final_disposition', function ($join) {
                    $join->on('series.id', '=', 'final_disposition.series_id')
                        ->whereRaw('final_disposition.id = (SELECT id FROM final_disposition f2 WHERE f2.series_id = series.id LIMIT 1)');
                })
                ->where('series_entity.entity_id', $entityId)
                ->where('series.office_id', $office->id)
                ->select(
                    'series.id',
                    'series.office_id',
                    'series.series_entity_id',
                    'series.series_code',
                    'series_entity.series_name',
                    'retention.administrative_retention',
                    'retention.central_retention',
                    'final_disposition.disposition_type',
                    'final_disposition.disposal_procedure'
                )
                ->get()
                ->unique('id');

            if ($series->isNotEmpty()) {
                // Get subseries for each series
                $seriesIds = $series->pluck('id')->toArray();
                $subseries = DB::table('subseries')
                    ->whereIn('series_id', $seriesIds)
                    ->get()
                    ->groupBy('series_id');

                // Get documentary types
                $documentaryTypes = DB::table('documentary_type')
                    ->whereIn('series_id', $seriesIds)
                    ->get()
                    ->groupBy('series_id');

                // Map series with their related data
                $series = $series->map(function ($series) use ($subseries, $documentaryTypes) {
                    $series->subseries = $subseries[$series->id] ?? collect();
                    $series->documentary_types = $documentaryTypes[$series->id] ?? collect();
                    return $series;
                });

                // Add to offices data array
                $officesData[] = [
                    'office' => $office,
                    'series' => $series
                ];
            }
        }

        $data = [
            'entity' => $entity,
            'offices' => $officesData
        ];

        // Generate PDF
        $pdf = PDF::loadView('reports.reports.retention-table', $data);
        $pdf->setPaper('a4', 'landscape');

        return $pdf->stream('tabla-retencion-documental.pdf');
    }


    public function generateEtiquetaPDF($systemCode)
    {
        $archive = DB::table('central_archive')
            ->join('office', 'central_archive.office_id', '=', 'office.id')
            ->join('series', 'central_archive.series_id', '=', 'series.id')
            ->join('subseries', 'central_archive.subseries_id', '=', 'subseries.id')
            ->join('entity', 'central_archive.entity_id', '=', 'entity.id')
            ->join('series_entity', 'entity.id', '=', 'series_entity.entity_id')
            //->select('central_archive.*', 'entity.*', 'offices.*', 'series.*', 'subseries.*', 'series_entity.*')
            ->where('central_archive.filed', $systemCode)->first();

        $pdf = PDF::loadView('reports.reports.etiqueta', [
            'archive' => $archive,
            'logo' => $archive->logo
        ]);
        return $pdf->stream('Rotulo.pdf');
        //return $archive;
    }

    public function generateAllEtiquetaPDF()
    {
        $archives = CentralArchive::with(['entity', 'office', 'series', 'subseries'])
            ->get();

        $pdf = PDF::loadView('reports.reports.alllabels', [
            'archives' => $archives
        ]);

        return $pdf->download('Rotulo.pdf');
    }

    public function generateReceipt($orderNumber)
    {
        // Obtener los datos del préstamo de documento
        $documentLoan = DocumentLoan::where('order_number', $orderNumber)->firstOrFail();

        // Generar el HTML para el PDF
        $html = view('reports.ticket.receipt', compact('documentLoan'))->render();

        // Configurar el tamaño del papel para formato tirilla
        $pdf = PDF::loadHTML($html)->setPaper([0, 0, 302, 567]);

        // Descargar el PDF
        return $pdf->stream('receipt_' . $orderNumber . '.pdf');
    }


    public function getArchiveAndDocumentRecords(): JsonResponse
    {
        try {
            // Retrieve all Retention records with their associated Series, ordered by creation date descending
            $retentions = Retention::with('series')->orderBy('created_at', 'desc')->get();

            // Retrieve all CentralArchive records with associated Entity, Office, Series, and Subseries, ordered by creation date descending
            $centralArchives = CentralArchive::with('entity', 'office', 'series', 'subseries')->orderBy('created_at', 'desc')->get();

            // Retrieve all HistoricFile records with associated Entity, Office, Series, and Subseries, ordered by creation date descending
            $historicFiles = HistoricFile::with('entity', 'office', 'series', 'subseries')->orderBy('created_at', 'desc')->get();

            // Retrieve all DocumentSending records with associated Department and Office, ordered by creation date descending
            $documentSendings = DocumentSending::with('department', 'office')->orderBy('created_at', 'desc')->get();

            // Retrieve all CorrespondenceTransfer records with associated Document and Office, ordered by creation date descending
            $correspondenceTransfers = CorrespondenceTransfer::with('document', 'office')->orderBy('created_at', 'desc')->get();

            // Retrieve all Reception records with associated Document, ordered by creation date descending
            $receptions = Reception::with('document')->orderBy('created_at', 'desc')->get();

            // Retrieve all RequestResponse records with associated Document, ordered by creation date descending
            $requestResponses = RequestResponse::with('document')->orderBy('created_at', 'desc')->get();

            // Retrieve all DocumentLoan records with associated Office, Entity, and User, ordered by creation date descending
            $documentLoans = DocumentLoan::with('office', 'entity', 'user', 'centralArchiveLoans.centralArchive', 'historicalArchiveLoans.historicFile')->orderBy('created_at', 'desc')->get();

            return response()->json([
                'retentions' => $retentions,
                'central_archives' => $centralArchives,
                'historic_files' => $historicFiles,
                'document_sendings' => $documentSendings,
                'correspondence_transfers' => $correspondenceTransfers,
                'receptions' => $receptions,
                'request_responses' => $requestResponses,
                'document_loans' => $documentLoans,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Could not retrieve records: ' . $e->getMessage()
            ], 500);
        }
    }



    public function getArchiveAndDocumentRecordsCalendar(Request $request): JsonResponse
    {
        try {
            $date = date('Y-m-d', strtotime($request->input('date')));

            // Retrieve CentralArchive records with associated Entity, Office, Series, and Subseries
            $centralArchives = CentralArchive::with('entity', 'office', 'series', 'subseries')
                ->whereDate('created_at', $date)
                ->get();

            // Retrieve HistoricFile records with associated Entity, Office, Series, and Subseries
            $historicFiles = HistoricFile::with('entity', 'office', 'series', 'subseries')
                ->whereDate('created_at', $date)
                ->get();

            // Retrieve DocumentSending records with associated Department and Office
            $documentSendings = DocumentSending::with('department', 'office')
                ->whereDate('created_at', $date)
                ->get();

            // Retrieve CorrespondenceTransfer records with associated Document and Office
            $correspondenceTransfers = CorrespondenceTransfer::with('document', 'office')
                ->whereDate('created_at', $date)
                ->get();

            // Retrieve Reception records with associated Document
            $receptions = Reception::with('document')
                ->whereDate('created_at', $date)
                ->get();

            // Retrieve RequestResponse records with associated Document
            $requestResponses = RequestResponse::with('document')
                ->whereDate('created_at', $date)
                ->get();

            // Retrieve DocumentLoan records with associated Office, Entity, and User
            $documentLoans = DocumentLoan::with('office', 'entity', 'user', 'centralArchiveLoans.centralArchive', 'historicalArchiveLoans.historicFile')
                ->whereDate('created_at', $date)
                ->get();

            return response()->json([
                'central_archives' => $centralArchives,
                'historic_files' => $historicFiles,
                'document_sendings' => $documentSendings,
                'correspondence_transfers' => $correspondenceTransfers,
                'receptions' => $receptions,
                'request_responses' => $requestResponses,
                'document_loans' => $documentLoans,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Could not retrieve records: ' . $e->getMessage()
            ], 500);
        }
    }
}
