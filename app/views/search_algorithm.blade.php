<div class="boxWrapper">
    {{ Form::open(array('url'=>'posts/searchalgorithm', 'id'=>'search_algorithms_form')) }}
        {{ Form::label('keywords', 'Keywords') }}
        {{ Form::text('keywords', null, array('class'=>'form-control', 'placeholder'=>'keywords separated by coma..')) }}
        {{ Form::label('programming_language', 'Programming Language') }}
        {{ Form::text('language', null, array('class'=>'form-control', 'placeholder'=>'algorithm language..')) }}
        <label class="checkbox-inline">{{ Form::checkbox('ratio', 'positive') }}Positive like/dislike ratio</label>
        <p><a href="javascript:void(0)" data-toggle="modal" data-target="#requestModal">Can't find what you are searching for? Submit a request</a></p>
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