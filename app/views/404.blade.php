@extends('layout')

@section('content')
    <div class="container application">
        <div class="row">
            <div class="col-md-12 main">
                <div class="boxWrapper">
                    <h1>We are sorry, but we can't return this page to you.</h1><br>
                    @if(count($errors))
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    @endif
                    {{ HTML::link('/', 'Back to main page') }}
                </div>
            </div>  
        </div>
    </div>
@stop