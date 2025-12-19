<?php

namespace App\Http\Controllers;

use App\Enums\MessageType;
use App\Enums\PaymentType;
use App\Http\Requests\PaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Throwable;

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

        // method create
    public function create(): Response
    {
        return inertia('Payments/Create', [
            'pageSettings' => fn() => [
                'title' => 'Tambah Metode Pembayaran',
                'subtitle' => 'Buat metode pembayaran baru disini. Klik simpan setelah selesai.',
                'method' => 'POST',
                'action' => route('payments.store'),
            ],
            'items' => fn() => [
                ['label' => 'Cuan+', 'href' => route('dashboard')],
                ['label' => 'Metode Pembayaran', 'href' => route('payments.index')],
                ['label' => 'Tambah Metode Pembayaran'],
            ],
            'paymentTypes' => fn() => PaymentType::options(),
        ]);
    }


    // method store
    public function store(PaymentRequest $request): RedirectResponse
    {
        try {
            Payment::create([
                'user_id' => Auth::id(),
                'name' => $request->name,
                'type' => $request->type,
                'account_number' => $request->account_number,
                'account_owner' => $request->account_owner,
            ]);

            flashMessage(MessageType::CREATED->message('Metode Pembayaran'));
            return to_route('payments.index');
        } catch (Throwable $e) {
            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');
            return to_route('payments.index');
        }
    }
}
