<?php

namespace Xbigdaddyx\Accuracy\Livewire;

use App\Livewire\Forms\ValidateForm;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

use Mary\Traits\Toast;
//use Teresa\CartonBoxGuard\Models\CartonBox;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Computed;
use Xbigdaddyx\Accuracy\Facades\VerificationFacade;
use Xbigdaddyx\Accuracy\Forms\ValidationForm;
use Xbigdaddyx\Accuracy\Models\CartonBox;
use Xbigdaddyx\Accuracy\Models\CartonBoxAttribute;
use Xbigdaddyx\Accuracy\Models\Polybag;
use Xbigdaddyx\Accuracy\Models\Tag;

class VerificationCarton extends Component
{
    use Toast;
    use LivewireAlert;
    public $carton;
    public $type;
    public $polybags;
    public $tags;
    public bool $showTable = false;
    public bool $completed = false;
    public bool $polybagCompleted = false;
    public ValidationForm $form;
    public $polybagForm = [
        'polybag_code' => null,
        'additional' => null
    ];
    public $tagForm = [
        'tag_code' => null,
    ];
    public function resetTagForm()
    {
        $this->tagForm['tag_code'] = null;
    }
    public function resetPolybagForm()
    {
        $this->polybagForm['polybag_code'] = null;
    }

    public function mount($carton)
    {
        session()->forget('carton');
        session()->forget('polybag');
        $this->carton = CartonBox::with('polybags', 'attributes')->find($carton);
        $this->polybags = $this->carton->polybags;
        session()->put('carton.id', $this->carton->id);

        if (session()->get('polybag.status') === null || empty(session()->get('polybag.status'))) {
            session()->put('polybag.status', 0);
        }

        if (session()->get('carton.max_quantity') === null || empty(session()->get('carton.max_quantity'))) {
            if (!Session::has('carton.first_polybag')) {

                session()->put('carton.first_polybag', $this->carton->polybags->sortBy('created_at')->first()->polybag_code ?? null);
            }
            if (!Session::has('carton.type')) {
                session()->put('carton.type', $this->carton->type);
            }

            session()->put('carton.validated', $this->polybags->count());
            session()->put('carton.max_quantity', $this->carton->quantity);
        }
        if (session()->get('carton.type') === 'RATIO' || session()->get('carton.type') === 'MIX') {
            $cartonBox = $this->carton;
            $this->tags =  Tag::whereHas('attributable', function (Builder $a) use ($cartonBox) {
                $a->where('carton_box_id', $cartonBox->id);
            })->whereNull('taggable_id')->get();
            if (session()->get('carton.tags') === null || count(session()->get('carton.tags')) > 0 || empty(session()->get('carton.tags'))) {
                session()->put(
                    'carton.tags',
                    $this->tags
                );
            }
            if (session()->get('carton.attributes') === null || count(session()->get('carton.attributes')) > 0 || empty(session()->get('carton.attributes'))) {
                session()->put('carton.attributes', $this->carton->attributes->toArray());
                session()->put('carton.total_attributes', $this->carton->attributes->sum('quantity'));
            }
        }

        if ($this->carton->is_completed === true) {
            $this->completed = true;
            return redirect(route('accuracy.completed.carton', ['carton' => $this->carton->id]));
        }
    }
    public function updated($property,)
    {

        if ($property === 'form.polybag_barcode') {
            if (str_contains($this->form->polybag_barcode, 'LPN')) {


                $explode = explode('&', $this->form->polybag_barcode ?? '');
                $sku = str_replace('item_number=', '', $explode[1]);
                $lpn = str_replace('lpn=', '', $explode[2]);

                $this->form->polybag_barcode = $sku;
                $this->form->additional = $lpn;


                //$this->polybagForm['box_code'] === $explode[]
            }
        }

        //dd($this->boxForm['box_code']);
    }
    public function showToast($type, $title, $description = null)
    {
        return $this->toast(
            type: $type,
            title: $title,
            description: $description,                  // optional (text)
            position: 'toast-top toast-end',    // optional (daisyUI classes)
            timeout: 6000,                      // optional (ms)
            redirectTo: null                    // optional (uri)
        );
    }
    public function toggleShowTable()
    {
        return $this->showTable = !$this->showTable;
    }
    public function render()
    {
        return view('accuracy::livewire.verification-carton');
    }
    #[On('validation')]
    public function changeCompleted($value)
    {

        if ($value === 'saved' || $value === 'validated') {

            $polybag_count = Polybag::where('carton_box_id', $this->carton->id)->count();
            $max_qty = (int)$this->carton->quantity;

            if ($polybag_count === $max_qty) {

                $this->completed = true;
                return redirect(route('accuracy.completed.carton', ['carton' => $this->carton->id]));
            }
        }
        return $this->completed = false;
    }

