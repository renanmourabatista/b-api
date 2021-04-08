<?php
namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    use HasFactory;
    
    protected $fillable = ['value', 'date', 'wallet_sender_id', 'wallet_receiver_id'];
}