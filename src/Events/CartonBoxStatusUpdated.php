<?php

namespace Xbigdaddyx\Accuracy\Events;

use Xbigdaddyx\Accuracy\Models\CartonBox;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CartonBoxStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        private readonly CartonBox $cartonBox,
        public ?string         $status = null,
    ) {
        $this->status = $status ?? $cartonBox->status;
    }

    public function broadcastOn(): Channel
    {
        return new PrivateChannel('accuracy.' . $this->cartonBox->id);
        // return new PrivateChannel('orders.'.$this->order->id);
    }
}
