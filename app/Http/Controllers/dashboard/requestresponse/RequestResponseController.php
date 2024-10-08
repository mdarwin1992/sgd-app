<?php

namespace App\Http\Controllers\dashboard\requestresponse;

use App\Helpers\DatabaseErrorHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\requestresponse\RequestResponseRequest;
use App\Models\DocumentLog;
use App\Models\RequestResponse;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RequestResponseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        //
        // Obtener todos los registros de la tabla 'Request Response' y ordenarlos por la fecha de creación
        $requestresponse = RequestResponse::with([
            'correspondenceTransfer',
            'correspondenceTransfer.office',
            'correspondenceTransfer.reception',
            'correspondenceTransfer.reception.document'
        ])
            ->orderBy('created_at', 'desc')
            ->get();

        // Retornar una respuesta JSON con los datos obtenidos y un mensaje de éxito
        return response()->json([
            'data' => $requestresponse,
            'message' => 'Request Response recovered'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(RequestResponseRequest $request)
    {
        //
        try {
            // Iniciar una transacción de base de datos
            DB::beginTransaction();

            // Crear un nuevo registro en la tabla 'Request responsé' con los datos enviados en la solicitud
            $requestresponse = RequestResponse::create($request->only([
                'correspondence_transfer_id', 'response_content', 'response_email',
                'response_document_path'
            ]));

            $documentLog = DocumentLog::create([
                'document_id' => $request->document_id,
                'action_type' => 'BUZÓN',
                'user_id' => Auth::id()
            ]);

            // Confirmar la transacción de la base de datos
            DB::commit();

            // Retornar una respuesta JSON con los datos del nuevo registro y un mensaje de éxito
            return response()->json([
                'data' => $requestresponse,
                'message' => 'Request Response Created'
            ], 200);

        } catch (QueryException $e) {
            // Revertir la transacción en caso de error en la consulta SQL
            DB::rollBack();

            // Manejar el error utilizando un manejador personalizado y retornar una respuesta JSON
            return DatabaseErrorHandler::handleException($e, 'RequestResponse', ['attributes' => $request->all()]);
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
        //
        // Buscar el registro de 'Request Response' por su ID
        $requestresponse = RequestResponse::find($id);

        // Verificar si el registro no existe y retornar un mensaje de error si es el caso
        if (!$requestresponse) {
            return response()->json(['message' => 'Request response not found'], 404);
        }

        // Retornar los datos del registro encontrado y un mensaje de éxito en formato JSON
        return response()->json([
            'data' => $requestresponse,
            'message' => 'Request response successfully recovered'
        ]);
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

            // Buscar el registro de 'Request Response' por su ID
            $requestresponse = RequestResponse::find($id);

            // Verificar si el registro no existe y retornar un mensaje de error si es el caso
            if (!$requestresponse) {
                return response()->json(['message' => 'Entity not found'], 404);
            }

            // Actualizar el registro con los datos enviados en la solicitud
            $requestresponse->update($request->only([
                'correspondence_transfer_id', 'response_content', 'response_email',
                'response_document_path'
            ]));

            // Confirmar la transacción de la base de datos
            DB::commit();

            // Retornar los datos actualizados y un mensaje de éxito en formato JSON
            return response()->json([
                'data' => $requestresponse,
                'message' => 'Request Response Updated'
            ]);
        } catch (QueryException $e) {
            // Revertir la transacción en caso de error en la consulta SQL
            DB::rollBack();

            // Manejar el error utilizando un manejador personalizado y retornar una respuesta JSON
            return DatabaseErrorHandler::handleException($e, 'RequestResponse', ['attributes' => $request->all()]);
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
        //
        try {
            // Iniciar una transacción de base de datos
            DB::beginTransaction();

            // Buscar el registro de 'Request Response' por su ID
            $requestresponse = RequestResponse::find($id);

            // Verificar si el registro no existe y retornar un mensaje de error si es el caso
            if (!$requestresponse) {
                return response()->json(['message' => 'Entity not found'], 404);
            }

            // Eliminar el registro
            $requestresponse->delete();

            // Confirmar la transacción de la base de datos
            DB::commit();

            // Retornar un mensaje de éxito en formato JSON
            return response()->json([
                'data' => $requestresponse,
                'message' => 'Request Response Deleted'
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
