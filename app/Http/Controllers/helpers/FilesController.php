<?php

namespace App\Http\Controllers\helpers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

class FilesController extends Controller
{
    public function upload(Request $request)
    {
        try {
            // Validación de la solicitud
            $request->validate([
                'filepath' => 'required|file|max:10240', // 10MB máximo
                'reference_code' => 'required|string',
            ]);

            // Preparación de la información del archivo
            $file = $request->file('filepath');
            $referenceCode = $request->input('reference_code');
            $dir = $referenceCode;
            $extension = $file->getClientOriginalExtension();
            $filename = $referenceCode . '.' . $extension;

            // Almacenamiento del archivo
            $path = 'public/upload/' . $dir;
            $filePath = Storage::putFileAs($path, $file, $filename);

            // Respuesta exitosa
            return response()->json([
                'message' => 'Se ha almacenado correctamente el archivo',
                'data' => $filePath
            ], Response::HTTP_OK);

        } catch (ValidationException $e) {
            // Manejo de errores de validación
            return response()->json([
                'status' => 'error',
                'message' => 'Error de validación: ' . $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);

        } catch (Exception $e) {
            // Manejo de otros errores
            Log::error('Error al subir el archivo: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Ocurrió un error al subir el archivo. Por favor, intente de nuevo.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function responseFile(Request $request)
    {
        try {
            // Validación de la solicitud
            $request->validate([
                'filepath' => 'required|file|max:10240', // 10MB máximo
                'directory' => 'required|string',
            ]);

            // Preparación de la información del archivo
            $file = $request->file('filepath');
            $referenceCode = $request->input('directory');
            $responseFile = $request->input('response_file');
            // $extension = $file->getClientOriginalExtension();
            $filename = $responseFile;

            // Almacenamiento del archivo
            $path = 'public/upload/' . $referenceCode . '/respuesta';
            $filePath = Storage::putFileAs($path, $file, $filename);

            // Respuesta exitosa
            return response()->json([
                'message' => 'Se ha almacenado correctamente el archivo',
                'data' => $filePath
            ], Response::HTTP_OK);

        } catch (ValidationException $e) {
            // Manejo de errores de validación
            return response()->json([
                'status' => 'error',
                'message' => 'Error de validación: ' . $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);

        } catch (Exception $e) {
            // Manejo de otros errores
            Log::error('Error al subir el archivo: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Ocurrió un error al subir el archivo. Por favor, intente de nuevo.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
