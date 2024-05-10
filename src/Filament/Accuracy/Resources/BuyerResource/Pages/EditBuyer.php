<?php

namespace Xbigdaddyx\Accuracy\Filament\Accuracy\Resources\BuyerResource\Pages;

use Xbigdaddyx\Accuracy\Filament\Accuracy\Resources\BuyerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBuyer extends EditRecord
{
    protected static string $resource = BuyerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
