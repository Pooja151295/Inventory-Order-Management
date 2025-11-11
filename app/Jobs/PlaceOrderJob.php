<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\OrderPlacedNotification;
use App\Services\OrderService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class PlaceOrderJob implements ShouldQueue
{
    use Queueable;

    public User $user;

    public array $items;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, array $items)
    {
        $this->user = $user;
        $this->items = $items;
    }

    /**
     * Execute the job.
     */
    public function handle(OrderService $orderService): void
    {
        $order = $orderService->placeOrder($this->user, $this->items);
        $this->user->notify(new OrderPlacedNotification($order));

    }
}
