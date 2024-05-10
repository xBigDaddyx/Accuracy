<?php

namespace Xbigdaddyx\Accuracy\Repositories;

use Illuminate\Database\Eloquent\Model;

use Xbigdaddyx\Accuracy\Models\CartonBox;
use Xbigdaddyx\Accuracy\Services\VerificationService;

class VerificationRepository
{
    protected $verificationService;

    public function __construct(VerificationService $verificationService)
    {
        $this->verificationService = $verificationService;
    }
    public function itemValidated(Model $cartonBox, string $polybag_code, Model $user, ?string $additional)
    {
        return $this->verificationService->savePolybag($cartonBox, $polybag_code, $user, $additional);
    }
    public function verification(Model $cartonBox, string $polybag_code, int $polybagStatus, ?string $tag_code, Model $user, ?string $additional)
    {
        switch ($cartonBox->type) {
            case 'SOLID':
                return $this->verificationService->solidPrinciple($cartonBox, $polybag_code, $user, $additional);
                break;
            case 'RATIO':
                return $this->verificationService->ratioPrinciple($cartonBox, $polybag_code, $polybagStatus, $tag_code, $user);
                // return 'RATIO';
                break;
            case 'MIX':
                return $this->verificationService->mixPrinciple($cartonBox, $polybag_code, $polybagStatus, $tag_code, $user);
                break;
        }
    }
}
