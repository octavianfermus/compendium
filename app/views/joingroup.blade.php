@extends('layout')

@section('content')
    <div class="container application">
        <div class="row row-eq-height">
            <div class="col-md-3 sidebar">
                <div class="text-center">
                    <a href="../messages/">Switch to messages</a>  
                </div>
                @include('sidebar')
            </div>
            <div class="col-md-9 main">
                <div id="groupManagementBox">
                    <div class="boxWrapper heading">
                        <h1 id="groupName"></h1>
                        <p id="groupType"></p>
                        <p id="groupLeader"></p>
                        <p id="groupDescription" class="hidden"></p>
                        <p class="text-right">
                            <button class="transparent" data-toggle="modal" data-target="#membersModal" id="seeMemberList">See member list <span id="memberNumber"></span></button>
                        </p>
                    </div>
                    <p>You are currently not a member of this group.</p>
                    <span id="buttons"></span>
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

    <script src="{{ URL::to('scripts/joinGroupScript.js') }}"></script>
@stop