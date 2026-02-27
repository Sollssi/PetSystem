<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'species' => ['required', 'string', 'max:80'],
            'breed' => ['required', 'string', 'max:120'],
            'age' => ['required', 'integer', 'min:0', 'max:40'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
