<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostamatsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'bounds[0][0]' => 'nullable|decimal:0,100',
            'bounds[0][1]' => 'nullable|decimal:0,100',
            'bounds[1][0]' => 'nullable|decimal:0,100',
            'bounds[1][1]' => 'nullable|decimal:0,100',
            'center[]' => 'nullable|array',
            'zoom' => 'nullable|integer',
            'rating_min' => 'nullable|decimal:0,5',
            'rating_max' => 'nullable|decimal:0,5',
            'category' => 'nullable|integer',
            'type' => 'nullable|integer',
            'with_reviews' => 'nullable|integer',
        ];
    }
}
