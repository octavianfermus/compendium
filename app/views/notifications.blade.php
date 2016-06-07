@extends('layout')

@section('content')
    <div class="boxWrapper heading">
            <h1>Notifications</h1>
            <p>Hello, <span class="person" id="person-me">{{Auth::user()->last_name;}} {{Auth::user()->first_name;}}</span>. Here you can manage your notifications.</p>
    </div>
    <div class="allNotifications">
    </div>
@stop