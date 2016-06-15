$(document).ready(function() {
    var root = "http://localhost:8080",
        crumbs = undefined,
        groups = undefined,
        lastSearch = "",
        searchResults = undefined,
        manageFilters = [],
        populateGroups = function(data) {
            var toAppend = "";
            $.each(data, function(index,value) {
                value.group_type = (value.private == 1 ? 'private group' : 'public group');
                if(value.visible) {
                    toAppend+='<div class="styled-list-member" listindex="'+index+'">'+
                        '<h2>'+
                            '<a href="'+root+'/groups/'+value.group_id+'">' + value.group_name + '</a> ' +
                            '<span>(' + (value.private == 1 ? 'private' : 'public') + ' group)</span>' +
                            '<span style="float: right; font-size: 14px; font-weight: 600; line-height: 28px;">' + (value.accepted == 1 ?
                                'Member since ' + value.since.split(" ")[0] :
                                'Requested to join since ' + value.since.split(" ")[0]) +
                            '</span>' +
                        '</h2>' +
                        '<p>' +
                        (value.group_description.trim() !== "" ?
                        '<span>Description: </span>' + value.group_description + createManageButtonGroup(value) : createManageButtonGroup(value)) +
                        '</p>'+
                        '<p>' +
                            '<span>Leader: </span>' +
                            '<a href="'+root+'/profile/'+value.leader_id+'">'+value.leader_name+'</a>' +
                            (value.leader_me == 1 ? ' <span>(you)</span>':'') +
                        '</p>' +
                        '<p><span>Member count: </span>' + value.memberCount + '</p>'+
                        '</div>';
                }
            });
            toAppend = toAppend || "<p>No groups found..</p>";
            $(".groupManager").html(toAppend);
            $(".cancelRequestButton").click(function() {
                var index = $(this).closest(".styled-list-member").attr("listindex");
                $.ajax({
                    method:'post',
                    url: root+'/users/cancelrequest',
                    data: {id:data[index].group_id},
                    success: function(datas) {
                        getGroups();
                        filter();
                    },
                    error: function(datas) {
                        console.log(datas);

                    }
                });    
            });
            $(".leaveGroupButton").click(function() {
                $(this).closest("span").children(".leaveGroupConfirmButton").removeClass("hidden");
                $(this).closest("span").children(".leaveGroupCancelButton").removeClass("hidden");
                $(this).addClass("hidden");
            });
            $(".leaveGroupCancelButton").click(function() {
                $(this).closest("span").children(".leaveGroupConfirmButton").addClass("hidden");
                $(this).closest("span").children(".leaveGroupCancelButton").addClass("hidden");
                $(this).closest("span").children(".leaveGroupButton").removeClass("hidden");    
            });
            $(".leaveGroupConfirmButton").click(function() {
                var index = $(this).closest(".styled-list-member").attr("listindex");
                $.ajax({
                    method:'post',
                    url: root+'/users/leavegroup',
                    data: {id:data[index].group_id},
                    success: function(datas) {
                        getGroups();
                        filter();
                    },
                    error: function(datas) {
                        console.log(datas);

                    }
                });
            });
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
        },
        leaveGroupButton = function(data) {
            return "<button class='transparent leaveGroupButton'>Leave group</button>" +
                "<button class='transparent hidden leaveGroupCancelButton'>Cancel</button>"+
                "<button class='transparent hidden leaveGroupConfirmButton'>Confirm</button>";
        },
        createCancelRequestButton = function(value) {
            var toReturn = "<button class='transparent cancelRequestButton'>"+
                "Cancel request" +
                "</button>";
            return toReturn;
        },
        createJoinButton = function(value) {
            var toReturn = "<button class='transparent joinGroupButton'>";
            if(value.private == 1) {
                toReturn+="Join request";
            } else {
                toReturn+="Join group";
            }
            toReturn+="</button>";
            return toReturn;
        },
        createButtonGroup = function(value) {
            return '<span style="float: right; text-align: right">' +
                (value.ownData !== null ?
                        (value.ownData.accepted == 1 ?
                         leaveGroupButton(value) :
                         createCancelRequestButton(value)) 
                     :
                    createJoinButton(value)) +
            '</span>';
        },
        createManageButtonGroup = function(value) {
            return '<span style="float: right; text-align: right">' +
                (value.accepted == 1 ?
                 leaveGroupButton(value) :
                 createCancelRequestButton(value)) +
            '</span>';
        },
        ajaxSearch = function() {
            $.ajax({
                method:'post',
                url: root+"/users/searchgroup",
                dataType: "json",
                data: {search: lastSearch},
                success: function(data) {
                    searchResults = data;
                    populateSearchResults(searchResults);
                },
                error: function(data) {
                    console.log(data);
                }
            });
        },
        filter = function() {
            manageFilters = $("#manageGroupsFilter").val().trim().split(",");
            console.log(manageFilters,groups);
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
        },
        populateSearchResults = function(data) {
            var toAppend = "";
            $.each(data, function(index,value) {
                value.group_type = (value.private == 1 ? 'private group' : 'public group');
                toAppend+='<div class="styled-list-member" listindex="'+index+'">'+
                    '<h2>'+
                        '<a href="'+root+'/groups/'+value.id+'">' + value.group_name + '</a> ' +
                        '<span>(' + (value.private == 1 ? 'private' : 'public') + ' group)</span>' +
                        (value.ownData !== null && value.ownData.accepted == 1 ?
                        '<span style="float: right; font-size: 14px; font-weight: 600; line-height: 28px; text-align: right">' + 
                              'Member since ' + value.ownData.updated_at.split(" ")[0] + 
                        '</span>' : "") +
                    '</h2>' +
                    '<p>' +
                    (value.description.trim() !== "" ?
                    '<span>Description: </span>' + value.description + createButtonGroup(value) : createButtonGroup(value)) +
                    '</p>'+
                    '<p>' +
                        '<span>Leader: </span>' +
                        '<a href="'+root+'/profile/'+value.leader_id+'">'+value.leader_name+'</a>' +
                        (value.leader_me == 1 ? ' <span>(you)</span>':'') +
                    '</p>' +
                    '<p><span>Member count: </span>' + value.memberCount + '</p>'+
                '</div>';
            });
            toAppend = toAppend || "<p>No groups found..</p>";
            $(".searchResults").html(toAppend);
            $(".joinGroupButton").click(function() {
                var index = $(this).closest(".styled-list-member").attr("listindex");
                $.ajax({
                    method:'post',
                    url: root+'/users/joingroup',
                    data: {id:data[index].id},
                    success: function(datas) {
                        if(datas.accepted == 1) {
                            data[index].memberCount += 1;
                        }
                        data[index].ownData = datas;
                        getGroups();
                        filter();
                        populateSearchResults(data);
                    },
                    error: function(datas) {
                        console.log(datas);
                    }
                });
            });
            $(".cancelRequestButton").click(function() {
                var index = $(this).closest(".styled-list-member").attr("listindex");
                $.ajax({
                    method:'post',
                    url: root+'/users/cancelrequest',
                    data: {id:data[index].id},
                    success: function(datas) {
                        ajaxSearch();
                        populateGroups();
                    },
                    error: function(datas) {
                        console.log(datas);

                    }
                });    
            });
            $(".leaveGroupButton").click(function() {
                $(this).closest("span").children(".leaveGroupConfirmButton").removeClass("hidden");
                $(this).closest("span").children(".leaveGroupCancelButton").removeClass("hidden");
                $(this).addClass("hidden");
            });
            $(".leaveGroupCancelButton").click(function() {
                $(this).closest("span").children(".leaveGroupConfirmButton").addClass("hidden");
                $(this).closest("span").children(".leaveGroupCancelButton").addClass("hidden");
                $(this).closest("span").children(".leaveGroupButton").removeClass("hidden");    
            });
            $(".leaveGroupConfirmButton").click(function() {
                var index = $(this).closest(".styled-list-member").attr("listindex");
                $.ajax({
                    method:'post',
                    url: root+'/users/leavegroup',
                    data: {id:data[index].id},
                    success: function(datas) {
                        ajaxSearch();
                        populateGroups();
                    },
                    error: function(datas) {
                        console.log(datas);

                    }
                });
            });
        };
    getGroups();
    $("#manageGroupsFilter").keypress(function(event){
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if(keycode == '13') {
            filter();
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
                data: {name:name,description:description,type:type},
                success: function (data) {
                    getGroups();
                    $("#linkToCreatedGroup").attr("href",root+"/groups/"+data.id);
                    $("#groupCreatedMessage").slideDown();
                    $("#createGroupName").val("");
                    $("#createGroupDescription").val("");
                    filter();
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
        var searchValue = $("#searchKeywords").val().trim();
        lastSearch = searchValue;
        ajaxSearch();
    });
});