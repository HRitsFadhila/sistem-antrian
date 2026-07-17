<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['poli_id', 'no_antrian', 'angka_antrian', 'status', 'tanggal'])]
class Antrian extends Model
{
    public function poli(): BelongsTo
    {
        return $this->belongsTo(Poli::class);
    }
}
