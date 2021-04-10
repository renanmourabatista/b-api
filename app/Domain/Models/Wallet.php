<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    protected $fillable = ['amount'];

    public function transfersSend(): HasMany
    {
        return $this->hasMany(Transfer::class, 'wallet_sender_id', 'id');
    }

    public function pendingTransfersSend(): HasMany
    {
        return $this->hasMany(Transfer::class, 'wallet_sender_id', 'id')
            ->where('status', '=',
            Transfer::STATUS_PENDING
            );
    }

    public function getTotalAmount(): float
    {
        return $this->amount - $this->pendingTransfersSend->sum('value');
    }

    public function transfersReceived(): HasMany
    {
        return $this->hasMany(Transfer::class, 'wallet_receiver_id', 'id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id', 'id');
    }
}