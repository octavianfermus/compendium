$(document).ready(function() {
    var groupID = window.location.href.split("#")[0].split("/")[window.location.href.split("/").length-1],
        root = "http://localhost:8080",
        members = [],
        requests = [],
        me,
        createRequestList = function() {
            var toAppend ="";
            $.each(requests,function(index,value) {
                toAppend += '<div class="styled-list-member" listindex="'+index+'">' +
                    '<h2>'+
                        '<a href="'+root+'/profile/'+value.id+'">'+value.last_name + " " +value.first_name+'</a>'+
                        '<span style="float: right; font-size: 14px; font-weight: 600; line-height: 28px;">Requested since '+value.since.split(" ")[0]+'</span>'+
                    '</h2>' +
                    '<p>' +
                        '<button class="transparent acceptRequest">Accept join request</button>' +
                        '<button class="transparent denyRequest">Deny join request</button>' + 
                        '<button class="transparent hidden requestCancel">Cancel</button>' + 
                        '<button class="transparent hidden acceptRequestConfirm">Confirm</button>'+
                        '<button class="transparent hidden denyRequestConfirm">Confirm</button>'+
                    '</p>'+
                '</div>';
            });
            $("#requestsModal .boxWrapper").html(toAppend || "<p>No active requests..</p>");
            $(".acceptRequest").click(function() {
                $(this).addClass("hidden");
                $(this).closest("p").children(".denyRequest").addClass("hidden");
                $(this).closest("p").children(".requestCancel").removeClass("hidden");
                $(this).closest("p").children(".acceptRequestConfirm").removeClass("hidden");
            });
            $(".denyRequest").click(function() {
                $(this).addClass("hidden");
                $(this).closest("p").children(".acceptRequest").addClass("hidden");
                $(this).closest("p").children(".requestCancel").removeClass("hidden");
                $(this).closest("p").children(".denyRequestConfirm").removeClass("hidden");
            });
            $(".requestCancel").click(function() {
                $(this).addClass("hidden");
                $(this).closest("p").children(".acceptRequest").removeClass("hidden");
                $(this).closest("p").children(".denyRequest").removeClass("hidden");
                $(this).closest("p").children(".denyRequestConfirm").addClass("hidden");
                $(this).closest("p").children(".acceptRequestConfirm").addClass("hidden");
            });
            $(".acceptRequestConfirm").click(function() {
                var listindex = $(this).closest(".styled-list-member").attr("listindex"),
                    userid = requests[listindex].id;
                $.ajax({
                    method: 'post',
                    url: root+'/users/acceptgrouprequest',
                    dataType:"json",
                    data: {id:groupID, userid: userid},
                    success: function(data) {
                        $("#requestsModal .styled-list-member[listindex='"+listindex+"']").remove();
                        members.push(requests[listindex]);
                        createMemberList(true);
                        $("#requestsModal .boxWrapper").html($("#requestsModal .boxWrapper").html() || "<p>No active requests..</p>");
                    }
                });
            });
            $(".denyRequestConfirm").click(function() {
                var listindex = $(this).closest(".styled-list-member").attr("listindex"),
                    userid = requests[listindex].id;
                $.ajax({
                    method: 'post',
                    url: root+'/users/denygrouprequest',
                    dataType:"json",
                    data: {id:groupID, userid: userid},
                    success: function(data) {
                        $("#requestsModal .styled-list-member[listindex='"+listindex+"']").remove();
                        $("#requestsModal .boxWrapper").html($("#requestsModal .boxWrapper").html() || "<p>No active requests..</p>");
                    }
                });
            });
        },
        createMemberList = function(leader) {
            var toAppend = "";
            $.each(members,function(index,value) {
                
            console.log(value.id, me);
                toAppend += '<div class="styled-list-member" listindex="'+index+'">' +
                    '<h2>'+
                        '<a href="'+root+'/profile/'+value.id+'">'+value.last_name + " " +value.first_name+ (value.id == leader ? ' (you)':"")+'</a>'+
                        '<span style="float: right; font-size: 14px; font-weight: 600; line-height: 28px;">Member since '+value.since.split(" ")[0]+'</span>'+
                    '</h2>' +
                    '<p>'+
                        (value.id == me ?
                            '<button class="transparent leaveGroup">Leave group</button>' +
                            '<button class="transparent leaveGroupCancel hidden">Cancel</button>' +
                            '<button class="transparent leaveGroupConfirm hidden">Confirm</button>' :
                            '<a href="'+root+'/messages/'+value.id+'" class="transparent">Send message</a>' +
                            (leader == true ? 
                            '<button class="transparent kickFromGroup">Kick from group</button>' + 
                            '<button class="transparent promoteToLeader">Promote to leader</button>' + 
                            '<button class="transparent hidden requestCancel">Cancel</button>' + 
                            '<button class="transparent hidden kickFromGroupConfirm">Confirm</button>'+
                            '<button class="transparent hidden promoteToLeaderConfirm">Confirm</button>'
                            :"")
                        ) +
                    '</p>'+
                '</div>';
            });
            $("#membersModal .boxWrapper").html(toAppend);
            $("#membersModal .promoteToLeader").click(function() {
                $(this).addClass("hidden");
                $(this).closest("p").children(".requestCancel").removeClass("hidden");
                $(this).closest("p").children(".promoteToLeaderConfirm").removeClass("hidden");
                $(this).closest("p").children(".kickFromGroup").addClass("hidden");
            });
            $("#membersModal .kickFromGroup").click(function() {
                $(this).addClass("hidden");
                $(this).closest("p").children(".requestCancel").removeClass("hidden");
                $(this).closest("p").children(".kickFromGroupConfirm").removeClass("hidden");
                $(this).closest("p").children(".promoteToLeader").addClass("hidden");
            });
            $("#membersModal .requestCancel").click(function() {
                $(this).addClass("hidden");
                $(this).closest("p").children(".kickFromGroup").removeClass("hidden");
                $(this).closest("p").children(".promoteToLeader").removeClass("hidden");
                $(this).closest("p").children(".promoteToLeaderConfirm").addClass("hidden");
                $(this).closest("p").children(".kickFromGroupConfirm").addClass("hidden");
            });
            $("#membersModal .kickFromGroupConfirm").click(function() {
                var listindex = $(this).closest(".styled-list-member").attr("listindex"),
                    userid = members[listindex].id;
                $.ajax({
                    method: 'post',
                    url: root+'/users/kickfromgroup',
                    dataType:"json",
                    data: {id:groupID, userid: userid},
                    success: function(data) {
                        $("#membersModal .styled-list-member[listindex='"+listindex+"']").remove();
                    }
                });
            });
            
            $("#membersModal .promoteToLeaderConfirm").click(function() {
                var listindex = $(this).closest(".styled-list-member").attr("listindex"),
                    userid = members[listindex].id;
                $.ajax({
                    method: 'post',
                    url: root+'/users/promotetoleader',
                    dataType:"json",
                    data: {id:groupID, userid: userid},
                    success: function(data) {
                        location.reload();
                    }
                });
            });
            $(".leaveGroup").click(function() {
                $(this).addClass("hidden");
                $(".leaveGroupCancel").removeClass("hidden");
                $(".leaveGroupConfirm").removeClass("hidden");
            });
            $(".leaveGroupCancel").click(function() {
                $(this).addClass("hidden");
                $(".leaveGroup").removeClass("hidden");
                $(".leaveGroupConfirm").addClass("hidden");
            });
            $(".leaveGroupConfirm").click(function() {
                $.ajax({
                    method: 'post',
                    url: root+'/users/leavegroup',
                    dataType:"json",
                    data: {id:groupID},
                    success: function(data) {
                        window.location=root+'/groups';
                    }
                });
            });
        },
        getPostData = function () {
            jQuery.ajax({
                method: 'get',
                url: root+"/users/groupinitialdata?id="+groupID,
                success: function (data) {
                    console.log(data);
                    $("#groupName").html(data.groupName);
                    if(data.groupDescription) {
                        $("#groupDescription").removeClass("hidden").html(description);
                    }
                    $("#groupLeader").html("<span>Leader: </span><a href='"+root+"/profile/"+data.leader_id+"'>" + data.leader_name + (data.leader_me == true ? " (You)</a>" : "</a>"));
                    if(data.privateGroup == 1) {
                        $("#groupType").html("<span>Group type: </span>Private");
                        $(".convertToPublicGroup").removeClass("hidden");
                        $(".convertToPrivateGroup").remove();
                        $(".convertToPublicGroup").click(function() {
                            $(this).addClass("hidden");
                            $(".confirmConvert").removeClass("hidden");
                            $(".cancelConvert").removeClass("hidden");
                        });
                        $(".cancelConvert").click(function() {
                            $(this).addClass("hidden");
                            $(".confirmConvert").addClass("hidden");
                            $(".convertToPublicGroup").removeClass("hidden");
                        });
                        $(".confirmConvert").click(function() {
                            $.ajax({
                                method: 'post',
                                url: root+'/users/convertgrouppublic',
                                dataType:"json",
                                data: {id:groupID},
                                success: function(data) {
                                    location.reload();
                                }
                            });
                        });
                    } else {
                        $("#groupType").html("<span>Group type: </span>Public");
                        $(".convertToPrivateGroup").removeClass("hidden");
                        $(".convertToPublicGroup").remove();
                        $(".convertToPrivateGroup").click(function() {
                            $(this).addClass("hidden");
                            $(".confirmConvert").removeClass("hidden");
                            $(".cancelConvert").removeClass("hidden");
                        });
                        $(".cancelConvert").click(function() {
                            $(this).addClass("hidden");
                            $(".confirmConvert").addClass("hidden");
                            $(".convertToPrivateGroup").removeClass("hidden");
                        });
                        
                        $(".confirmConvert").click(function() {
                            $.ajax({
                                method: 'post',
                                url: root+'/users/convertgroupprivate',
                                dataType:"json",
                                data: {id:groupID},
                                success: function(data) {
                                    location.reload();
                                }
                            });
                        });
                    }
                    if(data.leader_me == true && data.privateGroup == 1) {
                        requests = data.active_requests;
                        $("#seeActiveRequests").removeClass("hidden");
                        createRequestList();
                    }
                    me = data.me;
                    members = data.members;
                    createMemberList(data.leader_me);
                    /*
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
                    }*/
                },
                error: function (data) {
                    console.log("error");
                }
            });
        };
    getPostData();
    /*
    
        messageHistory = undefined,
        crumbs = undefined,
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
            $(".conversation .boxWrapper#messages").html(toAppend);
            if(initiated == false) {
                $(".boxWrapper p")[$(".boxWrapper p").length-1].scrollIntoView();
                initiated = true;
            }
        },
        

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
    */
});