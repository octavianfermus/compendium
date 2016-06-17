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
                   <li role="presentation"><a id="moderator-list-tab" role="tab" data-toggle="tab" aria-controls="moderator-list" aria-expanded="false" href="#moderator-list">See moderator list</a></li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade active in" id="manage-reports" aria-labelledby="manage-reports-tab"> 
                       1
                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="answered-reports" aria-labelledby="answered-reports-tab" style="position: relative">
                        2
                    </div> 
                    <div role="tabpanel" class="tab-pane fade" id="moderator-list" aria-labelledby="moderator-list-tab" style="position: relative">
                        3
                    </div> 
                </div>
            </div>
        </div>
    </div>
@stop