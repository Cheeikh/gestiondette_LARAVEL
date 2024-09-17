<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class ArchivedDette extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'archives';

    public $timestamps = true;

    protected $fillable = [ 'date_archived', 'client' ];
}
