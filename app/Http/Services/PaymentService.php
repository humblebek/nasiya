<?php

namespace App\Http\Services;

use App\Models\Balance;
use App\Models\Monthly;
use App\Models\Order;
use App\Models\Payment;

class PaymentService
{
    public function index()
    {
        $allPayment = Payment::all();
        return $allPayment;
    }

    public function store($request, $id)
    {
        // Check if the order exists
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        // Check if the payment month is valid for the order
        if ($request->payment_month > $order->payment_type) {
            return response()->json(['error' => 'Invalid payment month'], 400);
        }

        // Get the monthly record for the specified payment month
        $monthly = Monthly::where('order_id', $id)
            ->where('payment_month', $request->payment_month)
            ->where('status', 0) // Ensure the status is not already paid
            ->first();

        // If the monthly record does not exist or is already paid, return an error
        if (!$monthly) {
            return response()->json(['error' => 'Monthly installment not found or already paid'], 400);
        }
        // Add payment amount to Balance
        $balance = Balance::first();
        $newBalanceAmount = $balance->summa + $request->input('amount');
        $balance->update(['summa' => $newBalanceAmount]);

        // Create the payment record
        $storedPayment = Payment::create([
            'monthly_id' => $monthly->id,
            'amount' => $request->amount,
            'created_at' => now(),
        ]);

        // Calculate the remaining amount after deducting the payment amount from the monthly installment
        $remainingAmount = $request->amount - $monthly->rest_summa;

        // Update the monthly installment with the new remaining amount
        if (($monthly->rest_summa - $request->amount) <= 0) {
            $rest_summa = 0;
        } else {
            $rest_summa = $monthly->rest_summa - $request->amount;
        }
        $monthly->update([
            'rest_summa' =>  $rest_summa,
            'status' => ($rest_summa == '0') ? '1' : '0' // Update status based on whether rest_summa is zero
        ]);

        // If there is remaining amount after deducting from the current month,
        // distribute the remaining amount to the next month
        if ($remainingAmount > 0) {
            $currentMonth = $monthly;

            while ($remainingAmount > 0 && $currentMonth) {
                // Calculate the amount to deduct from the current month
                $amountToDeduct = min($remainingAmount, $currentMonth->rest_summa);

                // Update the current month's rest_summa
                $currentMonth->update([
                    'rest_summa' => max(0, $currentMonth->rest_summa - $amountToDeduct),
                    'status' => ($currentMonth->rest_summa - $amountToDeduct == 0) ? '1' : '0'
                ]);

                // Deduct the amount from remainingAmount
                $remainingAmount -= $amountToDeduct;

                // Move to the next month
                $currentMonth = Monthly::where('order_id', $id)
                    ->where('payment_month', '>', $currentMonth->payment_month)
                    ->where('status', 0)
                    ->orderBy('payment_month')
                    ->first();
            }
        }
        // Subtract payment amount from Order rest_summa and Update
        $order->update([
            'rest_summa' => $order->rest_summa - $request->amount
        ]);

        if ($order->rest_summa == 0) {
            $order->update(['status' => 1]);
        }

        return $storedPayment;
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();
    }
}
