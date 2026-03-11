<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreExpenseRequest extends FormRequest
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
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'integer', 'min:1'],
            'category' => ['sometimes', 'string', 'in:transport,food,souvenir,accommodation,other'],
            'paid_at' => ['required', 'date_format:Y-m-d'],
            'is_shared' => ['sometimes', 'boolean'],
        ];
    }
}
