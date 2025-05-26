<?php

namespace App\Http\Controllers\dashboard\documentsending;

use App\Helpers\DatabaseErrorHandler;
use App\Http\Controllers\Controller;
use App\Http\Controllers\helpers\HelpersController;
use App\Http\Requests\documentsending\DocumentSendingRequest;
use App\Mail\DocumentSendingMail;
use App\Models\DocumentSending;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DocumentSendingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        //
        // Obtener todos los registros de la tabla 'Document Sending' y ordenarlos por la fecha de creación
        $documentsending = DocumentSending::with('department')
            ->orderBy('updated_at', 'desc')
            ->get();;

        // Retornar una respuesta JSON con los datos obtenidos y un mensaje de éxito
        return response()->json([
            'data' => $documentsending,
            'message' => 'Document sending successfully recovered'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(DocumentSendingRequest $request)
    {
        try {
            DB::beginTransaction();

            $documentsending = DocumentSending::create($request->only([
                'send_date', 'subject', 'sender', 'recipient', 'page_count',
                'department_id', 'office_id', 'document_path'
            ]));

            $currentCount = DB::table('entity_counters')
                ->where([['entity_id', '=', $request->entity_id], ['current_code', '=', 2]])
                ->increment('current_count', 1); // Esto ya devuelve el nuevo valor del contador

            try {
                // Obtener el nombre de la empresa (ajusta esto según cómo almacenas el nombre de la empresa)
                $companyName = HelpersController::getLoggedUserEntityName();

                // Enviar correo electrónico
                Mail::to($documentsending->recipient)->send(new DocumentSendingMail(
                    $documentsending->subject,
                    $documentsending->sender,
                    $documentsending->recipient,
                    $documentsending->page_count,
                    $documentsending->document_path,
                    $companyName
                ));
            } catch (\Exception $mailException) {
                // Registrar el error específico del correo
                Log::error('Error al enviar el correo: ' . $mailException->getMessage());
            }

            DB::commit();

            return response()->json([
                'data' => $documentsending,
                'message' => 'Document sending successfully' . (isset($mailException) ? ' but email could not be sent' : ' and email sent')
            ], 200);

        } catch (QueryException $e) {
            DB::rollBack();
            return DatabaseErrorHandler::handleException($e, 'DocumentSending', ['attributes' => $request->all()]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error inesperado al crear documento: ' . $e->getMessage());
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
        // Buscar el registro de 'Entity' por su ID
        $documentSending = DocumentSending::with('department')->findOrFail($id);

        // Verificar si el registro no existe y retornar un mensaje de error si es el caso
        if (!$documentSending) {
            return response()->json(['message' => 'Entity not found'], 404);
        }

        // Retornar los datos del registro encontrado y un mensaje de éxito en formato JSON
        return response()->json([
            'data' => $documentSending,
            'message' => 'Document sending successfully recovered'
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

            // Buscar el registro de 'Document Sending' por su ID
            $documentsending = DocumentSending::find($id);

            // Verificar si el registro no existe y retornar un mensaje de error si es el caso
            if (!$documentsending) {
                return response()->json(['message' => 'Document sending not found'], 404);
            }

            // Actualizar el registro con los datos enviados en la solicitud
            $documentsending->update($request->only([
                'send_date', 'subject', 'sender', 'recipient', 'page_count',
                'department_id', 'office_id', 'document_path'
            ]));

            // Confirmar la transacción de la base de datos
            DB::commit();

            // Retornar los datos actualizados y un mensaje de éxito en formato JSON
            return response()->json([
                'data' => $documentsending,
                'message' => 'Document sending updated successfully'
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
        //
        try {
            // Iniciar una transacción de base de datos
            DB::beginTransaction();

            // Buscar el registro de 'Document Sending' por su ID
            $documentsending = DocumentSending::find($id);

            // Verificar si el registro no existe y retornar un mensaje de error si es el caso
            if (!$documentsending) {
                return response()->json(['message' => 'Document sending not found'], 404);
            }
            // Eliminar el registro
            $documentsending->delete();

            // Confirmar la transacción de la base de datos
            DB::commit();

            // Retornar un mensaje de éxito en formato JSON
            return response()->json([
                'data' => $documentsending,
                'message' => 'Document sending deleted successfully'
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
