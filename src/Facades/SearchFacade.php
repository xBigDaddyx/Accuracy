<?php

namespace Xbigdaddyx\Accuracy\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Teresa\CartonBoxGuard\CartonBoxGuard
 */
class SearchFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'SearchRepository';
    }
}
