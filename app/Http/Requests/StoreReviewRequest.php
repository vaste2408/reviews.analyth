<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
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
            'postamat_id' => 'integer',
            'thematic_id' => 'integer',
            'source_id' => 'integer',
            'marketplace_id' => 'integer',
            'confirmed' => 'bool',
            'need_reaction' => 'bool',
            'closed' => 'bool', //устранено
            'text' => 'required | string',
            'user_fio' => 'required | string',
            'user_phone' => 'required | string',
            'score' => 'required',
        ];
    }
}
