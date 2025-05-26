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

            Storage::putFileAs('/public/upload/' . $dir . '/', $file, $filename);
            $agent = 'storage/upload/' . $dir . '/' . $filename;
            $file->move('storage/upload/' . $dir . '/', $filename);

            // Respuesta exitosa
            return response()->json([
                'message' => 'Se ha almacenado correctamente el archivo',
                'data' => $agent
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

    public function logosFiles(Request $request)
    {
        try {
            // Validación de la solicitud
            $request->validate([
                'filepath' => 'required|file|max:10240', // 10MB máximo
            ]);

            // Preparación de la información del archivo
            $file = $request->file('filepath');
            $referenceCode = date('dmY');
            $dir = $referenceCode;
            $extension = $file->getClientOriginalExtension();
            $filename = $referenceCode . '.' . $extension;

            // Almacenamiento del archivo
            $path = 'public/upload/logo/' . $dir;
            $filePath = Storage::putFileAs($path, $file, $filename);

            Storage::putFileAs('/public/upload/logo/' . $dir . '/', $file, $filename);
            $agent = 'storage/upload/logo/' . $dir . '/' . $filename;
            $file->move('storage/upload/logo/' . $dir . '/', $filename);

            // Respuesta exitosa
            return response()->json([
                'message' => 'Se ha almacenado correctamente el archivo',
                'data' => $agent
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
            $filename = $responseFile;

            // Almacenamiento del archivo
            $path = 'public/upload/' . $referenceCode . '/respuesta';
            $filePath = Storage::putFileAs($path, $file, $filename);

            Storage::putFileAs('/public/upload/' . $referenceCode . '/respuesta', $file, $filename);
            $agent = 'storage/upload/' . $referenceCode . '/respuesta' . $filename;
            $file->move('storage/upload/' . $referenceCode . '/respuesta', $filename);

            // Respuesta exitosa
            return response()->json([
                'message' => 'Se ha almacenado correctamente el archivo',
                'data' => $agent
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

    public function CentralArchiveFile(Request $request)
    {
        try {
            // Validación de la solicitud
            $request->validate([
                'filepath' => 'required|file|max:10240', // 10MB máximo
                'response_file' => 'required|string',
            ]);

            // Preparación de la información del archivo
            $file = $request->file('filepath');
            $responseFile = $request->input('response_file');
            $extension = $file->getClientOriginalExtension();

            $filename = $responseFile . '.' . $extension;

            // Almacenamiento del archivo
            Storage::putFileAs('/public/upload/archivo_central/' . $responseFile . '/', $file, $filename);
            $agent = 'storage/upload/archivo_central/' . $responseFile . '/' . $filename;
            $file->move('storage/upload/archivo_central/' . $responseFile . '/', $filename);

            // Respuesta exitosa
            return response()->json([
                'message' => 'Se ha almacenado correctamente el archivo',
                'data' => $agent
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

    public function HistoricalFile(Request $request)
    {
        try {
            // Validación de la solicitud
            $request->validate([
                'filepath' => 'required|file|max:10240', // 10MB máximo
                'response_file' => 'required|string',
            ]);

            // Preparación de la información del archivo
            $file = $request->file('filepath');
            $responseFile = $request->input('response_file');
            $extension = $file->getClientOriginalExtension();

            $filename = $responseFile . '.' . $extension;

            // Almacenamiento del archivo
            Storage::putFileAs('/public/upload/archivo_historico/' . $responseFile . '/', $file, $filename);
            $agent = 'storage/upload/archivo_historico/' . $responseFile . '/' . $filename;
            $file->move('storage/upload/archivo_historico/' . $responseFile . '/', $filename);

            // Respuesta exitosa
            return response()->json([
                'message' => 'Se ha almacenado correctamente el archivo',
                'data' => $agent
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
