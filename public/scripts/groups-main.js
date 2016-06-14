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
        var searchValue = $("#searchKeywords").val().trim();
        $.ajax({
            method:'post',
            url: root+"/users/searchgroup",
            dataType: "json",
            data: {search: searchValue},
            success: function(data) {
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
                $(".searchResults").html(toAppend);
            },
            error: function(data) {
                console.log(data);
            }
        });
    });
});