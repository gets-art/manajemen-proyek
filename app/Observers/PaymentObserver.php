<?php

namespace App\Observers;

use App\Models\Payment;

class PaymentObserver
{
    /**
     * Handle the Payment "created" event.
     */
    public function created(Payment $payment): void
    {
        $this->updatePaymentable($payment->paymentable);
    }

    /**
     * Handle the Payment "updated" event.
     */
    public function updated(Payment $payment): void
    {
        $this->updatePaymentable($payment->paymentable);
    }

    /**
     * Handle the Payment "deleted" event.
     */
    public function deleted(Payment $payment): void
    {
        $this->updatePaymentable($payment->paymentable);
    }

    /**
     * Handle the Payment "restored" event.
     */
    public function restored(Payment $payment): void
    {
        $this->updatePaymentable($payment->paymentable);
    }

    /**
     * Handle the Payment "force deleted" event.
     */
    public function forceDeleted(Payment $payment): void
    {
        $this->updatePaymentable($payment->paymentable);
    }

    /**
     * Recalculate and update the paid_total of the paymentable model.
     */
    protected function updatePaymentable($paymentable): void
    {
        if ($paymentable && array_key_exists('paid_total', $paymentable->getAttributes())) {
            $totalPaid = $paymentable->payments()->sum('paid');
            $paymentable->paid_total = $totalPaid;
            $paymentable->save();
        }
    }
}
