<?php
namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Person extends Model
{
    protected $fillable = ['cpf', 'name', 'user_id'];

    public function isAnStoreOwner(): bool
    {
        return $this->company !== null;
    }

    public function company(): HasOne
    {
        return $this->HasOne(Company::class, 'person_id', 'id');
    }

    public function wallet(): HasOne
    {
        return $this->HasOne(Wallet::class, 'person_id', 'id');
    }
}