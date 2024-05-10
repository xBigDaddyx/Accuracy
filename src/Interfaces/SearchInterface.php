<?php

namespace Xbigdaddyx\Accuracy\Interfaces;

interface SearchInterface
{
    public function searchByCode(string $box_code);
    public function searchWithAdditional(string $box_code, string $po, string $carton_number);
    public function getPoList(string $box_code);
    public function getCartonNumberList(string $box_code, string $po);
}
