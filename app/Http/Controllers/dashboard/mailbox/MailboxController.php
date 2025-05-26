<?php

namespace App\Http\Controllers\dashboard\mailbox;

use App\Helpers\DatabaseErrorHandler;
use App\Http\Controllers\Controller;
use App\Http\Controllers\helpers\HelpersController;
use App\Http\Requests\mailbox\MailboxRequest;
use App\Mail\DocumentLinkMail;
use App\Mail\DocumentResponseMail;
use App\Models\Document;
use App\Models\DocumentLog;
use App\Models\RequestResponse;
use Dflydev\DotAccessData\Data;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class MailboxController extends Controller
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
        $requestresponse = RequestResponse::with(['document' => function ($query) {
            $query->with('documentStatus')->whereHas('documentStatus', function ($statusQuery) {
                $statusQuery->where('status', 'CONTESTADO');
            });
        }])->orderBy('created_at', 'desc')
            ->get();


        // Retornar una respuesta JSON con los datos obtenidos y un mensaje de éxito
        return response()->json([
            'data' => $requestresponse,
            'message' => 'Request Response recovered'
        ]);
    }

    public function getMailbox($id)
    {
        $user = Auth::user();
        $roles = $user->getRoleNames(); // Devuelve una colección de roles


        if ($roles[0] == 'EMPRESA') {

            $requestResponses = DB::table('document')
                ->join('correspondence_transfer', 'document.id', '=', 'correspondence_transfer.document_id')
                ->join('office', 'correspondence_transfer.office_id', '=', 'office.id')
                ->join('document_status', 'document.id', '=', 'document_status.document_id')
                ->where('document_status.status', '=', 'CONTESTADO')->get();

        } else {
            $officeId = $id;
            $requestResponses = DB::table('document')
                ->join('correspondence_transfer', 'document.id', '=', 'correspondence_transfer.document_id')
                ->join('office', 'correspondence_transfer.office_id', '=', 'office.id')
                ->join('document_status', 'document.id', '=', 'document_status.document_id')
                ->where([['document_status.status', '=', 'CONTESTADO'], ['office_id', '=', $officeId]])->get();
        }
        return response()->json([
            'data' => $requestResponses,
            'message' => 'Request Responses recovered'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(MailboxRequest $request)
    {
        //
        try {
            // Iniciar una transacción de base de datos
            DB::beginTransaction();

            $mailbox = RequestResponse::create([
                'response_content' => $request->response_content,
                'response_email' => $request->response_email,
                'response_document_path' => $request->response_document_path,
                'document_id' => $request->document_id,
            ]);

            // Validar que todos los parámetros necesarios estén presentes
            if (!empty($request->response_email)) {

                // Generar una URL firmada para el documento
                $documentLink = URL::signedRoute(
                    'dashboard.show-response',
                    ['id' => $request->directory, 'item' => $request->response_file]
                );

                // Registrar la URL generada
                Log::info('URL del documento generada:', ['url' => $documentLink]);

                // Enviar el correo electrónico
                Mail::to($request->response_email)->send(new DocumentResponseMail($documentLink, $request->directory, HelpersController::getLoggedUserEntityName()));

            }

            $documentLog = DocumentLog::create([
                'document_id' => $request->document_id,
                'action_type' => 'BUZÓN',
                'user_id' => Auth::id()
            ]);

            $documentStatus = DB::table('document_status')
                ->where('document_id', $request->input('document_id'))
                ->update([
                    'status' => 'CONTESTADO',
                ]);

            // Confirmar la transacción de la base de datos
            DB::commit();

            // Retornar una respuesta JSON con los datos del nuevo registro y un mensaje de éxito
            return response()->json([
                'data' => $mailbox,
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
                'message' => 'Error inesperado. Contacte al administrador.',
                $e->getMessage(),
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
    public function update(MailboxRequest $request, $id)
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
