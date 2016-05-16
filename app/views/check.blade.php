@extends('layout')

@section('content')
    <div class="container application">
        <div class="row">
            <div class="col-md-12">
                <h2>Logged in: @if(Auth::check()) Yes @else No @endif</h2>
                @if(Auth::check())
                <p>__________________________</p>
                <p>User: {{Auth::user()->last_name;}} {{Auth::user()->first_name;}}</p>
                <p>User type: {{Auth::user()->user_type}}</p>
                <p>email: {{Auth::user()->email;}}</p>
                <p>__________________________</p>
                @endif
            </div>
        </div>
    </div>
@stop