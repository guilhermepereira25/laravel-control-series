<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SeriesFormRequest extends FormRequest
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
            'name' => [
                'required', 
                'min:3'
            ],
            'seasonsQuantity' => [
                'required'
            ],
            'episodesQuantity' => [
                'required'
            ]
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'O campo nome é obrigatório',
            'name.min' => 'O campo nome deve pelo menos :min caracteres',
            'seasonsQuantity.required' => 'O campo temporadas não pode ser vazio',
            'episodesQuantity.required' => 'O campo episódios não pode ser vazio'
        ];
    }
}
