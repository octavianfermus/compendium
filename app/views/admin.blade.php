@extends('layout')

@section('content')
    <div class="container application">
        <div class="row">
            <div class="col-md-12">
                <div class="boxWrapper heading">
                    <h1>Administrator Management Page</h1>
                    <p>Hello, <span class="person" id="person-me">{{Auth::user()->last_name;}} {{Auth::user()->first_name;}}</span>. Here you can manage any user reports.</p>
                </div>
            </div> 
            <div class="col-md-12 main">
                <ul class="nav nav-tabs" style="margin-bottom: 25px"> 
                    <li role="presentation" class="active"><a id="manage-reports-tab" role="tab" data-toggle="tab" aria-controls="manage-reports" aria-expanded="false" href="#manage-reports">Manage unanswered reports</a></li>
                    <li role="presentation"><a id="answered-reports-tab" role="tab" data-toggle="tab" aria-controls="answered-reports" aria-expanded="true" href="#answered-reports">See answered reports</a></li>
                   <li role="presentation"><a id="user-list-tab" role="tab" data-toggle="tab" aria-controls="user-list" aria-expanded="false" href="#user-list">See user list</a></li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade active in" id="manage-reports" aria-labelledby="manage-reports-tab"> 
                    <span>Report Filters: </span><br>
                    <button class='transparent checked noHover' button-filter='All'>All</button>
                    <button class='transparent checked noHover' button-filter='Algorithm'>Algorithm</button>
                    <button class='transparent checked noHover' button-filter='Request'>Request</button>
                    <button class='transparent checked noHover' button-filter='Line Comment'>Line comment</button>
                    <button class='transparent checked noHover' button-filter='Algorithm Comment'>Algorithm comment</button>
                    <button class='transparent checked noHover' button-filter='Profile Comment'>Profile comment</button>
                    <button class='transparent checked noHover' button-filter='Algorithm Reply'>Algorithm reply</button>
                    <button class='transparent checked noHover' button-filter='Profile Reply'>Profile reply</button>
                    <button class='transparent checked noHover' button-filter='Profile'>Profile</button>
                    <div class="content" style="margin-top: 15px"></div>
                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="answered-reports" aria-labelledby="answered-reports-tab" style="position: relative">
                        <span>Report Filters: </span><br>
                        <button class='transparent checked noHover' button-filter='All'>All</button>
                        <button class='transparent checked noHover' button-filter='Algorithm'>Algorithm</button>
                        <button class='transparent checked noHover' button-filter='Request'>Request</button>
                        <button class='transparent checked noHover' button-filter='Line Comment'>Line comment</button>
                        <button class='transparent checked noHover' button-filter='Algorithm Comment'>Algorithm comment</button>
                        <button class='transparent checked noHover' button-filter='Profile Comment'>Profile comment</button>
                        <button class='transparent checked noHover' button-filter='Algorithm Reply'>Algorithm reply</button>
                        <button class='transparent checked noHover' button-filter='Profile Reply'>Profile reply</button>
                        <button class='transparent checked noHover' button-filter='Profile'>Profile</button>
                        <div class="content" style="margin-top: 15px"></div>
                    </div> 
                    <div role="tabpanel" class="tab-pane fade" id="user-list" aria-labelledby="user-list-tab" style="position: relative">
                    <span>Filters: </span>
                    <button class='transparent checked noHover' button-filter='banned'>Banned users</button>
                    <button class='transparent checked noHover' button-filter='normal'>Normal users</button>
                    <button class='transparent checked noHover' button-filter='moderators'>Moderators</button>
                    <button class='transparent checked noHover' button-filter='administrators'>Administrators</button>
                    <div class="content" style="margin-top: 15px"></div>
                    </div> 
                </div>
            </div>
        </div>
    </div>
    <script src="{{ URL::to('scripts/adminScript.js') }}"></script>
@stop