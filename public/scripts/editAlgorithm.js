$(document).ready(function() {
    var postId = window.location.href.split("/")[window.location.href.split("/").length-1],
        codeEditor = null,
        algorithm_id = null,
        requestedId = null,
        requests = null,
        root = globalSettings.getRoot(),
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
                $("textarea[name='byrequest']").val(data.request_id);
                requestedId = data.request_id;
                $("input[name='language']").val(data.language);
                algorithm_id = data.algorithm_id;
                $('#postedAlgorithmArea').ace({ 
                    theme: 'monokai',
                    height: 140
                });

                codeEditor = ace.edit("editor");
                codeEditor.setTheme("ace/theme/monokai");
                //codeEditor.getSession().setMode("ace/mode/php");
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

    },
        getRequestList = function(data) {
            var data = data,
                    toAppend="";
                $.each(data,function(index,value) {
                    toAppend +="<div>" +
                        "<h2>"+value.name+"</h2>" +
                        "<p><span>Language</span>: "+ value.language + "</p>"+
                        "<p><span>Description</span>: "+ value.description + "</p>"+
                        "<p>"+value.upvotes+" people upvoted this request.</p>" +
                        "<p><a href='javascript:void(0)' request_id='"+value.id+"'"+">I want to take this request</a></p>" +
                        "</div>";
                });
                $("#takeRequestModal .requested-box").html(toAppend);
                
            $("a[request_id]").click(function() {
                requestId = $(this).attr("request_id");
                var found = false,
                    requested = {};
                $.each(requests, function(index,value) {
                    if(found == false && value.id == requestId) {
                        found = true;
                        requested = value;
                    }
                });
                requestedValues = requested;
                requestedId = requestedValues.id;
                $("#post_algorithm_form input[name='algorithm_name']").val(requested.name);
                $("#post_algorithm_form input[name='algorithm_name']").attr("readonly","readonly");
                $("#post_algorithm_form textarea[name='algorithm_description']").val(requested.description);
                $("#post_algorithm_form textarea[name='algorithm_description']").attr("readonly","readonly");
                $("#post_algorithm_form input[name='language']").val(requested.language);
                $("#post_algorithm_form input[name='language']").attr("readonly","readonly");
                $('#takeRequestModal').modal('toggle');
            });
        },
        getRequests = function() {
            jQuery.ajax({
                method: 'get',
                url: root + "/requests/all",
                success: function (data) {
                    requests = data.data;
                    console.log(requests);
                    getRequestList(requests);
                    filterRequests();
                },
                error: function (data) {
                    console.log(data);
                } 
            });
        },
        filterRequests = function() {
            var newList = [],
            currentSearch = $("#takeRequestModal #searchRequests").val();
            $.each(requests,function(index,value) {
                if(value.name.toLowerCase().indexOf(currentSearch.toLowerCase()) >-1 || 
                   value.description.toLowerCase().indexOf(currentSearch.toLowerCase())>-1 ||
                  value.language.toLowerCase().indexOf(currentSearch.toLowerCase())>-1) {
                    newList.push(value);
                }
            });
            getRequestList(newList);
        };
    
    getPostData();
    $("#takeRequestModalOpener").click(function() {
        $('#takeRequestModal').modal('toggle');
        getRequests();
    });
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
    $("#cancelRequest").click(function() {
        $("#post_algorithm_form input[name='algorithm_name']").val("");
        $("#post_algorithm_form input[name='algorithm_name']").removeAttr("readonly");
        $("#post_algorithm_form textarea[name='algorithm_description']").val("");
        $("#post_algorithm_form textarea[name='algorithm_description']").removeAttr("readonly");
        $("#post_algorithm_form input[name='language']").val("");
        $("#post_algorithm_form input[name='language']").removeAttr("readonly");
        $("#post_algorithm_form input[name='original_link']").val("");
        $("[name='byrequest']").val(0);
        requestedValues = null;
        if(codeEditor) {
            codeEditor.setValue("");
            $("[name='algorithm_code']").val(codeEditor.getValue());
        }
    });
    $("#saveAsTemplate").click(function(e) {
        $("#isItTemplate").val("1");
        if(requestedId == null) {
            $("[name='byrequest']").val(0);
        } else {
            $("[name='byrequest']").val(requestedId);
        }
        $("[name='algorithm_code']").val(codeEditor.getValue());
        $("input[name='algorithm_id']").val(algorithm_id);
        $("#post_algorithm_form #submit_algorithm").click();
    });
    $("#publishAlgorithm").click(function(e) {
        $("#isItTemplate").val("0");
        if(requestedId == null) {
            $("[name='byrequest']").val(0);
        } else {
            $("[name='byrequest']").val(requestedId);
        }
        $("[name='algorithm_code']").val(codeEditor.getValue());
        $("input[name='algorithm_id']").val(algorithm_id);
        if(codeEditor.getValue().trim().length > 0 &&
           $("#post_algorithm_form input[name='algorithm_name']").val().trim().length > 0 &&
           $("#post_algorithm_form input[name='language']").val().trim().length > 0 &&
           $("#post_algorithm_form textarea[name='algorithm_description']").val().trim().length > 0) {
            $("#post_algorithm_form #submit_algorithm").click();
        } else {
            $(".fader#submitFader").fadeIn("slow");
            setTimeout(function(){
                $(".fader#submitFader").fadeOut("slow");
            },3000);
        }
    });
    
});