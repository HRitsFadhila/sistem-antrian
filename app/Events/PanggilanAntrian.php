<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
// PASTIKAN BARIS DI BAWAH INI MENGGUNAKAN ShouldBroadcastNow
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// UBAH JUGA DI SINI MENJADI ShouldBroadcastNow
class PanggilanAntrian implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $nomorAntrian;
    public $namaPoli;
    public $poli_id;

    public function __construct($nomorAntrian, $namaPoli, $poli_id)
    {
        $this->nomorAntrian = $nomorAntrian;
        $this->namaPoli = $namaPoli;
        $this->poli_id = $poli_id;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('antrian-channel'),
        ];
    }

    public function broadcastAs()
    {
        return 'panggilan.baru';
    }
}
