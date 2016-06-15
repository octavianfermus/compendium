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
                    <div class="boxWrapper">
                        <h1>Groups</h1>
                        <p>Here you can search for groups to join, create your own, or talk to people in groups you are already in.</p>     
                    </div>
                    <ul class="nav nav-tabs" style="margin-bottom: 25px">
                        <li role="presentation" class="active"><a id="manage-groups-tab" role="tab" data-toggle="tab" aria-controls="manage-groups" aria-expanded="true" href="#manage-groups">Manage groups</a></li>
                        <li role="presentation"><a id="create-group-tab" role="tab" data-toggle="tab" aria-controls="create-group" aria-expanded="true" href="#create-group">Create group</a></li>
                        <li role="presentation"><a id="search-groups-tab" role="tab" data-toggle="tab" aria-controls="search-groups" aria-expanded="false" href="#search-groups">Search groups</a></li>
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane fade active in" id="manage-groups" aria-labelledby="manage-groups-tab" style="position: relative">
                            <input type="text" class="form-control" id="manageGroupsFilter" placeholder="filter by name, group type and/or leader (separate tags by comma)..">
                            <div class="boxWrapper groupManager">
                               
                            </div>
                        </div> 
                        <div role="tabpanel" class="tab-pane fade" id="create-group" aria-labelledby="create-group-tab" style="position: relative">
                            <div class="boxWrapper">
                                <label>Group name</label>
                                <input class="form-control" id="createGroupName" placeholder="group name..">
                                <label>Group description (optional)</label>
                                <textarea class="form-control" id="createGroupDescription" placeholder="short description.."></textarea>
                                <label>Group type</label>
                                <select class="form-control" id="createGroupType">
                                    <option value="0">Public group</option>
                                    <option value="1">Private group</option>
                                </select>
                                <div class="text-right" style="margin-top: 10px">
                                    <span class="fader" id="submitFader">You must fill all mandatory fields before creating a group.</span>
                                    <button id="createGroup" class="btn">Create</button>
                                </div>
                                <div class="alert alert-success" id="groupCreatedMessage"style="margin: 10px 0; display: none">
                                    <a href="#" class="close">&times;</a>
                                    <strong>Group successfully created! </strong>Visit it by clicking <a href="javascript:void(0)" id="linkToCreatedGroup">this link</a>.
                                </div>
                            </div>
                        </div> 
                        <div role="tabpanel" class="tab-pane fade" id="search-groups" aria-labelledby="search-groups-tab"> 
                            <div class="search-groups boxWrapper">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="searchKeywords" placeholder="search groups by name.. leaving this field empty will return all the existing groups..">
                                    <span class="input-group-addon" id="searchGroupsButton" style="border: none; padding: 0;"><button class="btn">Search</button></span>
                                </div>
                            </div>
                            <div class="searchResults">
                            </div>
                        </div>
                    </div>
                    
                </div>
                
            </div>  
        </div>
    </div>
    <script src="{{ URL::to('scripts/groups-main.js') }}"></script>
@stop