<?php

namespace Xbigdaddyx\Accuracy\Controller;

use Illuminate\Database\Eloquent\Builder;
use Support\Controllers\Controller;
use Xbigdaddyx\Accuracy\Models\CartonBox;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class CartonNumberController extends Controller
{
    public function __invoke(Request $request): Collection
    {
        return CartonBox::query()
            ->select('carton_number', 'packing_lists.description')
            ->join('packing_lists', 'carton_boxes.packing_list_id', '=', 'packing_lists.id')

            ->when(
                $request->po,
                fn (Builder $query) => $query
                    ->whereHas('packingList', function (Builder $query) use ($request) {
                        $query->where('po', 'LIKE', "%{$request->po}%");
                    })


            )
            ->when(
                $request->box_code,
                fn (Builder $query) => $query
                    ->where('box_code', 'like', "%{$request->box_code}%")

            )
            ->when(
                $request->search,
                fn (Builder $query) => $query
                    ->where('carton_number', 'like', "%{$request->search}%")

            )
            ->when(
                $request->exists('selected'),
                fn (Builder $query) => $query->whereIn('id', $request->input('selected', [])),
                fn (Builder $query) => $query->limit(10)
            )
            ->get();
    }
}
