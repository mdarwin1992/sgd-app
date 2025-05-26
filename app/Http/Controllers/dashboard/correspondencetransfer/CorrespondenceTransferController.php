<?php

namespace App\Http\Controllers\dashboard\correspondencetransfer;

use App\Helpers\DatabaseErrorHandler;
use App\Http\Controllers\Controller;
use App\Http\Controllers\helpers\HelpersController;
use App\Http\Requests\correspondencetransfer\CorrespondenceTransferRequest;
use App\Mail\DocumentLinkMail;
use App\Models\CorrespondenceTransfer;
use App\Models\DocumentLog;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class CorrespondenceTransferController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        //
        // Obtener todos los registros de la tabla 'Entity' y ordenarlos por la fecha de creación
        $transfer = CorrespondenceTransfer::with([
            'office',
            'office.user',
            'document',
            'document.documentStatus' => function ($query) {
                $query->where('status', 'PROCESANDO');
            }
        ])->get();


        // Retornar una respuesta JSON con los datos obtenidos y un mensaje de éxito
        return response()->json([
            'data' => $transfer,
            'message' => 'Correspondence transfer successfully recovered'
        ]);
    }

    public function getCorrespondenceTransfer($id)
    {

        $officeId = $id ?? null;

        $transferQuery = DB::table('document')
            ->join('correspondence_transfer', 'document.id', '=', 'correspondence_transfer.document_id')
            ->join('office', 'correspondence_transfer.office_id', '=', 'office.id')
            ->join('document_status', 'document.id', '=', 'document_status.document_id')
            ->where('document_status.status', '=', 'PROCESANDO');

        if (is_null($officeId)) {
            $transferQuery->where([['document_status.status', '=', 'PROCESANDO'], ['office_id', '=', $officeId]]);
        }

        $transfer = $transferQuery->get();

        return response()->json([
            'data' => $transfer,
            'message' => 'Correspondence transfer successfully recovered'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CorrespondenceTransferRequest $request)
    {
        try {
            // Iniciar una transacción de base de datos
            DB::beginTransaction();

            // Crear un nuevo registro en la tabla 'Correspondence Transfer' con los datos enviados en la solicitud
            $transfer = CorrespondenceTransfer::create($request->only([
                'transfer_datetime',
                'office_id',
                'response_time',
                'response_deadline',
                'job_type',
                'document_id',
            ]));

            $documentLog = DocumentLog::create([
                'document_id' => $request->document_id,
                'action_type' => 'TRANSFERENCIA',
                'user_id' => Auth::id()
            ]);

            // Obtener los parámetros necesarios del request
            $id = $request->input('path');
            $item = $request->input('file_path');
            $recipientEmail = $request->input('email');
            $recipientName = $request->input('name'); // Nuevo campo para el nombre del destinatario
            $companyName = HelpersController::getLoggedUserEntityName(); // Asumiendo que el nombre de la empresa está en la configuración

            // Validar que todos los parámetros necesarios estén presentes
            if (empty($id) || empty($item) || empty($recipientEmail) || empty($recipientName)) {
                throw new \InvalidArgumentException('El id, item, correo electrónico y nombre del destinatario son requeridos.');
            }

            // Generar una URL firmada para el documento
            $documentLink = URL::signedRoute(
                'dashboard.show-file',
                ['id' => $id, 'item' => $item]
            );

            // Registrar la URL generada
            Log::info('URL del documento generada:', ['url' => $documentLink]);

            // Generar una contraseña aleatoria
            $password = $id;

            // Enviar el correo electrónico con la nueva clase DocumentLinkMail
            Mail::to($recipientEmail)->send(new DocumentLinkMail($documentLink, $password, $companyName, $recipientName));

            $documentStatus = DB::table('document_status')
                ->where('document_id', $request->input('document_id'))
                ->update([
                    'status' => 'PROCESANDO',
                ]);

            // Confirmar la transacción de la base de datos
            DB::commit();

            // Retornar una respuesta JSON con los datos del nuevo registro y un mensaje de éxito
            return response()->json([
                'data' => $transfer,
                'message' => 'Transferencia de correspondencia creada exitosamente. Enlace del documento enviado por correo electrónico.'
            ], 200);
        } catch (\InvalidArgumentException $e) {
            // Revertir la transacción en caso de error de validación
            DB::rollBack();

            // Retornar una respuesta JSON con el mensaje de error
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        } catch (QueryException $e) {
            // Revertir la transacción en caso de error en la consulta SQL
            DB::rollBack();

            // Manejar el error utilizando un manejador personalizado y retornar una respuesta JSON
            return DatabaseErrorHandler::handleException($e, 'CorrespondenceTransfer', ['attributes' => $request->all()]);
        } catch (\Exception $e) {
            // Revertir la transacción en caso de cualquier otro error
            DB::rollBack();

            // Registrar el error en los logs
            Log::error('Error inesperado al crear transferencia de correspondencia: ' . $e->getMessage());

            // Retornar una respuesta JSON con un mensaje de error general
            return response()->json([
                'message' => $e->getMessage()
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
        $transfer = CorrespondenceTransfer::with([
            'office',
            'office.user',
            'document',
            //'document.documentStatus'
        ])->findOrFail($id);

        // Verificar si el registro no existe y retornar un mensaje de error si es el caso
        if (!$transfer) {
            return response()->json(['message' => 'Entity not found'], 404);
        }

        // Retornar los datos del registro encontrado y un mensaje de éxito en formato JSON
        return response()->json([
            'data' => $transfer,
            'message' => 'Correspondence transfer successfully recovered.',
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CorrespondenceTransferRequest $request, $id)
    {
        try {
            // Iniciar una transacción de base de datos
            DB::beginTransaction();

            // Buscar el registro de 'Correspondence Transfer' por su ID
            $transfer = CorrespondenceTransfer::find($id);

            // Verificar si el registro no existe y retornar un mensaje de error si es el caso
            if (!$transfer) {
                return response()->json(['message' => 'Entity not found'], 404);
            }

            // Actualizar el registro con los datos enviados en la solicitud
            $transfer->update($request->only([
                'transfer_datetime',
                'office_id',
                'response_time',
                'response_deadline',
                'job_type',
                'reception_id'
            ]));

            // Confirmar la transacción de la base de datos
            DB::commit();

            // Retornar los datos actualizados y un mensaje de éxito en formato JSON
            return response()->json([
                'data' => $transfer,
                'message' => 'Correspondence transfer updated successfully.'
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
            $transfer = CorrespondenceTransfer::find($id);

            // Verificar si el registro no existe y retornar un mensaje de error si es el caso
            if (!$transfer) {
                return response()->json(['message' => 'Entity not found'], 404);
            }

            // Eliminar el registro
            $transfer->delete();

            // Confirmar la transacción de la base de datos
            DB::commit();

            // Retornar un mensaje de éxito en formato JSON
            return response()->json([
                'data' => $transfer,
                'message' => 'Correspondence transfer deleted successfully.'
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
