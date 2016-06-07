@extends('layout')

@section('content')
        
    <h1>Group conversations</h1>
    <p>Hello, <span class="person" id="person-me">Octavian</span>. Pick a conversation to see the containing messages and reply.</p>
    <select class="form-control">
        <option value="1">Javascript Talk</option>
        <option value="1">The PHP Elite</option>
    </select>
    <div class="row">
        <div class="col-md-12">
            <ul class="group-options">
                <li><a href="javascript:void(0)">Leave group</a></li>
                <li><a href="javascript:void(0)">Invite another person</a></li>
                <li><a href="javascript:void(0)">Create a new group</a></li>
            </ul>
            <div class="conversation">
                <div class="reply">
                    <p><span class="person">Joshua</span>: So I might have an issue regarding the algorithm you showed me</p>
                    <div class="reply">
                        <p><span class="person">John</span>: What seems to be the issue?</p>
                    </div>
                </div>
                <div class="reply">
                    <p><span class="person">Kristian</span>: I am out of here, see me in the MySQL group.</p>
                    <div class="reply">
                        <p><span class="person">Richard</span>: I will join you.</p>
                        <div class="reply">
                            <p><span class="person">Isac</span>: But I thought we will discuss the last algorithm posted.</p>
                            <div class="reply">
                                <p><span class="person">Richard</span>: It really looks alright to me</p>
                                <p><span class="person">Kristian</span>: its aight</p>
                                <p><span class="person">Isac</span>: Alright, see you after work</p>
                            </div>
                        </div>
                    </div>
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
@stop