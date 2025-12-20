<?php

namespace App\Http\Requests;

use App\Enums\BudgetType;
use App\Enums\MonthEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Enum;

class ExpenseRequest extends FormRequest
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
            'date' => [
                'required',
                'date',
            ],

            'description' => [
                'required',
                'string',
                'min:3',
                'max:255',
            ],

            'nominal' => [
                'required',
                'numeric',
            ],

            'type' => [
                'required',
                new Enum(BudgetType::class),
            ],

            'type_detail_id' => [
                'required',
                'exists:budgets_id',
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
            ],

            'payment_id' => [
                'required',
                'exists:payments_id',
            ],
        ];
    }

    public function attributes():array
    {
        return [
            'date' => 'Tanggal',
            'description' => 'Deskripsi',
            'nominal' => 'Nominal',
            'type' => 'Tipe',
            'type_detail_id' => 'Detail',
            'notes' => 'Catatan',
            'month' => 'Bulan',
            'year' => 'Tahun',
            'payment_id' => 'Pembayaran',
        ];
    }
}
