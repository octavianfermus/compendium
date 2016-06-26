/*global
    $,
    globalSettings,
    console
*/

$(document).ready(function () {
    
    'use strict';
    
    var profileId = window.location.href.split("#")[0].split("/")[window.location.href.split("/").length - 1],
        profileData,
        root = globalSettings.getRoot(),
        algorithms,
        comments,
        userData,
        reportType,
        lastReportIndex,
        lastReportSecIndex,
        statistics,
        initiated = 0,
        voteProfileReplyAjax = function (vote, comment_id, profile_id, tabindex, sectabindex) {
            $.ajax({
                method: 'post',
                url: root + "/users/votereply",
                dataType: "json",
                data: {comment_id: comment_id, vote: vote, profile_id: profile_id},
                success: function (data) {
                    $(".reply[tabindex='" + tabindex + "'] .reply[sectabindex='" + sectabindex + "'] .likeReply .green").html("(" + data.upvotes + ")");
                    $(".reply[tabindex='" + tabindex + "']  .reply[sectabindex='" + sectabindex + "'] .dislikeReply .red").html("(" + data.downvotes + ")");
                },
                error: function (data) {
                    console.log(data);
                }
            });
        },
        voteProfileCommentAjax = function (vote, comment_id, profile_id, tabindex) {
            $.ajax({
                method: 'post',
                url: root + "/users/votecomment",
                dataType: "json",
                data: {comment_id: comment_id, vote: vote, profile_id: profile_id},
                success: function (data) {
                    $(".reply[tabindex='" + tabindex + "'] .likeComment .green").html("(" + data.upvotes + ")");
                    $(".reply[tabindex='" + tabindex + "'] .dislikeComment .red").html("(" + data.downvotes + ")");
                },
                error: function (data) {
                    console.log(data);
                }
            });
        },
        
        populateComments = function () {
            var toAppend = "";
            $.each(comments, function (index, value) {
                value.text = value.text.split("\n").join("<br>");
                toAppend += '<div class="reply" tabindex="' + index + '" parent="parent" id="comment' + value.id + '">' +
                    '<p><span class="person"><a href="../profile/' + value.user_id + '">' + value.name + '</a></span> <span class="created"> on ' + value.created_at + '</span></p>' +
                    '<p>' + (value.deleted === 1 ? '<em>This comment was deleted.</em>' : value.text) + '</p>' +
                    (value.deleted === 0 ?
                            '<p><a href="javascript:void(0)" class="likeComment">Like <span class="green">(' + value.upvotes + ')</span></a> | <a href="javascript:void(0)" class="dislikeComment">Dislike <span class="red">(' + value.downvotes + ')</span></a> | ' + (value.canDelete === false ? (value.reported === 0 ? '| <a href="javascript:void(0)" class="reportComment">Report</a>' : "<p><strong><em>Your report was successfully submitted. Thank you!</em></strong></p>") : '<a href="javascript:void(0)" class="deleteComment">Delete</a>') + '</p><hr>' : "<hr>");
                $.each(value.replies, function (secIndex, secValue) {
                    toAppend += '<div class="reply" sectabindex="' + secIndex + '" parent="noparent" id="comment' + value.id + "_" + secValue.id + '">' +
                            '<p><span class="person"><a href="../profile/' + secValue.user_id + '">' + secValue.name + '</a></span> <span class="created"> on ' + secValue.created_at + '</span></p>';
                    secValue.text = secValue.text.split("\n").join("<br>");
                    toAppend += '<p>' + (secValue.deleted === 1 ? '<em>This comment was deleted.</em>' : secValue.text) + '</p>' +
                        (secValue.deleted === 0 ?
                                '<p><a href="javascript:void(0)" class="likeReply">Like <span class="green">(' + secValue.upvotes + ')</span></a> | <a href="javascript:void(0)" class="dislikeReply">Dislike <span class="red">(' + secValue.downvotes + ')</span></a> | ' + (secValue.canDelete === false ? (secValue.reported === 0 ? '| <a href="javascript:void(0)" class="reportReply">Report</a>' : "<p><strong><em>Your report was successfully submitted. Thank you!</em></strong></p>") :  '<a href="javascript:void(0)" class="deleteReply">Delete</a>') + '</p>' : "") +
                        ' <hr>' +
                        '</div>';
                
                });
                toAppend += '<p><a href="javascript:void(0)" class="replyComment">Reply to this</a></p></div>';
            });
            $("#profileComments").html(toAppend);
            $(".reportComment").click(function () {
                lastReportIndex = $(this).closest(".reply[parent='parent']").attr("tabindex");
                reportType = 'profile_discussion';
                $("#reportModal").modal('toggle');
            });
            $(".reportReply").click(function () {
                lastReportIndex = $(this).closest(".reply[parent='parent']").attr("tabindex");
                lastReportSecIndex = $(this).closest(".reply[parent='noparent']").attr("sectabindex");
                reportType = 'profile_discussion_replies';
                $("#reportModal").modal('toggle');
            });
            $(".deleteComment").click(function () {
                var tabindex = $(this).closest(".reply[parent='parent']").attr("tabindex");
                $.ajax({
                    method: 'post',
                    url: root + "/users/deletecomment",
                    dataType: "json",
                    data: {id: comments[tabindex].id},
                    success: function (data) {
                        comments[tabindex].deleted = 1;
                        populateComments();
                    },
                    error: function (data) {
                        console.log(data);
                    }
                });
            });
            $(".deleteReply").click(function () {
                var tabindex = $(this).closest(".reply[parent='parent']").attr("tabindex"),
                    sectabindex = $(this).closest(".reply[parent='noparent']").attr("sectabindex");
                $.ajax({
                    method: 'post',
                    url: root + "/users/deletereply",
                    dataType: "json",
                    data: {id: comments[tabindex].replies[sectabindex].id},
                    success: function (data) {
                        comments[tabindex].replies[sectabindex].deleted = 1;
                        populateComments();
                    },
                    error: function (data) {
                        console.log(data);
                    }
                });
            });
            $(".dislikeComment").click(function () {
                var tabindex = $(this).closest(".reply[parent='parent']").attr("tabindex");
                voteProfileCommentAjax(0, comments[tabindex].id, profileId, tabindex);
            });
            $(".likeComment").click(function () {
                var tabindex = $(this).closest(".reply[parent='parent']").attr("tabindex");
                voteProfileCommentAjax(1, comments[tabindex].id, profileId, tabindex);
            });
            $(".dislikeReply").click(function () {
                var tabindex = $(this).closest(".reply[parent='parent']").attr("tabindex"),
                    sectabindex = $(this).closest(".reply[parent='noparent']").attr("sectabindex");
                voteProfileReplyAjax(0, comments[tabindex].replies[sectabindex].id, profileId, tabindex, sectabindex);
            });
            $(".likeReply").click(function () {
                var tabindex = $(this).closest(".reply[parent='parent']").attr("tabindex"),
                    sectabindex = $(this).closest(".reply[parent='noparent']").attr("sectabindex");
                voteProfileReplyAjax(1, comments[tabindex].replies[sectabindex].id, profileId, tabindex, sectabindex);
            });
            $(".replyComment").click(function () {
                var tabindex = $(this).closest(".reply[parent='parent']").attr("tabindex"),
                    toAppend = "";
                if ($(".reply[tabindex='" + tabindex + "'] .replyToComment").length === 0) {
                    toAppend = '<div class="replyToComment">' +
                        '<div class="input-group">' +
                        '<textarea placeholder="Write your message here" aria-describedby="sendButton" class="form-control"></textarea>' +
                        '<span class="input-group-addon" id="sendButton"><button class="btn">Send</button></span>' +
                        '</div></div>';
                    $(".reply[tabindex='" + tabindex + "'][parent='parent']").append(toAppend);
                    $(".reply[tabindex='" + tabindex + "'][parent='parent'] .btn").click(function () {
                        var comment = $(".reply[tabindex='" + tabindex + "'][parent='parent'] textarea").val().trim();
                        if (comment.length > 0) {
                            $(".send-message textarea").val("");
                            $.ajax({
                                method: 'post',
                                url: root + "/users/reply",
                                dataType: "json",
                                data: {id: profileId, commentid: comments[tabindex].id, comment: comment},
                                success: function (data) {
                                    comments = data;
                                    populateComments();
                                    $(".reply[tabindex='" + tabindex + "'] .replyToComment").remove();
                                },
                                error: function (data) {

                                }
                            });
                        }
                    });
                } else {
                    $(".reply[tabindex='" + tabindex + "'] .replyToComment").remove();
                }
            });
            if (initiated === 0 && window.location.href.split("#comment").length > 1) {
                window.location.href = window.location.href;
                initiated = 1;
            }
        },
        populateStatistics = function () {
            $("#statistics tbody").append("<tr><td>Algorithm comments:</td><td>" + statistics.algorithm_comments + "</td></tr>");
            $("#statistics tbody").append("<tr><td>Algorithm likes:</td><td>" + statistics.algorithm_likes + "</td></tr>");
            $("#statistics tbody").append("<tr><td>Algorithm replies:</td><td>" + statistics.algorithm_replies + "</td></tr>");
            $("#statistics tbody").append("<tr><td>Created requests:</td><td>" + statistics.algorithm_requests + "</td></tr>");
            $("#statistics tbody").append("<tr><td>Given Commendations:</td><td>" + statistics.given_commendations + "</td></tr>");
            $("#statistics tbody").append("<tr><td>Profile comments:</td><td>" + statistics.profile_comments + "</td></tr>");
            $("#statistics tbody").append("<tr><td>Profile replies:</td><td>" + statistics.profile_replies + "</td></tr>");
        },
        getApproval = function (upvotes, downvotes) {
            if (upvotes === 0) {
                return 0;
            }
            return upvotes / (upvotes + downvotes) * 100;
        },
        createList = function (data) {
            $(".postedAlgorithms").html("");
            $.each(algorithms, function (index, value) {
                var toAppend = '<div class="postedAlgorithm" listindex="'+index+'">' +
                    '<h2><a target="_blank" href="../posts/' + value.id + '">' + value.name + '</a> (<span>Language</span>: ' + value.language + ')</h2>' +
                    '<p><span>Description</span>: ' + (value.description.trim().length ? value.description : "None") + '</p>' +
                    (!value.template ? '<p><span>Ratings</span>: ' + value.upvotes + ' upvotes, ' + value.downvotes + ' downvotes with an aproval of ' + getApproval(value.upvotes, value.downvotes) + '%</p>' +
                            '<p>' + value.views + ' views, ' + value.comments + ' comments</p>' : "") +
                    (profileId !== "me" ?
                                (value.reported === 0 ?
                                        '<p><button class="transparent reportAlgorithm">Report this algorithm</button></p>' :
                                        '<p><strong><em>Your report was successfully submitted. Thank you!</em></strong></p>')
                        : "") +
                    '</div>';
                $(".postedAlgorithms").append(toAppend);
            });
        },
        createTable = function (data) {
            $(".postedAlgorithmsTable tbody").html("");
            $.each(algorithms, function (index, value) {
                var toAppend = '<tr listindex="' + index + '">' +
                    '<td><a target="_blank" href="../posts/' + value.id + '">' + value.name + '</a></td>' +
                    '<td>' + value.language + '</td>' +
                    '<td>' + value.upvotes + '</td>' +
                    '<td>' + value.downvotes + '</td>' +
                    '<td>' + getApproval(value.upvotes, value.downvotes) + '% </td>' +
                    '<td>' + value.views + '</td>' +
                    '<td>' + value.comments + '</td>' +
                    '<td>' +
                    (profileId !== "me" ?
                                (value.reported === 0 ?
                                        '<button class="transparent reportAlgorithm">Report this algorithm</button>' :
                                        '<strong><em>Report successfully submitted.</em></strong>')
                        : "You can't report your own algorithms") +
                    '</td>' +
                    '</tr>';
                $(".postedAlgorithmsTable tbody").append(toAppend);
            });
        },
        populate = function () {
            $("#profileOf").html(userData.lastName + " " + userData.firstName);
            $(".commend-star #commendationNumber").html(userData.commendations.number);
            if (userData.commendations.commendedByYou === true) {
                $(".commend-star").addClass("green");
            }
            if (profileId !== "me") {
                if (userData.reported === 0) {
                    $(".boxWrapper.heading").append('<span class="report" style="float: right"><button class="transparent reportProfile">Report this profile</button></span>');
                    $(".reportProfile").click(function () {
                        lastReportIndex = profileId;
                        reportType = "users";
                        $("#reportModal").modal("toggle");
                    });
                } else {
                    $(".boxWrapper.heading").append('<span class="report" style="float: right"><strong><em>Your report was successfully submitted. Thank you!</em></strong></span>');
                }
            }
            if (algorithms.length > 0) {
                $(".switcher#postsSwitcher").removeClass("hidden");
                createList(algorithms);
                createTable(algorithms);
                $(".reportAlgorithm").click(function () {
                    lastReportIndex = $(this).closest("[listindex]").attr("listindex");
                    reportType = "algorithms";
                    $("#reportModal").modal("toggle");
                });
            } else {
                $("#postedErrorMessage").removeClass("hidden");
                $("#postedErrorMessage").html("This user doesn't currently have any posted algorithms..</p>");
                $(".postedAlgorithms").html("");
                $(".postedAlgorithmsTable tbody").html("");
                $(".switcher#postsSwitcher").addClass("hidden");
                $(".postedAlgorithmsTable").addClass("hidden");
            }
            populateComments();
            populateStatistics();
        },
        getProfileDetails = function () {
            $.ajax({
                method: 'get',
                url: root + "/users/profiledetails",
                dataType: "json",
                data: {id: profileId},
                success: function (data) {
                    userData = data.userData;
                    algorithms = data.algorithms;
                    comments = data.comments;
                    statistics = data.statistics;
                    populate();
                },
                error: function (data) {
                    console.log(data);
                }
            });
        };
    $(".commend-star").click(function () {
        if (userData.commendations.youCantCommend === false) {
            $.ajax({
                method: 'put',
                url: root + "/users/commend",
                dataType: "json",
                data: {id: profileId},
                success: function (data) {
                    if ($(".commend-star").hasClass("green")) {
                        $(".commend-star").removeClass("green");
                        userData.commendations.commendedByYou = false;
                    } else {
                        $(".commend-star").addClass("green");
                        userData.commendations.commendedByYou = true;
                    }
                    userData.commendations.number = data.number;
                    $(".commend-star #commendationNumber").html(userData.commendations.number);
                },
                error: function (data) {
                    console.log(data);
                }
            });
        }
    });
    
    $(".switcher#postsSwitcher").click(function () {
        if ($(this).siblings("table").hasClass("hidden")) {
            $(this).siblings("table").removeClass("hidden");
            $(this).siblings(".postedAlgorithms").addClass("hidden");
        } else {
            $(this).siblings("table").addClass("hidden");
            $(this).siblings(".postedAlgorithms").removeClass("hidden");
        }
    });
    $(".send-message #sendButton .btn").click(function () {
        var comment = $(".send-message textarea").val().trim();
        if (comment.length > 0) {
            $(".send-message textarea").val("");
            $.ajax({
                method: 'post',
                url: root + "/users/comment",
                dataType: "json",
                data: {id: profileId, comment: comment},
                success: function (data) {
                    comments = data;
                    populateComments();
                },
                error: function (data) {
                    
                }
            });
        }
    });
    $("#submitReport").click(function () {
        var data;
        switch (reportType) {
        case "algorithms":
            data = {
                user_id: globalSettings.getUserData().id,
                reported_id: algorithms[lastReportIndex].id,
                table: reportType,
                reported_user_id: profileId,
                user_reason: $("#reportModal select").val(),
                user_description: $("#reportModal textarea").val()
            };
            break;
        case "users":
            data = {
                user_id: globalSettings.getUserData().id,
                reported_id: profileId,
                table: reportType,
                reported_user_id: profileId,
                user_reason: $("#reportModal select").val(),
                user_description: $("#reportModal textarea").val()
            };
            break;
        case "profile_discussion":
            data = {
                user_id: globalSettings.getUserData().id,
                reported_id: comments[lastReportIndex].id,
                table: reportType,
                reported_user_id: comments[lastReportIndex].user_id,
                user_reason: $("#reportModal select").val(),
                user_description: $("#reportModal textarea").val()
            };
            break;
        default:
            data = {
                user_id: globalSettings.getUserData().id,
                reported_id: comments[lastReportIndex].replies[lastReportSecIndex].id,
                table: reportType,
                reported_user_id: comments[lastReportIndex].replies[lastReportSecIndex].user_id,
                user_reason: $("#reportModal select").val(),
                user_description: $("#reportModal textarea").val()
            };
        }
        
        $.ajax({
            method: 'post',
            url: globalSettings.getRoot() + "/reports/report",
            dataType: "json",
            data: data,
            success: function (data) {
                if (reportType == "algorithms") {
                    algorithms[lastReportIndex].reported = 1;
                    createList(algorithms);
                    createTable(algorithms);
                } 
                if (reportType == "users") {
                    $(".boxWrapper.heading .report").html('<span class="report" style="float: right"><strong><em>Your report was successfully submitted. Thank you!</em></strong></span>');
                } 
                if (reportType == "profile_discussion") {
                    comments[lastReportIndex].reported = 1;
                    populateComments(comments);
                } 
                if (reportType == "profile_discussion_replies") {
                    comments[lastReportIndex].replies[lastReportSecIndex].reported = 1;
                    populateComments(comments);
                }
                $("#reportModal").modal("toggle");
            },
            error: function (data) {
            }
        });
    });
    getProfileDetails();
    if ($("#sendPrivateMessage").length > 0) {
        $("#sendPrivateMessage").attr("href", $("#sendPrivateMessage").attr("href") + profileId);
    }
});