<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateItineraryItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'spot_id' => ['sometimes', 'nullable', 'integer', 'exists:spots,id'],
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'memo' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'date' => ['sometimes', 'required', 'date_format:Y-m-d'],
            'start_time' => ['sometimes', 'nullable', 'date_format:H:i'],
            'end_time' => ['sometimes', 'nullable', 'date_format:H:i'],
            'transport' => ['sometimes', 'nullable', 'string', 'in:train,car,walk,bus,taxi,none'],
            'sort_order' => ['sometimes', 'nullable', 'integer', 'min:0'],
        ];
    }
}
