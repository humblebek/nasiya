<?php

namespace App\Http\Services;
use App\Models\Balance;
use App\Models\Cost;

class CostService
{
    // Method to retrieve all costs
    public function index()
    {
        // Retrieve all costs
        $allCost = Cost::all();
        return $allCost;
    }

    // Method to store a new cost
    public function store($request)
    {
        // Create the cost
        $storedCost = Cost::create($request->all());

        // Retrieve the current balance
        $balance = Balance::first();

        // Calculate the new balance after deducting the cost amount
        $newBalanceAmount = $balance->summa - $request->input('amount');

        // Update the balance
        $balance->update(['summa' => $newBalanceAmount]);

        return $storedCost;
    }

    // Method to update an existing cost
    public function update(Cost $cost, $request)
    {
        $requestData = $request->all();

        // Get the stored cost amount
        $storedAmount = $cost->amount;

        // Update the cost
        $updatedCost = $cost->update($requestData);

        // Calculate the difference between the stored and updated cost amounts
        $difference = $requestData['amount'] - $storedAmount;

        // Retrieve the balance
        $balance = Balance::first();

        // Adjust the balance based on the difference
        if ($difference < 0) {
            $newBalanceAmount = $balance->summa + abs($difference); // Increase balance
        } elseif ($difference > 0) {
            $newBalanceAmount = $balance->summa - $difference; // Decrease balance
        }

        // Update the balance
        $balance->update(['summa' => $newBalanceAmount]);

        return $updatedCost;
    }

    // Method to delete a cost
    public function destroy(Cost $cost)
    {
        // Retrieve the balance
        $balance = Balance::first();

        // Retrieve the amount of the cost being deleted
        $deletedAmount = $cost->amount;

        // Calculate the new balance after adding back the deleted amount
        $newBalanceAmount = $balance->summa + $deletedAmount;

        // Update the balance
        $balance->update(['summa' => $newBalanceAmount]);

        // Delete the cost
        $cost->delete();
    }
}

