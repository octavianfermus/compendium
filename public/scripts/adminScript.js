/*global
    $,
    globalSettings,
    console
*/
$(document).ready(function () {
    'use strict';
    var userlist,
        reports,
        actionTarget,
        actionName,
        reportsActionTarget,
        reportsActionName,
        userListFilter = [],
        root = globalSettings.getRoot(),
        convertUserType = function (userType) {
            switch (userType) {
            case 0:
                return "<span style='color: red'>Banned</span>";
            case 1:
                return "<span>Normal user</span>";
            case 2:
                return "<span>Moderator</span>";
            case 3:
                return "<span>Administrator</span>";
            }
        },
        showAnsweredReports = function () {
            var toAppend = '<table class="invisi-table">';
            $.each(reports, function (index, value) {
                if (value.answered === 1) {
                    console.log(value);
                }
            });
            toAppend += '</table>';
            //$(".tab-pane#user-list .content").html(toAppend);
        },
        reportDetail = function (value) {
            switch (value.reportedType) {
            case "Algorithm":
                return '<p>' +
                        '<span>Link: </span><a href="' + root + value.linkTo + '">' + value.linkName + '</a>' +
                    '</p>';
            case "Request":
                return '<p>' +
                        '<span>Request name: </span>' + value.requestName +
                    '</p>' +
                    '<p>' +
                        '<span>Request description: </span>' + value.requestDescription +
                    '</p>' +
                    '<p>' +
                        '<span>Request language: </span>' + value.requestLanguage +
                    '</p>';
            case "Line Comment":
                return '<p>' +
                        '<span>Link: </span><a href="' + root + value.linkTo + '">' + value.linkName + '</a>' +
                        '<span> on line </span>' + (value.line + 1) +
                    '</p>' +
                    '<p>' +
                        '<span>Comment: </span>' + value.text +
                    '</p>';
            case "Algorithm Comment":
                return '<p>' +
                        '<span>Link: </span><a href="' + root + value.linkTo + '">' + value.linkName + '</a>' +
                        '</p>' +
                    '<p>' +
                        '<span>Comment: </span>' + value.text +
                    '</p>';
            case "Profile Comment":
                return '<p>' +
                        '<span>Link: </span><a href="' + root + value.linkTo + '">' + value.linkName + '</a>' +
                        '</p>' +
                    '<p>' +
                        '<span>Comment: </span>' + value.text +
                    '</p>';
            case "Algorithm Reply":
                return '<p>' +
                        '<span>Link: </span><a href="' + root + value.linkTo + '">' + value.linkName + '</a>' +
                        '</p>' +
                    '<p>' +
                        '<span>Comment: </span>' + value.text +
                    '</p>';
            case "Profile Reply":
                return '<p>' +
                        '<span>Link: </span><a href="' + root + value.linkTo + '">' + value.linkName + '</a>' +
                        '</p>' +
                    '<p>' +
                        '<span>Comment: </span>' + value.text +
                    '</p>';
            case "Profile":
                return '<p>' +
                        '<span>Link: </span><a href="' + root + value.linkTo + '">' + value.linkName + '</a>' +
                        '</p>';
            }
        },
        
        setAsAnsweredButton = function () {
            return '<button class="transparent setAsAnswered">Set as answered</button>';
        },
        warnUsersButton = function (value) {
            var toReturn = "";
            if (value.reported_warned === 0) {
                toReturn += '<button class="transparent warnReportedUser">Warn reported</button>';
            }
            if (value.reporter_warned === 0) {
                toReturn += '<button class="transparent warnReporter">Warn reporter</button>';
            }
            if (value.reporter_warned === 0 && value.reported_warned === 0) {
                toReturn += '<button class="transparent warnBoth">Warn both users</button>';
            }
            return toReturn;
        },
        banUsersButton = function (value) {
            var toReturn = "";
            if (value.reported_type !== 0) {
                toReturn += '<button class="transparent banReportedUser">Ban reported</button>';
            }
            if (value.reporter_type !== 0) {
                toReturn += '<button class="transparent banReporter">Ban reporter</button>';
            }
            if (value.reporter_type !== 0 && value.reported_type !== 0) {
                toReturn += '<button class="transparent banBoth">Ban both users</button>';
            }
            return toReturn;
        },
        deleteContentButton = function () {
            return '<button class="transparent deleteContent">Delete content</button>';
        },
        createUnansweredReportListButtons = function (value) {
            return warnUsersButton(value) + banUsersButton(value) + deleteContentButton() + setAsAnsweredButton() + confirmCancelButtons();
        },
        createUnansweredReportListEvents = function () {
            $(".styled-list-member .cancelAction").click(function () {
                $(".styled-list-member .transparent").removeClass("hidden");
                $(".styled-list-member .transparent.cancelAction").addClass("hidden");
                $(".styled-list-member .transparent.confirmAction").addClass("hidden");
            });
            $(".styled-list-member .banReportedUser").click(function () {
                reportsActionTarget = $(this).closest(".styled-list-member").attr("listindex");
                reportsActionName = "banReportedUser";
                $(".styled-list-member .transparent").removeClass("hidden");
                $(".styled-list-member .transparent.cancelAction").addClass("hidden");
                $(".styled-list-member .transparent.confirmAction").addClass("hidden");
                $(".styled-list-member[listindex='" + reportsActionTarget + "'] .transparent").addClass("hidden");
                $(".styled-list-member[listindex='" + reportsActionTarget + "'] .transparent.cancelAction").removeClass("hidden");
                $(".styled-list-member[listindex='" + reportsActionTarget + "'] .transparent.confirmAction").removeClass("hidden");
            });
            $(".styled-list-member .banReporter").click(function () {
                reportsActionTarget = $(this).closest(".styled-list-member").attr("listindex");
                reportsActionName = "banReporter";
                $(".styled-list-member .transparent").removeClass("hidden");
                $(".styled-list-member .transparent.cancelAction").addClass("hidden");
                $(".styled-list-member .transparent.confirmAction").addClass("hidden");
                $(".styled-list-member[listindex='" + reportsActionTarget + "'] .transparent").addClass("hidden");
                $(".styled-list-member[listindex='" + reportsActionTarget + "'] .transparent.cancelAction").removeClass("hidden");
                $(".styled-list-member[listindex='" + reportsActionTarget + "'] .transparent.confirmAction").removeClass("hidden");
            });
            $(".styled-list-member .banBoth").click(function () {
                reportsActionTarget = $(this).closest(".styled-list-member").attr("listindex");
                reportsActionName = "banBoth";
                $(".styled-list-member .transparent").removeClass("hidden");
                $(".styled-list-member .transparent.cancelAction").addClass("hidden");
                $(".styled-list-member .transparent.confirmAction").addClass("hidden");
                $(".styled-list-member[listindex='" + reportsActionTarget + "'] .transparent").addClass("hidden");
                $(".styled-list-member[listindex='" + reportsActionTarget + "'] .transparent.cancelAction").removeClass("hidden");
                $(".styled-list-member[listindex='" + reportsActionTarget + "'] .transparent.confirmAction").removeClass("hidden");
            });
            $(".styled-list-member .warnReportedUser").click(function () {
                reportsActionTarget = $(this).closest(".styled-list-member").attr("listindex");
                reportsActionName = "warnReportedUser";
                $(".styled-list-member .transparent").removeClass("hidden");
                $(".styled-list-member .transparent.cancelAction").addClass("hidden");
                $(".styled-list-member .transparent.confirmAction").addClass("hidden");
                $(".styled-list-member[listindex='" + reportsActionTarget + "'] .transparent").addClass("hidden");
                $(".styled-list-member[listindex='" + reportsActionTarget + "'] .transparent.cancelAction").removeClass("hidden");
                $(".styled-list-member[listindex='" + reportsActionTarget + "'] .transparent.confirmAction").removeClass("hidden");
            });
            $(".styled-list-member .warnReporter").click(function () {
                reportsActionTarget = $(this).closest(".styled-list-member").attr("listindex");
                reportsActionName = "warnReporter";
                $(".styled-list-member .transparent").removeClass("hidden");
                $(".styled-list-member .transparent.cancelAction").addClass("hidden");
                $(".styled-list-member .transparent.confirmAction").addClass("hidden");
                $(".styled-list-member[listindex='" + reportsActionTarget + "'] .transparent").addClass("hidden");
                $(".styled-list-member[listindex='" + reportsActionTarget + "'] .transparent.cancelAction").removeClass("hidden");
                $(".styled-list-member[listindex='" + reportsActionTarget + "'] .transparent.confirmAction").removeClass("hidden");
            });
            $(".styled-list-member .warnBoth").click(function () {
                reportsActionTarget = $(this).closest(".styled-list-member").attr("listindex");
                reportsActionName = "warnBoth";
                $(".styled-list-member .transparent").removeClass("hidden");
                $(".styled-list-member .transparent.cancelAction").addClass("hidden");
                $(".styled-list-member .transparent.confirmAction").addClass("hidden");
                $(".styled-list-member[listindex='" + reportsActionTarget + "'] .transparent").addClass("hidden");
                $(".styled-list-member[listindex='" + reportsActionTarget + "'] .transparent.cancelAction").removeClass("hidden");
                $(".styled-list-member[listindex='" + reportsActionTarget + "'] .transparent.confirmAction").removeClass("hidden");
            });
            
            $(".styled-list-member .setAsAnswered").click(function () {
                reportsActionTarget = $(this).closest(".styled-list-member").attr("listindex");
                reportsActionName = "setAsAnswered";
                $(".styled-list-member .transparent").removeClass("hidden");
                $(".styled-list-member .transparent.cancelAction").addClass("hidden");
                $(".styled-list-member .transparent.confirmAction").addClass("hidden");
                $(".styled-list-member[listindex='" + reportsActionTarget + "'] .transparent").addClass("hidden");
                $(".styled-list-member[listindex='" + reportsActionTarget + "'] .transparent.cancelAction").removeClass("hidden");
                $(".styled-list-member[listindex='" + reportsActionTarget + "'] .transparent.confirmAction").removeClass("hidden");
            });
            $(".styled-list-member .confirmAction").click(function () {
                switch (reportsActionName) {
                case "banReportedUser":
                    $.ajax({
                        method: 'put',
                        url: root + '/users/banuser',
                        data: {id: reports[reportsActionTarget].reported_id},
                        success: function (data) {
                            getAdminData();
                        }
                    });
                    break;
                case "banReporter":
                    $.ajax({
                        method: 'put',
                        url: root + '/users/banuser',
                        data: {id: reports[reportsActionTarget].reporter_id},
                        success: function (data) {
                            getAdminData();
                        }
                    });
                    break;
                case "banBoth":
                    $.ajax({
                        method: 'put',
                        url: root + '/users/banuser',
                        data: {id: reports[reportsActionTarget].reported_id},
                        success: function (data) {
                            $.ajax({
                                method: 'put',
                                url: root + '/users/banuser',
                                data: {id: reports[reportsActionTarget].reporter_id},
                                success: function (data) {
                                    getAdminData();
                                }
                            });
                        }
                    });
                    break;
                case "warnReportedUser":
                    $.ajax({
                        method: 'put',
                        url: root + '/users/warn',
                        data: {id: reports[reportsActionTarget].id, warn_id: reports[reportsActionTarget].reported_id},
                        success: function (data) {
                            getAdminData();
                        }
                    });
                    break;
                case "warnReporter":
                    $.ajax({
                        method: 'put',
                        url: root + '/users/warn',
                        data: {id: reports[reportsActionTarget].id, warn_id: reports[reportsActionTarget].reporter_id},
                        success: function (data) {
                            getAdminData();
                        }
                    });
                    break;
                case "warnBoth":
                    $.ajax({
                        method: 'put',
                        url: root + '/users/warn',
                        data: {id: reports[reportsActionTarget].id, warn_id: reports[reportsActionTarget].reported_id},
                        success: function (data) {
                            $.ajax({
                                method: 'put',
                                url: root + '/users/warn',
                                data: {id: reports[reportsActionTarget].id, warn_id: reports[reportsActionTarget].reporter_id},
                                success: function (data) {
                                    getAdminData();
                                }
                            });
                        }
                    });
                    break;
                case "setAsAnswered":
                    $.ajax({
                        method: 'put',
                        url: root + '/users/setasanswered',
                        data: {id: reports[reportsActionTarget].id},
                        success: function (data) {
                            getAdminData();
                        }
                    });
                    break;
                }
            });
        },
        otherInformation = function(value) {
            var toReturn = "";
            if(value.reported_type == 0 || value.reporter_type == 0 || value.reporter_warns > 0 || value.reported_warns > 0) {
                toReturn += '<p><span>Other information</span><br>';
                if(value.reported_type ==0) {
                    toReturn+= value.reported_name + ' is currently banned.<br>';
                } else {
                    if(value.reported_warns) {
                    toReturn+= value.reported_name + ' currently has ' + value.reported_warns + ' warnings.<br>';    
                    }
                }
                if(value.reporter_type ==0) {
                    toReturn+= value.reporter_name + ' is currently banned.<br>';
                } else {
                    if(value.reporter_warns) {
                    toReturn+= value.reporter_name + ' currently has ' + value.reporter_warns + ' warnings.<br>';    
                    }
                }
                toReturn+='</p>';
                return toReturn;
            } else {
                return "";
            }
        },
        showUnansweredReports = function () {
            var toAppend = '';
            $.each(reports, function (index, value) {
                if (value.answered === 0) {
                    var appendChecker = true;
                    if (!$("#manage-reports [button-filter='Algorithm']").hasClass("checked") && value.reportedType === "Algorithm") {
                        appendChecker = false;
                    }
                    if (!$("#manage-reports [button-filter='Line Comment']").hasClass("checked") && value.reportedType === "Line Comment") {
                        appendChecker = false;
                    }
                    if (!$("#manage-reports [button-filter='Request']").hasClass("checked") && value.reportedType === "Request") {
                        appendChecker = false;
                    }
                    if (!$("#manage-reports [button-filter='Algorithm Comment']").hasClass("checked") && value.reportedType === "Algorithm Comment") {
                        appendChecker = false;
                    }
                    if (!$("#manage-reports [button-filter='Algorithm Reply']").hasClass("checked") && value.reportedType === "Algorithm Reply") {
                        appendChecker = false;
                    }
                    if (!$("#manage-reports [button-filter='Profile Comment']").hasClass("checked") && value.reportedType === "Profile Comment") {
                        appendChecker = false;
                    }
                    
                    if (!$("#manage-reports [button-filter='Profile Reply']").hasClass("checked") && value.reportedType === "Profile Reply") {
                        appendChecker = false;
                    }
                    if (!$("#manage-reports [button-filter='Profile']").hasClass("checked") && value.reportedType === "Profile") {
                        appendChecker = false;
                    }
                    if (appendChecker) {
                        toAppend +=
                            '<div class="styled-list-member" style="padding-bottom: 15px" listindex="' + index + '">' +
                                '<h2 style="font-size: 17px; font-weight: 600">' +
                                    '<a href="' + root + '/profile/' + value.reporter_id + '">' + value.reporter_name + '</a>' +
                                    '<span> reported </span>' +
                                    '<a href="' + root + '/profile/' + value.reported_id + '">' + value.reported_name + '</a>' +
                                    '<span style="float: right">' + value.created_at.split(" ")[0] + '</span>' +
                                '</h2>' +
                                '<p>' +
                                    '<span>Reported content type and reason: </span>' + value.reportedType +
                                    '<span> for </span>' + value.reason +
                                '</p>' +
                                '<p>' +
                                    '<span>Description: </span>' +
                                        (value.description.trim() || "None provided") +
                                '</p>' +
                                '<div style="border: 1px solid gray;padding: 0;margin: 0 0 0 15px;">' +
                                    reportDetail(value) +
                                '</div>' +
                                otherInformation(value) +
                                '<div style="text-align: right; padding: 0;margin: 5px 0 0px 8px;">' +
                                    createUnansweredReportListButtons(value) +
                                '</div>' +
                            '</div>';
                    }
                }
            });
            $(".tab-pane#manage-reports .content").html(toAppend || "No unanswered reports.");
            createUnansweredReportListEvents();
        },
        unbanUserButton = function () {
            return "<button class='transparent unbanUser'>Unban this user</button>";
        },
        banUserButton = function () {
            return "<button class='transparent banUser'>Ban this user</button>";
        },
        demoteToUserButton = function () {
            return "<button class='transparent demoteToUser'>Demote to normal user</button>";
        },
        promoteToModButton = function () {
            return "<button class='transparent promoteToMod'>Promote to moderator</button>";
        },
        confirmCancelButtons = function () {
            return "<button class='transparent cancelAction hidden'>Cancel</button'><button class='transparent confirmAction hidden'>Confirm</button'>";
        },
        createUserListButtons = function (value) {
            var yourType = globalSettings.getUserData().user_type,
                usersType = value.user_type;
            if (usersType === 3) {
                return "<strong>You cannot act upon an administrator</strong>";
            }
            if (usersType === 2) {
                if (yourType === 3) {
                    return banUserButton() + demoteToUserButton() + confirmCancelButtons();
                } else {
                    return "<strong>You cannot act upon a moderator</strong>";
                }
            }
            if (usersType === 1) {
                return banUserButton() + promoteToModButton() + confirmCancelButtons();
            }
            if (usersType === 0) {
                return unbanUserButton() + confirmCancelButtons();
            }
        },
        createUserListEvents = function () {
            $("tr .demoteToUser").click(function () {
                actionTarget = $(this).closest("tr").attr("listindex");
                actionName = "demote";
                $("tr .transparent").removeClass("hidden");
                $("tr .transparent.cancelAction").addClass("hidden");
                $("tr .transparent.confirmAction").addClass("hidden");
                $("tr[listindex='" + actionTarget + "'] .transparent").addClass("hidden");
                $("tr[listindex='" + actionTarget + "'] .transparent.cancelAction").removeClass("hidden");
                $("tr[listindex='" + actionTarget + "'] .transparent.confirmAction").removeClass("hidden");
            });
            $("tr .promoteToMod").click(function () {
                actionTarget = $(this).closest("tr").attr("listindex");
                actionName = "promote";
                $("tr .transparent").removeClass("hidden");
                $("tr .transparent.cancelAction").addClass("hidden");
                $("tr .transparent.confirmAction").addClass("hidden");
                $("tr[listindex='" + actionTarget + "'] .transparent").addClass("hidden");
                $("tr[listindex='" + actionTarget + "'] .transparent.cancelAction").removeClass("hidden");
                $("tr[listindex='" + actionTarget + "'] .transparent.confirmAction").removeClass("hidden");
            });
            $("tr .unbanUser").click(function () {
                actionTarget = $(this).closest("tr").attr("listindex");
                actionName = "unban";
                $("tr .transparent").removeClass("hidden");
                $("tr .transparent.cancelAction").addClass("hidden");
                $("tr .transparent.confirmAction").addClass("hidden");
                $("tr[listindex='" + actionTarget + "'] .transparent").addClass("hidden");
                $("tr[listindex='" + actionTarget + "'] .transparent.cancelAction").removeClass("hidden");
                $("tr[listindex='" + actionTarget + "'] .transparent.confirmAction").removeClass("hidden");
            });
            $("tr .banUser").click(function () {
                actionTarget = $(this).closest("tr").attr("listindex");
                actionName = "ban";
                $("tr .transparent").removeClass("hidden");
                $("tr .transparent.cancelAction").addClass("hidden");
                $("tr .transparent.confirmAction").addClass("hidden");
                $("tr[listindex='" + actionTarget + "'] .transparent").addClass("hidden");
                $("tr[listindex='" + actionTarget + "'] .transparent.cancelAction").removeClass("hidden");
                $("tr[listindex='" + actionTarget + "'] .transparent.confirmAction").removeClass("hidden");
            });
            $("tr .cancelAction").click(function () {
                $("tr .transparent").removeClass("hidden");
                $("tr .transparent.cancelAction").addClass("hidden");
                $("tr .transparent.confirmAction").addClass("hidden");
            });
            $("tr .confirmAction").click(function () {
                switch (actionName) {
                case "demote":
                    $.ajax({
                        method: 'put',
                        url: root + '/users/demoteuser',
                        data: {id: userlist[actionTarget].id},
                        success: function (data) {
                            getAdminData();
                        }
                    });
                    break;
                case "promote":
                    $.ajax({
                        method: 'put',
                        url: root + '/users/promoteuser',
                        data: {id: userlist[actionTarget].id},
                        success: function (data) {
                            getAdminData();
                        }
                    });
                    break;
                case "ban":
                    $.ajax({
                        method: 'put',
                        url: root + '/users/banuser',
                        data: {id: userlist[actionTarget].id},
                        success: function (data) {
                            getAdminData();
                        }
                    });
                    break;
                case "unban":
                    $.ajax({
                        method: 'put',
                        url: root + '/users/unbanuser',
                        data: {id: userlist[actionTarget].id},
                        success: function (data) {
                            getAdminData();
                        }
                    });
                    break;
                }
            });
        },
        showUserList = function () {
            var toAppend = '<table class="invisi-table">';
            $.each(userlist, function (index, value) {
                var appendChecker = true;
                if (!$("#user-list [button-filter='banned']").hasClass("checked") && value.user_type === 0) {
                    appendChecker = false;
                }
                if (!$("#user-list [button-filter='normal']").hasClass("checked") && value.user_type === 1) {
                    appendChecker = false;
                }
                if (!$("#user-list [button-filter='moderators']").hasClass("checked") && value.user_type === 2) {
                    appendChecker = false;
                }
                if (!$("#user-list [button-filter='administrators']").hasClass("checked") && value.user_type === 3) {
                    appendChecker = false;
                }
                if (appendChecker) {
                    toAppend +=
                        '<tr listindex="' + index + '">' +
                        '<td><strong><a href="'+root+'/profile/'+value.id+'">' + value.last_name + ' ' + value.first_name + '</a></strong></td>' +
                        '<td><em>' + convertUserType(value.user_type) + '</em></td>' +
                        '<td>' + value.warnCount + ' warnings</td>' +
                        '<td>' +
                            (createUserListButtons(value)) +
                        '</td>' +
                        '</tr>';
                }
            });
            toAppend += '</table>';
            if (toAppend === '<table class="invisi-table"></table>') {
                toAppend = "No users found";
            }
            $(".tab-pane#user-list .content").html(toAppend);
            createUserListEvents();
        },
        populate = function () {
            showUnansweredReports();
            showAnsweredReports();
            showUserList();
        },
        getAdminData = function () {
            $.ajax({
                method: 'get',
                url: root + '/users/admindata',
                success: function (data) {
                    if (data.state === "success") {
                        userlist = data.data.userlist;
                        reports = data.data.reports;
                        populate();
                    }
                }
            });
        };
    $("#user-list [button-filter]").click(function () {
        if ($(this).hasClass('checked')) {
            $(this).removeClass('checked');
        } else {
            $(this).addClass('checked');
        }
        showUserList();
    });
    $("#manage-reports [button-filter]").click(function () {
        if($(this).attr("button-filter")==="All") {
            if (!$(this).hasClass('checked')) {
                $("#manage-reports [button-filter]").addClass("checked");
                showUnansweredReports();
            } else {
                $("#manage-reports [button-filter]").removeClass("checked");
                showUnansweredReports();
            }
        } else {
            if ($(this).hasClass('checked')) {
                $("#manage-reports [button-filter='All']").removeClass("checked");
                $(this).removeClass('checked');
            } else {
                $(this).addClass('checked');
                if($("#manage-reports [button-filter]").length - 1 === $("#manage-reports .checked[button-filter]").length) {
                    $("#manage-reports [button-filter='All']").addClass("checked");
                }
            }
            showUnansweredReports();
        }
    });
    getAdminData();
    
});