<?php
namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Person extends Model
{
    use HasFactory;

    protected $fillable = ['cpf', 'name'];

    protected $table = 'persons';

    public function isAShopkeeper(): bool
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

    public function user(): HasOne
    {
        return $this->HasOne(User::class, 'person_id', 'id');
    }
}