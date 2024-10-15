<?php

namespace App\Http\Controllers;

use App\Mail\PrintPayment;
use App\Http\Requests\StorePaymentRequest;
use App\Models\Payment;
use App\Models\PrintJob;
use App\Models\User;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    public function create(PrintJob $printJob): View|Factory|Application
    {
        return view('pages.payment.create', compact('printJob'));
    }

    public function store(StorePaymentRequest $request, PrintJob $printJob): RedirectResponse
    {
        DB::beginTransaction();

        try {
            if ($printJob->amount > 0) {
                $payment = $this->createPayment($request, $printJob);
            } else {
                return redirect()->route('payment.create', $printJob)->with('error', 'Payment unsuccessful. Try again.');
            }
            $this->updatePrintJobAmount($printJob, $request['amount']);

            DB::commit();
            $this->sendPaymentConfirmationEmail($printJob->user, $payment);

            return redirect()->route('print.upload')->with('success', 'Payment successful. Confirmation email sent. Refresh your page to see changes.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return redirect()->route('payment.create', $printJob)->with('error', 'Payment unsuccessful. Try again.');
        }
    }

    /**
     * @throws Exception
     */
    private function createPayment(StorePaymentRequest $request, PrintJob $printJob): Payment
    {
        $status = ($request['amount'] < $printJob->amount)
            ? config('constants.status.incomplete')
            : config('constants.status.paid');

        $payment = Payment::create([
            'print_job_id' => $printJob->id,
            'amount' => $request['amount'],
            'status' => $status,
        ]);

        $printJob->status = $status;
        $printJob->save();
        return $payment;
    }

    private function updatePrintJobAmount(PrintJob $printJob, float $paymentAmount): void
    {
        $printJob->amount -= $paymentAmount;
        $printJob->save();
    }

    private function sendPaymentConfirmationEmail(User $user, Payment $payment): void
    {
        Mail::to($user)->send(new PrintPayment($payment));
    }
}
