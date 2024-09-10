<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class ArticleFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'articleService'; // This should match the service provider binding key
    }
}
