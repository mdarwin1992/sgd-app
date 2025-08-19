<?php

namespace App\Http\Controllers\dashboard\series;

use App\Models\Counter;
use App\Models\Subseries;
use App\Models\SeriesEntity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
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

    public function getWithSubseries($entityId)
    {
        try {
            // 1. Validar que el entityId es un número válido.
            if (!is_numeric($entityId)) {
                return response()->json(['message' => 'El ID de la entidad debe ser un número.'], 400);
            }

            // 2. Realizar la consulta usando "eager loading" con with().
            // 'with('subseries')' le dice a Eloquent que cargue la relación 'subseries' que definimos en el modelo.
            // Esto evita el problema de N+1 queries, haciendo la consulta muy eficiente.
            $seriesWithSubseries = SeriesEntity::where('entity_id', $entityId)
                ->with(['subseries' => function ($query) {
                    // Opcional: Ordenar las subseries por su código o nombre.
                    $query->orderBy('subseries_code', 'asc');
                }])
                ->orderBy('series_name', 'asc') // Ordenar las series principales por su código.
                ->get();


            // 3. Devolver los datos en formato JSON.
            return response()->json(['data' => $seriesWithSubseries], 200);
        } catch (\Exception $e) {
            Log::error('Error al obtener series con subseries: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error interno del servidor al obtener la lista de series.',
                'error' => $e->getMessage()
            ], 500);
        }
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

    /*  public function storeMultiple(Request $request)
    {
        try {
            // 1. Validar la solicitud entrante.
            // Las reglas de validación coinciden exactamente con la estructura del JSON del frontend.
            $validatedData = $request->validate([
                'series_entity_id' => 'required|exists:series_entity,id',
                'subseries' => 'required|array|min:1',
                'subseries.*.name' => 'required|string|max:100',
                'subseries.*.code' => 'required|string|max:10', // Puede ser 'integer' si siempre es numérico
            ]);

            // Iniciar una transacción para asegurar la integridad de los datos.
            // Si algo falla, se revierte toda la operación.
            DB::beginTransaction();

            $seriesId = $validatedData['series_entity_id'];
            $highestNewCode = 0;

            // 2. Crear cada una de las subseries recibidas.
            foreach ($validatedData['subseries'] as $subseries) {
                // Crear el registro en la tabla de subseries.
                // Asegúrate de que el modelo 'Subseries' y su tabla tengan los campos
                // 'series_id', 'subseries_name' y 'subseries_code'.
                Subseries::create([
                    'series_id' => $seriesId,
                    'subseries_name' => $subseries['name'],
                    'subseries_code' => $subseries['code'],
                ]);

                // Mientras se crean, se mantiene un registro del código de subserie más alto.
                // Esto es más eficiente que consultar la base de datos de nuevo.
                if ((int)$subseries['code'] > $highestNewCode) {
                    $highestNewCode = (int)$subseries['code'];
                }
            }

            // 3. Actualizar la tabla de contadores.
            // Se busca el contador asociado a la serie padre.
            $counter = Counter::where('series_entity_id', $seriesId)->first();

            if ($counter) {
                // Si el contador ya existe, se actualiza el 'child_count'
                // con el código más alto de las subseries que acabamos de crear.
                $counter->child_count = $highestNewCode;
                $counter->save();
            } else {
                // Este es un caso de respaldo (fallback). Si una serie no tiene un contador,
                // se registra una advertencia y se crea uno para mantener la consistencia.
                Log::warning("No se encontró un contador para series_entity_id: {$seriesId}. Se creará uno nuevo.");

                // Se necesita el código de la serie padre para crear el nuevo contador.
                $seriesCode = DB::table('series_entity')->where('id', $seriesId)->value('series_code');

                Counter::create([
                    'series_entity_id' => $seriesId,
                    'parent_count' => $seriesCode,      // Código de la serie padre.
                    'child_count' => $highestNewCode,   // Código de la subserie más alta creada.
                ]);
            }

            // Si todas las operaciones fueron exitosas, se confirman los cambios en la base de datos.
            DB::commit();

            // Se retorna una respuesta de éxito.
            return response()->json([
                'message' => 'Subseries creadas exitosamente'
            ], 201);
        } catch (ValidationException $e) {
            // Si la validación falla, se revierten los cambios y se devuelve el error.
            DB::rollBack();
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // Para cualquier otra excepción, se revierten los cambios y se registra el error.
            DB::rollBack();
            Log::error('Error al crear Subseries: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error interno del servidor al crear las Subseries.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
 */
    public function storeMultiple(Request $request)
    {
        try {
            // 1. Validar la solicitud entrante.
            $validatedData = $request->validate([
                'series_entity_id' => 'required|exists:series_entity,id',
                'subseries' => 'required|array|min:1',
                'subseries.*.name' => 'required|string|max:100',
                'subseries.*.code' => 'required|string|max:10',
            ]);

            DB::beginTransaction();

            $seriesId = $validatedData['series_entity_id'];
            $highestNewCode = 0;

            // 2. Crear cada una de las subseries recibidas.
            foreach ($validatedData['subseries'] as $subseries) {
                Subseries::create([
                    'series_id' => $seriesId,
                    'subseries_name' => $subseries['name'],
                    'subseries_code' => $subseries['code'],
                ]);

                // Se mantiene el registro del código de subserie más alto para usarlo en el contador.
                if ((int)$subseries['code'] > $highestNewCode) {
                    $highestNewCode = (int)$subseries['code'];
                }
            }

            // 3. Actualizar la tabla de contadores (Lógica Corregida y Completa)

            // Primero, obtenemos el código de la serie padre. Este valor es nuestro 'parent_count'.
            // Esto asegura que el parent_count siempre sea el correcto para la serie que estamos modificando.
            $parentSeriesCode = DB::table('subseries')->where('id', $seriesId)->value('subseries_code');

            // Si por alguna razón la serie no tuviera código, se establece como 0 para evitar errores.
            if (is_null($parentSeriesCode)) {
                Log::error("La serie con ID {$seriesId} no tiene un series_code asignado. Se usará 0 como fallback.");
                $parentSeriesCode = 0;
            }

            // Usamos 'updateOrCreate' para actualizar el contador si existe, o crearlo si no.
            // Esta es la forma más limpia y segura de manejar esta lógica.
            Counter::updateOrCreate(
                // Criterios de búsqueda (Cláusula WHERE):
                [
                    'series_entity_id' => $seriesId
                ],
                // Valores para actualizar (si se encuentra) o para crear (si no se encuentra):
                [
                    'parent_count' => $parentSeriesCode, // Se asegura de que el código del padre esté siempre actualizado.
                    'child_count'  => $highestNewCode,   // Se establece con el código más alto de la subserie recién creada.
                ]
            );

            DB::commit();

            return response()->json([
                'message' => 'Subseries y contador actualizados exitosamente'
            ], 201);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear Subseries: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error interno del servidor al crear las Subseries.',
                'error' => $e->getMessage()
            ], 500);
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
        try {
            $validatedData = $request->validate([
                'subseries_name' => 'required|string|max:100',
                'subseries_code' => 'required|string|max:10',
            ]);

            $subseries = Subseries::find($id);

            if (!$subseries) {
                return response()->json(['message' => 'Subserie no encontrada.'], 404);
            }

            $subseries->update($validatedData);

            return response()->json([
                'message' => 'Subserie actualizada exitosamente.',
                'data' => $subseries
            ], 200);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Error de validación', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error al actualizar subserie: ' . $e->getMessage());
            return response()->json(['message' => 'Error interno del servidor.'], 500);
        }
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
