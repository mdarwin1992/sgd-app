<?php

namespace App\Http\Requests\office;

use Illuminate\Foundation\Http\FormRequest;

class OfficeRequest extends FormRequest
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
            'code' => 'required',
            //'status' => 'required|in:ACTIVO,INACTIVO',
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
            'code' => 'código',
        ];
    }
}
