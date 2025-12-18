<?php

namespace App\Http\Controllers;

use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;

class PaymentController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth'),
        ];
    }

    // create method index
    public function index(): Response
    {
        $payments = Payment::query()
            ->select([
                'id',
                'user_id',
                'name',
                'type',
                'account_number',
                'account_owner',
                'created_at'
            ])

            ->where('user_id', Auth::id())
            ->filter(request()->only(['search']))
            ->sorting(request()->only(['field', 'direction']))
            ->paginate(request()->load ?? 10);

        return inertia('Payments/Index', [
            'pageSettings' => fn() => [
                'title' => 'Metode Pembayaran',
                'subtitle' => 'Menampilkan semua data metode pembayaran yang tersedia pada akun anda.',
            ],
            'payments' => fn() => PaymentResource::collection($payments)->additional([
                'meta' => [
                    'has_pages' => $payments->hasPages(),
                ],
            ]),

            'state' => fn() => [
                'page' => request()->page ?? 1,
                'search' => request()->search ?? '',
                'load' => 10,
            ],

            'items' => fn() => [
                ['label' => 'Cuan+', 'href' => route('dashboard')],
                ['label' => 'Metode Pembayaran'],
            ],

        ]);
    }
}
