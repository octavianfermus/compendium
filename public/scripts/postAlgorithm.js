
$(document).ready(function() {
    
    var codeEditor = null;
    $("#continueToCode").click(function(e) {
        e.preventDefault();
        codeEditor = ace.edit("editor");
        codeEditor.setTheme("ace/theme/monokai");
        codeEditor.getSession().setMode("ace/mode/javascript");
        $(".ace_editor").css({
            width: "100%",
            height: 500,
            "margin-top": 35,
            "margin-bottom": 35,
            "position": "relative"
        });
        $(".text-right.hidden").removeClass("hidden");
        $("#continueToCode").hide();
    });
    
    $("#saveAsTemplate").click(function(e) {
        $("#isItTemplate").val("1");
        $("[name='algorithm_code']").val(codeEditor.getValue());
        $("#post_algorithm_form #submit_algorithm").click();
    });
    $("#publishAlgorithm").click(function(e) {
        $("#isItTemplate").val("0");
        $("[name='algorithm_code']").val(codeEditor.getValue());
        $("#post_algorithm_form #submit_algorithm").click();
    });
    
});