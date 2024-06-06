<?php

namespace App\Http\Resources;

use App\Models\Balance;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $balance = Balance::first();
        $newBalanceAmount = $balance->summa + $this->initial_payment;

        $balance->update(['summa' => $newBalanceAmount]);

        return [
            'id'=>$this->id,
            'client ID'=>$this->client_id,
            'admin ID'=>$this->user_id,
            'device ID'=>$this->device_id,
            'payment type(#month)'=>$this->payment_type,
            'payment day'=>$this->payment_day,
            'body price'=>$this->body_price,
            'sold summa'=>$this->summa,
            'initial_payment'=>$this->initial_payment,
            'rest summa'=>$this->rest_summa,
            'benefit'=>$this->benefit,
            'box' => ($this->box == '0') ? 'No box' : (($this->box == '1') ? 'Yes box' : 'Unknown'),
            'status' => ($this->status == '0') ? 'Not finished' : (($this->status == '1') ? 'Finished' : 'Unknown'),
            'order date'=>$this->order_date,

        ];
    }
}
