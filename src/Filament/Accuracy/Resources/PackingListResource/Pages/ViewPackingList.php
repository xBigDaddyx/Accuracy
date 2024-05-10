<?php

namespace Xbigdaddyx\Accuracy\Filament\Accuracy\Resources\PackingListResource\Pages;

use  Xbigdaddyx\Accuracy\Models\PackingList;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPackingList extends ViewRecord
{
    protected static string $resource = PackingList::class;
    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }
}
