<?php

namespace Xbigdaddyx\Accuracy\Livewire\components;

use Livewire\Component;
use  Xbigdaddyx\Accuracy\Models\CartonBox;
use  Xbigdaddyx\Accuracy\Models\Tag;

class AccuracyPolybagTable extends Component
{

    public $carton;
    public $type;
    public $tags;
    public array $headers;

    public function mount($carton)
    {

        $this->carton = CartonBox::find($carton->id);
        $this->type = $this->carton->type;

        $this->headers = [
            ['key' => 'polybag_code', 'label' => 'Polybag Code'],
            ['key' => 'cartonBox.box_code', 'label' => 'Carton Box Code'],
        ];
    }
    public function render()
    {

        return view('accuracy::livewire.components.accuracy-polybag-table');
    }
}
