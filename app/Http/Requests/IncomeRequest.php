<?php

namespace App\Http\Requests;

use App\Enums\MonthEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Enum;

class IncomeRequest extends FormRequest
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
            'source_id' => [
                'required',
                'exists:budgets,id',
            ],

            'date' => [
                'required',
                'date',
            ],

            'nominal' => [
                'required',
                'numeric',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:255',
            ],

            'month' => [
                'required',
                new Enum(MonthEnum::class),
            ],

            'year' => [
                'required',
                'numeric',
            ]
            
        ];
    }

    public function attributes():array
    {
        return [
            'source_id' => 'Sumber',
            'date' => 'Tanggal',
            'nominal' => 'Nominal',
            'notes' => 'Catatan',
            'month' => 'Bulan',
            'year' => 'Tahun'
        ];
    }
}
