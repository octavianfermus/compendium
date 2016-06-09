@extends('layout')

@section('content')
    <div class="row row-eq-height">
        <div class="col-md-12 main">
            <div class="row">
                <div class="col-md-12">
                    <div class="boxWrapper heading">
                        <h1><span id="profileOf"></span>'s Profile <button id="commendPerson" href="javascript:void(0)" class="commend-star"> <span class="glyphicon glyphicon-star"></span><span id="commendationNumber">0</span></button></h1>
                    </div>
                </div>
                <div class="col-md-12">
                    <ul class="nav nav-tabs" style="margin-bottom: 25px">
                        <li role="presentation" class="active"><a id="posts-tab" role="tab" data-toggle="tab" aria-controls="posts" aria-expanded="true" href="#posts">Posts</a></li>
                        <li role="presentation"><a id="profile-comments-tab" role="tab" data-toggle="tab" aria-controls="search-algorithmsprofile-comments" aria-expanded="false" href="#profile-comments">Profile Comments</a></li>
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane fade active in" id="posts" aria-labelledby="posts-tab" style="position: relative">
                            @include('profile_algorithms')
                        </div> 
                        <div role="tabpanel" class="tab-pane fade" id="profile-comments" aria-labelledby="profile-comments-tab"> 
                            @include('search_algorithm')

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ URL::to('scripts/viewProfile.js') }}"></script>
@stop