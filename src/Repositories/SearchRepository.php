<?php

namespace Xbigdaddyx\Accuracy\Repositories;

use Xbigdaddyx\Accuracy\Services\SearchService;

class SearchRepository
{
    protected $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }
    public function searchCarton(string $box_code, ?string $po = null, ?string $carton_number = null)
    {
        if ($po !== null || $carton_number !== null) {

            return $this->searchService->searchWithAdditional($box_code, $po, $carton_number);
        }
        return $this->searchService->searchByCode($box_code);
    }
    public function get(string $box_code, string $attribute, ?string $po = null)
    {
        switch ($attribute) {
            case 'PO':
                return $this->searchService->getPoList($box_code);
                break;
            case 'CartonNumber':
                return $this->searchService->getCartonNumberList($box_code, $po);
                break;
        }
    }
}
