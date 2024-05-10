<?php

namespace Xbigdaddyx\Accuracy\Livewire\components;

use Livewire\Component;
//use Teresa\CartonBoxGuard\Models\CartonBox;
use Livewire\Attributes\Reactive;

class AccuracyPolybagAttributes extends Component
{

    public $carton;
    #[Reactive]
    public $polybags;
    #[Reactive]
    public $type;
    #[Reactive]
    public $tags;

    public function render()
    {
        return view('accuracy::livewire.components.accuracy-polybag-attributes');
    }
}
