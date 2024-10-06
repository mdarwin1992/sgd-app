<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class DatabaseErrorHandler
{
    // Mapa de códigos de error SQL a mensajes de error personalizados y códigos HTTP
    // Cada entrada está en el formato: 'código' => ['mensaje de error', código_http]
    private const ERROR_MESSAGES = [
        // Errores de violación de integridad de datos
        '23000' => ['Violation of Integrity Constraint (Violación de clave única o duplicada)', 409],
        '23001' => ['Violation of Foreign Key Constraint (Violación de clave foránea)', 409],
        '23502' => ['Not Null Violation (No se puede insertar un valor NULL en este campo)', 400],
        '23503' => ['Foreign Key Violation (Violación de clave foránea)', 409],
        '23505' => ['Unique Violation (Ya existe un registro con esa clave única)', 409],
        '23514' => ['Check Violation (La operación violaría una restricción CHECK)', 400],

        // Errores de conexión con la base de datos
        '08001' => ['SQL Connection Error (No se puede establecer conexión con la base de datos)', 503],
        '08004' => ['SQL Server Rejected Connection (El servidor rechazó la conexión)', 503],
        '08006' => ['SQL Connection Lost (Se perdió la conexión con la base de datos)', 503],
        '08007' => ['SQL Transaction Timeout (Se agotó el tiempo de espera de la transacción)', 504],

        // Errores de sintaxis y consulta SQL
        '42000' => ['Syntax Error or Access Rule Violation (Error de sintaxis en la consulta SQL)', 400],
        '42S02' => ['Table or View Not Found (La tabla especificada no existe)', 404],
        '42S22' => ['Column Not Found (La columna especificada no existe)', 400],

        // Errores de recursos insuficientes
        '53000' => ['Insufficient Resources (Recursos insuficientes para completar la operación)', 503],
        '53100' => ['Too Many Connections (Se ha alcanzado el límite de conexiones simultáneas)', 503],
        '53200' => ['Disk Full (Se ha alcanzado el límite de uso de disco)', 503],
        '53300' => ['Out of Memory (Se ha alcanzado el límite de uso de memoria)', 503],

        // Excepciones de datos (formato, longitud, etc.)
        '22001' => ['Data Exception - Value Too Long (El valor es demasiado largo para el campo)', 400],
        '22003' => ['Data Exception - Numeric Value Out of Range (El valor numérico está fuera de rango)', 400],
        '22007' => ['Data Exception - Invalid Date Format (Formato de fecha/hora inválido)', 400],
        '22012' => ['Data Exception - Division by Zero (División por cero)', 400],
        '22P02' => ['Data Exception - Invalid Text Representation (Cadena de caracteres no válida para el tipo de datos)', 400],
    ];

    /**
     * Maneja las excepciones relacionadas con la base de datos y genera una respuesta JSON apropiada.
     *
     * @param Throwable $e La excepción capturada.
     * @param string|null $model El nombre del modelo afectado (opcional, para mayor contexto).
     * @param array $context Información adicional para el registro en logs.
     * @return JsonResponse Respuesta JSON con el mensaje de error y el código HTTP correspondiente.
     */
    public static function handleException(Throwable $e, string $model = null, array $context = []): JsonResponse
    {
        // Obtener el código de error SQL si la excepción es de tipo PDOException o tiene una excepción PDO relacionada
        $errorCode = $e instanceof \PDOException
            ? $e->getCode()
            : ($e->getPrevious() instanceof \PDOException ? $e->getPrevious()->getCode() : null);

        // Añadir contexto adicional (información de error) a los logs
        $logContext = array_merge($context, [
            'code' => $errorCode,       // Código de error SQL
            'file' => $e->getFile(),    // Archivo donde ocurrió el error
            'line' => $e->getLine(),    // Línea donde ocurrió el error
            'trace' => $e->getTraceAsString(), // Rastreo completo del error
            'model' => $model,          // Nombre del modelo afectado (si se proporcionó)
        ]);

        // Registrar el error en los logs con el contexto adicional
        Log::error('Database Error: ' . $e->getMessage(), $logContext);

        // Buscar el mensaje de error correspondiente al código de error SQL
        // Si no se encuentra, se usa un mensaje de error genérico
        $errorInfo = self::ERROR_MESSAGES[$errorCode] ?? ['Ha ocurrido un error inesperado. Por favor, contacte al administrador.', 500];

        // Devolver una respuesta JSON con el mensaje de error y el código HTTP apropiado
        return response()->json([
            'code' => $errorCode,        // Código de error SQL
            'mensaje' => $errorInfo[0],  // Mensaje de error para el usuario
            'model' => $model            // Modelo afectado (si aplica)
        ], $errorInfo[1]);               // Código HTTP correspondiente
    }
}
