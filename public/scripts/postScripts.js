/*global
    $,
    console,
    onMouseUpdate,
    globalSettings,
*/

$(document).ready(function () {

    'use strict';
    
    var postId = window.location.href.split("#")[0].split("/")[window.location.href.split("/").length - 1],
        root = globalSettings.getRoot(),
        yourVote,
        reportType,
        comments,
        lineComments,
        currentLineSubmitter,
        initiated = 0,
        lastReportIndex,
        lastReportSecIndex,
        creatorId,
        voteLineCommentAjax = function (vote, comment_id, algorithm_id, tabindex) {
            $.ajax({
                method: 'post',
                url: "../users/voteinlinecomment",
                dataType: "json",
                data: {comment_id: comment_id, vote: vote, algorithm_id: algorithm_id},
                success: function (data) {
                    lineComments[currentLineSubmitter][tabindex].upvotes = data.upvotes;
                    lineComments[currentLineSubmitter][tabindex].downvotes = data.downvotes;
                    $(".conversation .reply[tabindex='" + tabindex + "'] .likeLineComment .green").html("(" + data.upvotes + ")");
                    $(".conversation .reply[tabindex='" + tabindex + "'] .dislikeLineComment .red").html("(" + data.downvotes + ")");
                },
                fail: function (data) {
                    console.log(data);
                }
            });
        },
        voteCommentAjax = function (vote, comment_id, algorithm_id, tabindex) {
            $.ajax({
                method: 'post',
                url: "../users/votecomment",
                dataType: "json",
                data: {comment_id: comment_id, vote: vote, algorithm_id: algorithm_id},
                success: function (data) {
                    $(".reply[tabindex='" + tabindex + "'] .likeComment .green").html("(" + data.upvotes + ")");
                    $(".reply[tabindex='" + tabindex + "'] .dislikeComment .red").html("(" + data.downvotes + ")");
                },
                fail: function (data) {
                    console.log(data);
                }
            });
        },
        populateInlineUI = function () {
            $(".lineComments .conversation").empty();
            if (lineComments === [] || lineComments[currentLineSubmitter] === undefined) {
                $(".lineComments .conversation").append("<p>No other comments on this line yet..</p>");
            } else {
                $.each(lineComments[currentLineSubmitter], function (index, value) {
                    value.text = value.text.split("/n").join("<br>");
                    $(".lineComments .conversation").append('<div class="reply" tabindex="' + index + '" parent="parent">' +
                        '<p><span class="person"><a href="../profile/' + value.user_id + '">' + value.name + '</a></span> <span class="created"> on ' + value.created_at + '</span></p>' +
                        '<p>' + (parseInt(value.deleted, 10) === 1 ? '<em>This comment was deleted.</em>' : value.text) + '</p>' +
                        (parseInt(value.deleted, 10) === 0 ?
                                '<p><a href="javascript:void(0)" class="likeLineComment">Like <span class="green">(' + value.upvotes + ')</span></a> | <a href="javascript:void(0)" class="dislikeLineComment">Dislike <span class="red">(' + value.downvotes + ')</span></a> ' + (parseInt(value.deleted, 10) === 0 ? (value.user_id !== globalSettings.getUserData().id ? (parseInt(value.reported, 10) === 0 ? ' | <a href="javascript:void(0)" class="reportLineComment">Report</a>' : '<p><strong><em>Your report was successfully submitted. Thank you!</em></strong></p>') : "") : '<a href="javascript:void(0)" class="deleteLineComment">Delete</a>') + '</p><hr>' : "<hr>"));
                });
                $(".reportLineComment").click(function () {
                    reportType = "inline_algorithm_comments";
                    lastReportIndex = $(this).closest(".reply[parent='parent']").attr("tabindex");
                    $("#reportModal").modal("toggle");
                });
                $(".deleteLineComment").click(function () {
                    var tabindex = $(this).closest(".reply[parent='parent']").attr("tabindex");
                    $.ajax({
                        method: 'post',
                        url: root + "/users/deletelinecomment",
                        dataType: "json",
                        data: {id: lineComments[currentLineSubmitter][tabindex].id},
                        success: function (data) {
                            console.log(lineComments[currentLineSubmitter][tabindex].id, data);
                            lineComments[currentLineSubmitter][tabindex].deleted = 1;
                            populateInlineUI();
                        },
                        fail: function (data) {
                            console.log(data);
                        }
                    });
                });
                $(".dislikeLineComment").click(function () {
                    var tabindex = $(this).closest(".reply[parent='parent']").attr("tabindex");
                    voteLineCommentAjax(0, lineComments[currentLineSubmitter][tabindex].id, postId, tabindex);
                });
                $(".likeLineComment").click(function () {
                    var tabindex = $(this).closest(".reply[parent='parent']").attr("tabindex");
                    voteLineCommentAjax(1, lineComments[currentLineSubmitter][tabindex].id, postId, tabindex);
                });
            }
        },
        parseLineComments = function (data) {
            lineComments = [];
            $.each(data, function (index, value) {
                lineComments[value.line] = lineComments[value.line] || [];
                lineComments[value.line].push(value);
            });
            $.each(lineComments, function (index, value) {
                if (value !== undefined) {
                    $(".ace_gutter-cell[tabindex='" + index + "']").html("<span class='commentSection' style='position: absolute; left: 5px'>" + value.length + " comments </span>" + (index + 1));
                }
            });
        },
        voteReplyAjax = function (vote, comment_id, algorithm_id, tabindex, sectabindex) {
            $.ajax({
                method: 'post',
                url: "../users/votereply",
                dataType: "json",
                data: {comment_id: comment_id, vote: vote, algorithm_id: algorithm_id},
                success: function (data) {
                    $(".reply[tabindex='" + tabindex + "'] .reply[sectabindex='" + sectabindex + "'] .likeReply .green").html("(" + data.upvotes + ")");
                    $(".reply[tabindex='" + tabindex + "']  .reply[sectabindex='" + sectabindex + "'] .dislikeReply .red").html("(" + data.downvotes + ")");
                },
                fail: function (data) {
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
                    '<p>' + (parseInt(value.deleted, 10) === 1 ? '<em>This comment was deleted.</em>' : value.text) + '</p>' +
                    (parseInt(value.deleted, 10) === 0 ?
                            '<p><a href="javascript:void(0)" class="likeComment">Like <span class="green">(' + value.upvotes + ')</span></a> | <a href="javascript:void(0)" class="dislikeComment">Dislike <span class="red">(' + value.downvotes + ')</span></a>' + (value.canDelete === false ? (value.reported === 0 ? '| <a href="javascript:void(0)" class="reportComment">Report</a>' : "<p><strong><em>Your report was successfully submitted. Thank you!</em></strong></p>") : '| <a href="javascript:void(0)" class="deleteComment">Delete</a>') + ' </p><hr>' : "<hr>");
                $.each(value.replies, function (secIndex, secValue) {
                    toAppend += '<div class="reply" sectabindex="' + secIndex + '" parent="noparent" id="comment' + value.id + "_" + secValue.id + '">' +
                        '<p><span class="person"><a href="../profile/' + secValue.user_id + '">' + secValue.name + '</a></span> <span class="created"> on ' + secValue.created_at + '</span></p>';
                    secValue.text = secValue.text.split("\n").join("<br>");
                    toAppend += '<p>' + (parseInt(secValue.deleted, 10) === 1 ? '<em>This comment was deleted.</em>' : secValue.text) + '</p>' +
                        (parseInt(secValue.deleted, 10) === 0 ?
                                '<p><a href="javascript:void(0)" class="likeReply">Like <span class="green">(' + secValue.upvotes + ')</span></a> | <a href="javascript:void(0)" class="dislikeReply">Dislike <span class="red">(' + secValue.downvotes + ')</span></a> |' + (secValue.canDelete === false ? (secValue.reported === 0 ? '| <a href="javascript:void(0)" class="reportReply">Report</a>' : "<p><strong><em>Your report was successfully submitted. Thank you!</em></strong></p>") : '<a href="javascript:void(0)" class="deleteReply">Delete</a>') + '</p>' : "") +
                            ' <hr>' +
                        '</div>';
                
                });
                toAppend += '<p><a href="javascript:void(0)" class="replyComment">Reply to this</a></p></div>';
            });
            $("#algorithmComments").html(toAppend);
            $(".reportComment").click(function () {
                lastReportIndex = $(this).closest(".reply[parent='parent']").attr("tabindex");
                reportType = 'algorithm_discussion';
                $("#reportModal").modal('toggle');
            });
            $(".reportReply").click(function () {
                lastReportIndex = $(this).closest(".reply[parent='parent']").attr("tabindex");
                lastReportSecIndex = $(this).closest(".reply[parent='noparent']").attr("sectabindex");
                reportType = 'algorithm_discussion_replies';
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
                    fail: function (data) {
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
                    fail: function (data) {
                        console.log(data);
                    }
                });
            });
            $(".dislikeComment").click(function () {
                var tabindex = $(this).closest(".reply[parent='parent']").attr("tabindex");
                voteCommentAjax(0, comments[tabindex].id, postId, tabindex);
            });
            $(".likeComment").click(function () {
                var tabindex = $(this).closest(".reply[parent='parent']").attr("tabindex");
                voteCommentAjax(1, comments[tabindex].id, postId, tabindex);
            });
            $(".dislikeReply").click(function () {
                var tabindex = $(this).closest(".reply[parent='parent']").attr("tabindex"),
                    sectabindex = $(this).closest(".reply[parent='noparent']").attr("sectabindex");
                voteReplyAjax(0, comments[tabindex].replies[sectabindex].id, postId, tabindex, sectabindex);
            });
            $(".likeReply").click(function () {
                var tabindex = $(this).closest(".reply[parent='parent']").attr("tabindex"),
                    sectabindex = $(this).closest(".reply[parent='noparent']").attr("sectabindex");
                voteReplyAjax(1, comments[tabindex].replies[sectabindex].id, postId, tabindex, sectabindex);
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
                                url: "../users/respondtocomment",
                                dataType: "json",
                                data: {id: postId, commentid: comments[tabindex].id, comment: comment},
                                success: function (data) {
                                    comments = data;
                                    populateComments();
                                    $(".reply[tabindex='" + tabindex + "'] .replyToComment").remove();
                                },
                                fail: function (data) {

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
        getPostData = function () {
            $.ajax({
                method: 'get',
                url: "../post/postdata",
                dataType: "json",
                data: {id: postId},
                success: function (data) {
                    console.log(data);
                    comments = data.comments;
                    setTimeout(function () {
                        parseLineComments(data.inline_comments);
                    }, 2000);
                    populateComments();
                    $(".commend-star #commendationNumber").html(data.commendations.number);
                    if (data.commendations.commendedByYou === true) {
                        $(".commend-star").addClass("green");
                    }
                    $(".commend-star").unbind().click(function () {
                        if (data.commendations.youCantCommend === false) {
                            $.ajax({
                                method: 'put',
                                url: root + "/users/commend",
                                dataType: "json",
                                data: {id: data.user_id},
                                success: function (data2) {
                                    if ($(".commend-star").hasClass("green")) {
                                        $(".commend-star").removeClass("green");
                                    } else {
                                        $(".commend-star").addClass("green");
                                    }
                                    $(".commend-star #commendationNumber").html(data2.number);
                                },
                                fail: function (data) {
                                    console.log(data);
                                }
                            });
                        }
                    });
                    creatorId = data.user_id;
                    $("#creatorUsername").html(data.username);
                    $("#creatorUsername").attr("href", "../profile/" + data.user_id);
                    $("#upvoteSpan").html(data.upvotes);
                    $("#downvoteSpan").html(data.downvotes);
                    $("#viewSpan").html(data.views);
                    $("#algorithmName").html(data.name);
                    if (creatorId !== globalSettings.getUserData().id) {
                        if (data.reported === 0) {
                            $(".boxWrapper.heading").append('<p class="report text-right"><button class="transparent reportAlgorithm">Report this algorithm</button></p>');
                            $(".reportAlgorithm").click(function () {
                                lastReportIndex = postId;
                                reportType = "algorithms";
                                $("#reportModal").modal("toggle");
                            });
                        } else {
                            $(".boxWrapper.heading").append('<p class="report text-right"><strong><em>Your report was successfully submitted. Thank you!</em></strong></p>');
                        }
                    }
                    if (data.original_link === "") {
                        $("#originalLink").attr("href", "javascript:void(0)");
                        $("#originalLink").html("None provided");
                    } else {
                        $("#originalLink").attr("href", data.original_link);
                    }
                    $("#algorithmDescription").append(data.description);
                    $("#language").append(data.language);
                    if (parseInt(data.request_id, 10) !== 0) {
                        $("#thisRequest").removeClass("hidden");
                    }
                    $('#postedAlgorithmArea').ace({
                        theme: 'monokai',
                        height: 400
                    });

                    var x = null,
                        y = null,
                        decorator,
                        editor;

                    function onMouseUpdate(e) {
                        x = e.pageX;
                        y = e.pageY;
                    }

                    document.addEventListener('mousemove', onMouseUpdate, false);
                    document.addEventListener('mouseenter', onMouseUpdate, false);

                    
                    function getMouseX() {
                        return x;
                    }

                    function getMouseY() {
                        return y;
                    }

                    decorator = $('#postedAlgorithmArea').data('ace');
                    editor = decorator.editor.ace;

                    editor.setReadOnly(true);
                    editor.setValue(data.content);
                    setTimeout(function () {
                        $.each($(".ace_gutter-cell"), function (index, value) {
                            $(value).attr("tabindex", index);
                        });
                        $(".ace_gutter-cell").click(function () {
                            currentLineSubmitter = $(this).attr("tabindex");
                            $(".lineComments").remove();
                            $("body").append('<div class="lineComments"></div>');
                            $(".lineComments").css({
                                position: 'absolute',
                                top: getMouseY() + 10,
                                left: getMouseX() + 10,
                                width: "40%",
                                "max-height": "70%",
                                'z-index': 1000,
                                border: '1px solid #ccc',
                                background: 'white',
                                padding: '0 10px',
                                'overflow-y': 'scroll',
                                'resize': 'both'
                            });
                            $(".lineComments").append('<div class="boxWrapper"><label>Your Message</label><textarea class="form-control"></textarea><div class="text-right"><button class="btn" id="commentCloser" style="margin-top: 10px; margin-right: 3px">Close</button><button class="btn" id="submitInline" style="margin-top: 10px">Submit</button></div></div>');
                            $(".lineComments").append('<div class="conversation"></div>');
                            populateInlineUI();
                            $(".lineComments").draggable();
                            $("#submitInline").click(function () {
                                var comment = $(".lineComments textarea").val().trim();
                                if (comment.length > 0) {
                                    $(".lineComments textarea").val("");
                                    $.ajax({
                                        method: 'post',
                                        url: "../users/commentline",
                                        dataType: "json",
                                        data: {id: postId, line: currentLineSubmitter, comment: comment},
                                        success: function (data) {
                                            parseLineComments(data);
                                            populateInlineUI();
                                        },
                                        fail: function (data) {

                                        }
                                    });
                                }
                            });
                            $("#commentCloser").click(function () {
                                $(".lineComments").remove();
                            });
                        });

                    }, 1000);
                },
                error: function (data) {
                    console.log(window.location.href.split("/")[window.location.href.split("/").length - 1]);
                }
            });
        },
        voteAlgorithmAjax = function (vote) {
            $.ajax({
                method: 'post',
                url: "../users/votealgorithm",
                dataType: "json",
                data: {id: postId, vote: vote},
                success: function (data) {
                    $("#upvoteSpan").html(data.upvotes);
                    $("#downvoteSpan").html(data.downvotes);
                },
                error: function (data) {
                    console.log(data);
                }
            });
        };
    getPostData();
    $(".upvote a").click(function () {
        voteAlgorithmAjax(1);
    });
    $(".downvote a").click(function () {
        voteAlgorithmAjax(0);
    });
    $(".send-message #sendButton .btn").click(function () {
        var comment = $(".send-message textarea").val().trim();
        if (comment.length > 0) {
            $(".send-message textarea").val("");
            $.ajax({
                method: 'post',
                url: "../users/discussalgorithm",
                dataType: "json",
                data: {id: postId, comment: comment},
                success: function (data) {
                    comments = data;
                    populateComments();
                },
                fail: function (data) {
                    
                }
            });
        }
    });
    
    $("#submitReport").click(function () {
        var data;
        switch (reportType) {
        case "algorithm_discussion_replies":
            data = {
                user_id: globalSettings.getUserData().id,
                reported_id: comments[lastReportIndex].replies[lastReportSecIndex].id,
                table: reportType,
                reported_user_id: comments[lastReportIndex].replies[lastReportSecIndex].user_id,
                user_reason: $("#reportModal select").val(),
                user_description: $("#reportModal textarea").val()
            };
            break;
        case "algorithm_discussion":
            data = {
                user_id: globalSettings.getUserData().id,
                reported_id: comments[lastReportIndex].id,
                table: reportType,
                reported_user_id: comments[lastReportIndex].user_id,
                user_reason: $("#reportModal select").val(),
                user_description: $("#reportModal textarea").val()
            };
            break;
        case "algorithms":
            data = {
                user_id: globalSettings.getUserData().id,
                reported_id: postId,
                table: reportType,
                reported_user_id: creatorId,
                user_reason: $("#reportModal select").val(),
                user_description: $("#reportModal textarea").val()
            };
            break;
        default:
            data = {
                user_id: globalSettings.getUserData().id,
                reported_id: lineComments[currentLineSubmitter][lastReportIndex].id,
                table: reportType,
                reported_user_id: lineComments[currentLineSubmitter][lastReportIndex].user_id,
                user_reason: $("#reportModal select").val(),
                user_description: $("#reportModal textarea").val()
            };
        }
        
        console.log(lastReportIndex);
        console.log(data);
        
        $.ajax({
            method: 'post',
            url: globalSettings.getRoot() + "/users/report",
            dataType: "json",
            data: data,
            success: function (data) {
                if (reportType === "algorithm_discussion_replies") {
                    comments[lastReportIndex].replies[lastReportSecIndex].reported = 1;
                    populateComments();
                }
                if (reportType === "algorithm_discussion") {
                    comments[lastReportIndex].reported = 1;
                    populateComments();
                }
                if (reportType === "inline_algorithm_comments") {
                    lineComments[currentLineSubmitter][lastReportIndex].reported = 1;
                    populateInlineUI();
                }
                if (reportType === "algorithms") {
                    $(".boxWrapper.heading .report").html("<strong><em>Your report was successfully submitted. Thank you!</em></strong>");
                }
                $("#reportModal").modal("toggle");
            },
            error: function (data) {
            }
        });
    });
});