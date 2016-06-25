$(document).ready(function () {
    var conversationID = window.location.href.split("#")[0].split("/")[window.location.href.split("/").length-1],
        root = globalSettings.getRoot(),
        messageHistory = undefined,
        crumbs = undefined,
        initiated = false,
        timestamp = 0,
        userlist = undefined,
        populateCrumbs = function(data) {
            var toAppend ="";
            crumbs = data;
            $.each(crumbs, function(index, value) {

                toAppend +='<li '+(value.link == conversationID ? 'class="selected"':(value.seen == 0 ? 'class="notSeen"': ""))+'>'+
                    '<a href="'+root+'/messages/'+value.link +'">'+
                    '<span>'+value.name+'</span>'+
                    '<span style="float:right; margin-right: 4px" class="newSpan">'+(value.seen == 0 ? 'New!' : "")+'</span>' +
                    '<p>'+value.from + ": " + value.message+'</p>'+
                    '</a>'+
                    '</li>';
            });
            $(".sidebar ul.messageList").html(toAppend);
        },
        populateMessages = function() {
            var toAppend = "",
                fromMe = null,
                day = null;
            $.each(messageHistory, function(index, value) {
                if(day === null) {
                    day = value.timestamp.split(" ")[0];
                    fromMe = null;
                    toAppend += '<p class="text-center" style="border-bottom: 1px solid #f1bb59; margin: 4px;"><strong>'+day+'</strong></p>';
                } else {
                    if(day!== value.timestamp.split(" ")[0]) {
                        day = value.timestamp.split(" ")[0];
                        fromMe = null;
                        toAppend += '</div><p class="text-center" style="border-bottom: 1px solid #f1bb59; margin: 4px;"><strong>'+day+'</strong></p>';
                    }
                }
                if(fromMe !== value.from_me) {
                    toAppend += '</div><div class="reply">';
                    
                } 
                toAppend += '<p>'+(fromMe === null || fromMe !== value.from_me ? '<a href="'+root+'/profile/'+value.id+'">'+value.name+'</a>' : '')+'<span style="float: right">'+value.timestamp.split(" ")[1]+'</span></p>' + 
                    '<p>'+value.message+'</p>';
                fromMe = value.from_me;
            });
            $(".conversation .boxWrapper#messages").html(toAppend);
            if(initiated == false) {
                if($(".boxWrapper p").length) {
                    $(".boxWrapper p")[$(".boxWrapper p").length-1].scrollIntoView();
                }
                initiated = true;
            }
        },
        getPostData = function () {
            jQuery.ajax({
                method: 'get',
                url: root+"/messaging/messagehistory",
                data: {id: conversationID, timestamp: timestamp},
                success: function (data) {
                    if(timestamp == undefined) {
                        populateCrumbs(data.crumb);
                    }
                    if(timestamp!== data.timestamp) {
                        timestamp = data.timestamp;
                        $("#talkingTo span").html("<a href='"+root+"/profile/"+conversationID+"'>"+data.talkingTo+"</a>");
                        messageHistory = data.history;
                        populateMessages();
                        populateCrumbs(data.crumb);
                    }
                },
                error: function (data) {
                    console.log("error");
                }
            });

        };

    getPostData();
    setInterval(function() {
        getPostData();
    }, 500);
    $(".send-message input[type='text']").keypress(function(e) {
        if(e.keyCode == 13) { 
            $(".send-message #sendButton .btn").click();
        }
    });
    $(".send-message #sendButton .btn").click(function() {
        var comment = $(".send-message input").val().trim();
        if(comment.length>0) {
            $(".send-message input").val("");
            jQuery.ajax({
                method: 'post',
                url: root+"/messaging/messageuser",
                dataType: "json",
                data: {id: conversationID, comment: comment},
                success: function(data) {
                    if($(".boxWrapper p").length) {
                        $(".boxWrapper p")[$(".boxWrapper p").length-1].scrollIntoView();
                    }
                },
                error: function(data) {
                    
                }
            });
        }
    });
    $(".search-user #searchUserButton .btn").click(function() {
        var comment = $(".search-user input").val().trim(),
            toAppend = "";
        $(".search-user input").val("");
        $.each(userlist,function(index,value) {
            if(comment === "" || comment.toLowerCase().split(" ").indexOf(value.first_name.toLowerCase())>-1 ||
               comment.toLowerCase().split(" ").indexOf(value.last_name.toLowerCase())>-1) {
                toAppend +='<div class="boxWrapper">'+
                    '<p><span>Name: </span>'+value.last_name+ " " +value.first_name + '</p>'+
                    '<a href="'+root+'/profile/'+value.id+'">See profile</a> | <a href="'+root+'/messages/'+value.id+'">Send message</a>'+
                    '</div>';
            }
        });
        if(toAppend == "") {
            toAppend = "<div class='boxwrapper'><p>No results based on your queries. Please try again.</p></div>";
        }
        $(".searchResults").html(toAppend);
    });
});