<?php

namespace Xbigdaddyx\Accuracy\Livewire\components;

use  Xbigdaddyx\Accuracy\Models\CartonBox;
use  Xbigdaddyx\Accuracy\Models\Tag;
use Livewire\Component;
//use Teresa\CartonBoxGuard\Models\CartonBox;
use Livewire\Attributes\Reactive;

class AccuracyPolybagStats extends Component
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
        if (session()->get('carton.type') === 'RATIO' || session()->get('carton.type') === 'MIX') {
            return view('accuracy::livewire.components.accuracy-polybag-stats', ['count' => $this->polybags->count(), 'tags_count' => $this->tags->count()]);
        }
        return view('accuracy::livewire.components.accuracy-polybag-stats', ['count' => $this->polybags->count()]);
    }
}
