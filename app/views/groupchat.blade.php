@extends('layout')

@section('content')
    <div class="container application">
        <div class="row row-eq-height">
            <div class="col-md-3 sidebar">
                <div class="text-center">
                    <a href="../messages/">Switch to messages</a>  
                    <a href="../groups/" class="closeConversation">Back to groups</a>
                </div>
                @include('sidebar')
            </div>
            <div class="col-md-9 main">
                <div class="conversation" id="conversation">
                    <div class="boxWrapper heading">
                        <h1 id="groupName"></h1>
                        <p id="groupType"></p>
                        <p id="groupLeader"></p>
                        <p id="groupDescription" class="hidden"></p>
                        <p class="text-right">
                            <button class="transparent hidden convertToPrivateGroup">Convert to private group</button>
                            <button class="transparent hidden convertToPublicGroup">Convert to public group</button>
                            <button class="transparent hidden cancelConvert">Cancel</button>
                            <button class="transparent hidden confirmConvert">Confirm</button>
                            <button class="transparent" data-toggle="modal" data-target="#membersModal" id="seeMemberList">See member list <span id="memberNumber"></span></button>
                            <button class="transparent hidden" id="seeActiveRequests" data-toggle="modal" data-target="#requestsModal">See active requests</button>
                        </p>
                    </div>
                    <div class="boxWrapper" style="height: 385px; overflow: auto">
                    </div>
                    <div class="send-message">
                        <div class="input-group">
                                <input type="text" class="form-control" placeholder="Write your message here" aria-describedby="sendButton">
                                <span class="input-group-addon" id="sendButton"><button class="btn">Send</button></span>
                        </div>
                    </div>
                </div>
            </div>  
        </div>
    </div>

<div class="modal fade" id="membersModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Group members</h4>
            </div>
            <div class="modal-body">
               <div class="boxWrapper">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div class="modal fade" id="requestsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Group Requests</h4>
            </div>
            <div class="modal-body">
               <div class="boxWrapper">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
    <script src="{{ URL::to('scripts/group-messaging.js') }}"></script>
@stop