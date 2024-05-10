<?php

namespace Xbigdaddyx\Accuracy\Forms;

use Livewire\Attributes\Rule;
use Livewire\Form;

class ValidationForm extends Form
{
    #[Rule('required')]
    public $tag_barcode = '';
    #[Rule('required')]
    public $polybag_barcode = '';
    public $additional;
}
