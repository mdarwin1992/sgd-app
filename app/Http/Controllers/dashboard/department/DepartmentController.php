<?php

namespace App\Http\Controllers\dashboard\department;

use App\Helpers\DatabaseErrorHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\department\DepartmentRequest;
use App\Models\Department;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        //
        // Obtener todos los registros de la tabla 'Department' y ordenarlos por la fecha de creación
        $departments = Department::with('entity:id,nit,verification_digit,name,logo')->get();

        // Retornar una respuesta JSON con los datos obtenidos y un mensaje de éxito
        return response()->json([
            'data' => $departments,
            'message' => 'Department successfully recovered'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(DepartmentRequest $request)
    {
        //
        try {
            // Iniciar una transacción de base de datos
            DB::beginTransaction();

            // Crear un nuevo registro en la tabla 'Department' con los datos enviados en la solicitud
            $department = Department::create($request->only([
                'code', 'name', 'entity_id', 'status'
            ]));

            // Confirmar la transacción de la base de datos
            DB::commit();

            // Retornar una respuesta JSON con los datos del nuevo registro y un mensaje de éxito
            return response()->json([
                'data' => $department,
                'message' => 'Department created successfully'
            ], 200);

        } catch (QueryException $e) {
            // Revertir la transacción en caso de error en la consulta SQL
            DB::rollBack();

            // Manejar el error utilizando un manejador personalizado y retornar una respuesta JSON
            return DatabaseErrorHandler::handleException($e, 'Department', ['attributes' => $request->all()]);
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
        // Buscar el registro de 'Department' por su ID
        $department = Department::with('entity:id,nit,verification_digit,name,logo')->find($id);

        // Verificar si el registro no existe y retornar un mensaje de error si es el caso
        if (!$department) {
            return response()->json(['message' => 'Department not found'], 404);
        }

        // Retornar los datos del registro encontrado y un mensaje de éxito en formato JSON
        return response()->json([
            'data' => $department,
            'message' => 'Department retrieved successfully'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        //
        try {
            // Iniciar una transacción de base de datos
            DB::beginTransaction();

            // Buscar el registro de 'Department' por su ID
            $department = Department::find($id);

            // Verificar si el registro no existe y retornar un mensaje de error si es el caso
            if (!$department) {
                return response()->json(['message' => 'Entity not found'], 404);
            }

            // Actualizar el registro con los datos enviados en la solicitud
            $department->update($request->only([
                'code', 'name', 'entity_id', 'status'
            ]));

            // Confirmar la transacción de la base de datos
            DB::commit();

            // Retornar los datos actualizados y un mensaje de éxito en formato JSON
            return response()->json([
                'data' => $department,
                'message' => 'Department updated successfully'
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

            // Buscar el registro de 'Department' por su ID
            $department = Department::find($id);

            // Verificar si el registro no existe y retornar un mensaje de error si es el caso
            if (!$department) {
                return response()->json(['message' => 'Entity not found'], 404);
            }

            // Eliminar el registro
            $department->delete();

            // Confirmar la transacción de la base de datos
            DB::commit();

            // Retornar un mensaje de éxito en formato JSON
            return response()->json([
                'data' => $department,
                'message' => 'Entity deleted successfully'
            ], 200);

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
