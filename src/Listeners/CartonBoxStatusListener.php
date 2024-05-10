<?php

namespace Xbigdaddyx\Accuracy\Listeners;

use Illuminate\Database\Eloquent\Model;
use Xbigdaddyx\Accuracy\Events\CartonBoxStatusUpdated;


class CartonBoxStatusListener
{
    public function handle(CartonBoxStatusUpdated $event)
    {
    }
}
