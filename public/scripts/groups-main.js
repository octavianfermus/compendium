$(document).ready(function() {
    var root = "http://localhost:8080",
        crumbs = undefined,
        groups = undefined,
        manageFilters = [],
        populateGroups = function(data) {
            var toAppend = "";
            $.each(data, function(index,value) {
                value.group_type = (value.private == 1 ? 'private group' : 'public group');
                toAppend+='<div>'+
                    '<h2>'+
                        '<a href="'+root+'/groups/'+value.group_id+'">' + value.group_name + '</a> ' +
                        '<span>(' + (value.private == 1 ? 'private' : 'public') + ' group)</span>' +
                        '<span style="float: right; font-size: 14px; font-weight: 600; line-height: 28px;">' + (value.accepted == 1 ?
                            'Member since ' + value.since.split(" ")[0] + '</span>' :
                            'NOT ACCEPTED TO DO' ) +
                        '</span>' +
                    '</h2>' +
                    '<p><span>Description: </span>' + value.group_description + '</p>' +
                    '<p>' +
                        '<span>Leader: </span>' +
                        '<a href="'+root+'/profile/'+value.leader_id+'">'+value.leader_name+'</a>' +
                        (value.leader_me == 1 ? ' <span>(you)</span>':'') +
                    '</p>' +
                    '<p><span>Member count: </span>' + value.memberCount + '</p>'+
                    '</div>';
            });
            toAppend = toAppend || "<p>No groups found..</p>";
            $(".groupManager").html(toAppend);
        },
        getGroups = function() {
            $.ajax({
                method: 'get',
                url: root+"/users/getmygroups",
                success: function(data) {
                    groups = data;
                    populateGroups(data);
                },
                error: function(data) {
                    
                }
            });
        };
    getGroups();
    $("#manageGroupsFilter").keypress(function(event){
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if(keycode == '13') {
            manageFilters = $(this).val().trim().split(",");
            if(manageFilters.length>0 && manageFilters[0].trim().length > 0){
                var filtered = [];
                $.each(groups, function(index,value) {
                    var pushIt = false;
                    $.each(manageFilters, function(iindex,vvalue) {
                        if(vvalue.trim().length > 0) {
                            if(value.group_name.toLowerCase().indexOf(vvalue.toLowerCase().trim()) > -1 ||
                               value.group_description.toLowerCase().indexOf(vvalue.toLowerCase().trim()) > -1 ||
                               value.leader_name.toLowerCase().indexOf(vvalue.toLowerCase().trim()) > -1 ||
                               value.group_type.toLowerCase().indexOf(vvalue.toLowerCase().trim()) > -1) {
                                pushIt = true;
                            }
                        }
                    });
                    if(pushIt === true) {
                        filtered.push(value);
                    }
                });
                populateGroups(filtered);
            } else {
                 populateGroups(groups);
            }
        }
    });
    $("#groupManagementBox").removeClass("hidden");
    $("#createGroup").click(function() {
        var name = $("#createGroupName").val().trim(),
            description = $("#createGroupDescription").val().trim(),
            type = $("#createGroupType").val().trim();
        if(name==""||type=="") {
            $(".fader#submitFader").fadeIn("slow");
            setTimeout(function(){
                $(".fader#submitFader").fadeOut("slow");
            },3000);
        } else {
            jQuery.ajax({
                method: 'post',
                url: root+"/users/creategroup",
                dataType: "json",
                data: {name:name,description:description,type:type == 'private' ? true : false},
                success: function (data) {
                    getGroups();
                    $("#linkToCreatedGroup").attr("href",root+"/groups/"+data.id);
                    $("#groupCreatedMessage").slideDown();
                    $("#createGroupName").val("");
                    $("#createGroupDescription").val("");
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }
    });
    
    $("#groupCreatedMessage .close").click(function(e) {
        e.preventDefault();
        $("#groupCreatedMessage").slideUp();
    });
    
    $("#searchGroupsButton .btn").click(function(e) {
        console.log("whatever man, can we finish this up");
        $.ajax({
            method:'post',
            url: root+"/users/searchgroup",
            dataType: "json",
            data: {},
            success: function(data) {
                console.log(data);
            },
            error: function(data) {
                console.log(data);
            }
        });
    });
        /*messageHistory = undefined,
        initiated = false,
        timestamp = undefined,
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
            $(".conversation .boxWrapper").html(toAppend);
            if(initiated == false) {
                $(".boxWrapper p")[$(".boxWrapper p").length-1].scrollIntoView();
                initiated = true;
            }
        },
        getPostData = function () {
            jQuery.ajax({
                method: 'get',
                url: root+"/users/messagehistory?id="+conversationID,
                success: function (data) {
                    if(timestamp!== data.timestamp) {
                        timestamp = data.timestamp;
                        if(data.history) {
                            $("#talkingTo").html("<a href='"+root+"/profile/"+conversationID+"'>"+data.talkingTo+"</a>");
                            messageHistory = data.history;
                            $(".main .conversation").removeClass("hidden"); 
                            populateMessages();
                            $(".closeConversation").removeClass("hidden");
                        } else {
                            if($(".main .conversation").length>0) {
                                $(".main .conversation").remove(); 
                                $("#allMessagesBox").removeClass("hidden");
                                $.ajax({
                                    method: 'get',
                                    url: root+'/userlist',
                                    success: function(data) {
                                       userlist = data;
                                    }
                                });
                                    
                            };
                        }
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
    }, 2000);
    $(".send-message #sendButton .btn").click(function() {
        var comment = $(".send-message input").val().trim();
        if(comment.length>0) {
            $(".send-message input").val("");
            jQuery.ajax({
                method: 'post',
                url: root+"/users/messageuser",
                dataType: "json",
                data: {id: conversationID, comment: comment},
                success: function(data) {
                   $(".boxWrapper p")[$(".boxWrapper p").length-1].scrollIntoView();
                },
                error: function(data) {
                    
                }
            });
        }
    });
    $(".search-user #searchUserButton .btn").click(function() {
        var comment = $(".search-user input").val().trim(),
            toAppend = "";
        if(comment.length>0) {
            $(".search-user input").val("");
            $.each(userlist,function(index,value) {
                if(comment.toLowerCase().split(" ").indexOf(value.first_name.toLowerCase())>-1 ||
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
        }
    });*/
});