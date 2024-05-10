@extends('accuracy::layouts.app')

@section('content')
<div class="px-4 py-2 sm:px-6 lg:px-4 lg:py-2 mx-auto ">

    @livewire('verification-carton', ['carton'=>$carton] )

</div>


@endsection
