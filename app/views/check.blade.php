@extends('layout')

@section('content')
    <p>Logged in: @if(Auth::check()) Yes @else No @endif</p>
    @if(Auth::check())
    <p>__________________________</p>
    <p>User: {{Auth::user()->last_name;}} {{Auth::user()->first_name;}}</p>
    <p>User type: {{Auth::user()->user_type}}</p>
    <p>email: {{Auth::user()->email;}}</p>
    <p>__________________________</p>
    @endif
@stop