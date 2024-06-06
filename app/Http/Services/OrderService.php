<?php

namespace App\Http\Services;

use App\Models\Balance;
use App\Models\Monthly;
use App\Models\Order;
use Carbon\Carbon;

class OrderService
{
    public function index()
    {
        $allOrder = Order::all();
        return $allOrder;
    }

    public function store($request)
    {
        $validatedData = $request->validated();

        // Check if the device has already been ordered
        $existingOrder = Order::where('device_id', $request->device_id)->first();
        if ($existingOrder) {
            return response()->json(['error' => 'Device has already been ordered'], 400);
        }

        $storedOrder = Order::create([
            'id' => $request->id,
            'client_id' => $validatedData['client_id'],
            'user_id' => $validatedData['user_id'],
            'device_id' => $validatedData['device_id'],
            'payment_type' => $validatedData['payment_type'],
            'payment_day' => $validatedData['payment_day'],
            'body_price' => $validatedData['body_price'],
            'summa' => $validatedData['summa'],
            'initial_payment' => $validatedData['initial_payment'],
            'rest_summa' => $validatedData['summa'] - $validatedData['initial_payment'],
            'benefit' => $validatedData['summa'] - $validatedData['body_price'],
            'box' => $validatedData['box'],
            'status' => 0,
            'order_date' => $validatedData['order_date'],
        ]);

        if ($storedOrder->rest_summa == 0) {
            $storedOrder->update(['status' => 1]);
        }



        // Calculate the starting year and month based on the order_date of the stored order
        $startYear = date('Y', strtotime($storedOrder->order_date));
        $startMonth = date('m', strtotime($storedOrder->order_date));

        // Calculate the starting year and month for monthly payments (one month after the order date)
        $startingDate = Carbon::createFromFormat('Y-m-d', $storedOrder->order_date)->addMonth();
        $startYear = $startingDate->year;
        $startMonth = $startingDate->month;

        // Create monthly payments
        for ($i = 0; $i < $storedOrder->payment_type; $i++) {
            $year = $startYear + floor(($startMonth + $i - 1) / 12);
            $month = ($startMonth + $i) % 12;
            if ($month == 0) {
                $month = 12;
            }

            $yearMonth = sprintf('%04d-%02d', $year, $month);
            $monthlySumma = ($storedOrder->summa - $storedOrder->initial_payment) / $storedOrder->payment_type;
            $monthlyRestSumma = ($storedOrder->summa - $storedOrder->initial_payment) / $storedOrder->payment_type;
            // Create Monthly record
            $monthlyRecord = Monthly::create([
                'order_id' => $storedOrder->id,
                'payment_month' => $i + 1,
                'month' => $yearMonth,
                'summa' => $monthlySumma,
                'rest_summa' =>  $monthlyRestSumma,
                'comment' => '.',
                'status' => ($monthlyRestSumma == 0) ? 1 : 0,
                'created_at' => $storedOrder->order_date,
            ]);
        }

        return $storedOrder;
    }

    public function update(Order $order, $request)
    {
        $requestData = $request->all();
        // Get the stored initial_payment amount
        $storedInitial = $order->initial_payment;
        // Update the initial_payment
        $updatedInitial = $order->update($requestData);
        // Calculate the difference between the stored and updated initial_payment amounts
        $difference = $requestData['initial_payment'] - $storedInitial;
        // Retrieve the balance
        $balance = Balance::first();

        if ($difference < 0) {
            $newBalanceAmount = $balance->summa - abs($difference);
        } elseif ($difference > 0) {
            $newBalanceAmount = $balance->summa + $difference;
        }
        // Update the balance
        $balance->update(['summa' => $newBalanceAmount]);

        return $updatedInitial;
    }

    public function destroy(Order $order)
    {
        $order->delete();
    }

    public function showRelevantOrders()
    {
        $today = Carbon::today();

        // Fetch missed orders
        $missedOrders = Order::with(['client:id,name,surname,phone', 'device:id'])
            ->whereBetween('payment_day', [
                $today->copy()->subDays(3)->format('d'),
                $today->copy()->subDays(1)->format('d')
            ])->get(['id', 'client_id', 'device_id', 'payment_day', 'summa']);

        // Fetch upcoming orders
        $upcomingOrders = Order::with(['client:id,name,surname,phone', 'device:id'])
            ->whereBetween('payment_day', [
                $today->format('d'),
                $today->copy()->addDays(3)->format('d')
            ])->get(['id', 'client_id', 'device_id', 'payment_day', 'summa']);

        // Transform the missed orders
        $missedOrders = $missedOrders->map(function ($order) use ($today) {
            $paymentDay = Carbon::createFromFormat('d', $order->payment_day);
            $daysMissed = $today->diffInDays($paymentDay, false); // negative value indicates missed days

            return [
                'order_id' => $order->id,
                'client_name' => $order->client->name,
                'client_surname' => $order->client->surname,
                'client_phone' => $order->client->phone,
                'device_id' => $order->device_id,
                'summa' => $order->summa,
                'payment_day' => $order->payment_day,
                'days_missed' => $daysMissed,
            ];
        });

        // Transform the upcoming orders
        $upcomingOrders = $upcomingOrders->map(function ($order) use ($today) {
            $paymentDay = Carbon::createFromFormat('d', $order->payment_day);
            $daysUntil = $today->diffInDays($paymentDay, false); // positive value indicates upcoming days

            return [
                'order_id' => $order->id,
                'client_name' => $order->client->name,
                'client_surname' => $order->client->surname,
                'client_phone' => $order->client->phone,
                'device_id' => $order->device_id,
                'summa' => $order->summa,
                'payment_day' => $order->payment_day,
                'days_until' => $daysUntil,
            ];
        });

        return [
            'missed_orders' => $missedOrders,
            'upcoming_orders' => $upcomingOrders,
        ];
    }
}
