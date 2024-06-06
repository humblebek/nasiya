<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Http\Services\OrderService;
use App\Models\Order;

class OrderController extends Controller
{
    // Service to handle order-related operations
    public $orderService;

    // Constructor to inject the OrderService
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    // Fetch and return a collection of orders
    public function index()
    {
        // Retrieve all orders from the service and return the collection of orders as a resource
        $dataIndex = $this->orderService->index();
        return OrderResource::collection($dataIndex);
    }

    public function store(StoreOrderRequest $request)
    {
        // Create a new order using the service
        $dataStore = $this->orderService->store($request);
        return response()->json(['message' => 'Order created successfully', 'order' => $dataStore], 201);
    }

    public function show(Order $order)
    {
        // Return the order as a resource
        return new OrderResource($order);
    }

    public function update(UpdateOrderRequest $request, Order $order)
    {
        // Update the order using the service
        $dataUpdate = $this->orderService->update($order, $request);
        return response()->json(['message' => 'Order updated successfully', 'order' => new OrderResource($dataUpdate)], 201);
    }

    public function destroy(Order $order)
    {
        // Delete the order using the service
        $this->orderService->destroy($order);
        return response()->json(['message' => 'Order deleted successfully'], 201);
    }

    // Show relevant orders (missed and upcoming)
    public function showRelevantOrders()
    {
        // Retrieve relevant orders from the service
        $dataRelevant = $this->orderService->showRelevantOrders();
        // Return the missed and upcoming orders in a JSON response
        return response()->json([
            'missed_orders' => $dataRelevant['missed_orders'],
            'upcoming_orders' => $dataRelevant['upcoming_orders'],
        ], 200);
    }
}

