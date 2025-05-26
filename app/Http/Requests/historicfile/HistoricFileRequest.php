<?php

namespace App\Http\Requests\historicfile;

use Illuminate\Foundation\Http\FormRequest;

class HistoricFileRequest extends FormRequest
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
            'entity_id' => 'required|numeric',
            'system_code' => 'required|string|max:255',
            'filed' => 'required|string|max:255',
            'office_id' => 'required|numeric',
            'series_id' => 'required|numeric',
            'subseries_id' => 'required|numeric',
            'shelf_number' => 'nullable|string|max:50',
            'tray' => 'nullable|string|max:50',
            'box_number' => 'nullable|string|max:50',
            'main_conservation_medium' => 'required|in:CAJA,CARPETA,LEGADO,TOMO',
            'preserved_in' => 'nullable',
            'ord_number' => 'nullable|string|max:50',
            'folio_number' => 'nullable|string|max:50',
            'folder_year' => 'nullable|numeric',
            'support' => 'nullable|string|max:50',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'document_reference' => 'nullable|string|max:250',
            'third_parties' => 'nullable|string|max:250',
            'object_observations' => 'nullable|string',
            'file_path' => 'required|string|max:255',
        ];
    }

    public function attributes()
    {
        return [
            'entity_id' => 'Entity ID',
            'system_code' => 'System Code',
            'filed' => 'Filed',
            'office_id' => 'Office ID',
            'series_id' => 'Series ID',
            'subseries_id' => 'Subseries ID',
            'shelf_number' => 'Shelf Number',
            'tray' => 'Tray',
            'box_number' => 'Box Number',
            'main_conservation_medium' => 'Main Conservation Medium',
            'preserved_in' => 'Preserved In',
            'ord_number' => 'Order Number',
            'folio_number' => 'Folio Number',
            'folder_year' => 'Folder Year',
            'support' => 'Support',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'document_reference' => 'Document Reference',
            'third_parties' => 'Third Parties',
            'object_observations' => 'Object Observations',
            'file_path' => 'File Path',
        ];
    }
}
