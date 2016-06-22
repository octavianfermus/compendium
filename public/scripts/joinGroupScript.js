$(document).ready(function() {
    var groupID = window.location.href.split("#")[0].split("/")[window.location.href.split("/").length-1],
        root = "http://localhost:8080",
        members = [],
        requested,
        me,  
        crumbs = undefined,
        populateCrumbs = function(data) {
            var toAppend ="",
            crumbs = data;
            $.each(crumbs, function(index, value) {

                toAppend +='<li '+(value.group_id == groupID ? 'class="selected"':(value.read == 0 ? 'class="notSeen"': ""))+'>'+
                    '<a href="'+root+'/groups/'+value.group_id +'">'+
                    '<span>'+value.group_name+'</span>'+
                    '<span style="float:right; margin-right: 4px" class="newSpan">'+(value.read == 0 ? 'New!' : "")+'</span>' +
                    '</a>'+
                    '</li>';
            });
            $(".sidebar ul.messageList").html(toAppend);
        },
        crumbAjax = function() {
            $.ajax({
                method: 'get',
                url: root+'/users/groupcrumb',
                success: function(data) {
                    populateCrumbs(data.crumb);
                },
                error: function(data) {
                    console.log(data);
                }
            });
        },
        createMemberList = function(leader) {
            var toAppend = "";
            $.each(members,function(index,value) {
                 toAppend += '<div class="styled-list-member" listindex="'+index+'">' +
                    '<h2>'+
                        '<a href="'+root+'/profile/'+value.id+'">'+value.last_name + " " +value.first_name+'</a>'+
                        '<span style="float: right; font-size: 14px; font-weight: 600; line-height: 28px;">Member since '+value.since.split(" ")[0]+'</span>'+
                    '</h2>' +
                    '<p>'+
                        '<a href="'+root+'/messages/'+value.id+'" class="transparent">Send message</a>' +
                    '</p>'+
                '</div>';
            });
            $("#membersModal .boxWrapper").html(toAppend);    
        },
        createJoinButton = function(value) {
            if(value.private == 1) {
                if(value.requested == 1) {
                    return "<button class='transparent cancelRequestButton'>Cancel request</button>";
                } else {
                    return "<button class='transparent joinGroupButton'>Join request</button>";
                }
            }
            return "<button class='transparent joinGroupButton'>Join group</button>";
        },
        cancelAjax = function() {
            $.ajax({
                method:'delete',
                url: root+'/messaging/cancelrequest',
                data: {id:groupID},
                success: function(datas) {
                    requested = 0;
                    $("#buttons").html(createJoinButton({
                        private: 1,
                        requested: requested
                    }));
                    $(".joinGroupButton").click(function() {
                        joinAjax();
                    });
                    $(".cancelRequestButton").click(function() {
                        cancelAjax();    
                    });
                },
                error: function(datas) {
                    console.log(datas);
                }
            });
        },
        joinAjax = function() {
            $.ajax({
                method:'post',
                url: root+'/messaging/joingroup',
                data: {id:groupID},
                success: function(datas) {
                    if(datas.accepted == 1) {
                        location.reload();
                    } else {
                        requested = 1;
                        $("#buttons").html(createJoinButton({
                            private: 1,
                            requested: requested
                        }));
                        $(".joinGroupButton").click(function() {
                            joinAjax();
                        });
                        $(".cancelRequestButton").click(function() {
                            cancelAjax();    
                        });
                    }
                },
                error: function(datas) {
                    console.log(datas);
                }
            });
        },
        getPostData = function () {
            jQuery.ajax({
                method: 'get',
                url: root+"/users/groupinitialdata?id="+groupID,
                success: function (data) {
                    $("#groupName").html(data.groupName);
                    if(data.groupDescription) {
                        $("#groupDescription").removeClass("hidden").html(description);
                    }
                    $("#groupLeader").html("<span>Leader: </span><a href='"+root+"/profile/"+data.leader_id+"'>" + data.leader_name + (data.leader_me == true ? " (You)</a>" : "</a>"));
                    if(data.privateGroup == 1) {
                        $("#groupType").html("<span>Group type: </span>Private");
                        $("#buttons").html(createJoinButton({
                            private: 1,
                            requested: requested
                        }));
                    } else {
                        $("#groupType").html("<span>Group type: </span>Public");
                        $("#buttons").html(createJoinButton({
                            private: 0
                        }));
                    }
                    if(data.leader_me == true && data.privateGroup == 1) {
                        requests = data.active_requests;
                        $("#seeActiveRequests").removeClass("hidden");
                        createRequestList();
                    }
                    me = data.me;
                    members = data.members;
                    createMemberList(data.leader_me);
                    $(".joinGroupButton").click(function() {
                        joinAjax();
                    });
                    $(".cancelRequestButton").click(function() {
                        cancelAjax();    
                    });
                },
                error: function (data) {
                    console.log("error");
                }
            });
        };
    getPostData();
    crumbAjax();
    setInterval(function() {crumbAjax();}, 10000);
});