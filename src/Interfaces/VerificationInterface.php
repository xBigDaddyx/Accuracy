<?php

namespace Xbigdaddyx\Accuracy\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface VerificationInterface
{
    public function checkQuantity(int $polybag_count, int $max);
    public function savePolybag(Model $cartonBox, string $polybag_code, Model $user, ?string $additonal);
    public function saveTag(Model $cartonBox, string $tag_code, Model $user, string $type, ?string $taggable_id = null, ?string $taggable_type = null);
    public function solidPrinciple(Model $cartonBox, string $polybag_code, Model $user, ?string $additonal);
    public function ratioPrinciple(Model $cartonBox, string $polybag_code, int $polybagStatus, string $tag_code, Model $user);
    public function mixPrinciple(Model $cartonBox, string $polybag_code, int $polybagStatus, string $tag_code, Model $user);
}
