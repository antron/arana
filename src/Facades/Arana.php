<?php

namespace Antron\Arana\Facades;

use Illuminate\Support\Facades\Facade;

class Arana extends Facade
{

    public static function getFacadeAccessor()
    {
        return 'arana';
    }

}
