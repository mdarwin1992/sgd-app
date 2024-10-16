<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Http\Controllers\helpers\HelpersController;
use App\Models\Document;
use App\Models\DocumentStatus;
use App\Models\CorrespondenceTransfer;
use App\Models\Entity;
use App\Models\Reception;
use App\Models\RequestResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
                1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr', 5 => 'May', 6 => 'Jun',
                7 => 'Jul', 8 => 'Ago', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic'
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

}
