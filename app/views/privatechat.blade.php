@extends('layout')

@section('content')
    <div class="container application">
        <div class="row row-eq-height">
            <div class="col-md-3 sidebar">
                <div class="text-center">
                    <a href="../groups/">Switch to groups</a>  
                    <a href="../messages/" class="closeConversation hidden">Close Conversation</a>
                </div>
                @include('sidebar')
            </div>
            <div class="col-md-9 main">
                <div class="conversation" id="conversation">
                    <div class="boxWrapper heading">
                        <h3 id="talkingTo" style="margin: 0">Now talking to <span></span></h3>
                    </div>
                    <div class="boxWrapper" id="messages" style="height: 385px; overflow: auto">
                    </div>
                    <div class="send-message" style="margin-bottom: 10px">
                        <div class="input-group">
                                <input type="text" class="form-control" placeholder="Write your message here" aria-describedby="sendButton">
                                <span class="input-group-addon" id="sendButton"><button class="btn">Send</button></span>
                        </div>
                    </div>
                </div>
            </div>  
        </div>
    </div>
    <script src="{{ URL::to('scripts/messaging.js') }}"></script>
@stop