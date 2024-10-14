<?php

namespace App\Http\Requests\department;

use Illuminate\Foundation\Http\FormRequest;

class DepartmentRequest extends FormRequest
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
            'code' => 'required|string|max:50|unique:department,code,' . $this->route('department'),
            'name' => 'required|string|max:100',
            'entity_id' => 'required',
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
            'name' => 'nombre',
            'entity_id' => 'entidad',
            'status' => 'estado',
        ];
    }
}
