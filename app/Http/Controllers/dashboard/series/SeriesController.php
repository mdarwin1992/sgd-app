<?php

namespace App\Http\Controllers\dashboard\series;

use App\Http\Controllers\Controller;
use App\Models\Counter;
use App\Models\SeriesEntity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class SeriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        try {
            $validatedData = $request->validate([
                'entity_id' => 'required|exists:entity,id',
                'series_name' => 'required|string|max:100',
            ]);

            DB::beginTransaction();

            $series = SeriesEntity::create([
                'entity_id' => $request->entity_id,
                'series_name' => $request->series_name,
            ]);

            $newCounter = Counter::create([
                'parent_count' => null,
                'child_count' => null,
                'series_entity_id' => $series->id,
            ]);

            DB::commit();

            return response()->json(['message' => 'Serie creada exitosamente', 'data' => $series], 200);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Error de validación', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear/actualizar serie: ' . $e->getMessage());
            return response()->json(['message' => 'Error al crear/actualizar Serie Documental', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        try {
            $series = SeriesEntity::with(['entity'])->where('entity_id', '=', $id)->get();
            return response()->json(['data' => $series]);
        } catch (\Exception $e) {
            Log::error('Error fetching series: ' . $e->getMessage());
            return response()->json(['message' => 'Error fetching series', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
