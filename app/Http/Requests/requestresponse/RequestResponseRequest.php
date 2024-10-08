<?php

namespace App\Http\Requests\requestresponse;

use Illuminate\Foundation\Http\FormRequest;

class RequestResponseRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado a realizar esta solicitud.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Obtiene las reglas de validación que se aplican a la solicitud.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'correspondence_transfer_id' => 'required|exists:correspondence_transfer,id',
            'response_content' => 'required|string',
            'response_email' => 'required|email|max:255',
            'response_document_path' => 'required|string|max:255',
            'response_status' => 'required|string|max:30',
        ];
    }

    /**
     * Obtiene los atributos personalizados para los errores del validador.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'correspondence_transfer_id' => 'transferencia de correspondencia',
            'response_content' => 'contenido de la respuesta',
            'response_email' => 'correo electrónico de respuesta',
            'response_document_path' => 'ruta del documento de respuesta',
            'response_status' => 'estado de la respuesta',
        ];
    }
}
