<?php

namespace App\Http\Controllers\auth;

use App\Helpers\DatabaseErrorHandler;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class RegisterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        //
        try {
            //Le damos inicio a la transacion
            DB::beginTransaction();

            $validatedRegister = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required',
            ]);

            if ($validatedRegister->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validatedRegister->errors(),
                    'message' => 'Fallo de validación'
                ], 422);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
            ])->assignRole($request->rol);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'Registro exitoso'
            ], 200);

        } catch (QueryException $e) {
            // Revertir transacción en caso de error SQL
            DB::rollBack();
            return DatabaseErrorHandler::handleException($e, 'User', ['attributes' => $request->all()]);

        } catch (\Exception $e) {
            // Revertir transacción en caso de error no esperado
            DB::rollBack();
            Log::error('Error inesperado al crear negocio: ' . $e->getMessage());

            // Retornar respuesta JSON con mensaje de error
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
    public
    function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public
    function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public
    function destroy($id)
    {
        //
    }
}
