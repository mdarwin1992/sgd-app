<?php

namespace App\Http\Controllers\dashboard\office;

use App\Helpers\DatabaseErrorHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\office\OfficeRequest;
use App\Models\Office;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OfficeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $offices = Office::with(['department', 'user'])->get();
        return response()->json([
            'data' => $offices,
            'message' => 'offices successfully recovered'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(OfficeRequest $request)
    {


        try {
            // Iniciar una transacción de base de datos
            DB::beginTransaction();

            // Crear un nuevo registro en la tabla 'Office' con los datos enviados en la solicitud
            $office = Office::create($request->only([
                'code', 'name', 'department_id', 'user_id', 'status'
            ]));

            // Confirmar la transacción de la base de datos
            DB::commit();

            // Retornar una respuesta JSON con los datos del nuevo registro y un mensaje de éxito
            return response()->json([
                'data' => $office,
                'message' => "Office created successfully"
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
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $office = Office::with(['department', 'user'])->find($id);
        if (!$office) {
            return response()->json(['message' => 'Office not found'], 404);
        }
        return response()->json([
            'data' => $office,
            'message' => 'offices successfully recovered'
        ]);
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
        try {
            // Iniciar una transacción de base de datos
            DB::beginTransaction();

            // Buscar el registro de 'Office' por su ID
            $office = Office::find($id);

            // Verificar si el registro no existe y retornar un mensaje de error si es el caso
            if (!$office) {
                return response()->json(['message' => 'Entity not found'], 404);
            }

            // Actualizar el registro con los datos enviados en la solicitud
            $office->update($request->only([
                'code', 'name', 'department_id', 'user_id', 'status'
            ]));

            // Confirmar la transacción de la base de datos
            DB::commit();

            // Retornar los datos actualizados y un mensaje de éxito en formato JSON
            return response()->json([
                'data' => $office,
                'message' => "Office updated successfully"
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
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        try {
            // Iniciar una transacción de base de datos
            DB::beginTransaction();

            // Buscar el registro de 'Office' por su ID
            $office = Office::find($id);

            // Verificar si el registro no existe y retornar un mensaje de error si es el caso
            if (!$office) {
                return response()->json(['message' => 'Entity not found'], 404);
            }

            // Eliminar el registro
            $office->delete();

            // Confirmar la transacción de la base de datos
            DB::commit();

            // Retornar un mensaje de éxito en formato JSON
            return response()->json([
                'data' => $office,
                'message' => "Office deleted successfully"
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