    public function validation()
    {
        if (session()->get('carton.type') === 'SOLID') {
            $validate = VerificationFacade::verification($this->carton, $this->form->polybag_barcode, 0, null, auth()->user(), $this->form->additional);
        } elseif (session()->get('carton.type') === 'RATIO' || session()->get('carton.type') === 'MIX') {

            if (session()->get('polybag.status') === 1) {
                $validate = VerificationFacade::verification($this->carton, $this->form->polybag_barcode, 1, $this->form->tag_barcode, auth()->user());
                if (session()->get('carton.type') === 'MIX') {
                    if ($validate === 'updated' && $this->carton->polybags->count() !== $this->carton->quantity) {
                        $this->form->reset();
                        session()->put('polybag.status', 0);
                    }
                    return redirect(route('accuracy.validation.polybag.release', ['carton' => $this->carton->id]));
                }
                $this->form->reset();
                return redirect(route('accuracy.validation.polybag.release', ['carton' => $this->carton->id]));
            }

            $validate = VerificationFacade::verification($this->carton, $this->form->polybag_barcode, 0, $this->form->tag_barcode, auth()->user());
        }



        if ($validate === 'validated') {
            VerificationFacade::itemValidated($this->carton, $this->form->polybag_barcode, auth()->user(), $this->form->additional);

            $this->polybags = CartonBox::find($this->carton->id)->polybags;
            $this->showToast('warning', 'Polybag Validated', 'Go for next!');
            $this->form->reset();
        } elseif ($validate === 'invalid') {
            $this->dispatch('swal', [
                'title' => 'Invalid Polybag',
                'text' => 'Please check the polybag may wrong size or color.',
                'icon' => 'error',
                'allowOutsideClick' => false,
                'showConfirmButton' => true,

            ]);

            $this->resetPolybagForm();
        } elseif ($validate === 'polybag completed') {
            session()->put('polybag.status', 1);
            $this->dispatch('swal', [
                'title' => 'All garment validated',
                'text' => 'Next, Please scan the polybag/carton barcode to complete.',
                'icon' => 'success',
                'allowOutsideClick' => false,
                'showConfirmButton' => true,

            ]);
        } else if ($validate === 'incorrect') {
            $this->form->reset();
            $this->dispatch('swal', [
                'title' => 'Incorrect garment tag',
                'text' => 'Please check the garment may wrong size or color.',
                'icon' => 'error',
                'allowOutsideClick' => false,
                'showConfirmButton' => true,

            ]);
        } else if ($validate === 'max') {

            $this->form->reset();
            $this->dispatch('swal', [
                'title' => 'Maximum ratio reached',
                'text' => 'The ratio for this garment is reached maximum, please validate next ratio.',
                'icon' => 'warning',
                'allowOutsideClick' => false,
                'showConfirmButton' => true,

            ]);
        } else if ($validate === 'saved') {
            $cartonBox = $this->carton;
            $this->tags =  Tag::whereHas('attributable', function (Builder $a) use ($cartonBox) {
                $a->where('carton_box_id', $cartonBox->id);
            })->with('attributable')->whereNull('taggable_id')->get();

            if (session()->get('carton.type') === 'MIX') {

                if ($this->tags->count() === (int)$this->tags->first()->attributable->quantity) {
                    session()->put('polybag.status', 1);
                    $this->dispatch('swal', [
                        'title' => 'All garment validated',
                        'text' => 'Next, Please scan the polybag/carton barcode to complete.',
                        'icon' => 'success',
                        'allowOutsideClick' => false,
                        'showConfirmButton' => true,

                    ]);
                }
            }
            if (session()->get('carton.type') === 'RATIO') {

                if ($this->tags->count() === (int)session()->get('carton.total_attributes')) {
                    session()->put('polybag.status', 1);
                    $this->dispatch('swal', [
                        'title' => 'All garment validated',
                        'text' => 'Next, Please scan the polybag/carton barcode to complete.',
                        'icon' => 'success',
                        'allowOutsideClick' => false,
                        'showConfirmButton' => true,

                    ]);
                }
            }

            $this->form->reset();
            $this->showToast('warning', 'Garment Validated', 'Go for next!');
        }
    }
}
