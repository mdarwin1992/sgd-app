<?php

namespace App\Http\Requests\correspondencetransfer;

use Illuminate\Foundation\Http\FormRequest;

class CorrespondenceTransferRequest extends FormRequest
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
            'transfer_datetime' => 'required|date',
            'office_id' => 'required|exists:office,id',
            'response_time' => 'required|string|max:10',
            'response_deadline' => 'required|date',
            'job_type' => 'required|string',
            'document_id' => 'required|exists:document,id',
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
            'transfer_datetime' => 'fecha y hora de transferencia',
            'office_id' => 'oficina',
            'response_time' => 'tiempo de respuesta',
            'response_deadline' => 'fecha límite de respuesta',
            'job_type' => 'tipo de trabajo',
            'document_id' => 'documento',
        ];
    }
}
