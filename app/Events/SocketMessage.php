<?php

namespace App\Events;

use App\Http\Resources\MessageResource;
use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SocketMessage implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Message $message;

    /**
     * Create a new event instance.
     */
    public function __construct(Message $message)
    {
        // store the model instance (ok for ShouldBroadcastNow)
        $this->message = $message;
    }

    /**
     * The channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $m = $this->message;

        if (!empty($m->group_id)) {
            $channelName = "message.group.{$m->group_id}";
        } else {
            $ids = [
                intval($m->sender_id),
                intval($m->receiver_id),
            ];
            sort($ids);
            $channelName = 'message.user.' . implode('-', $ids);
        }

        // TEMP DEBUG: log channel name (remove later)
        Log::info('SocketMessage broadcasting on channel: ' . $channelName);

        return [ new PrivateChannel($channelName) ];
    }

    /**
     * Force a consistent broadcast event name.
     */
    public function broadcastAs(): string
    {
        return 'SocketMessage';
    }

    /**
     * Data that will be broadcast with the event.
     */
    public function broadcastWith(): array
    {
        $payload = [
            'message' => (new MessageResource($this->message))->resolve(),
        ];

        // TEMP DEBUG: log the payload (remove later)
        Log::info('SocketMessage payload prepared', $payload);

        return $payload;
    }
}
