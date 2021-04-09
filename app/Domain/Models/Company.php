<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Company extends Model
{
    protected $fillable = ['cnpj'];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id', 'id');
    }
}