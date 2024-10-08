<?php

namespace App\Http\Requests\document;

use Illuminate\Foundation\Http\FormRequest;

class DocumentRequest extends FormRequest
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
            'reference_code' => 'required|string|max:50,reference_code,' . $this->route('document'),
            'system_code' => 'nullable|string|max:50,system_code' . $this->route('document'),
            'received_date' => 'required|date',
            'origin' => 'required|string|max:100',
            'sender_name' => 'required|string|max:100',
            'subject' => 'required|string|max:255',
            'has_attachments' => 'required|in:SI,NO',
            'page_count' => 'nullable|integer',
            'file_path' => 'nullable|string|max:255',
            'transfer_status' => 'nullable|string|max:30',
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
            'reference_code' => 'código de referencia',
            'system_code' => 'código del sistema',
            'received_date' => 'fecha de recepción',
            'origin' => 'origen',
            'sender_name' => 'nombre del remitente',
            'subject' => 'asunto',
            'has_attachments' => 'tiene adjuntos',
            'page_count' => 'número de páginas',
            'file_path' => 'ruta del archivo',
            'transfer_status' => 'estado de transferencia',
        ];
    }
}
