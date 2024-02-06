@extends('layouts.app')
@section('content')
    @yield('data')

    @include('pool-forum::'.config('pool-forum.views.folder').'shared.scripts.avatar')
    @include('pool-forum::'.config('pool-forum.views.folder').'shared.scripts.input-boolean')
@endsection
