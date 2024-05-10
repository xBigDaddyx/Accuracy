<?php

namespace Xbigdaddyx\Accuracy\Services;

use Illuminate\Database\Eloquent\Builder;
use Xbigdaddyx\Accuracy\Interfaces\SearchInterface;
use Xbigdaddyx\Accuracy\Models\CartonBox;
use Xbigdaddyx\Accuracy\Models\PackingList;

class SearchService implements SearchInterface
{
    public function searchByCode(string $box_code)
    {
        $cartons = CartonBox::where('box_code', $box_code)->get();

        if ($cartons->count() !== 0) {
            if ($cartons->count() > 1) {
                return 'multiple';
            }
            return CartonBox::where('box_code', $box_code)->first();
        }
        return 'not found';
    }
    public function searchWithAdditional(string $box_code, string $po, string $carton_number)
    {
        return CartonBox::where('box_code', $box_code)->where('carton_number', $carton_number)->whereHas('packingList', function (Builder $query) use ($po) {
            $query->where('po', $po);
        })->first();
    }
    public function getPoList(string $box_code)
    {
        PackingList::select('po')->whereHas('cartonBoxes', function (Builder $query) use ($box_code) {
            $query->where('box_code', $box_code);
        })->distinct('po')->get();
    }
    public function getCartonNumberList(string $box_code, string $po)
    {
        CartonBox::select('carton_number')->where('box_code', $box_code)->whereHas('packingList', function (Builder $query) use ($po) {
            $query->where('po', $po);
        })->distinct('carton_number')->get();
    }
}
