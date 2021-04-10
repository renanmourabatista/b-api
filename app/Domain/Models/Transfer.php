<?php
namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transfer extends Model
{
    use HasFactory;

    const STATUS_PENDING        = 0;
    const STATUS_AUTHORIZED     = 1;
    const STATUS_NOT_AUTHORIZED = 2;

    protected $fillable = [
        'value',
        'status',
        'notification_date',
        'wallet_sender_id',
        'wallet_receiver_id',
        'transfer_reverted_id'
    ];

    public function senderWallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'wallet_sender_id', 'id');
    }

    public function receiverWallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'wallet_receiver_id', 'id');
    }

    public function revertedFrom(): BelongsTo
    {
        return $this->belongsTo(Transfer::class, 'transfer_reverted_id', 'id');
    }
}