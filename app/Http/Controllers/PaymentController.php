<?php

namespace App\Http\Controllers;

use App\Mail\PrintPayment;
use App\Http\Requests\StorePaymentRequest;
use App\Models\PrintJob;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    public function store(StorePaymentRequest $request, PrintJob $printJob): RedirectResponse
    {
        $totalAmount = $printJob->payment->amount + $request['amount'];
        $printJob->payment()->update([
            'amount' => $totalAmount,
            'status' => $totalAmount < $printJob->amount ? config('constants.status.unpaid') : config('constants.status.paid'),
        ]);
        Mail::to($printJob->user)->send(new PrintPayment($printJob->payment));
        return redirect()->route('print.upload')->with('success', 'Payment successful. Confirmation email sent.');
    }
}
