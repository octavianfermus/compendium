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
                <div class="conversation hidden" id="conversation">
                    <div class="boxWrapper heading">
                        <h1 id="groupName"></h1>
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
                <div id="allMessagesBox" class="hidden">
                    <div class="boxWrapper">
                        <h1>Private messages</h1>
                        <p>Search for a user to chat with or select an existent conversation from the right!</p>
                        
                    </div>
                    <div class="boxWrapper">
                        <div class="search-user">
                            <div class="input-group">
                                <input type="text" placeholder="search by name.." class="form-control">
                                <span class="input-group-addon" id="searchUserButton" style="border: none; padding: 0;"><button class="btn">Search</button></span>
                            </div>
                        </div>
                        <div class="searchResults">
                        </div>
                    </div>
                </div>
                
            </div>  
        </div>
    </div>
    <script src="{{ URL::to('scripts/messaging.js') }}"></script>
@stop