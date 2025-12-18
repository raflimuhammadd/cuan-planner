<?php

namespace App\Http\Requests;

use App\Enums\PaymentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Enum;

class PaymentRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
            ],

            'type' => [
                'required',
                new Enum(PaymentType::class),
            ],

            'account_number' => [
                'nullable',
                'string',
                'min:3',
                'max:255',
            ],

            'account_owner' => [
                'nullable',
                'string',
                'min:3',
                'max:255',
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Nama',
            'type' => 'Tipe',
            'account_number' => 'Nomor Rekening',
            'account_owner' => 'Nama Rekening',

        ];
    }
}
