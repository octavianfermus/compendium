
$(document).ready(function() {
    /*
    $('#postedAlgorithmArea').ace({ 
        theme: 'monokai',
        height: 140
    });
    
    var x = null;
    var y = null;

    document.addEventListener('mousemove', onMouseUpdate, false);
    document.addEventListener('mouseenter', onMouseUpdate, false);

    function onMouseUpdate(e) {
        x = e.pageX;
        y = e.pageY;
    }
    
    function getMouseX() {
        return x;
    }

    function getMouseY() {
        return y;
    }
    
    var decorator = $('#postedAlgorithmArea').data('ace');
    editor = decorator.editor.ace;
    
    editor.setReadOnly(true);
    editor.setValue("function isPrime(value) {\n\tfor(var i = 2; i < value; i++) {\n\t\tif(value % i === 0) {\n\t\t\treturn false;\n\t\t}\n\t}\n\treturn value > 1;\n}");
    setTimeout(function() {
        $($(".ace_gutter .ace_layer .ace_gutter-cell")[0]).append("<span class='commentSection' style='position: absolute; left: 5px'>2 comments </span>");
        $(".ace_gutter .ace_layer .ace_gutter-cell").click(function(e) {
            if($(this).children('.commentSection').length>0) {
                $(".lineComments").remove();
                $("body").append('<div class="lineComments"></div>');
                $(".lineComments").css({
                    position: 'absolute',
                    top: getMouseY() + 10,
                    left: getMouseX() + 10,
                    width: "30%",
                    'z-index': 1000,
                    border: '1px solid #ccc',
                    background: 'white',
                    padding: '0 10px',
                    'overflow-y': 'scroll',
                    'resize': 'both'
                });
                $(".lineComments").append('<div class="boxWrapper"><label>Your Message</label><textarea class="form-control"></textarea><div class="text-right"><button class="btn" id="commentCloser" style="margin-top: 10px; margin-right: 3px">Close</button><button class="btn" style="margin-top: 10px">Submit</button></div></div>');
                $(".lineComments").append('<div class="conversation"><div class="reply"><p><span class="person"><a href="javascript:void(0)">Richard Sluder</a></span></p><p>Great! just what I needed. Thumbs up</p></div></div>');
                $(".lineComments").append('<div class="conversation"><div class="reply"><p><span class="person"><a href="javascript:void(0)">Emilia</a></span></p><p>Easy and concise, but is there a faster version?</p></div></div>');
            }
            $(".lineComments").draggable();
            $("#commentCloser").unbind().click(function() {
                $(".lineComments").remove();
            });
        });
    }, 2000);
    
    */
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
    $(".switcher").click(function() {
        if($(this).siblings("table").hasClass("hidden")) {
            $(this).siblings("table").removeClass("hidden");
            $(this).siblings(".postedAlgorithms").addClass("hidden");
        } else {
            $(this).siblings("table").addClass("hidden");
            $(this).siblings(".postedAlgorithms").removeClass("hidden");
        }
    });
});