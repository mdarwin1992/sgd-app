<?php

namespace App\Http\Controllers\dashboard\entity;

use App\Helpers\DatabaseErrorHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\entity\EntityRequest;
use App\Models\Entity;
use App\Models\EntityCounter;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EntityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Obtener todos los registros de la tabla 'Entity' y ordenarlos por la fecha de creación
        $entities = Entity::all();

        // Retornar una respuesta JSON con los datos obtenidos y un mensaje de éxito
        return response()->json([
            'data' => $entities,
            'message' => 'Entity successfully recovered'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(EntityRequest $request)
    {
        try {
            // Iniciar una transacción de base de datos
            DB::beginTransaction();

            // Crear un nuevo registro en la tabla 'Entity' con los datos enviados en la solicitud
            $entity = Entity::create($request->only([
                'nit', 'verification_digit', 'name', 'type', 'address', 'phone', 'email', 'creation_date',
                'legal_representative', 'employee_count', 'website', 'logo'
            ]));

            // Crear los contadores iniciales para la entidad
            $countersToCreate = [
                ['entity_id' => $entity->id, 'current_count' => 1, 'current_code' => 1],
                ['entity_id' => $entity->id, 'current_count' => 1, 'current_code' => 2],
                ['entity_id' => $entity->id, 'current_count' => 1, 'current_code' => 3],
                ['entity_id' => $entity->id, 'current_count' => 1, 'current_code' => 4]
            ];

            // Insertar todos los contadores
            foreach ($countersToCreate as $counter) {
                EntityCounter::create($counter);
            }

            // Confirmar la transacción de la base de datos
            DB::commit();

            // Retornar una respuesta JSON con los datos del nuevo registro y un mensaje de éxito
            return response()->json([
                'data' => [
                    'entity' => $entity,
                    'counters' => $entity->counters
                ],
                'message' => 'Entity and counters created successfully'
            ], 200);

        } catch (QueryException $e) {
            // Revertir la transacción en caso de error en la consulta SQL
            DB::rollBack();

            // Manejar el error utilizando un manejador personalizado y retornar una respuesta JSON
            return DatabaseErrorHandler::handleException($e, 'Entity', ['attributes' => $request->all()]);
        } catch (\Exception $e) {
            // Revertir la transacción en caso de cualquier otro error
            DB::rollBack();

            // Registrar el error en los logs
            Log::error('Error inesperado al crear negocio: ' . $e->getMessage());

            // Retornar una respuesta JSON con un mensaje de error general
            return response()->json([
                'message' => 'Error inesperado. Contacte al administrador.'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        // Buscar el registro de 'Entity' por su ID
        $entity = Entity::find($id);

        // Verificar si el registro no existe y retornar un mensaje de error si es el caso
        if (!$entity) {
            return response()->json(['message' => 'Entity not found'], 404);
        }

        // Retornar los datos del registro encontrado y un mensaje de éxito en formato JSON
        return response()->json([
            'data' => $entity,
            'message' => 'Entity successfully recovered'
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(EntityRequest $request, $id)
    {
        try {
            // Iniciar una transacción de base de datos
            DB::beginTransaction();

            // Buscar el registro de 'Entity' por su ID
            $entity = Entity::find($id);

            // Verificar si el registro no existe y retornar un mensaje de error si es el caso
            if (!$entity) {
                return response()->json(['message' => 'Entity not found'], 404);
            }

            // Actualizar el registro con los datos enviados en la solicitud
            $entity->update($request->only([
                'nit', 'verification_digit', 'name', 'type', 'address', 'phone', 'email', 'creation_date',
                'legal_representative', 'employee_count', 'website', 'logo', 'status'
            ]));

            // Confirmar la transacción de la base de datos
            DB::commit();

            // Retornar los datos actualizados y un mensaje de éxito en formato JSON
            return response()->json([
                'data' => $entity,
                'message' => 'Entity updated successfully'
            ]);
        } catch (QueryException $e) {
            // Revertir la transacción en caso de error en la consulta SQL
            DB::rollBack();

            // Manejar el error utilizando un manejador personalizado y retornar una respuesta JSON
            return DatabaseErrorHandler::handleException($e, 'Entity', ['attributes' => $request->all()]);
        } catch (\Exception $e) {
            // Revertir la transacción en caso de cualquier otro error
            DB::rollBack();

            // Registrar el error en los logs
            Log::error('Error inesperado al actualizar negocio: ' . $e->getMessage());

            // Retornar una respuesta JSON con un mensaje de error general
            return response()->json([
                'message' => 'Error inesperado. Contacte al administrador.'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            // Iniciar una transacción de base de datos
            DB::beginTransaction();

            // Buscar el registro de 'Entity' por su ID
            $entity = Entity::find($id);

            // Verificar si el registro no existe y retornar un mensaje de error si es el caso
            if (!$entity) {
                return response()->json(['message' => 'Entity not found'], 404);
            }

            // Eliminar el registro
            $entity->delete();

            // Confirmar la transacción de la base de datos
            DB::commit();

            // Retornar un mensaje de éxito en formato JSON
            return response()->json([
                'data' => $entity,
                'message' => 'Entity deleted successfully'
            ]);

        } catch (\Exception $e) {
            // Revertir la transacción en caso de cualquier otro error
            DB::rollBack();

            // Registrar el error en los logs
            Log::error('Error inesperado al eliminar negocio: ' . $e->getMessage());

            // Retornar una respuesta JSON con un mensaje de error general
            return response()->json([
                'message' => 'Error inesperado. Contacte al administrador.'
            ], 500);
        }
    }

}
