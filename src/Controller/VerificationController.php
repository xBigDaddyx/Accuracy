<?php

namespace Xbigdaddyx\Accuracy\Controller;

use  Xbigdaddyx\Accuracy\Models\CartonBox;
use  Xbigdaddyx\Accuracy\Models\Polybag;
use Domain\Users\Models\User;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Support\Controllers\Controller;


class VerificationController extends Controller
{
    public function index(Request $request, $carton)
    {


        return view('accuracy::pages.verification', ['carton' => $carton]);
    }
    public function completed($carton)
    {

        $carton_detail = CartonBox::with('completedBy')->find($carton);
        $user = User::find($carton_detail->completed_by);
        return view('accuracy::pages.completed', ['carton' => $carton_detail, 'user' => $user]);
    }
}
