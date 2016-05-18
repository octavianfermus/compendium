$(document).ready(function() {
    if($("#errorModal .modal-body").html().trim().length!==0) {
        $('#errorModal').modal('toggle');
    };
});