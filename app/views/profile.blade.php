@extends('layout')

@section('content')
    <div class="row row-eq-height">
        <div class="col-md-12 main">
            <div class="row">
                <div class="col-md-12">
                    <div class="boxWrapper heading">
                        <h1><span id="profileOf"></span>'s Profile <button id="commentPerson" href="javascript:void(0)" class="commend-star"> <span class="glyphicon glyphicon-star"></span><span id="commendationNumber">0</span></button></h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ URL::to('scripts/viewProfile.js') }}"></script>
@stop