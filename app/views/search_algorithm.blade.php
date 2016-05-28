<div class="boxWrapper">
    {{ Form::open(array('url'=>'posts/searchalgorithm', 'id'=>'search_algorithms_form')) }}
        {{ Form::label('keywords', 'Keywords') }}
        {{ Form::text('keywords', null, array('class'=>'form-control', 'placeholder'=>'keywords separated by coma..')) }}
        {{ Form::label('programming_language', 'Programming Language') }}
        {{ Form::text('language', null, array('class'=>'form-control', 'placeholder'=>'algorithm language..')) }}
        <label class="checkbox-inline">{{ Form::checkbox('ratio', 'positive') }}Positive like/dislike ratio</label>
        <p><a href data-toggle="modal" data-target="#requestModal">Can't find what you are searching for? Submit a request</a></p>
        <div class="text-right">
        {{ Form::submit('Search', array('class'=>'btn', 'id'=>'submit'))}}
        </div>
    {{ Form::close() }}
    
</div>
<div style="position: relative; margin-top: 25px">
    <p class="hidden" id="searchErrorMessage"></p>
    <a href="javascript:void(0)" class="switcher hidden" id="searchPostsSwitcher">Switch View Mode</a>
    <div class="hidden searchedAlgorithms">
        
    </div>
    <table class="hidden searchedAlgorithmsTable">
        <thead>
            <th>Name</th>
            <th>Language</th>
            <th>Upvotes</th>
            <th>Downvotes</th>
            <th>Approval</th>
            <th>Views</th>
            <th>Comments</th>
            <th class="text-center">Publisher</th>
        </thead>
        <tbody></tbody>
    </table>
</div>

<div class="modal fade" id="requestModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Request Algorithm</h4>
            </div>
            <div class="modal-body">
                {{ Form::open(array('url'=>'users/submitrequest', 'id'=>'submit_algorithm_form')) }}
                {{ Form::label('algorithm_name', 'Algorithm Name') }}
                {{ Form::text('algorithm_name', null, array('class'=>'form-control', 'placeholder'=>'algorithm name..')) }}
                {{ Form::label('algorithm_description', 'Algorithm Description') }}
                {{ Form::textarea('algorithm_description', null, array('class'=>'form-control', 'rows'=>'2', 'placeholder'=>'add a short description..')) }}
                {{ Form::label('language', 'Programming Language') }}
                {{ Form::text('language', null, array('class'=>'form-control', 'placeholder'=>'programming language..')) }}
                {{ Form::submit('Submit Request', array('class'=>'btn hidden'))}}
                {{ Form::close() }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="submitRequest">Submit Request</button>
            </div>
        </div>
    </div>
</div>