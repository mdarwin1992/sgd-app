<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentStatus;
use App\Models\CorrespondenceTransfer;
use App\Models\Entity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportsController extends Controller
{
    public function documentStatusChart()
    {
        $statuses = DocumentStatus::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();

        $labels = $statuses->pluck('status');
        $data = $statuses->pluck('total');

        return response()->json([
            'labels' => $labels,
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0'],
                ]
            ]
        ]);
    }

    public function documentsByEntityBarChart()
    {
        $documents = Document::select('entity.name as entity_name', DB::raw('count(*) as total'))
            ->join('entity', 'document.entity_id', '=', 'entity.id')
            ->groupBy('entity.id', 'entity.name')
            ->get();

        $labels = $documents->pluck('entity_name');
        $data = $documents->pluck('total');

        return response()->json([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Documents per Entity',
                    'data' => $data,
                    'backgroundColor' => '#36A2EB',
                ]
            ]
        ]);
    }

    public function documentProcessTimeline()
    {
        $processes = CorrespondenceTransfer::select(
            'document_id',
            'transfer_datetime as start',
            DB::raw('DATE_ADD(transfer_datetime, INTERVAL response_time DAY) as end'),
            'job_type as title'
        )->get();

        return response()->json($processes);
    }

    public function generateReport(Request $request)
    {
        $reportType = $request->input('report_type');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        switch ($reportType) {
            case 'document_flow':
                return $this->documentFlowReport($startDate, $endDate);
            case 'response_times':
                return $this->responseTimesReport($startDate, $endDate);
            case 'entity_activity':
                return $this->entityActivityReport($startDate, $endDate);
            default:
                return response()->json(['error' => 'Invalid report type'], 400);
        }
    }

    private function documentFlowReport($startDate, $endDate)
    {
        $flow = DocumentStatus::select('status', DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('status')
            ->get();

        return response()->json($flow);
    }

    private function responseTimesReport($startDate, $endDate)
    {
        $responseTimes = CorrespondenceTransfer::select(
            'office.name as office_name',
            DB::raw('AVG(DATEDIFF(response_deadline, transfer_datetime)) as avg_response_time')
        )
            ->join('office', 'correspondence_transfer.office_id', '=', 'office.id')
            ->whereBetween('transfer_datetime', [$startDate, $endDate])
            ->groupBy('office.id', 'office.name')
            ->get();

        return response()->json($responseTimes);
    }

    private function entityActivityReport($startDate, $endDate)
    {
        $activity = Entity::select(
            'entity.name as entity_name',
            DB::raw('count(document.id) as document_count')
        )
            ->leftJoin('document', 'entity.id', '=', 'document.entity_id')
            ->whereBetween('document.created_at', [$startDate, $endDate])
            ->groupBy('entity.id', 'entity.name')
            ->get();

        return response()->json($activity);
    }
}
