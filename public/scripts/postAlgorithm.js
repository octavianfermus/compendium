
$(document).ready(function() {
    
    var codeEditor = null,
        requests = null,
        requestedValues = null,
        requestedId = null,
        root = globalSettings.getRoot(),
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
    $("#continueToCode").click(function(e) {
        e.preventDefault();
        if($("#post_algorithm_form input[name='algorithm_name']").val().trim().length > 0 &&
           $("#post_algorithm_form input[name='language']").val().trim().length > 0 &&
           $("#post_algorithm_form textarea[name='algorithm_description']").val().trim().length > 0
          ) {
            if(!codeEditor) {
                codeEditor = ace.edit("editor");
                codeEditor.setTheme("ace/theme/monokai");
                codeEditor.getSession();
                $(".ace_editor").css({
                    width: "100%",
                    height: 500,
                    "margin-top": 35,
                    "margin-bottom": 35,
                    "position": "relative"
                });
            }
            $("#post_algorithm_form").slideUp();
            $("h1#algorithmName").html($("#post_algorithm_form input[name='algorithm_name']").val());
            $("p#language").append($("#post_algorithm_form input[name='language']").val());
            $("a#creatorUsername").html($("#person-me").html());
            $("p#algorithmDescription").append($("#post_algorithm_form textarea[name='algorithm_description']").val());
            $("#partTwo").slideDown();
            $(".text-right.hidden").removeClass("hidden");
            $("#continueToCode").addClass("hidden");
        } else {
            $(".fader").fadeIn("slow");
            setTimeout(function(){
                $(".fader").fadeOut("slow");
            },3000);
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
        if($("[name='algorithm_code']").val().trim().length>0) {
            $("#post_algorithm_form #submit_algorithm").click();
        } else {
            $(".fader#submitFader").fadeIn("slow");
            setTimeout(function(){
                $(".fader#submitFader").fadeOut("slow");
            },3000);
        }
    });
    $("#takeRequestModal #searchRequests").keyup(function(e) {
        filterRequests();
    });
    $("#takeRequestModalOpener").click(function() {
        if($("#post_algorithm_form").css("display") !== "none") {
            $('#takeRequestModal').modal('toggle');
            getRequests();
        }
    });
    $("#cancelRequest").click(function() {
        if($("#post_algorithm_form").css("display") !== "none") {
            $("#post_algorithm_form input[name='algorithm_name']").val("");
            $("#post_algorithm_form input[name='algorithm_name']").removeAttr("readonly");
            $("#post_algorithm_form textarea[name='algorithm_description']").val("");
            $("#post_algorithm_form textarea[name='algorithm_description']").removeAttr("readonly");
            $("#post_algorithm_form input[name='language']").val("");
            $("#post_algorithm_form input[name='language']").removeAttr("readonly");
            $("#post_algorithm_form input[name='original_link']").val("");
            $("h1#algorithmName").html("");
            $("p#language").html("<span>Language: </span>");
            $("a#creatorUsername").html("");
            $("p#algorithmDescription").html("<span>Description: </span>");
            requestedValues = null;
        } else {
            $("#post_algorithm_form").slideDown();
            $("#partTwo").slideUp();
            $("#continueToCode").removeClass("hidden");
            if(codeEditor) {
                codeEditor.setValue("");
                $("[name='algorithm_code']").val(codeEditor.getValue());
            }
        }
 
        
    });
});