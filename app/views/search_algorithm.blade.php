<div class="boxWrapper">
    {{ Form::open(array('url'=>'posts/searchalgorithm', 'id'=>'search_algorithms_form')) }}
        {{ Form::label('keywords', 'Keywords') }}
        {{ Form::text('keywords', null, array('class'=>'form-control', 'placeholder'=>'keywords separated by coma..')) }}
        {{ Form::label('programming_language', 'Programming Language') }}
        {{ Form::text('language', null, array('class'=>'form-control', 'placeholder'=>'algorithm language..')) }}
        <label class="checkbox-inline">{{ Form::checkbox('ratio', 'positive') }}Positive like/dislike ratio</label>
        <label class="checkbox-inline">{{ Form::checkbox('owned', 'positive') }}Don't include my own algorithms</label>
        <p><a href data-toggle="modal" data-target="#requestModal">Can't find what you are searching for? Submit a request</a></p>
        <div class="text-right">
        {{ Form::submit('Search', array('class'=>'btn', 'id'=>'submit'))}}
        </div>
    {{ Form::close() }}
</div>
<div style="position: relative; margin-top: 25px">
    <p class="hidden" id="searchErrorMessage"></p>
    <div class="hidden searchedAlgorithms">
        
    </div>
</div>

<div class="modal fade" id="reportModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Report</h4>
            </div>
        <div class="modal-body">
            <label>Describe your problem. (optional)</label>
            <textarea class="form-control" placeholder="short problem description.."></textarea>
            <label>Why are you reporting this?</label>
            <select class="form-control">
                <option>Indecency</option>
                <option>Copied content without source</option>
                <option>Duplicate</option>
                <option>Other</option>
            </select>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" id="submitReport">Submit Report</button>
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
                <div class="requestedAlgorithms">
                    <p class="text-justify">Here is a list of algorithms already requested by the users. You can upvote the requests you are interested in. If you can't find the algorithm you were looking for, submit the request yourself!</p>
                    <input class="form-control" id="searchRequests" placeholder="Search..">
                    <div class="requested-box"></div>
                    <a href="javascript:void(0)" id="letMeRequest">Can't find it.. I'll make a request</a>
                </div>
                {{ Form::open(array('url'=>'requests/submit', 'id'=>'submit_algorithm_form')) }}
                {{ Form::label('algorithm_name', 'Algorithm Name') }}
                {{ Form::text('algorithm_name', null, array('class'=>'form-control', 'placeholder'=>'algorithm name..')) }}
                {{ Form::label('algorithm_description', 'Algorithm Description') }}
                {{ Form::textarea('algorithm_description', null, array('class'=>'form-control', 'rows'=>'2', 'placeholder'=>'add a short description..')) }}
                {{ Form::label('language', 'Programming Language') }}
                {{ Form::text('language', null, array('class'=>'form-control', 'placeholder'=>'programming language..')) }}
                {{ Form::submit('Submit Request', array('class'=>'btn hidden'))}}
                <a href="javascript:void(0)" id="existentRequests">Return to the existent requests</a>
                {{ Form::close() }}
            </div>
            <div class="modal-footer">
                <span class="fader">All the fields are required.</span>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary hidden" id="submitRequest">Submit Request</button>
            </div>
        </div>
    </div>
</div>

<script src="{{ URL::to('scripts/searchAlgorithms.js') }}"></script>