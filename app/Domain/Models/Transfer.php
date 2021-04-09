<?php
namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transfer extends Model
{
    use HasFactory;
    
    protected $fillable = ['value'];

    public function senderWallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'wallet_sender_id', 'id');
    }

    public function receiverWallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'wallet_receiver_id', 'id');
    }
}