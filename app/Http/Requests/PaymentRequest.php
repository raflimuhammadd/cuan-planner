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
        $accountNumberRules = ['nullable', 'string'];

        // Add validation based on payment type
        switch ($this->type) {
            case PaymentType::DEBIT->value:
                // Nomor rekening bank: 10-16 digit
                $accountNumberRules[] = 'required';
                $accountNumberRules[] = 'regex:/^[0-9]+$/';
                $accountNumberRules[] = 'min:10';
                $accountNumberRules[] = 'max:16';
                break;

            case PaymentType::CREDIT->value:
                // Nomor kartu kredit: 16 digit
                $accountNumberRules[] = 'required';
                $accountNumberRules[] = 'regex:/^[0-9]+$/';
                $accountNumberRules[] = 'size:16';
                break;

            case PaymentType::EWALLET->value:
                // Nomor e-wallet (nomor HP): 10-13 digit
                $accountNumberRules[] = 'required';
                $accountNumberRules[] = 'regex:/^[0-9]+$/';
                $accountNumberRules[] = 'min:8';
                $accountNumberRules[] = 'max:11';
                break;

            case PaymentType::CASH->value:
            default:
                // Kas tidak perlu nomor rekening
                break;
        }

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

            'account_number' => $accountNumberRules,

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

    public function messages(): array
    {
        return [
            'account_number.required' => 'Nomor Rekening wajib diisi untuk tipe pembayaran ini.',
            'account_number.regex' => 'Nomor Rekening harus berupa angka saja.',
            'account_number.min' => 'Nomor Rekening minimal :min digit.',
            'account_number.max' => 'Nomor Rekening maksimal :max digit.',
            'account_number.size' => 'Nomor Kartu Kredit harus :size digit.',
        ];
    }
}
