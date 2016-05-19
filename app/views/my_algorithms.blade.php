<div class="boxWrapper">
    <a href="javascript:void(0)" class="switcher" id="myPostsSwitcher">Switch View Mode</a>
    <div class="postedAlgorithms">
        
    </div>
    <table class="hidden postedAlgorithmsTable">
        <thead>
            <th>Name</th>
            <th>Language</th>
            <th>Upvotes</th>
            <th>Downvotes</th>
            <th>Approval</th>
            <th>Views</th>
            <th>Comments</th>
            <th class="text-center">Publish / Delete</th>
        </thead>
        <tbody></tbody>
    </table>
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