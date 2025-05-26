<?php

namespace App\Http\Controllers\dashboard\retenciondocumental;

use App\Http\Controllers\Controller;
use App\Http\Controllers\helpers\CounterController;
use App\Models\Counter;
use App\Models\DocumentaryType;
use App\Models\FinalDisposition;
use App\Models\Office;
use App\Models\Retention;
use App\Models\Series;
use App\Models\SeriesEntity;
use App\Models\Subseries;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class RetencionDocumentalController extends Controller
{
    /**
     * Display a listing of the series.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $series = Series::with(['subseries', 'retention', 'finalDisposition', 'documentaryTypes'])->get();
            return response()->json($series);
        } catch (\Exception $e) {
            Log::error('Error fetching series: ' . $e->getMessage());
            return response()->json(['message' => 'Error fetching series', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created series in storage or update if exists.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'office_id' => 'required',
                'series_entity_id' => 'required',
                'series_code' => 'required|string',
                'subseries' => 'array',
                'subseries.*.name' => 'required|string|max:100',
                'subseries.*.code' => 'required|string|max:10',
                'administrative_retention' => 'required|integer',
                'central_retention' => 'required|integer',
                'disposition_type' => 'required|array',
                'disposition_type.*' => 'required',
                'disposal_procedure' => 'nullable|string',
                'documentary_types' => 'required|array',
                'documentary_types.*' => 'required',
                'entity_id' => 'required',
            ]);

            DB::beginTransaction();

            // Crear la serie documental
            $series = Series::create([
                'office_id' => $validatedData['office_id'],
                'series_entity_id' => $validatedData['series_entity_id'],
                'series_code' => $validatedData['series_code'],
            ]);

            // Crear las subseries
            if (!empty($validatedData['subseries'])) {
                foreach ($validatedData['subseries'] as $subseries) {
                    Subseries::create([
                        'series_id' => $series->id,
                        'subseries_name' => $subseries['name'],
                        'subseries_code' => $subseries['code'],
                    ]);
                }
            }

            // Crear la retención
            Retention::create([
                'series_id' => $series->id,
                'administrative_retention' => $validatedData['administrative_retention'],
                'central_retention' => $validatedData['central_retention'],
            ]);

            // Crear la disposición final
            foreach ($validatedData['disposition_type'] as $dispositionType) {
                FinalDisposition::create([
                    'series_id' => $series->id,
                    'disposition_type' => $dispositionType,
                    'disposal_procedure' => $validatedData['disposal_procedure'],
                ]);
            }

            // Crear los tipos documentales
            foreach ($validatedData['documentary_types'] as $documentType) {
                DocumentaryType::create([
                    'series_id' => $series->id,
                    'document_name' => $documentType,
                ]);
            }


            $counter = Counter::where('series_entity_id', $validatedData['series_entity_id'])
                ->latest()
                ->first();

            if ($counter) {
                if (is_null($counter->parent_count) && is_null($counter->child_count)) {
                    // Actualizar ambos parent_count y child_count si ambos son nulos
                    $lastSeries = DB::table('series')->select('series_code')
                        ->orderBy('id', 'desc')
                        ->first();

                    $lastconus = $lastSeries->series_code;

                    // Si parent_count y child_count no son nulos, solo actualiza child_count
                    $lastSubseries = DB::table('subseries')->select('subseries_code')
                        ->join('series', 'subseries.series_id', '=', 'series.id')
                        ->where('series_entity_id', '=', $validatedData['series_entity_id'])
                        ->orderBy('subseries.id', 'desc')
                        ->first();

                    $conus = $lastSubseries->subseries_code + 1;

                    DB::table('counters')
                        ->where('series_entity_id', $validatedData['series_entity_id'])
                        ->update([
                            'parent_count' => $lastconus,
                            'child_count' => $conus,
                        ]);
                } else {
                    // Si parent_count y child_count no son nulos, solo actualiza child_count
                    $lastSubseries = DB::table('subseries')->select('subseries_code')
                        ->join('series', 'subseries.series_id', '=', 'series.id')
                        ->where('series_entity_id', '=', $validatedData['series_entity_id'])
                        ->orderBy('subseries.id', 'desc')
                        ->first();

                    $conus = $lastSubseries->subseries_code + 1;

                    DB::table('counters')
                        ->where('series_entity_id', $validatedData['series_entity_id'])
                        ->update([
                            'child_count' => $conus,
                        ]);
                }
            } else {
                // Manejar el caso en que no se encuentra ningún counter
                DB::table('counters')
                    ->insert([
                        'series_entity_id' => $validatedData['series_entity_id'],
                        'parent_count' => 1,
                        'child_count' => 1,
                    ]);
            }


            DB::commit();

            // Cargar las relaciones para la respuesta
            //$series->load(['subseries', 'retention', 'finalDisposition', 'documentaryTypes']);

            return response()->json([
                'message' => 'Serie Documental creada exitosamente',
                'series' => $counter
            ], 201);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear Serie Documental: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error al crear Serie Documental',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getUsedSeries()
    {
        $usedSeries = Series::whereHas('centralArchives')
            ->with('seriesEntity:id,series_name')
            ->select('series.id', 'series.series_entity_id', 'series.series_code')
            ->addSelect(DB::raw('(SELECT series_id FROM central_archive WHERE central_archive.series_id = series.id LIMIT 1) as central_archive_series_id'))
            ->get()
            ->map(function ($series) {
                return [
                    'id' => $series->id,
                    'name' => $series->seriesEntity->series_name,
                    'code' => $series->series_code,
                    'series_id' => $series->central_archive_series_id,
                ];
            });

        return response()->json($usedSeries);
    }

    /**
     * Display the specified series.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $series = Series::with(['subseries', 'retention', 'finalDisposition', 'documentaryTypes'])->findOrFail($id);
            return response()->json($series);
        } catch (\Exception $e) {
            Log::error('Error fetching series: ' . $e->getMessage());
            return response()->json(['message' => 'Error fetching series', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified series in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'series_entity_id' => 'required',
                'series_code' => 'required|string|max:10',
                'subseries' => 'array',
                'subseries.*.name' => 'required|string|max:100',
                'subseries.*.code' => 'required|string|max:10',
                'administrative_retention' => 'required|integer',
                'central_retention' => 'required|integer',
                'disposition_type' => 'required|in:CT,E,S',
                'disposal_procedure' => 'nullable|string',
                'documentary_types' => 'array',
                'documentary_types.*' => 'required|string|max:100',
            ]);

            DB::beginTransaction();

            $series = Series::findOrFail($id);
            $series->update([
                'series_entity_id' => $validatedData['series_entity_id'],
                'series_code' => $validatedData['series_code'],
            ]);

            // Update Subseries
            if (isset($validatedData['subseries'])) {
                $subseriesCounter = 0;
                foreach ($validatedData['subseries'] as $subseries) {
                    $subseriesCounter++;
                    Subseries::updateOrCreate(
                        ['series_id' => $series->id, 'subseries_code' => $subseries['code']],
                        [
                            'subseries_name' => $subseries['name'],
                            'counter' => $series->counter . '.' . $subseriesCounter,
                        ]
                    );
                }
                // Delete subseries not in the new list
                Subseries::where('series_id', $series->id)
                    ->whereNotIn('subseries_code', array_column($validatedData['subseries'], 'code'))
                    ->delete();
            }

            // Update Retention
            Retention::updateOrCreate(
                ['series_id' => $series->id],
                [
                    'administrative_retention' => $validatedData['administrative_retention'],
                    'central_retention' => $validatedData['central_retention'],
                ]
            );

            // Update FinalDisposition
            FinalDisposition::updateOrCreate(
                ['series_id' => $series->id],
                [
                    'disposition_type' => $validatedData['disposition_type'],
                    'disposal_procedure' => $validatedData['disposal_procedure'],
                ]
            );

            // Update DocumentaryTypes
            if (isset($validatedData['documentary_types'])) {
                // Delete existing documentary types not in the new list
                DocumentaryType::where('series_id', $series->id)
                    ->whereNotIn('document_name', $validatedData['documentary_types'])
                    ->delete();

                foreach ($validatedData['documentary_types'] as $documentType) {
                    DocumentaryType::updateOrCreate(
                        ['series_id' => $series->id, 'document_name' => $documentType],
                        []
                    );
                }
            }

            DB::commit();

            // Load relationships
            $series->load(['subseries', 'retention', 'finalDisposition', 'documentaryTypes']);

            return response()->json(['message' => 'Series updated successfully', 'series' => $series], 200);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation error', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating series: ' . $e->getMessage());
            return response()->json(['message' => 'Error updating series', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified series from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $series = Series::findOrFail($id);

            // Delete related records
            Subseries::where('series_id', $id)->delete();
            Retention::where('series_id', $id)->delete();
            FinalDisposition::where('series_id', $id)->delete();
            DocumentaryType::where('series_id', $id)->delete();

            // Delete the series
            $series->delete();

            DB::commit();

            return response()->json(['message' => 'Series deleted successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting series: ' . $e->getMessage());
            return response()->json(['message' => 'Error deleting series', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Generate a PDF for the specified series.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function print($id)
    {
        try {
            $series = Series::with(['subseries', 'retention', 'finalDisposition', 'documentaryTypes'])->findOrFail($id);

            $pdf = PDF::loadView('series.print', compact('series'));

            return $pdf->download('series_' . $series->series_code . '.pdf');
        } catch (\Exception $e) {
            Log::error('Error generating PDF for series: ' . $e->getMessage());
            return response()->json(['message' => 'Error generating PDF', 'error' => $e->getMessage()], 500);
        }
    }
}
