<?php

namespace Xbigdaddyx\Accuracy\Filament\Accuracy\Resources\BuyerResource\Pages;

use Xbigdaddyx\Accuracy\Filament\Accuracy\Resources\BuyerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBuyers extends ListRecords
{
    protected static string $resource = BuyerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
