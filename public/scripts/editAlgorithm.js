$(document).ready(function() {
    var postId = window.location.href.split("/")[window.location.href.split("/").length-1],
        codeEditor = null,
        algorithm_id = null,
        getPostData = function () {
        jQuery.ajax({
            method: 'get',
            url: "../templatedata",
            dataType: "json",
            data: {id: postId},
            success: function (data) {
                console.log(data);

                $("input[name='algorithm_name']").val(data.name);
                $("input[name='original_link']").val(data.original_link);
                $("textarea[name='algorithm_description']").val(data.description);
                $("select[name='language']").val(data.language);
                algorithm_id = data.algorithm_id;
                $('#postedAlgorithmArea').ace({ 
                    theme: 'monokai',
                    height: 140
                });

               

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
                codeEditor.setValue(data.content);
                
            },
            error: function (data) {
                console.log(window.location.href.split("/")[window.location.href.split("/").length-1]);
            }

        });

    };
    getPostData();
    $("#executeCommand").click(function() {
        var data = {
                data: {
                    id: algorithm_id
                }
            };
        jQuery.ajax({
            method: 'PUT',
            url: "../deletealgorithm",
            dataType: "json",
            data: data,
            success: function (data) {
                window.location.href = "http://"+window.location.href.split("/")[2];
                console.log(data);
            },
            error: function (data) {
                console.log("error");
                console.log(data);
            }
        });
    });
    $("#saveAsTemplate").click(function(e) {
        $("#isItTemplate").val("1");
        $("[name='algorithm_code']").val(codeEditor.getValue());
        $("input[name='algorithm_id']").val(algorithm_id);
        $("#post_algorithm_form #submit_algorithm").click();
    });
    $("#publishAlgorithm").click(function(e) {
        $("#isItTemplate").val("0");
        $("[name='algorithm_code']").val(codeEditor.getValue());
        $("input[name='algorithm_id']").val(algorithm_id);
        $("#post_algorithm_form #submit_algorithm").click();
    });
    
});