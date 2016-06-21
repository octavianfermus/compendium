@extends('layout')

@section('content')
    <div class="container application">
        <div class="row">
            <div class="col-md-12">
                <div class="boxWrapper">
                    <div class="text-right">
                        <a href="javascript:void(0)" id="cancelRequest">Reset progress</a>
                        <span> | </span>
                        <a href="javascript:void(0)" id="takeRequestModalOpener">I want to create an algorithm based on a request..</a>
                    </div>
                    {{ Form::open(array('url'=>'post/edit', 'id'=>'post_algorithm_form')) }}
                        {{ Form::label('algorithm_name', 'Algorithm name') }}
                        {{ Form::text('algorithm_name', null, array('class'=>'form-control', 'placeholder'=>'algorithm name..')) }}
                        {{ Form::label('programming_language', 'Programming Language') }}
                        {{ Form::text('language', null, array('class'=>'form-control')) }}
                        {{ Form::label('description', 'Description') }}
                        {{ Form::textarea('algorithm_description', null, array('class'=>'form-control', 'rows'=>'2', 'placeholder'=>'add a short description..')) }}
                        {{ Form::label('original_link', 'Original Link (optional)') }}
                        {{ Form::text('original_link', null, array('class'=>'form-control', 'placeholder'=>'original algorithm location..')) }}
                        <div id="editor"></div>
                        {{ Form::textarea('algorithm_code', null, array('class'=>'hidden', 'placeholder'=>'add a short description..')) }}
                        {{ Form::text('template', null, array('class'=>'hidden','id'=>'isItTemplate')) }}
                        {{ Form::text('algorithm_id', null, array('class'=>'hidden','id'=>'algorithm_id')) }}
                        {{ Form::text('byrequest', null, array('class'=>'hidden','id'=>'isItByRequest')) }}
                        {{ Form::submit('Submit changes', array('class'=>'btn hidden', 'id'=>'submit_algorithm'))}}
                    {{ Form::close() }}
                        <div class="text-right">
                            <span class="fader" id="submitFader">You must fill all mandatory fields before publishing an algorithm.</span>
                            <button class="btn danger" data-toggle="modal" data-target="#confirmationModal">Delete</button>
                            <button class="btn" id="saveAsTemplate">Save as template</button>
                            <button class="btn" id="publishAlgorithm">Publish</button>
                        </div>
                </div>
                <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Are you sure?</h4>
                            </div>
                            <div class="modal-body">
                                <p>You cannot undo this operation. Are you sure?</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn" data-dismiss="modal" id="executeCommand">Yes</button>
                                <button type="button" class="btn" data-dismiss="modal">No</button>
                            </div>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->
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
                <div class="modal fade" id="takeRequestModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">Create Algorithm by Request</h4>
                            </div>
                            <div class="modal-body">
                                <div class="requestedAlgorithms">
                                    <input class="form-control" id="searchRequests" placeholder="Search..">
                                    <div class="requested-box"></div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ URL::to('scripts/errorModal.js') }}"></script>
    <script src="{{ URL::to('scripts/ace/ace.js') }}"></script>
    <script src="{{ URL::to('scripts/ace/mode-ruby.js') }}"></script>
    <script src="{{ URL::to('scripts/jquery-ace.min.js') }}"></script>
    <script src="{{ URL::to('scripts/editAlgorithm.js') }}"></script>
@stop