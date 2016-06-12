@extends('layout')

@section('content')
    <div class="container application">
        <div class="row main">
            <div class="col-md-12">
                <div class="boxWrapper heading">
                    <h1>Your Profile <button id="commendPerson" href="javascript:void(0)" class="commend-star"> <span class="glyphicon glyphicon-star"></span></button></h1>
                    <p>Hello, <span class="person" id="person-me">{{Auth::user()->last_name;}} {{Auth::user()->first_name;}}</span>. Here you can manage your profile and see some related statistics. </p>
                </div>
            </div>
            <div class="col-md-12">
                <ul class="nav nav-tabs" style="margin-bottom: 25px"> 
                    <li role="presentation" class="active"><a id="profile-comments-tab" role="tab" data-toggle="tab" aria-controls="search-algorithmsprofile-comments" aria-expanded="false" href="#profile-comments">Profile Comments</a></li>
                    <li role="presentation"><a id="posts-tab" role="tab" data-toggle="tab" aria-controls="posts" aria-expanded="true" href="#posts">Posts</a></li>
                   <li role="presentation"><a id="statistics-tab" role="tab" data-toggle="tab" aria-controls="statistics" aria-expanded="false" href="#statistics">Statistics</a></li>
                    <li role="presentation"><a id="settings-tab" role="tab" data-toggle="tab" aria-controls="settings" aria-expanded="false" href="#settings">Settings</a></li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade active in" id="profile-comments" aria-labelledby="profile-comments-tab"> 
                        @include('profile_comments')
                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="posts" aria-labelledby="posts-tab" style="position: relative">
                        @include('profile_algorithms')
                    </div> 
                    <div role="tabpanel" class="tab-pane fade" id="statistics" aria-labelledby="statistics-tab" style="position: relative">
                        @include('profile_statistics')
                    </div> 
                    <div role="tabpanel" class="tab-pane fade" id="settings" aria-labelledby="settings-tab" style="position: relative">
                        @include('profile_settings')
                    </div> 
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Errors</h4>
                </div>
                <div class="modal-body">
                    @if(count($errors))
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <script src="{{ URL::to('scripts/viewProfile.js') }}"></script>
@stop