<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Office;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GeneralReportControllers extends Controller
{
    public function generateReport(Request $request)
    {
        // Log the incoming request data for debugging
        Log::info('Request Data:', $request->all());

        $request->validate([
            'report_type' => 'required|string',
            'filter_type' => 'required|string',
            'start_date' => 'required_if:filter_type,date_range|date',
            'end_date' => 'required_if:filter_type,date_range|date|after_or_equal:start_date',
            'selected_year' => 'nullable|integer|min:2000|max:2099',
            'status_id' => 'nullable|string',
            'department_id' => 'nullable|exists:department,id',
            'office_id' => 'nullable|exists:office,id',
        ]);

        $reportType = $request->input('report_type');
        $filterType = $request->input('filter_type');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $selectedYear = $request->input('selected_year');
        $statusId = $request->input('status_id');
        $departmentId = $request->input('department_id');
        $officeId = $request->input('office_id');

        $query = (object) [
            'filterType' => $filterType,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'selectedYear' => $selectedYear,
            'statusId' => $statusId,
            'departmentId' => $departmentId,
            'officeId' => $officeId,
        ];

        // Generate report based on the selected type
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
            case 'prestamos_activos':
                return $this->generateActiveLoansReport($query);
            case 'prestamos_vencidos':
                return $this->generateOverdueLoansReport($query);
            case 'prestamos_por_fecha':
                return $this->generateLoansByDateReport($query);
            case 'prestamos_por_oficina':
                return $this->generateLoansByOfficeReport($query);
            case 'prestamos_por_usuario':
                return $this->generateLoansByUserReport($query);
            case 'devoluciones':
                return $this->generateReturnsReport($query);
            default:
                return response()->json(['error' => 'Tipo de reporte no válido'], 400);
        }
    }


    private function generateGeneralSummary($query)
    {
        $totalDocuments = DB::table('document')->count();
        $statusCounts = DB::table('document_status')
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $latestDocuments = DB::table('document')
            ->limit(5)
            ->select('reference_code', 'received_date', 'sender_name', 'subject')
            ->orderBy('received_date', 'desc')
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
        $statusReport = DB::table('document')
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

        if ($query->filterType === 'date_range') {
            $statusReport->whereBetween('document.received_date', [$query->startDate, $query->endDate]);
        } elseif ($query->filterType === 'year') {
            $statusReport->whereYear('document.received_date', $query->selectedYear);
        }

        if ($query->statusId) {
            $statusReport->where('document_status.status', $query->statusId);
        }

        $statusReport = $statusReport->get();

        return response()->json([
            'report_type' => 'Estado de Documentos',
            'headers' => ['Código', 'Fecha Recibido', 'Remitente', 'Asunto', 'Archivo', 'Estado'],
            'data' => $statusReport->map(function ($doc) {
                return [
                    $doc->reference_code,
                    $doc->received_date,
                    $doc->sender_name,
                    $doc->subject,
                    $doc->file_path,
                    $doc->status
                ];
            })
        ]);
    }

    private function generateDocumentFlowReport($query)
    {
        $flowReport = DB::table('document')
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
            ->join('document_status', 'document.id', '=', 'document_status.document_id')
            ->orderBy('document.received_date', 'desc');

        if ($query->filterType === 'date_range') {
            $flowReport->whereBetween('document.received_date', [$query->startDate, $query->endDate]);
        } elseif ($query->filterType === 'year') {
            $flowReport->whereYear('document.received_date', $query->selectedYear);
        }

        $flowReport = $flowReport->get();

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
        $responseTimeReport = DB::table('document')
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
            ->orderBy('document.received_date', 'desc');

        if ($query->filterType === 'date_range') {
            $responseTimeReport->whereBetween('document.received_date', [$query->startDate, $query->endDate]);
        } elseif ($query->filterType === 'year') {
            $responseTimeReport->whereYear('document.received_date', $query->selectedYear);
        }

        $responseTimeReport = $responseTimeReport->get();

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
        $transferReport = DB::table('document')
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
            ->orderBy('document.received_date', 'desc');

        if ($query->filterType === 'date_range') {
            $transferReport->whereBetween('document.received_date', [$query->startDate, $query->endDate]);
        } elseif ($query->filterType === 'year') {
            $transferReport->whereYear('document.received_date', $query->selectedYear);
        }

        if ($query->departmentId) {
            $transferReport->where('office.department_id', $query->departmentId);
        }

        if ($query->officeId) {
            $transferReport->where('correspondence_transfer.office_id', $query->officeId);
        }

        $transferReport = $transferReport->get();

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
        $activityReport = DB::table('document')
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
            ->orderBy('document.received_date', 'desc');

        if ($query->filterType === 'date_range') {
            $activityReport->whereBetween('document.received_date', [$query->startDate, $query->endDate]);
        } elseif ($query->filterType === 'year') {
            $activityReport->whereYear('document.received_date', $query->selectedYear);
        }

        if ($query->departmentId) {
            $activityReport->where('department.id', $query->departmentId);
        }

        if ($query->officeId) {
            $activityReport->where('office.id', $query->officeId);
        }

        $activityReport = $activityReport->get();

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
        $receptionReport = DB::table('document')
            ->join('reception', 'document.id', '=', 'reception.document_id')
            ->select(
                'document.reference_code',
                'document.received_date',
                'document.sender_name',
                'document.subject',
                'document.file_path',
                'reception.created_at as reception_date'
            )
            ->orderBy('document.received_date', 'desc');

        if ($query->filterType === 'date_range') {
            $receptionReport->whereBetween('document.received_date', [$query->startDate, $query->endDate]);
        } elseif ($query->filterType === 'year') {
            $receptionReport->whereYear('document.received_date', $query->selectedYear);
        }

        $receptionReport = $receptionReport->get();

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
        $responseReport = DB::table('document')
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
            ->orderBy('document.received_date', 'desc');

        if ($query->filterType === 'date_range') {
            $responseReport->whereBetween('document.received_date', [$query->startDate, $query->endDate]);
        } elseif ($query->filterType === 'year') {
            $responseReport->whereYear('document.received_date', $query->selectedYear);
        }

        $responseReport = $responseReport->get();

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

    private function generateActiveLoansReport($query)
    {
        $activeLoansReport = DB::table('document_loans')
            ->select(
                'document_loans.registration_date',
                'document_loans.order_number',
                'document_loans.identification',
                'document_loans.names',
                'office.name as office_name',
                'document_loans.return_date',
                'document_loans.type_of_document_borrowed',
                'entity.name as entity_name',
                'users.name as user_name',
                'document_loans.state'
            )
            ->join('office', 'document_loans.office_id', '=', 'office.id')
            ->join('entity', 'document_loans.entity_id', '=', 'entity.id')
            ->join('users', 'document_loans.user_id', '=', 'users.id')
            ->where('document_loans.state', 1); // 1 means loaned

        if ($query->filterType === 'date_range') {
            $activeLoansReport->whereBetween('document_loans.registration_date', [$query->startDate, $query->endDate]);
        } elseif ($query->filterType === 'year') {
            $activeLoansReport->whereYear('document_loans.registration_date', $query->selectedYear);
        }

        if ($query->departmentId) {
            $activeLoansReport->where('office.department_id', $query->departmentId);
        }

        if ($query->officeId) {
            $activeLoansReport->where('document_loans.office_id', $query->officeId);
        }

        $activeLoansReport = $activeLoansReport->get();

        return response()->json([
            'report_type' => 'Préstamos Activos',
            'headers' => ['Fecha de Registro', 'Número de Orden', 'Identificación', 'Nombres', 'Oficina', 'Fecha de Devolución', 'Tipo de Documento', 'Entidad', 'Usuario', 'Estado'],
            'data' => $activeLoansReport->map(function ($loan) {
                $documentType = $loan->type_of_document_borrowed == 1 ? 'Archivo Central' : ($loan->type_of_document_borrowed == 2 ? 'Activo Histórico' : 'Desconocido');
                $loanState = $loan->state == 1 ? 'Documento Prestado' : 'Documento Retornado';
                return [
                    $loan->registration_date,
                    $loan->order_number,
                    $loan->identification,
                    $loan->names,
                    $loan->office_name,
                    $loan->return_date,
                    $documentType,
                    $loan->entity_name,
                    $loan->user_name,
                    $loanState
                ];
            })
        ]);
    }

    private function generateOverdueLoansReport($query)
    {
        $overdueLoansReport = DB::table('document_loans')
            ->select(
                'document_loans.registration_date',
                'document_loans.order_number',
                'document_loans.identification',
                'document_loans.names',
                'office.name as office_name',
                'document_loans.return_date',
                'document_loans.type_of_document_borrowed',
                'entity.name as entity_name',
                'users.name as user_name',
                'document_loans.state'
            )
            ->join('office', 'document_loans.office_id', '=', 'office.id')
            ->join('entity', 'document_loans.entity_id', '=', 'entity.id')
            ->join('users', 'document_loans.user_id', '=', 'users.id')
            ->where('document_loans.state', 1) // 1 means loaned
            ->where('document_loans.return_date', '<', now());

        if ($query->filterType === 'date_range') {
            $overdueLoansReport->whereBetween('document_loans.registration_date', [$query->startDate, $query->endDate]);
        } elseif ($query->filterType === 'year') {
            $overdueLoansReport->whereYear('document_loans.registration_date', $query->selectedYear);
        }

        if ($query->departmentId) {
            $overdueLoansReport->where('office.department_id', $query->departmentId);
        }

        if ($query->officeId) {
            $overdueLoansReport->where('document_loans.office_id', $query->officeId);
        }

        $overdueLoansReport = $overdueLoansReport->get();

        return response()->json([
            'report_type' => 'Préstamos Vencidos',
            'headers' => ['Fecha de Registro', 'Número de Orden', 'Identificación', 'Nombres', 'Oficina', 'Fecha de Devolución', 'Tipo de Documento', 'Entidad', 'Usuario', 'Estado'],
            'data' => $overdueLoansReport->map(function ($loan) {
                $documentType = $loan->type_of_document_borrowed == 1 ? 'Archivo Central' : ($loan->type_of_document_borrowed == 2 ? 'Activo Histórico' : 'Desconocido');
                $loanState = $loan->state == 1 ? 'Documento Prestado' : 'Documento Retornado';
                return [
                    $loan->registration_date,
                    $loan->order_number,
                    $loan->identification,
                    $loan->names,
                    $loan->office_name,
                    $loan->return_date,
                    $documentType,
                    $loan->entity_name,
                    $loan->user_name,
                    $loanState
                ];
            })
        ]);
    }

    private function generateLoansByDateReport($query)
    {
        $loansByDateReport = DB::table('document_loans')
            ->select(
                'document_loans.registration_date',
                'document_loans.order_number',
                'document_loans.identification',
                'document_loans.names',
                'office.name as office_name',
                'document_loans.return_date',
                'document_loans.type_of_document_borrowed',
                'entity.name as entity_name',
                'users.name as user_name',
                'document_loans.state'
            )
            ->join('office', 'document_loans.office_id', '=', 'office.id')
            ->join('entity', 'document_loans.entity_id', '=', 'entity.id')
            ->join('users', 'document_loans.user_id', '=', 'users.id');

        if ($query->filterType === 'date_range') {
            $loansByDateReport->whereBetween('document_loans.registration_date', [$query->startDate, $query->endDate]);
        } elseif ($query->filterType === 'year') {
            $loansByDateReport->whereYear('document_loans.registration_date', $query->selectedYear);
        }

        if ($query->departmentId) {
            $loansByDateReport->where('office.department_id', $query->departmentId);
        }

        if ($query->officeId) {
            $loansByDateReport->where('document_loans.office_id', $query->officeId);
        }

        $loansByDateReport = $loansByDateReport->get();

        return response()->json([
            'report_type' => 'Préstamos por Fecha',
            'headers' => ['Fecha de Registro', 'Número de Orden', 'Identificación', 'Nombres', 'Oficina', 'Fecha de Devolución', 'Tipo de Documento', 'Entidad', 'Usuario', 'Estado'],
            'data' => $loansByDateReport->map(function ($loan) {
                $documentType = $loan->type_of_document_borrowed == 1 ? 'Archivo Central' : ($loan->type_of_document_borrowed == 2 ? 'Activo Histórico' : 'Desconocido');
                $loanState = $loan->state == 1 ? 'Documento Prestado' : 'Documento Retornado';
                return [
                    $loan->registration_date,
                    $loan->order_number,
                    $loan->identification,
                    $loan->names,
                    $loan->office_name,
                    $loan->return_date,
                    $documentType,
                    $loan->entity_name,
                    $loan->user_name,
                    $loanState
                ];
            })
        ]);
    }

    private function generateLoansByOfficeReport($query)
    {
        $loansByOfficeReport = DB::table('document_loans')
            ->select(
                'office.name as office_name',
                DB::raw('COUNT(*) as loan_count'),
                DB::raw('GROUP_CONCAT(CONCAT_WS("|", document_loans.registration_date, document_loans.order_number, document_loans.identification, document_loans.names, document_loans.return_date, document_loans.type_of_document_borrowed, entity.name, users.name, document_loans.state) SEPARATOR "||") as loan_details')
            )
            ->join('office', 'document_loans.office_id', '=', 'office.id')
            ->join('entity', 'document_loans.entity_id', '=', 'entity.id')
            ->join('users', 'document_loans.user_id', '=', 'users.id')
            ->groupBy('office.name');

        if ($query->filterType === 'date_range') {
            $loansByOfficeReport->whereBetween('document_loans.registration_date', [$query->startDate, $query->endDate]);
        } elseif ($query->filterType === 'year') {
            $loansByOfficeReport->whereYear('document_loans.registration_date', $query->selectedYear);
        }

        if ($query->departmentId) {
            $loansByOfficeReport->where('office.department_id', $query->departmentId);
        }

        if ($query->officeId) {
            $loansByOfficeReport->where('document_loans.office_id', $query->officeId);
        }

        $loansByOfficeReport = $loansByOfficeReport->get();

        return response()->json([
            'report_type' => 'Préstamos por Oficina',
            'headers' => ['Oficina', 'Cantidad de Préstamos', 'Detalles de Préstamos'],
            'data' => $loansByOfficeReport->map(function ($loan) {
                $loanDetails = collect(explode('||', $loan->loan_details))->map(function ($detail) {
                    $parts = explode('|', $detail);
                    $documentType = $parts[5] == 1 ? 'Archivo Central' : ($parts[5] == 2 ? 'Activo Histórico' : 'Desconocido');
                    $loanState = $parts[8] == 1 ? 'Documento Prestado' : 'Documento Retornado';
                    return [
                        'registration_date' => $parts[0] ?? '',
                        'order_number' => $parts[1] ?? '',
                        'identification' => $parts[2] ?? '',
                        'names' => $parts[3] ?? '',
                        'return_date' => $parts[4] ?? '',
                        'type_of_document_borrowed' => $documentType,
                        'entity_name' => $parts[6] ?? '',
                        'user_name' => $parts[7] ?? '',
                        'state' => $loanState,
                    ];
                })->toArray();

                return [
                    $loan->office_name,
                    $loan->loan_count,
                    $loanDetails
                ];
            })
        ]);
    }

    private function generateLoansByUserReport($query)
    {
        $loansByUserReport = DB::table('document_loans')
            ->select(
                'users.name as user_name',
                DB::raw('COUNT(*) as loan_count'),
                DB::raw('GROUP_CONCAT(CONCAT_WS("|", document_loans.registration_date, document_loans.order_number, document_loans.identification, document_loans.names, document_loans.return_date, document_loans.type_of_document_borrowed, entity.name, office.name, document_loans.state) SEPARATOR "||") as loan_details')
            )
            ->join('office', 'document_loans.office_id', '=', 'office.id')
            ->join('entity', 'document_loans.entity_id', '=', 'entity.id')
            ->join('users', 'document_loans.user_id', '=', 'users.id')
            ->groupBy('users.name');

        if ($query->filterType === 'date_range') {
            $loansByUserReport->whereBetween('document_loans.registration_date', [$query->startDate, $query->endDate]);
        } elseif ($query->filterType === 'year') {
            $loansByUserReport->whereYear('document_loans.registration_date', $query->selectedYear);
        }

        if ($query->departmentId) {
            $loansByUserReport->where('office.department_id', $query->departmentId);
        }

        if ($query->officeId) {
            $loansByUserReport->where('document_loans.office_id', $query->officeId);
        }

        $loansByUserReport = $loansByUserReport->get();

        return response()->json([
            'report_type' => 'Préstamos por Usuario',
            'headers' => ['Usuario', 'Cantidad de Préstamos', 'Detalles de Préstamos'],
            'data' => $loansByUserReport->map(function ($loan) {
                $loanDetails = collect(explode('||', $loan->loan_details))->map(function ($detail) {
                    $parts = explode('|', $detail);
                    $documentType = $parts[5] == 1 ? 'Archivo Central' : ($parts[5] == 2 ? 'Activo Histórico' : 'Desconocido');
                    $loanState = $parts[8] == 1 ? 'Documento Prestado' : 'Documento Retornado';
                    return [
                        'registration_date' => $parts[0] ?? '',
                        'order_number' => $parts[1] ?? '',
                        'identification' => $parts[2] ?? '',
                        'names' => $parts[3] ?? '',
                        'return_date' => $parts[4] ?? '',
                        'type_of_document_borrowed' => $documentType,
                        'entity_name' => $parts[6] ?? '',
                        'office_name' => $parts[7] ?? '',
                        'state' => $loanState,
                    ];
                })->toArray();

                return [
                    $loan->user_name,
                    $loan->loan_count,
                    $loanDetails
                ];
            })
        ]);
    }

    private function generateReturnsReport($query)
    {
        $returnsReport = DB::table('document_returns')
            ->select(
                'document_returns.document_loan_order_number',
                'document_returns.return_date',
                'document_returns.document_conditions',
                'document_returns.comments',
                'document_loans.identification',
                'document_loans.names',
                'office.name as office_name',
                'document_loans.type_of_document_borrowed'
            )
            ->join('document_loans', 'document_returns.document_loan_order_number', '=', 'document_loans.order_number')
            ->join('office', 'document_loans.office_id', '=', 'office.id');

        if ($query->filterType === 'date_range') {
            $returnsReport->whereBetween('document_returns.return_date', [$query->startDate, $query->endDate]);
        } elseif ($query->filterType === 'year') {
            $returnsReport->whereYear('document_returns.return_date', $query->selectedYear);
        }

        if ($query->departmentId) {
            $returnsReport->where('office.department_id', $query->departmentId);
        }

        if ($query->officeId) {
            $returnsReport->where('document_loans.office_id', $query->officeId);
        }

        $returnsReport = $returnsReport->get();

        return response()->json([
            'report_type' => 'Devoluciones',
            'headers' => ['Número de Orden', 'Fecha de Devolución', 'Condiciones del Documento', 'Comentarios', 'Identificación', 'Nombres', 'Oficina', 'Tipo de Documento'],
            'data' => $returnsReport->map(function ($return) {
                $documentType = $return->type_of_document_borrowed == 1 ? 'Archivo Central' : ($return->type_of_document_borrowed == 2 ? 'Activo Histórico' : 'Desconocido');
                return [
                    $return->document_loan_order_number,
                    $return->return_date,
                    $return->document_conditions,
                    $return->comments,
                    $return->identification,
                    $return->names,
                    $return->office_name,
                    $documentType
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
            'year' => 'nullable|integer|min:2000|max:2099',
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
