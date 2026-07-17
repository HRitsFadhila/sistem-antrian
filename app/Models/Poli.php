<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama', 'prefix', 'status'])]

class Poli extends Model
{
    public function antrians(): HasMany
    {
        return $this->hasMany(Antrian::class);
    }
}
