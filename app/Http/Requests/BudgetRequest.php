<?php

namespace App\Http\Requests;

use App\Enums\BudgetType;
use App\Enums\MonthEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Enum;

class BudgetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'detail' => [
                'required',
                'string',
                'min:3',
                'max:255',
            ],
            'nominal' => [
                'required',
                'numeric',
            ],
            'month' => [
                'required',
                new Enum(MonthEnum::class),
            ],
            'year' => [
                'required',
                'numeric',
            ],
            'type' => [
                'required',
                new Enum(BudgetType::class),
            ],
        ];
    }

    // method attribute
    public function attributes():array
    {
        return [
            'detail' => 'Rincian',
            'nominal' => 'Nominal',
            'month' => 'Bulan',
            'year' => 'Tahun',
            'type' => 'Tipe',
        ];
    }
}
