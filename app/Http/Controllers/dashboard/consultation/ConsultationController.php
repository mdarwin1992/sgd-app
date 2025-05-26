<?php

namespace App\Http\Controllers\dashboard\consultation;

use App\Http\Controllers\Controller;
use App\Models\CentralArchive;
use App\Models\HistoricFile;
use App\Models\Series;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    //

    public function findDocument($id)
    {
        $query = CentralArchive::query()
            ->with([
                'entity:id,name',
                'office:id,name,code',
                'series:id,series_code',
                'Series.SeriesEntity:id,series_name',
                'series.documentaryTypes:id,series_id,document_name',
                'series.retention:id,series_id,administrative_retention,central_retention',
                'series.finalDisposition:id,series_id,disposition_type,disposal_procedure',
                'subseries:id,subseries_name,subseries_code'
            ]);

        if ($id) {
            $query->where('filed', '=', $id);
        }

        $documents = $query->get();

        return response()->json($documents);
    }

    public function findDocumentHistoric($id)
    {
        $query = HistoricFile::query()
            ->with([
                'entity:id,name',
                'office:id,name,code',
                'series:id,series_code',
                'Series.SeriesEntity:id,series_name',
                'series.documentaryTypes:id,series_id,document_name',
                'series.retention:id,series_id,administrative_retention,central_retention',
                'series.finalDisposition:id,series_id,disposition_type,disposal_procedure',
                'subseries:id,subseries_name,subseries_code'
            ]);

        if ($id) {
            $query->where('filed', '=', $id);
        }

        $documents = $query->get();

        return response()->json($documents);
    }

    public function searchByBox($id)
    {

        $query = CentralArchive::query()
            ->with([
                'entity:id,name',
                'office:id,name,code',
                'series:id,series_code',
                'Series.SeriesEntity:id,series_name',
                'series.documentaryTypes:id,series_id,document_name',
                'series.retention:id,series_id,administrative_retention,central_retention',
                'series.finalDisposition:id,series_id,disposition_type,disposal_procedure',
                'subseries:id,subseries_name,subseries_code'
            ]);

        if ($id) {
            $query->where('box_number', '=', $id);
        }

        $documents = $query->get();

        return response()->json($documents);
    }
    public function searchByBoxHistoric($id)
    {

        $query = HistoricFile::query()
            ->with([
                'entity:id,name',
                'office:id,name,code',
                'series:id,series_code',
                'Series.SeriesEntity:id,series_name',
                'series.documentaryTypes:id,series_id,document_name',
                'series.retention:id,series_id,administrative_retention,central_retention',
                'series.finalDisposition:id,series_id,disposition_type,disposal_procedure',
                'subseries:id,subseries_name,subseries_code'
            ]);

        if ($id) {
            $query->where('box_number', '=', $id);
        }

        $documents = $query->get();

        return response()->json($documents);
    }

    public function searchBySerial($id)
    {

        $query = CentralArchive::query()
            ->with([
                'entity:id,name',
                'office:id,name,code',
                'series:id,series_code',
                'Series.SeriesEntity:id,series_name',
                'series.documentaryTypes:id,series_id,document_name',
                'series.retention:id,series_id,administrative_retention,central_retention',
                'series.finalDisposition:id,series_id,disposition_type,disposal_procedure',
                'subseries:id,subseries_name,subseries_code'
            ]);

        if ($id) {
            $query->where('series_id', '=', $id);
        }

        $documents = $query->get();
        return response()->json($documents);
    }
    public function searchBySerialHistoric($id)
    {

        $query = HistoricFile::query()
            ->with([
                'entity:id,name',
                'office:id,name,code',
                'series:id,series_code',
                'Series.SeriesEntity:id,series_name',
                'series.documentaryTypes:id,series_id,document_name',
                'series.retention:id,series_id,administrative_retention,central_retention',
                'series.finalDisposition:id,series_id,disposition_type,disposal_procedure',
                'subseries:id,subseries_name,subseries_code'
            ]);

        if ($id) {
            $query->where('series_id', '=', $id);
        }

        $documents = $query->get();
        return response()->json($documents);
    }

    public function searchByYear($id)
    {
        $query = CentralArchive::query()
            ->with([
                'entity:id,name',
                'office:id,name,code',
                'series:id,series_code',
                'Series.SeriesEntity:id,series_name',
                'series.documentaryTypes:id,series_id,document_name',
                'series.retention:id,series_id,administrative_retention,central_retention',
                'series.finalDisposition:id,series_id,disposition_type,disposal_procedure',
                'subseries:id,subseries_name,subseries_code'
            ]);

        if ($id) {
            $query->whereYear('start_date', $id);
        }

        $documents = $query->get();


        return response()->json($documents);
    }
    public function searchByYearHistoric($id)
    {
        $query = HistoricFile::query()
            ->with([
                'entity:id,name',
                'office:id,name,code',
                'series:id,series_code',
                'Series.SeriesEntity:id,series_name',
                'series.documentaryTypes:id,series_id,document_name',
                'series.retention:id,series_id,administrative_retention,central_retention',
                'series.finalDisposition:id,series_id,disposition_type,disposal_procedure',
                'subseries:id,subseries_name,subseries_code'
            ]);

        if ($id) {
            $query->whereYear('start_date', $id);
        }

        $documents = $query->get();


        return response()->json($documents);
    }

}
