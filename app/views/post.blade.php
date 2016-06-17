@extends('layout')
@section('content')
    <div class="container application">
        <div class="row main">
            <div class="col-md-12">
                <div class="boxWrapper heading">
                    <div class="statisticsBox">
                        <p class="upvote"><a href="javascript:void(0)"><span class="glyphicon glyphicon-arrow-up"></span></a> <span id="upvoteSpan"></span> upvotes</p>
                        <p class="downvote"><a href="javascript:void(0)"><span class="glyphicon glyphicon-arrow-down"></span></a> <span id="downvoteSpan"></span> downvotes </p>
                        <p class="views"><span class="glyphicon glyphicon-eye-open"></span> <span id="viewSpan"></span> views </p>
                        
                    </div>
                    <h1 id="algorithmName"></h1>
                    <p id="language"><span>Language: </span></p>
                    <p class="subtitle">By <a href="../profile/" id="creatorUsername"></a><button id="commendPerson" href="javascript:void(0)" class="commend-star"> <span class="glyphicon glyphicon-star"></span><span id="commendationNumber">0</span></button></p>
                    <p><span>Original Link</span>: <a href id="originalLink">Find the original link here</a></p>
                    <p id="algorithmDescription"><span>Description: </span></p>
                    <p id="thisRequest" class="text-right hidden"><em>This algoritm is made by request!</em></p>
                </div> 
                <textarea id="postedAlgorithmArea" rows="4" style="width: 100%;"></textarea>

                <div class="boxWrapper bg-color">
                    <div class="conversation">
                        <div class="send-message" style="margin-bottom: 10px; margin-top: 0">
                            <div class="input-group">
                                <textarea placeholder="Write your message here" aria-describedby="sendButton" class="form-control"></textarea>
                                <span class="input-group-addon" id="sendButton"><button class="btn">Send</button></span>
                            </div>
                        </div>
                        <div id="algorithmComments">

                        </div>
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
    <script src="{{ URL::to('scripts/ace/ace.js') }}"></script>
    <script src="{{ URL::to('scripts/ace/mode-ruby.js') }}"></script>
    <script src="{{ URL::to('scripts/jquery-ace.min.js') }}"></script>
    <script src="{{ URL::to('scripts/jquery-ui.min.js') }}"></script>
    <script src="{{ URL::to('scripts/postScripts.js') }}"></script>
@stop