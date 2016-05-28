@extends('layout')

@section('content')
    
        <div class="container application">
            <div class="row">
                <div class="col-md-12 main">
                    <div class="boxWrapper">
                        <h1>Algorithm management</h1>
                        <p>Hello, <span class="person" id="person-me">{{Auth::user()->last_name;}} {{Auth::user()->first_name;}}</span>. Here you can search algorithms or choose to post one yourself.</p>
                    </div>
                    <ul class="nav nav-tabs" style="margin-bottom: 25px">
                        <li role="presentation" class="active"><a id="my-posts-tab" role="tab" data-toggle="tab" aria-controls="my-posts" aria-expanded="true" href="#my-posts">My posts</a></li>
                        <li role="presentation"><a id="search-algorithms-tab" role="tab" data-toggle="tab" aria-controls="search-algorithms" aria-expanded="false" href="#search-algorithms">Search</a></li>
                        <li role="presentation"><a id="post-new-tab" role="tab" data-toggle="tab" aria-controls="post-new" aria-expanded="false" href="#post-new">Post a new algorithm</a></li>
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane fade active in" id="my-posts" aria-labelledby="my-posts-tab" style="position: relative">
                            @include('my_algorithms')
                        </div> 
                        <div role="tabpanel" class="tab-pane fade" id="search-algorithms" aria-labelledby="search-algorithms-tab"> 
                            @include('search_algorithm')
                            
                        </div>
                        <div role="tabpanel" class="tab-pane fade" id="post-new" aria-labelledby="post-new-tab"> 
                            @include('post_algorithm')
                        </div>
                    </div>
                </div> 
            </div>
        </div>

        <div class="modal fade" id="requestModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Request Algorithm</h4>
              </div>
              <div class="modal-body">
                <label>Algorithm Name</label>
                <input type="text" class="form-control" placeholder="algorithm title..">
                <label>Algorithm Description</label>
                <textarea class="form-control" placeholder="short algorithm description.."></textarea>
                <label>Programming Language</label>
                <input class="form-control" name="language" placeholder="programming language..">
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Submit Request</button>
              </div>
            </div>
          </div>
        </div>
    <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Info</h4>
                </div>
                <div class="modal-body">
                    @if(count($errors))
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <script src="{{ URL::to('scripts/postedAlgorithms.js') }}"></script>
    <script src="{{ URL::to('scripts/errorModal.js') }}"></script>
    <script src="{{ URL::to('scripts/ace/ace.js') }}"></script>
    <script src="{{ URL::to('scripts/ace/mode-ruby.js') }}"></script>
    <script src="{{ URL::to('scripts/jquery-ace.min.js') }}"></script>
    <script src="{{ URL::to('scripts/postAlgorithm.js') }}"></script>
@stop