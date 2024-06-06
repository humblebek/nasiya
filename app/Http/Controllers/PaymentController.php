<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Http\Services\PaymentService;
use App\Models\Payment;


class PaymentController extends Controller
{
    // Service to handle payment-related operations
    public $paymentService;

    // Constructor to inject the PaymentService
    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index()
    {
        // Retrieve all payments from the service and return the collection of payments
        $dataIndex = $this->paymentService->index();
        return $dataIndex;
    }

    public function store(StorePaymentRequest $request, $id)
    {
        // Create a new payment using the service
        $dataStore = $this->paymentService->store($request, $id);
        return response()->json(['message' => 'Payment created successfully', 'payment' => $dataStore], 201);
    }

    public function show(Payment $payment)
    {
        // Return the payment
        return $payment;
    }

    public function destroy(Payment $payment)
    {
        // Delete the payment using the service
        $this->paymentService->destroy($payment);
        return response()->json(['message' => 'Payment deleted successfully'], 201);
    }
}

