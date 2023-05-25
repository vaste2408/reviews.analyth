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
            'postamat_id' => 'sometimes | integer',
            'thematic_id' => 'sometimes | integer',
            'source_id' => 'sometimes | integer',
            'marketplace_id' => 'sometimes | integer',
            'emotion_id' => 'sometimes | integer',
            'confirmed' => 'sometimes | bool',
            'need_reaction' => 'sometimes | bool',
            'closed' => 'sometimes | bool',
            'text' => 'required | string',
            'user_fio' => 'required | string',
            'user_phone' => 'required | string',
            'score' => 'required | numeric',
        ];
    }
}
