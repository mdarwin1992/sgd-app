<?php

namespace App\Http\Controllers\dashboard\retenciondocumental;

use App\Models\Office;
use App\Models\Series;
use App\Models\Counter;
use App\Models\Retention;
use App\Models\Subseries;
use App\Models\SeriesEntity;
use Illuminate\Http\Request;
use App\Models\DocumentaryType;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\FinalDisposition;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\helpers\CounterController;

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
                'administrative_retention' => 'required|integer',
                'central_retention' => 'required|integer',
                'disposition_type' => 'required|array',
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


            DB::commit();

            // Cargar las relaciones para la respuesta
            //$series->load(['subseries', 'retention', 'finalDisposition', 'documentaryTypes']);

            return response()->json([
                'message' => 'Serie Documental creada exitosamente',
                'series' => $series
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

    public function storeBatch(Request $request)
    {
        // 1. Validación para el lote completo
        $rules = [
            'records' => 'required|array',
            'records.*.office_id' => 'required',
            'records.*.series_entity_id' => 'required',
            'records.*.series_code' => 'required|string',
            'records.*.administrative_retention' => 'required',
            'records.*.central_retention' => 'required',
            'records.*.disposition_type' => 'required|array',
            'records.*.documentary_types' => 'required|array',
            'records.*.entity_id' => 'required',
            'records.*.disposal_procedure' => 'nullable|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación en el lote de datos',
                'errors' => $validator->errors()
            ], 422);
        }

        $records = $validator->validated()['records'];

        DB::beginTransaction();

        try {
            $createdSeriesList = [];
            $recordCount = count($records);

            // 2. Iterar sobre cada registro del lote
            foreach ($records as $recordData) {

                // Crear la serie documental
                $series = Series::create([
                    'office_id' => $recordData['office_id'],
                    'series_entity_id' => $recordData['series_entity_id'],
                    'series_code' => $recordData['series_code'],
                ]);

                // Crear la retención
                Retention::create([
                    'series_id' => $series->id,
                    'administrative_retention' => $recordData['administrative_retention'],
                    'central_retention' => $recordData['central_retention'],
                ]);

                // Crear la disposición final
                foreach ($recordData['disposition_type'] as $dispositionType) {
                    FinalDisposition::create([
                        'series_id' => $series->id,
                        'disposition_type' => $dispositionType,
                        'disposal_procedure' => $recordData['disposal_procedure'] ?? null,
                    ]);
                }

                // Crear los tipos documentales
                foreach ($recordData['documentary_types'] as $documentType) {
                    DocumentaryType::create([
                        'series_id' => $series->id,
                        'document_name' => $documentType,
                    ]);
                }

                $createdSeriesList[] = $series->load(['retention', 'finalDisposition', 'documentaryTypes']);
            }

            DB::commit();

            return response()->json([
                'message' => "{$recordCount} Series Documentales creadas exitosamente",
                'data' => $createdSeriesList
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear lote de Series Documentales: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Ocurrió un error al procesar el lote.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getUsedSeries()
    {
        $usedSeries = Series::whereHas('centralArchives')
            ->with(
                'seriesEntity:id,series_name',
                'office'
            )
            ->select('series.id', 'series.series_entity_id', 'series.series_code', 'series.office_id')
            ->addSelect(DB::raw('(SELECT series_id FROM central_archive WHERE central_archive.series_id = series.id LIMIT 1) as central_archive_series_id'))
            ->get()
            ->map(function ($series) {
                return [
                    'id' => $series->id,
                    'name' => $series->seriesEntity->series_name,
                    'code' => $series->series_code,
                    'series_id' => $series->central_archive_series_id,
                    'office' => $series->office->name,
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
