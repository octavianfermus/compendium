@extends('layout')
@section('content')

    <div class="container application">
        <div class="row">
            <div class="col-md-12 main posted">
                <div class="boxWrapper">
                    <div class="statisticsBox">
                        <p class="upvote"><a href="javascript:void(0)"><span class="glyphicon glyphicon-arrow-up"></span></a> <span id="upvoteSpan"></span> upvotes</p>
                        <p class="downvote"><a href="javascript:void(0)"><span class="glyphicon glyphicon-arrow-down"></span></a> <span id="downvoteSpan"></span> downvotes </p>
                        <p class="views"><span class="glyphicon glyphicon-eye-open"></span> <span id="viewSpan"></span> views </p>
                        <p class="report"><a href="javascript:void(0)" data-toggle="modal" data-target="#reportModal"><span class="glyphicon glyphicon-warning-sign"></span> Report</a></p>
                    </div>
                    <h1 id="algorithmName"></h1>
                    <p id="language"><span>Language: </span></p>
                    <p class="subtitle">By <a href="../users/" id="creatorUsername"></a></p>
                    <p><span>Original Link</span>: <a href id="originalLink">Find the original link here</a></p>
                    <p id="algorithmDescription"><span>Description: </span></p>
                    <p id="thisRequest" class="text-right hidden"><em>This algoritm is made by request!</em></p>
                </div> 
                <textarea id="postedAlgorithmArea" rows="4" style="width: 100%;"></textarea>

                <div class="boxWrapper">
                    <div class="conversation">
                        <div id="algorithmComments">
                            
                        </div>
                        <div class="send-message">
                            <div class="input-group">
                                <textarea placeholder="Write your message here" aria-describedby="sendButton" class="form-control"></textarea>
                                <span class="input-group-addon" id="sendButton"><button class="btn">Send</button></span>
                            </div>
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
            <h4 class="modal-title" id="myModalLabel">Report this page</h4>
          </div>
          <div class="modal-body">
            <label>Describe your problem with this page</label>
            <textarea class="form-control" placeholder="short description"></textarea>
            <label>Why are you reporting this page?</label>
            <select class="form-control">
                <option>Wrong Section</option>
                <option>Indecency</option>
                <option>Copied content without source</option>
                <option>Duplicate</option>
                <option>Other</option>
            </select>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary">Submit Request</button>
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