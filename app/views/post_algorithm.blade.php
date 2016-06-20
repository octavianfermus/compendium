<div class="boxWrapper">
    <div class="text-right">
        <a href="javascript:void(0)" id="cancelRequest">Reset progress</a>
        <span> | </span>
        <a href="javascript:void(0)" id="takeRequestModalOpener">I want to create an algorithm based on a request..</a>
    </div>
    
    {{ Form::open(array('url'=>'post/pushalgorithm', 'id'=>'post_algorithm_form')) }}
        {{ Form::label('algorithm_name', 'Algorithm name') }}
        {{ Form::text('algorithm_name', null, array('class'=>'form-control', 'placeholder'=>'algorithm name..')) }}
        {{ Form::label('language', 'Programming Language') }}
        {{ Form::text('language', null, array('class'=>'form-control', 'placeholder'=>'algorithm language..')) }}
        {{ Form::label('description', 'Description') }}
        {{ Form::textarea('algorithm_description', null, array('class'=>'form-control', 'rows'=>'2', 'placeholder'=>'add a short description..')) }}
        {{ Form::label('original_link', 'Original Link (optional)') }}
        {{ Form::text('original_link', null, array('class'=>'form-control', 'placeholder'=>'original algorithm location..')) }}
        {{ Form::textarea('algorithm_code', null, array('class'=>'hidden', 'placeholder'=>'add a short description..')) }}
        {{ Form::text('template', null, array('class'=>'hidden','id'=>'isItTemplate')) }}
        {{ Form::text('byrequest', null, array('class'=>'hidden','id'=>'isItByRequest')) }}
    {{ Form::close() }}
    <div class="text-right" style="margin-top: 35px;">
        <span class="fader">You cannot continue without filling all the required fields</span>
        <button class="btn" id="continueToCode">Continue</button>
    </div>
    <div id="partTwo" style="display: none">
        <div class="boxWrapper" id="mockupTitle">
            <h1 id="algorithmName"></h1>
            <p id="language"><span>Language: </span></p>
            <p class="subtitle">By <a href="../users/" id="creatorUsername"></a></p>
            <p><span>Original Link</span>: <a href="javascript:void(0)" id="originalLink">Find the original link here</a></p>
            <p id="algorithmDescription"><span>Description: </span></p>
        </div>
        <div id="editor"></div>
        <div class="text-right hidden">
            <span class="fader" id="submitFader">You cannot submit an empty algorithm</span>
            <button class="btn" id="saveAsTemplate">Save as template</button>
            <button class="btn" id="publishAlgorithm">Publish</button>
        </div>
    </div>
    
        
</div>

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