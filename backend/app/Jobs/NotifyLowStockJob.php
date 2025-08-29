<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\LowStockNotification;

class NotifyLowStockJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $warehouse;
    protected $product;
    protected $balance;
    protected $threshold;

    public function __construct($warehouse, $product, $balance, $threshold)
    {
        $this->warehouse = $warehouse;
        $this->product = $product;
        $this->balance = $balance;
        $this->threshold = $threshold;
    }

    public function handle()
    {


        $users = User::role(['Stock Manager', 'Super Admin'])->get();

        foreach ($users as $user) {
            // send notification
            $user->notify(new LowStockNotification( $this->warehouse ,
            $this->product ,
            $this->balance,
            $this->threshold));
        }

        Notification::send($users, new LowStockNotification(
            $this->warehouse, $this->product, $this->balance, $this->threshold
        ));
    }
}
