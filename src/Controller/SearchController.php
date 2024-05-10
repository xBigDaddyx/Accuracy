<?php

namespace Xbigdaddyx\Accuracy\Controller;

use Support\Controllers\Controller;

class SearchController extends Controller
{
    public function index()
    {
        return view('accuracy::pages.search');
    }
}
