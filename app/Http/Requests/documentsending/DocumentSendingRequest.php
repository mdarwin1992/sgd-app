<?php

namespace App\Http\Requests\documentsending;

use Illuminate\Foundation\Http\FormRequest;

class DocumentSendingRequest extends FormRequest
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
            'send_date' => 'required|date',
            'subject' => 'required|string|max:255',
            'sender' => 'required|string|max:100',
            'recipient' => 'required|string|max:100',
            'page_count' => 'nullable|integer',
            'department_id' => 'required|exists:department,id',
            'document_path' => 'required|string|max:255',
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
            'send_date' => 'fecha de envío',
            'subject' => 'asunto',
            'sender' => 'remitente',
            'recipient' => 'destinatario',
            'page_count' => 'número de páginas',
            'department_id' => 'departamento',
            'document_path' => 'ruta del documento',
        ];
    }
}
