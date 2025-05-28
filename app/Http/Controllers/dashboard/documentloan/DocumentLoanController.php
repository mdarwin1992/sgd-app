<?php

namespace App\Http\Controllers\dashboard\documentloan;

use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\DatabaseErrorHandler;
use App\Models\CentralArchiveLoans;
use App\Models\DocumentLoan;
use App\Models\DocumentReturn;
use App\Models\HistoricalArchiveLoan;
use Illuminate\Support\Facades\Log;


class DocumentLoanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $documentLoans = DocumentLoan::with(['centralArchiveLoans.CentralArchive', 'historicalArchiveLoans.historicFile', 'documentReturn'])->get();

        return response()->json([
            'message' => 'Lista de préstamos encontrados',
            'data' => $documentLoans
        ]);
    }

    public function getOrderNumber()
    {
        $documentLoans = DocumentLoan::with(['centralArchiveLoans.CentralArchive', 'historicalArchiveLoans.historicFile'])
            ->select('order_number')
            ->where('state', '=', '1')
            ->get();

        return response()->json([
            'message' => 'Lista de préstamos encontrados',
            'data' => $documentLoans
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $documentloan = DocumentLoan::create([
                'registration_date' => date('Y-m-d'),
                'order_number' => date('Ymdhis'),
                'identification' => $request->identification,
                'names' => $request->names,
                'office_id' => $request->office_id,
                'return_date' => $request->return_date,
                'type_of_document_borrowed' => $request->type_of_document_borrowed,
                'entity_id' => $request->entity_id,
                'user_id' => $request->user_id,
            ]);

            if ($request->type_of_document_borrowed == 1) {
                $centralarchiveloans = CentralArchiveLoans::create([
                    'document_loans_order_number' => date('Ymdhis'),
                    'central_archive_id' => $request->central_archive_id
                ]);
            } else {
                $centralarchiveloans = HistoricalArchiveLoan::create([
                    'document_loans_order_number' => date('Ymdhis'),
                    'historic_file_id' => $request->historic_file_id
                ]);
            }

            DB::commit();

            return response()->json([
                'data' => [
                    'documentloan' => $documentloan,
                    'centralarchiveloans' => $centralarchiveloans
                ],
                'message' => 'Documentary loan and counters created successfully'
            ], 200);
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error inesperado al crear prestamo documental: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error inesperado. Contacte al administrador.'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): JsonResponse
    {
        try {
            $documentLoan = DocumentLoan::with(['centralArchiveLoans.CentralArchive', 'historicalArchiveLoans.historicFile'])
                ->findOrFail($id);

            return response()->json([
                'message' => 'Préstamo encontrado',
                'data' => $documentLoan
            ]);
        } catch (\Exception $e) {
            Log::error('Error al buscar préstamo: ' . $e->getMessage());
            return response()->json([
                'message' => 'Préstamo no encontrado.'
            ], 404);
        }
    }

    public function getLoanCentralArchive($id): JsonResponse
    {
        $documentloan = DocumentLoan::with(['office', 'centralArchiveLoans.centralArchive', 'historicalArchiveLoans.HistoricFile'])
            ->where('order_number', $id)
            ->first();


        return response()->json([
            'data' => $documentloan
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $documentLoan = DocumentLoan::findOrFail($id);

            $documentLoan->update([
                'identification' => $request->identification,
                'names' => $request->names,
                'office_id' => $request->office_id,
                'return_date' => $request->return_date,
                'type_of_document_borrowed' => $request->type_of_document_borrowed,
            ]);

            return response()->json([
                'message' => 'Préstamo actualizado correctamente',
                'data' => $documentLoan
            ]);
        } catch (\Exception $e) {
            Log::error('Error al actualizar préstamo: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error al actualizar préstamo. Contacte al administrador.'
            ], 500);
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id): JsonResponse
    {
        try {
            $documentLoan = DocumentLoan::findOrFail($id);
            $documentLoan->delete();

            return response()->json([
                'message' => 'Préstamo eliminado correctamente'
            ]);
        } catch (\Exception $e) {
            Log::error('Error al eliminar préstamo: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error al eliminar préstamo. Contacte al administrador.'
            ], 500);
        }
    }

    /**
     * Change the state of the specified document loan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function changeState(Request $request, $id): JsonResponse
    {
        try {
            $documentLoan = DocumentLoan::findOrFail($id);
            $documentLoan->state = $request->state;
            $documentLoan->save();

            return response()->json([
                'message' => 'Estado del préstamo actualizado correctamente',
                'data' => $documentLoan
            ]);
        } catch (\Exception $e) {
            Log::error('Error al cambiar el estado del préstamo: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error al cambiar el estado del préstamo. Contacte al administrador.'
            ], 500);
        }
    }

    /**
     * Return a borrowed document.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function returnDocument(Request $request): JsonResponse
    {
        try {
            $orderNumber = $request->order_number;

            // Find the document loan by order number
            $documentLoan = DocumentLoan::where('order_number', $orderNumber)->firstOrFail();

            // Create a document return record
            $documentReturn = DocumentReturn::create([
                'document_loan_order_number' => $orderNumber,
                'document_conditions' => $request->document_conditions,
                'comments' => $request->comments,
            ]);

            // Optionally, update the document loan state if needed
            DB::table('document_loans')->where('document_loans.id', '=', $documentLoan->id)->update([
                'state' => 0,
            ]);

            return response()->json([
                'message' => 'Document returned successfully',
                'data' => $documentReturn
            ]);
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
