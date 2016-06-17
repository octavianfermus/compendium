@extends('layout')

@section('content')
    <div class="container application">
        <div class="row main">
            <div class="col-md-12">
                <div class="boxWrapper heading">
                    <h1 style="margin-bottom: 5px"><span id="profileOf"></span>'s Profile <button id="commendPerson" href="javascript:void(0)" class="commend-star"> <span class="glyphicon glyphicon-star"></span><span id="commendationNumber">0</span></button></h1>
                    <a href="../messages/" id="sendPrivateMessage" class="transparent">Send private message</a>
                </div>
            </div>
            <div class="col-md-12">
                <ul class="nav nav-tabs" style="margin-bottom: 25px"> 
                    <li role="presentation" class="active"><a id="profile-comments-tab" role="tab" data-toggle="tab" aria-controls="search-algorithmsprofile-comments" aria-expanded="false" href="#profile-comments">Profile Comments</a></li>
                    <li role="presentation"><a id="posts-tab" role="tab" data-toggle="tab" aria-controls="posts" aria-expanded="true" href="#posts">Posts</a></li>
                   <li role="presentation"><a id="statistics-tab" role="tab" data-toggle="tab" aria-controls="statistics" aria-expanded="false" href="#statistics">Statistics</a></li>
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
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="reportModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Report</h4>
                </div>
            <div class="modal-body">
                <label>Describe your problem. (optional)</label>
                <textarea class="form-control" placeholder="short problem description.."></textarea>
                <label>Why are you reporting this?</label>
                <select class="form-control">
                    <option>Indecent content</option>
                    <option>Offensive content</option>
                    <option>Irrelevant content</option>
                    <option>Other</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="submitReport">Submit Report</button>
            </div>
            </div>
        </div>
    </div>
    <script src="{{ URL::to('scripts/viewProfile.js') }}"></script>
@stop