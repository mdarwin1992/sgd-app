<?php

namespace App\Http\Controllers\dashboard\correspondencetransfer;

use App\Helpers\DatabaseErrorHandler;
use App\Http\Controllers\Controller;
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
use Illuminate\Support\Facades\Http;
use Twilio\Rest\Client;

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
        $transfer = $transfers = CorrespondenceTransfer::with([
            'office',
            'reception.document',
            'office.user'
        ])->get();

        // Retornar una respuesta JSON con los datos obtenidos y un mensaje de éxito
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
                'transfer_datetime', 'office_id', 'response_time', 'response_deadline',
                'job_type', 'reception_id'
            ]));

            $documentLog = DocumentLog::create([
                'document_id' => $request->document_id,
                'action_type' => 'TRANSFERENCIA',
                'user_id' => Auth::id()
            ]);

            // Obtener el enlace del documento, correo electrónico y número de WhatsApp del request
            $documentLink = $request->input('document_link');
            $recipientEmail = $request->input('recipient_email');

            // Validar que el enlace del documento, el correo electrónico y el número de WhatsApp estén presentes
            if (empty($documentLink) || empty($recipientEmail)) {
                throw new \InvalidArgumentException('El enlace del documento, el correo electrónico y el número de WhatsApp son requeridos.');
            }

            Mail::to($recipientEmail)->send(new DocumentLinkMail($documentLink));


            // Confirmar la transacción de la base de datos
            DB::commit();

            // Retornar una respuesta JSON con los datos del nuevo registro y un mensaje de éxito
            return response()->json([
                'data' => $transfer,
                'message' => 'Correspondence transfer created successfully. Document link sent by email and WhatsApp.'
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
            return DatabaseErrorHandler::handleException($e, 'Entity', ['attributes' => $request->all()]);

        } catch (\Exception $e) {
            // Revertir la transacción en caso de cualquier otro error
            DB::rollBack();

            // Registrar el error en los logs
            Log::error('Error inesperado al crear transferencia de correspondencia: ' . $e->getMessage());

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
        $transfer = CorrespondenceTransfer::with([
            'office',
            'reception.document',
            'office.user'
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
                'transfer_datetime', 'office_id', 'response_time', 'response_deadline',
                'job_type', 'reception_id'
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
