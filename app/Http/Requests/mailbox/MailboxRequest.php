<?php

namespace App\Http\Requests\mailbox;

use Illuminate\Foundation\Http\FormRequest;

class MailboxRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'correspondence_transfer_id' => 'required',
            'response_content' => 'required|string',
        ];
    }

    public function attributes()
    {
        return [
            'correspondence_transfer_id' => 'transferencia de correspondencia',
            'response_content' => 'contenido de la respuesta',
        ];
    }
}
