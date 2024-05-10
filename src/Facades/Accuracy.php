<?php

namespace Xbigdaddyx\Accuracy\Facades;

use Illuminate\Support\Facades\Facade;

class Accuracy extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'accuracy';
    }
}
