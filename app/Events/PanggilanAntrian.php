<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PanggilanAntrian
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $nomorAntrian;
    public $namaPoli;
    public $poli_id;

    public function __construct($nomorAntrian, $poli_id, $namaPoli)
    {
        $this->nomorAntrian = $nomorAntrian;
        $this->poli_id = $poli_id;
        $this->namaPoli = $namaPoli;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('antrian-channel'),
        ];
    }

    public function broadcastAs()
    {
        return 'panggilan.baru';
    }
}
