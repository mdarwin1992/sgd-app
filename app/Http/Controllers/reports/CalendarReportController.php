<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class CalendarReportController extends Controller
{
    public function getDocumentProcessTimeline()
    {
        $events = [];

        // Obtener datos de recepción
        $receptions = DB::table('reception')
            ->join('document', 'reception.document_id', '=', 'document.id')
            ->select('document.reference_code', 'document.received_date', 'document.sender_name', 'document.subject', 'reception.created_at')
            ->get();

        foreach ($receptions as $reception) {
            $events[] = [
                'title' => 'Recepción: ' . $reception->reference_code,
                'start' => $reception->created_at,
                'description' => "Remitente: {$reception->sender_name}\nAsunto: {$reception->subject}",
                'type' => 'reception',
                'referenceCode' => $reception->reference_code
            ];
        }

        // Obtener datos de transferencia de correspondencia
        $transfers = DB::table('correspondence_transfer')
            ->join('document', 'correspondence_transfer.document_id', '=', 'document.id')
            ->select('document.reference_code', 'document.received_date', 'document.sender_name', 'document.subject', 'correspondence_transfer.transfer_datetime')
            ->get();

        foreach ($transfers as $transfer) {
            $events[] = [
                'title' => 'Transferencia: ' . $transfer->reference_code,
                'start' => $transfer->transfer_datetime,
                'description' => "Remitente: {$transfer->sender_name}\nAsunto: {$transfer->subject}",
                'type' => 'transfer',
                'referenceCode' => $transfer->reference_code
            ];
        }

        // Obtener datos de respuesta a solicitud
        $responses = DB::table('request_response')
            ->join('document', 'request_response.document_id', '=', 'document.id')
            ->select('document.reference_code', 'document.received_date', 'document.sender_name', 'document.subject', 'request_response.created_at')
            ->get();

        foreach ($responses as $response) {
            $events[] = [
                'title' => 'Respuesta: ' . $response->reference_code,
                'start' => $response->created_at,
                'description' => "Remitente: {$response->sender_name}\nAsunto: {$response->subject}",
                'type' => 'response',
                'referenceCode' => $response->reference_code
            ];
        }

        // Obtener datos de cambios de estado
        $statuses = DB::table('document_status')
            ->join('document', 'document_status.document_id', '=', 'document.id')
            ->select('document.reference_code', 'document.received_date', 'document.sender_name', 'document.subject', 'document_status.status', 'document_status.created_at')
            ->get();

        foreach ($statuses as $status) {
            $events[] = [
                'title' => 'Estado: ' . $status->status,
                'start' => $status->created_at,
                'description' => "Documento: {$status->reference_code}\nRemitente: {$status->sender_name}\nAsunto: {$status->subject}",
                'type' => 'status',
                'referenceCode' => $status->reference_code
            ];
        }

        // Obtener datos de envío de documento (independiente)
        $sendings = DB::table('document_sending')
            ->select('send_date', 'subject', 'sender', 'recipient')
            ->get();

        foreach ($sendings as $sending) {
            $events[] = [
                'title' => 'Envío de documento',
                'start' => $sending->send_date,
                'description' => "Remitente: {$sending->sender}\nDestinatario: {$sending->recipient}\nAsunto: {$sending->subject}",
                'type' => 'sending'
            ];
        }

        return response()->json($events);
    }
}
