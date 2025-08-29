<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class LowStockNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $warehouse, $product, $balance, $threshold;

    public function __construct($warehouse, $product, $balance, $threshold)
    {
        $this->warehouse = $warehouse;
        $this->product = $product;
        $this->balance = $balance;
        $this->threshold = $threshold;
    }

    public function via($notifiable)
    {
        return ['mail', 'database']; // or SMS, Slack, etc
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("Low Stock Alert: {$this->product->name}")
            ->line("Warehouse: {$this->warehouse->name}")
            ->line("Product: {$this->product->name}")
            ->line("Balance: {$this->balance}")
            ->line("Threshold: {$this->threshold}")
            ->line("Time: " . now());
    }

    public function toArray($notifiable)
    {
        return [
            'warehouse' => $this->warehouse->name,
            'product'   => $this->product->name,
            'balance'   => $this->balance,
            'threshold' => $this->threshold,
        ];
    }
}
