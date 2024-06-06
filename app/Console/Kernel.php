<?php

namespace App\Console;

use App\Models\Cost;
use App\Models\Investor;
use App\Models\InvestorSalary;
use App\Models\Order;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function () {
            $now = Carbon::now();
            if ($now->format('t') == $now->format('d') && $now->format('H:i') == '23:59') {
                $firstDayMonth = $now->copy()->startOfMonth();
                $lastDayMonth = $now->copy()->endOfMonth();

                $payment = Payment::whereBetween('created_at', [$firstDayMonth, $lastDayMonth])->sum('amount') + Order::whereBetween('created_at', [$firstDayMonth, $lastDayMonth])->sum('initial_payment');
                $cost = Cost::whereBetween('created_at', [$firstDayMonth, $lastDayMonth])->sum('amount');

                $benefit = $payment - $cost;

                $investors = Investor::all();

                foreach ($investors as $investor) {
                    $fraction = ($investor->percentage * $benefit) / 100;

                    InvestorSalary::create([
                        'investor_id' => $investor->id,
                        'amount' => $fraction,
                        'month' => date('Y-m'),
                        'status' => 0,
                    ]);
                }
            }
        })->everyFifteenSeconds();
    }

    /**
     * Register the commands for the application.monthlyOn(31, '23:59')
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
