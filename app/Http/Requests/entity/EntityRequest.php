<?php

namespace App\Http\Requests\entity;

use Illuminate\Foundation\Http\FormRequest;

class EntityRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado a realizar esta solicitud.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Ajusta esto según tus necesidades de autorización
    }

    /**
     * Obtiene las reglas de validación que se aplican a la solicitud.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'nit' => 'required|integer',                // Validación del NIT
            'verification_digit' => 'required|integer', // Validación del dígito de verificación
            'name' => 'required|string|max:100',              // Validación del nombre de la entidad
            'type' => 'required|in:PÚBLICO,PRIVADO',          // Validación del tipo de entidad
            'address' => 'nullable|string|max:150',           // Validación de la dirección
            'phone' => 'nullable|string|max:20',              // Validación del teléfono
            'email' => 'nullable|email|max:100',              // Validación del correo electrónico
            'creation_date' => 'nullable|date',               // Validación de la fecha de creación
            'legal_representative' => 'nullable|string', // Validación del representante legal
            'employee_count' => 'nullable|integer',           // Validación del número de empleados
            'logo' => 'nullable|string|max:100',              // Validación del logo
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
            'nit' => 'NIT',
            'verification_digit' => 'dígito de verificación',
            'name' => 'nombre',
            'type' => 'tipo',
            'address' => 'dirección',
            'phone' => 'teléfono',
            'email' => 'correo electrónico',
            'creation_date' => 'fecha de creación',
            'legal_representative' => 'representante legal',
            'employee_count' => 'número de empleados',
            'website' => 'sitio web',
            'logo' => 'logo',
            'status' => 'estado',
        ];
    }

}
