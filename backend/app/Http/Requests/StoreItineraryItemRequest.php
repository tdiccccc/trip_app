<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreItineraryItemRequest extends FormRequest
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
            'spot_id' => ['nullable', 'integer', 'exists:spots,id'],
            'title' => ['required', 'string', 'max:255'],
            'memo' => ['nullable', 'string', 'max:1000'],
            'date' => ['required', 'date_format:Y-m-d'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
            'transport' => ['nullable', 'string', 'in:train,car,walk,bus,none'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
