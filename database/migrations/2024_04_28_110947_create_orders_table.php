<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('device_id')->constrained();
            $table->integer('payment_type');
            $table->integer('payment_day');
            $table->integer('body_price');
            $table->integer('summa');
            $table->integer('initial_payment');
            $table->integer('rest_summa');
            $table->integer('benefit');
            $table->integer('box');
            $table->integer('status')->default(0);
            $table->date('order_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
