<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReviewRequest extends FormRequest
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
            'postamat_id' => 'integer', //привязать к постамату
            'source_id' => 'integer', //привязать к источнику
            'marketplace_id' => 'integer', //привязать к маркетплейсу
            'thematic_id' => 'integer', //привязать к тематике
            'emotion_id' => 'integer', //привязать к характеру
            'confirmed' => 'bool', //подтверждён
            'need_reaction' => 'bool', //нужно устранение
            'closed' => 'bool', //устранено
        ];
    }
}
