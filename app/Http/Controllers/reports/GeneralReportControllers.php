<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Office;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GeneralReportControllers extends Controller
{
    public function generateReport(Request $request)
    {
        $request->validate([
            'report_type' => 'required|string',
            'filter_type' => 'required|string',
            'start_date' => 'required_if:filter_type,date_range|date',
            'end_date' => 'required_if:filter_type,date_range|date|after_or_equal:start_date',
            'year' => 'nullable:filter_type,year|integer|min:2000|max:2099',
            'status_id' => 'nullable|string',
            'department_id' => 'nullable|exists:department,id',
            'office_id' => 'nullable|exists:office,id',
        ]);

        $reportType = $request->input('report_type');
        $filterType = $request->input('filter_type');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $year = $request->input('year');
        $statusId = $request->input('status_id');
        $departmentId = $request->input('department_id');
        $officeId = $request->input('office_id');

        $query = DB::table('document')
            ->select(
                'document.reference_code',
                'document.received_date',
                'document.sender_name',
                'document.subject',
                'document.file_path',
                'document_status.status'
            )
            ->join('document_status', 'document.id', '=', 'document_status.document_id')
            ->orderBy('document.received_date', 'desc');

        // Aplicar filtros de fecha
        if ($filterType === 'date_range') {
            $query->whereBetween('document.received_date', [$startDate, $endDate]);
        } elseif ($filterType === 'year') {
            $query->whereYear('document.received_date', $year);
        }

        // Aplicar filtros opcionales
        if ($statusId) {
            $query->where('document_status.status', $statusId);
        }

        if ($departmentId) {
            $query->join('correspondence_transfer', 'document.id', '=', 'correspondence_transfer.document_id')
                ->join('office', 'correspondence_transfer.office_id', '=', 'office.id')
                ->where('office.department_id', $departmentId);
        }

        if ($officeId) {
            $query->join('correspondence_transfer', 'document.id', '=', 'correspondence_transfer.document_id')
                ->where('correspondence_transfer.office_id', $officeId);
        }

        // Generar reporte según el tipo seleccionado
        switch ($reportType) {
            case 'resumen_general':
                return $this->generateGeneralSummary($query);
            case 'estado_documentos':
                return $this->generateDocumentStatusReport($query);
            case 'flujo_documentos':
                return $this->generateDocumentFlowReport($query);
            case 'tiempos_respuesta':
                return $this->generateResponseTimeReport($query);
            case 'transferencias_correspondencia':
                return $this->generateCorrespondenceTransferReport($query);
            case 'actividad_entidad':
                return $this->generateEntityActivityReport($query);
            case 'recepcion':
                return $this->generateReceptionReport($query);
            case 'respuesta_solicitud':
                return $this->generateRequestResponseReport($query);
            case 'envio_documento':
                return $this->generateDocumentSendingReport($query);
            default:
                return response()->json(['error' => 'Tipo de reporte no válido'], 400);
        }
    }

    private function generateGeneralSummary($query)
    {
        $totalDocuments = $query->count();
        $statusCounts = $query->groupBy('document_status.status')
            ->select('document_status.status', DB::raw('count(*) as count'))
            ->pluck('count', 'status')
            ->toArray();

        $latestDocuments = $query->limit(5)
            ->select('document_status.status', DB::raw('count(*) as count'))
            ->groupBy('document_status.status')
            ->get()
            ->toArray();

        return response()->json([
            'report_type' => 'Resumen General',
            'data' => [
                'Total documentos' => $totalDocuments,
                'Documentos por estado' => $statusCounts,
                'Ultimos documentos' => $latestDocuments
            ]
        ]);
    }

    private function generateDocumentStatusReport($query)
    {
        $statusReport = $query->get();

        return response()->json([
            'report_type' => 'Estado de Documentos',
            'headers' => ['Código', 'Fecha Recibido', 'Remitente', 'Asunto', 'Archivo', 'Estado'],
            'data' => $statusReport->map(function ($doc) {
                return [
                    $doc->reference_code,
                    $doc->received_date,
                    $doc->sender_name,
                    $doc->subject,
                    $doc->status
                ];
            })
        ]);
    }

    private function generateDocumentFlowReport($query)
    {
        $flowReport = $query
            ->leftJoin('reception', 'document.id', '=', 'reception.document_id')
            ->leftJoin('correspondence_transfer', 'document.id', '=', 'correspondence_transfer.document_id')
            ->leftJoin('request_response', 'document.id', '=', 'request_response.document_id')
            ->select(
                'document.reference_code',
                'document.received_date',
                'document.sender_name',
                'document.subject',
                'document.file_path',
                'reception.created_at as reception_date',
                'correspondence_transfer.transfer_datetime',
                'request_response.created_at as response_date',
                'document_status.status'
            )
            ->get();

        return response()->json([
            'report_type' => 'Flujo de Documentos',
            'headers' => ['Código', 'Fecha Recibido', 'Remitente', 'Asunto', 'Archivo', 'Fecha Recepción', 'Fecha Transferencia', 'Fecha Respuesta', 'Estado Actual'],
            'data' => $flowReport->map(function ($doc) {
                return [
                    $doc->reference_code,
                    $doc->received_date,
                    $doc->sender_name,
                    $doc->subject,
                    $doc->file_path,
                    $doc->reception_date,
                    $doc->transfer_datetime,
                    $doc->response_date,
                    $doc->status
                ];
            })
        ]);
    }

    private function generateResponseTimeReport($query)
    {
        $responseTimeReport = $query
            ->leftJoin('correspondence_transfer', 'document.id', '=', 'correspondence_transfer.document_id')
            ->leftJoin('request_response', 'document.id', '=', 'request_response.document_id')
            ->select(
                'document.reference_code',
                'document.received_date',
                'document.sender_name',
                'document.subject',
                'document.file_path',
                'correspondence_transfer.response_deadline',
                'request_response.created_at as response_date',
                DB::raw('DATEDIFF(request_response.created_at, correspondence_transfer.response_deadline) as days_difference')
            )
            ->whereNotNull('request_response.created_at')
            ->get();

        return response()->json([
            'report_type' => 'Tiempos de Respuesta',
            'headers' => ['Código', 'Fecha Recibido', 'Remitente', 'Asunto', 'Archivo', 'Fecha Límite', 'Fecha Respuesta', 'Diferencia (días)'],
            'data' => $responseTimeReport->map(function ($doc) {
                return [
                    $doc->reference_code,
                    $doc->received_date,
                    $doc->sender_name,
                    $doc->subject,
                    $doc->file_path,
                    $doc->response_deadline,
                    $doc->response_date,
                    $doc->days_difference
                ];
            })
        ]);
    }

    private function generateCorrespondenceTransferReport($query)
    {
        $transferReport = $query
            ->join('correspondence_transfer', 'document.id', '=', 'correspondence_transfer.document_id')
            ->join('office', 'correspondence_transfer.office_id', '=', 'office.id')
            ->select(
                'document.reference_code',
                'document.received_date',
                'document.sender_name',
                'document.subject',
                'document.file_path',
                'correspondence_transfer.transfer_datetime',
                'office.name as office_name',
                'correspondence_transfer.response_deadline'
            )
            ->get();

        return response()->json([
            'report_type' => 'Transferencias de Correspondencia',
            'headers' => ['Código', 'Fecha Recibido', 'Remitente', 'Asunto', 'Archivo', 'Fecha Transferencia', 'Oficina Destino', 'Fecha Límite Respuesta'],
            'data' => $transferReport->map(function ($doc) {
                return [
                    $doc->reference_code,
                    $doc->received_date,
                    $doc->sender_name,
                    $doc->subject,
                    $doc->file_path,
                    $doc->transfer_datetime,
                    $doc->office_name,
                    $doc->response_deadline
                ];
            })
        ]);
    }

    private function generateEntityActivityReport($query)
    {
        $activityReport = $query
            ->join('correspondence_transfer', 'document.id', '=', 'correspondence_transfer.document_id')
            ->join('office', 'correspondence_transfer.office_id', '=', 'office.id')
            ->join('department', 'office.department_id', '=', 'department.id')
            ->select(
                'department.name as department_name',
                'office.name as office_name',
                DB::raw('COUNT(distinct document.id) as document_count'),
                DB::raw('GROUP_CONCAT(CONCAT_WS("|", document.reference_code, document.received_date, document.sender_name, document.subject, document.file_path) SEPARATOR "||") as document_details')
            )
            ->groupBy('department.name', 'office.name')
            ->orderBy('document.received_date', 'desc')
            ->get();

        return response()->json([
            'report_type' => 'Actividad de Entidad',
            'headers' => ['Departamento', 'Oficina', 'Cantidad de Documentos', 'Detalles de Documentos'],
            'data' => $activityReport->map(function ($activity) {
                $documentDetails = collect(explode('||', $activity->document_details))->map(function ($detail) {
                    $parts = explode('|', $detail);
                    return [
                        'reference_code' => $parts[0] ?? '',
                        'received_date' => $parts[1] ?? '',
                        'sender_name' => $parts[2] ?? '',
                        'subject' => $parts[3] ?? '',
                        'file_path' => $parts[4] ?? '',
                    ];
                })->toArray();

                return [
                    $activity->department_name,
                    $activity->office_name,
                    $activity->document_count,
                    $documentDetails
                ];
            })
        ]);
    }

    private function generateReceptionReport($query)
    {
        $receptionReport = $query
            ->join('reception', 'document.id', '=', 'reception.document_id')
            ->select(
                'document.reference_code',
                'document.received_date',
                'document.sender_name',
                'document.subject',
                'document.file_path',
                'reception.created_at as reception_date'
            )
            ->get();

        return response()->json([
            'report_type' => 'Recepción de Documentos',
            'headers' => ['Código', 'Fecha Recibido', 'Remitente', 'Asunto', 'Archivo', 'Fecha de Recepción'],
            'data' => $receptionReport->map(function ($doc) {
                return [
                    $doc->reference_code,
                    $doc->received_date,
                    $doc->sender_name,
                    $doc->subject,
                    $doc->file_path,
                    $doc->reception_date
                ];
            })
        ]);
    }

    private function generateRequestResponseReport($query)
    {
        $responseReport = $query
            ->join('request_response', 'document.id', '=', 'request_response.document_id')
            ->select(
                'document.reference_code',
                'document.received_date',
                'document.sender_name',
                'document.subject',
                'document.file_path',
                'request_response.created_at as response_date',
                'request_response.response_content'
            )
            ->get();

        return response()->json([
            'report_type' => 'Respuestas a Solicitudes',
            'headers' => ['Código', 'Fecha Recibido', 'Remitente', 'Asunto', 'Archivo', 'Fecha de Respuesta', 'Contenido de Respuesta'],
            'data' => $responseReport->map(function ($doc) {
                return [
                    $doc->reference_code,
                    $doc->received_date,
                    $doc->sender_name,
                    $doc->subject,
                    $doc->file_path,
                    $doc->response_date,
                    $doc->response_content
                ];
            })
        ]);
    }

    private function generateDocumentSendingReport()
    {
        $sendingReport = DB::table('document_sending')
            ->join('document', 'document_sending.document_id', '=', 'document.id')
            ->select(
                'document.reference_code',
                'document.received_date',
                'document.sender_name',
                'document.subject',
                'document.file_path',
                'document_sending.send_date',
                'document_sending.recipient',
                'document_sending.page_count'
            )
            ->get();

        return response()->json([
            'report_type' => 'Envío de Documentos',
            'headers' => ['Código', 'Fecha Recibido', 'Remitente', 'Asunto', 'Archivo', 'Fecha de Envío', 'Destinatario', 'Número de Páginas'],
            'data' => $sendingReport->map(function ($doc) {
                return [
                    $doc->reference_code,
                    $doc->received_date,
                    $doc->sender_name,
                    $doc->subject,
                    $doc->file_path,
                    $doc->send_date,
                    $doc->recipient,
                    $doc->page_count
                ];
            })
        ]);
    }

    public function generatePdfReport(Request $request)
    {
        $request->validate([
            'report_type' => 'required|string',
            'filter_type' => 'required|string',
            'start_date' => 'required_if:filter_type,date_range|date',
            'end_date' => 'required_if:filter_type,date_range|date|after_or_equal:start_date',
            'year' => 'nullable:filter_type,year|integer|min:2000|max:2099',
            'status_id' => 'nullable|string',
            'department_id' => 'nullable|exists:department,id',
            'office_id' => 'nullable|exists:office,id',
        ]);

        $reportData = $this->generateReport($request);

        // Ensure we have data to work with
        if (!$reportData->getData() || !property_exists($reportData->getData(), 'data')) {
            return response()->json(['error' => 'No data available for PDF generation'], 400);
        }

        $data = $reportData->getData();

        $pdf = PDF::loadView('reports.reports.pdftemplate', [
            'reportType' => $data->report_type,
            'headers' => $data->headers ?? [],
            'data' => $data->data,
            'filterInfo' => [
                'Filter Type' => $request->input('filter_type'),
                'Start Date' => $request->input('start_date'),
                'End Date' => $request->input('end_date'),
                'Year' => $request->input('year'),
                'Status' => $request->input('status_id'),
                'Department' => $request->input('department_id') ? Department::find($request->input('department_id'))->name : null,
                'Office' => $request->input('office_id') ? Office::find($request->input('office_id'))->name : null,
            ]
        ]);

        // Generate a unique filename
        $fileName = str_replace(' ', '_', strtolower($data->report_type)) . '_' . date('Y-m-d_H-i-s') . '.pdf';

        // Return the PDF for direct viewing in the browser
        return $pdf->stream($fileName);
    }

}
