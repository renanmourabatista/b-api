<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Wallet extends Model
{
    protected $fillable = ['amount'];

    public function transfers(): BelongsToMany
    {
        return $this->belongsToMany(Transfer::class, 'transfer_wallet', 'wallet_id', 'transfer_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id', 'id');
    }
}