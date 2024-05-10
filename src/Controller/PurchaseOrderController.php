<?php

namespace Xbigdaddyx\Accuracy\Controller;

use Illuminate\Database\Eloquent\Builder;
use Support\Controllers\Controller;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Xbigdaddyx\Accuracy\Models\PackingList;

class PurchaseOrderController extends Controller
{
    public function __invoke(Request $request): Collection
    {

        return PackingList::query()
            ->select('po', 'buyers.name as description')
            ->join('buyers', 'packing_lists.buyer_id', '=', 'buyers.id')
            ->when(
                $request->box_code,
                fn (Builder $query) => $query
                    ->whereHas('cartonBoxes', function (Builder $query) use ($request) {
                        $query->where('box_code', 'like', "%{$request->box_code}%");
                    })
            )
            ->when(
                $request->search,
                fn (Builder $query) => $query
                    ->where('po', 'like', "%{$request->search}%")

            )
            ->when(
                $request->exists('selected'),
                fn (Builder $query) => $query->whereIn('id', $request->input('selected', [])),
                fn (Builder $query) => $query->limit(10)
            )
            ->get();
    }
}
