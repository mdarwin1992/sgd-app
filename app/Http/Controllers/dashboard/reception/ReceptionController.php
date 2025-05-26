<?php

namespace App\Http\Controllers\dashboard\reception;

use App\Helpers\DatabaseErrorHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\document\DocumentRequest;
use App\Models\Document;
use App\Models\DocumentLog;
use App\Models\DocumentStatus;
use App\Models\Reception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReceptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {

        // Obtener todos los registros de la tabla 'Entity' y ordenarlos por la fecha de creación
        $document = Document::whereHas('documentStatus', function ($query) {
            $query->where('status', 'RECIBIDA');
        })->with(['reception', 'documentStatus'])->get();


        // Retornar una respuesta JSON con los datos obtenidos y un mensaje de éxito
        return response()->json([
            'data' => $document,
            'message' => 'Entity successfully recovered'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(DocumentRequest $request)
    {
        try {
            // Iniciar una transacción de base de datos
            DB::beginTransaction();

            // Crear un nuevo registro en la tabla 'Document' con los datos enviados en la solicitud
            $document = Document::create($request->only([
                'reference_code', 'system_code', 'received_date', 'origin', 'sender_name', 'subject', 'has_attachments', 'page_count', 'file_path',
            ]));

            $documentLog = DocumentLog::create([
                'document_id' => $document->id,
                'action_type' => 'RECEPCIÓN',
                'user_id' => Auth::id()
            ]);

            $reception = Reception::create([
                'document_id' => $document->id,
            ]);

            $DocumentStatus = DocumentStatus::create([
                'document_id' => $document->id,
                'status' => 'RECIBIDA',
            ]);

            $currentCount = DB::table('entity_counters')
                ->where([['entity_id', '=', $request->entity_id], ['current_code', '=', 1]])
                ->increment('current_count', 1); // Esto ya devuelve el nuevo valor del contador

            // Confirmar la transacción de la base de datos
            DB::commit();

            // Retornar una respuesta JSON con los datos del nuevo registro y un mensaje de éxito
            return response()->json([
                'data' => $document,
                'message' => 'Document created successfully'
            ], 200);

        } catch (QueryException $e) {
            // Revertir la transacción en caso de error en la consulta SQL
            DB::rollBack();

            // Manejar el error utilizando un manejador personalizado y retornar una respuesta JSON
            return DatabaseErrorHandler::handleException($e, 'Document', ['attributes' => $request->all()]);
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
        // Buscar el registro de 'Document' por su ID
        $document = Document::with(['reception:id,document_id,created_at', 'documentStatus:id,document_id,status'])
            ->findOrFail($id);

        // Verificar si el registro no existe y retornar un mensaje de error si es el caso
        if (!$document) {
            return response()->json(['message' => 'Entity not found'], 404);
        }

        // Retornar los datos del registro encontrado y un mensaje de éxito en formato JSON
        return response()->json([
            'data' => $document,
            'message' => 'Document successfully recovered'
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(DocumentRequest $request, $id)
    {
        try {
            // Iniciar una transacción de base de datos
            DB::beginTransaction();

            // Buscar el registro de 'Document' por su ID
            $document = Document::find($id);

            // Verificar si el registro no existe y retornar un mensaje de error si es el caso
            if (!$document) {
                return response()->json(['message' => 'Entity not found'], 404);
            }

            // Actualizar el registro con los datos enviados en la solicitud
            $document->update($request->only([
                'reference_code', 'system_code', 'received_date', 'origin', 'sender_name',
                'subject', 'has_attachments', 'page_count', 'file_path', 'transfer_status'
            ]));

            // Confirmar la transacción de la base de datos
            DB::commit();

            // Retornar los datos actualizados y un mensaje de éxito en formato JSON
            return response()->json([
                'data' => $document,
                'message' => 'Document updated successfully'
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
        try {
            // Iniciar una transacción de base de datos
            DB::beginTransaction();

            // Buscar el registro de 'Document' por su ID
            $document = Document::find($id);

            // Verificar si el registro no existe y retornar un mensaje de error si es el caso
            if (!$document) {
                return response()->json(['message' => 'Entity not found'], 404);
            }

            // Eliminar el registro
            $document->delete();

            // Confirmar la transacción de la base de datos
            DB::commit();

            // Retornar un mensaje de éxito en formato JSON
            return response()->json([
                'data' => $document,
                'message' => 'Document deleted successfully'
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
