<?php

namespace App\Data\Contracts\Repositories;

interface BaseRepository
{
    public function model(): string;
}