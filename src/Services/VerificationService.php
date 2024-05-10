<?php

namespace Xbigdaddyx\Accuracy\Services;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Xbigdaddyx\Accuracy\Interfaces\VerificationInterface;
use Xbigdaddyx\Accuracy\Models\CartonBoxAttribute;
use Xbigdaddyx\Accuracy\Models\Polybag;
use Xbigdaddyx\Accuracy\Models\Tag;

class VerificationService implements VerificationInterface
{
    public function solidPrinciple(Model $cartonBox, string $polybag_code, Model $user, ?string $additional)
    {

        if ($cartonBox->is_completed !== true) {
            if ($cartonBox->attributes()->exists()) {
                if ($polybag_code !== $cartonBox->attributes->first()->tag) {
                    return 'invalid';
                }
            } else {
                return 'there is no inputed attribute for solid principle';
            }
            return 'validated';
        }
    }

    public function ratioPrinciple(Model $cartonBox, string $polybag_code, int $polybagStatus, string $tag_code, Model $user)
    {

        $carton_attributes = $cartonBox->attributes;
        $attributable_tags = Tag::whereHas('attributable', function (Builder $a) use ($cartonBox) {
            $a->where('carton_box_id', $cartonBox->id);
        })->whereNull('taggable_id')->get();
        $attribute_counts = $attributable_tags->count();

        if ($polybagStatus !== 1) {
            if ($carton_attributes->contains('tag', (string)$tag_code)) {
                return $this->saveTag($cartonBox, $tag_code, $user, 'RATIO');
            }
            return 'incorrect';
        }

        if ($attribute_counts > 0 && $attribute_counts === $carton_attributes->sum('quantity')) {

            if ($polybag_code !== null || !empty($polybag)) {
                $polybag = $this->savePolybag($cartonBox, $polybag_code, $user, null);
                if ($polybag || empty($polybag)) {
                    $polybag_id = Polybag::where('carton_box_id', $cartonBox->id)->orderBy('created_at', 'DESC')->first()->id;
                    DB::transaction(function () use ($attribute_counts, $cartonBox, $polybag_id) {
                        for ($i = 0; $i <= $attribute_counts; $i++) {
                            Tag::whereHas('attributable', function (Builder $a) use ($cartonBox) {
                                $a->where('carton_box_id', $cartonBox->id);
                            })->whereNull('taggable_id')->update([
                                'taggable_id' => $polybag_id,
                                'taggable_type' => 'Xbigdaddyx\Accuracy\Models\Polybag',
                            ]);
                        }
                    });
                    return 'updated';
                }
            }
            return 'polybag completed';
        }
    }
    public function mixPrinciple(Model $cartonBox, string $polybag_code, int $polybagStatus, string $tag_code, Model $user)
    {
        $carton_attributes = $cartonBox->attributes;
        if ($carton_attributes->where('tag', $tag_code)->first() !== null || !empty($carton_attributes->where('tag', $tag_code)->first())) {
            $tag = CartonBoxAttribute::with('tags')->find($carton_attributes->where('tag', $tag_code)->first()->id);
            if ($tag->count() > 0) {
                if ($polybagStatus !== 1) {
                    if ($tag->tags->count() !== (int)$tag->quantity) {
                        $this->saveTag($cartonBox, $tag_code, $user, 'MIX', null, null);
                        return 'saved';
                    }
                    return 'polybag completed';
                } else if ($polybagStatus) {
                    $attributable_tags = Tag::whereHas('attributable', function (Builder $a) use ($cartonBox) {
                        $a->where('carton_box_id', $cartonBox->id);
                    })->whereNull('taggable_id')->get();
                    $attribute_counts = $attributable_tags->count();
                    if ($attribute_counts > 0 && $attribute_counts === (int)$tag->quantity) {
                        if ($polybag_code !== null || !empty($polybag_code)) {
                            $polybag_value = $this->savePolybag($cartonBox, $polybag_code, $user, null);


                            if ($polybag_value || empty($polybag_value)) {
                                $polybag_id = Polybag::where('carton_box_id', $cartonBox->id)->orderBy('created_at', 'DESC')->first()->id;
                                DB::transaction(function () use ($attribute_counts, $cartonBox, $polybag_id) {
                                    for ($i = 0; $i <= $attribute_counts; $i++) {
                                        Tag::whereHas('attributable', function (Builder $a) use ($cartonBox) {
                                            $a->where('carton_box_id', $cartonBox->id);
                                        })->whereNull('taggable_id')->update([
                                            'taggable_id' => $polybag_id,
                                            'taggable_type' => 'Xbigdaddyx\Accuracy\Models\Polybag',
                                        ]);
                                    }
                                });
                                return 'updated';
                            }
                        }
                    }
                }
                return 'scan polybag';
            }
        }
        return 'incorrect';
    }
    public function saveTag(Model $cartonBox, string $tag_code, Model $user, string $type, ?string $taggable_id = null, ?string $taggable_type = null)
    {
        $attribute_model = CartonBoxAttribute::find($cartonBox->attributes->where('tag', (string)$tag_code)->first()->id);

        $tag_quantity = $attribute_model->quantity;
        $scanned_tag = Tag::whereHas('attributable', function (Builder $query) use ($attribute_model) {
            $query->where('id', $attribute_model->id);
        })->where('taggable_id', null);
        if ($scanned_tag->count() === (int)$tag_quantity) {
            return 'max';
        } else {
            $tag_value = new Tag();
            $tag_value->type = $type;
            $tag_value->tag = (string)$tag_code;
            $tag_value->created_by = $user->id;
            $tag_value->taggable_id = $taggable_id;
            $tag_value->taggable_type = $taggable_type;
            $attribute_model->tags()->save($tag_value);
            return 'saved';
        }
    }
    public function savePolybag(Model $cartonBox, string $polybag_code, Model $user, ?string $additional)
    {
        if ($this->checkQuantity($cartonBox->polybags->count(), $cartonBox->quantity)) {
            $this->setCompleted($cartonBox, $user);

            Polybag::create(
                [
                    'polybag_code' => $polybag_code,
                    'carton_box_id' => $cartonBox->id,
                    'additional' => $additional,
                    'created_by' => $user->id,
                ]
            );
            return redirect(route('accuracy.completed.carton.release', ['carton' => $cartonBox->id]));
        }

        return Polybag::create(
            [
                'polybag_code' => $polybag_code,
                'carton_box_id' => $cartonBox->id,
                'additional' => $additional,
                'created_by' => $user->id,
            ]
        );
    }
    // Check quantity is at maximum quantity or not
    public function checkQuantity(int $polybag_count, int $max)
    {
        if ($polybag_count === ($max - 1) || $polybag_count === $max) {
            return true;
        }
        return false;
    }

    // Function to set status of carton box to be completed
    public function setCompleted(Model $cartonBox, Model $user)
    {
        $cartonBox->is_completed = true;
        $cartonBox->completed_at = Carbon::now();
        $cartonBox->completed_by = $user->id;
        $cartonBox->save();
    }
}
