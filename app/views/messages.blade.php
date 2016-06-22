@extends('layout')

@section('content')
    <div class="container application">
        <div class="row row-eq-height">
            <div class="col-md-3 sidebar">
                <div class="text-center">
                    <a href="../groups/">Switch to groups</a>  
                </div>
                @include('sidebar')
            </div>
            <div class="col-md-9 main">
                <div id="allMessagesBox">
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
    <script src="{{ URL::to('scripts/messaging-main.js') }}"></script>
@stop