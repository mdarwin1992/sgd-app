<?php

namespace App\Http\Controllers\dashboard\centralarchive;

use Illuminate\Http\Request;
use mysql_xdevapi\Exception;
use App\Models\CentralArchive;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Helpers\DatabaseErrorHandler;
use Illuminate\Database\QueryException;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Requests\centralarchive\CentralArchiveRequest;

class CentralArchiveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $archives = CentralArchive::with([
            'office',
            'entity',
            'series.seriesEntity',
            'subseries',
            'centralArchiveLoans.documentLoan'
        ])->get()->map(function ($archive) {
            return [
                'filed' => $archive->filed,
                'document_reference' => $archive->document_reference,
                'office' => ['name' => optional($archive->office)->name],
                'series' => [
                    'series_entity' => [
                        'series_name' => optional(optional($archive->series)->seriesEntity)->series_name
                    ]
                ],
                'folio_number' => $archive->folio_number,
                'third_parties' => $archive->third_parties,
                'id' => $archive->id,
            ];
        });

        return response()->json([
            'data' => $archives,

            'message' => 'Archivos recuperados correctamente'
        ], 200);
    }

    public function getCentralArchive()
    {

        $centralArchive = DB::table('central_archive')
            ->select(
                'central_archive.id as central_archive_id',
                'central_archive.filed',
                'central_archive.document_reference',
                'central_archive.folio_number',
                'central_archive.third_parties',
                'office.id as office_id',
                'office.name as office_name',
                'series_entity.series_name',
                'document_loans.order_number',
                'document_loans.state'
            )
            ->leftJoin('office', 'central_archive.office_id', '=', 'office.id')
            ->leftJoin('central_archive_loans', 'central_archive.id', '=', 'central_archive_loans.central_archive_id')
            ->leftJoin('document_loans', 'central_archive_loans.document_loans_order_number', '=', 'document_loans.order_number')
            ->leftJoin('series', 'central_archive.series_id', '=', 'series.id')
            ->leftJoin('series_entity', 'series.series_entity_id', '=', 'series_entity.id')

            ->get();

        // Aplicamos el map para transformar la estructura de los datos
        $mappedCentralArchive = $centralArchive->map(function ($item) {
            return [
                'id' => $item->central_archive_id,
                'filed' => $item->filed,
                'document_reference' => $item->document_reference,
                'folio_number' => $item->folio_number,
                'terceros' => $item->third_parties,
                'office' => [
                    'name' => $item->office_name,
                ],
                "series" => [
                    "series_entity" => [
                        "series_name" => $item->series_name,
                    ]
                ],
                'state' => $item->state,
                'order_number' => $item->order_number,
            ];
        });

        return response()->json([
            'data' => $mappedCentralArchive, // Ahora devolvemos los datos mapeados
            'message' => 'Archivos recuperados correctamente'
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CentralArchiveRequest $request)
    {
        DB::beginTransaction();

        try {
            // Crear un nuevo registro en la tabla central_archive
            $centralArchive = CentralArchive::create($request->validated());

            $currentCount = DB::table('entity_counters')
                ->where([['entity_id', '=', $request->entity_id], ['current_code', '=', 3]])
                ->increment('current_count', 1); // Esto ya devuelve el nuevo valor del contador

            // Confirmar la transacción
            DB::commit();

            // Retornar una respuesta JSON con los datos del nuevo registro y un mensaje de éxito
            return response()->json([
                'data' => $centralArchive,
                'message' => 'Central Archive record created successfully'
            ], 200);
        } catch (QueryException $e) {
            // Revertir la transacción en caso de error en la consulta SQL
            DB::rollBack();

            // Manejar el error utilizando un manejador personalizado y retornar una respuesta JSON
            return DatabaseErrorHandler::handleException($e, 'Central Archive', ['attributes' => $request->all()]);
        } catch (\Exception $e) {
            // Revertir la transacción en caso de cualquier otro error
            DB::rollBack();

            // Registrar el error en los logs
            Log::error('Error inesperado al crear registro en Central Archive: ' . $e->getMessage());

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
        try {
            $centralArchive = CentralArchive::findOrFail($id);
            return response()->json(['data' => $centralArchive]);
        } catch (\Exception $e) {
            Log::error('Error al obtener registro de Central Archive: ' . $e->getMessage());
            return response()->json(['message' => 'Error al obtener el registro.'], 500);
        }
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
        DB::beginTransaction();

        try {
            $centralArchive = CentralArchive::findOrFail($id);
            $centralArchive->update($request->validated());
            DB::commit();

            return response()->json([
                'data' => $centralArchive,
                'message' => 'Central Archive record updated successfully'
            ]);
        } catch (QueryException $e) {
            DB::rollBack();
            return DatabaseErrorHandler::handleException($e, 'Central Archive', ['attributes' => $request->all()]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar registro de Central Archive: ' . $e->getMessage());
            return response()->json(['message' => 'Error al actualizar el registro.'], 500);
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
        DB::beginTransaction();

        try {
            $centralArchive = CentralArchive::findOrFail($id);
            $centralArchive->delete();
            DB::commit();

            return response()->json([
                'message' => 'Central Archive record deleted successfully'
            ], 200);
        } catch (QueryException $e) {
            DB::rollBack();
            return DatabaseErrorHandler::handleException($e, 'Central Archive');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar registro de Central Archive: ' . $e->getMessage());
            return response()->json(['message' => 'Error al eliminar el registro.'], 500);
        }
    }
}
