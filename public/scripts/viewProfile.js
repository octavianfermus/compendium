$(document).ready(function() {
    var profileId = window.location.href.split("#")[0].split("/")[window.location.href.split("/").length-1],
        profileData = undefined,
        root = "http://localhost:8080",
        algorithms = undefined,
        comments = undefined,
        statistics = undefined,
        initiated = 0,
        voteProfileReplyAjax = function(vote, comment_id, profile_id, tabindex, sectabindex) {
            jQuery.ajax({
                method: 'post',
                url: "../users/voteprofilereply",
                dataType: "json",
                data: {comment_id: comment_id, vote: vote, profile_id: profile_id},
                success: function (data) {
                    $(".reply[tabindex='"+tabindex+"'] .reply[sectabindex='"+sectabindex+"'] .likeReply .green").html("("+data.upvotes+")");
                    $(".reply[tabindex='"+tabindex+"']  .reply[sectabindex='"+sectabindex+"'] .dislikeReply .red").html("("+data.downvotes+")");
                },
                fail: function(data) {
                    console.log(data);
                }
            });
        },
        voteProfileCommentAjax = function(vote, comment_id, profile_id, tabindex) {
            jQuery.ajax({
                method: 'post',
                url: "../users/voteprofilecomment",
                dataType: "json",
                data: {comment_id: comment_id, vote: vote, profile_id: profile_id},
                success: function (data) {
                    $(".reply[tabindex='"+tabindex+"'] .likeComment .green").html("("+data.upvotes+")");
                    $(".reply[tabindex='"+tabindex+"'] .dislikeComment .red").html("("+data.downvotes+")");
                },
                fail: function(data) {
                    console.log(data);
                }
            });
        },
        populateComments = function() {
            var toAppend = "";
            $.each(comments, function(index, value) {
                value.text = value.text.split("\n").join("<br>");
                toAppend += '<div class="reply" tabindex="'+index+'" parent="parent" id="comment'+value.id+'">'+
                    '<p><span class="person"><a href="../profile/'+value.user_id+'">'+value.name+'</a></span> <span class="created"> on '+value.created_at+'</span></p>'+
                    '<p>' + (value.deleted == 1 ? '<em>This comment was deleted.</em>' : value.text) + '</p>' +
                    (value.deleted == 0 ?
                    '<p><a href="javascript:void(0)" class="likeComment">Like <span class="green">('+value.upvotes+')</span></a> | <a href="javascript:void(0)" class="dislikeComment">Dislike <span class="red">('+value.downvotes+')</span></a> | '+(value.canDelete == 0 ? '<a href="javascript:void(0)">Report</a>' : '<a href="javascript:void(0)" class="deleteComment">Delete</a>' )+ '</p><hr>': "<hr>");
                $.each(value.replies, function(secIndex, secValue) {
                    toAppend +='<div class="reply" sectabindex="'+secIndex+'" parent="noparent" id="comment'+value.id+"_"+secValue.id+'">' +
                    '<p><span class="person"><a href="../profile/'+secValue.user_id+'">'+secValue.name+'</a></span> <span class="created"> on '+secValue.created_at+'</span></p>';
                    secValue.text = secValue.text.split("\n").join("<br>");
                    toAppend += '<p>'+(secValue.deleted == 1 ? '<em>This comment was deleted.</em>' : secValue.text ) + '</p>' +
                    (secValue.deleted == 0 ?
                    '<p><a href="javascript:void(0)" class="likeReply">Like <span class="green">('+secValue.upvotes+')</span></a> | <a href="javascript:void(0)" class="dislikeReply">Dislike <span class="red">('+secValue.downvotes+')</span></a> | '+(secValue.canDelete == 0 ? '<a href="javascript:void(0)">Report</a>' :  '<a href="javascript:void(0)" class="deleteReply">Delete</a>' )+ '</p>': "")+
                    ' <hr>'+
                    '</div>';
                
                });
                toAppend+='<p><a href="javascript:void(0)" class="replyComment">Reply to this</a></p></div>';
            });
            $("#profileComments").html(toAppend);
            $(".deleteComment").click(function() {
                var tabindex = $(this).closest(".reply[parent='parent']").attr("tabindex");
                jQuery.ajax({
                    method: 'post',
                    url: root+"/users/deleteprofilecomment",
                    dataType: "json",
                    data: {id: comments[tabindex].id},
                    success: function (data) {
                        comments[tabindex].deleted = 1;
                        populateComments();
                    },
                    fail: function(data) {
                        console.log(data);
                    }
                });
            });
            $(".deleteReply").click(function() {
                console.log(comments);
                var tabindex = $(this).closest(".reply[parent='parent']").attr("tabindex"),
                    sectabindex = $(this).closest(".reply[parent='noparent']").attr("sectabindex");;
                jQuery.ajax({
                    method: 'post',
                    url: root+"/users/deleteprofilereply",
                    dataType: "json",
                    data: {id: comments[tabindex].replies[sectabindex].id},
                    success: function (data) {
                        comments[tabindex].replies[sectabindex].deleted = 1;
                        populateComments();
                    },
                    fail: function(data) {
                        console.log(data);
                    }
                });
            });
            $(".dislikeComment").click(function() {
                var tabindex = $(this).closest(".reply[parent='parent']").attr("tabindex");
                voteProfileCommentAjax(0,comments[tabindex].id, profileId, tabindex);
            });
            $(".likeComment").click(function() {
                var tabindex = $(this).closest(".reply[parent='parent']").attr("tabindex");
                voteProfileCommentAjax(1,comments[tabindex].id, profileId, tabindex);
            });
            $(".dislikeReply").click(function() {
                var tabindex = $(this).closest(".reply[parent='parent']").attr("tabindex"),
                    sectabindex = $(this).closest(".reply[parent='noparent']").attr("sectabindex");
                voteProfileReplyAjax(0,comments[tabindex].replies[sectabindex].id, profileId, tabindex, sectabindex);
            });
            $(".likeReply").click(function() {
                var tabindex = $(this).closest(".reply[parent='parent']").attr("tabindex"),
                    sectabindex = $(this).closest(".reply[parent='noparent']").attr("sectabindex");
                voteProfileReplyAjax(1,comments[tabindex].replies[sectabindex].id, profileId, tabindex, sectabindex);
            });
            $(".replyComment").click(function() {
                var tabindex = $(this).closest(".reply[parent='parent']").attr("tabindex"),
                    toAppend ="";
                if($(".reply[tabindex='"+tabindex+"'] .replyToComment").length == 0) {
                    toAppend = '<div class="replyToComment">' +
                        '<div class="input-group">' +
                        '<textarea placeholder="Write your message here" aria-describedby="sendButton" class="form-control"></textarea>' +
                        '<span class="input-group-addon" id="sendButton"><button class="btn">Send</button></span>' +
                        '</div></div>';
                    $(".reply[tabindex='"+tabindex+"'][parent='parent']").append(toAppend);
                    $(".reply[tabindex='"+tabindex+"'][parent='parent'] .btn").click(function() {
                        var comment = $(".reply[tabindex='"+tabindex+"'][parent='parent'] textarea").val().trim();
                        if(comment.length>0) {
                            $(".send-message textarea").val("");
                            jQuery.ajax({
                                method: 'post',
                                url: "../users/respondtoprofilecomment",
                                dataType: "json",
                                data: {id: profileId, commentid: comments[tabindex].id, comment: comment},
                                success: function(data) {
                                    comments = data;
                                    populateComments();
                                    $(".reply[tabindex='"+tabindex+"'] .replyToComment").remove();
                                },
                                fail: function(data) {

                                }
                            });
                        }
                    });
                } else {
                    $(".reply[tabindex='"+tabindex+"'] .replyToComment").remove();
                }
            });
            if(initiated === 0 && window.location.href.split("#comment").length>1) {
                window.location.href=window.location.href;
                initiated = 1;
            }
        },
        populateStatistics = function() {
            $("#statistics tbody").append("<tr><td>Algorithm comments:</td><td>"+statistics.algorithm_comments+"</td></tr>");
            $("#statistics tbody").append("<tr><td>Algorithm likes:</td><td>"+statistics.algorithm_likes+"</td></tr>");
            $("#statistics tbody").append("<tr><td>Algorithm replies:</td><td>"+statistics.algorithm_replies+"</td></tr>");
            $("#statistics tbody").append("<tr><td>Created requests:</td><td>"+statistics.algorithm_requests+"</td></tr>");
            $("#statistics tbody").append("<tr><td>Given Commendations:</td><td>"+statistics.given_commendations+"</td></tr>");
            $("#statistics tbody").append("<tr><td>Profile comments:</td><td>"+statistics.profile_comments+"</td></tr>");
            $("#statistics tbody").append("<tr><td>Profile replies:</td><td>"+statistics.profile_replies+"</td></tr>");
        },
        getApproval = function (upvotes, downvotes) {
            if (upvotes === 0) {
                return 0;
            }
            return upvotes / (upvotes + downvotes) * 100;
        },
        createList = function(data) {
           $(".postedAlgorithms").html("");
            $.each(algorithms, function (index, value) {
                var toAppend = '<div class="postedAlgorithm">' +
                    '<h2><a target="_blank" href="../posts/' + value.id + '">' + value.name + '</a> (<span>Language</span>: ' + value.language + ')</h2>' +          
                    '<p><span>Description</span>: ' + (value.description.trim().length ? value.description : "None") + '</p>' +
                    (!value.template ? '<p><span>Ratings</span>: ' + value.upvotes + ' upvotes, ' + value.downvotes + ' downvotes with an aproval of ' + getApproval(value.upvotes, value.downvotes) + '%</p>' +
                    '<p>' + value.views + ' views, 0 comments</p>' :"") +
                    '</div>';
                $(".postedAlgorithms").append(toAppend);
            }); 
        },
        createTable = function(data) {
            $(".postedAlgorithmsTable tbody").html("");
            $.each(algorithms, function (index, value) {
                var toAppend = '<tr>' +
                    '<td><a target="_blank" href="../posts/' + value.id + '">' + value.name + '</a></td>' +
                    '<td>' + value.language + '</td>' +
                    '<td>' + value.upvotes + '</td>' +
                    '<td>' + value.downvotes + '</td>' +
                    '<td>' + getApproval(value.upvotes, value.downvotes) + '% </td>' +
                    '<td>' + value.views + '</td>' +
                    '<td> 0 </td>' +
                    '</tr>';
                $(".postedAlgorithmsTable tbody").append(toAppend);
            });
        },
        populate = function() {
            $("#profileOf").html(userData.lastName + " " + userData.firstName);
            $(".commend-star #commendationNumber").html(userData.commendations.number);
            if(userData.commendations.commendedByYou == true) {
                $(".commend-star").addClass("green");
            }
            if(algorithms.length > 0) {
                $(".switcher#postsSwitcher").removeClass("hidden");
                createList(algorithms);
                createTable(algorithms);
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
        getProfileDetails = function() {
            jQuery.ajax({
                method: 'post',
                url: root+"/profiledetails",
                dataType: "json",
                data: {id: profileId},
                success: function (data) {
                    userData = data.userData;
                    algorithms = data.algorithms;
                    comments = data.comments;
                    statistics = data.statistics;
                    console.log(statistics);
                    populate();
                },
                fail: function(data) {
                    console.log(data);
                }
            });
        };
    $(".commend-star").click(function() {
        if(userData.commendations.youCantCommend == false) {
            jQuery.ajax({
                method: 'put',
                url: root+"/users/commend",
                dataType: "json",
                data: {id: profileId},
                success: function (data) {
                    if($(".commend-star").hasClass("green")) {
                        $(".commend-star").removeClass("green");
                        userData.commendations.commendedByYou == false;
                    } else {
                        $(".commend-star").addClass("green");
                        userData.commendations.commendedByYou == true;
                    }
                    userData.commendations.number = data.number;
                    $(".commend-star #commendationNumber").html(userData.commendations.number);
                },
                fail: function(data) {
                    console.log(data);
                }
            });
        }
    });
    
    $(".switcher#postsSwitcher").click(function() {
        if($(this).siblings("table").hasClass("hidden")) {
            $(this).siblings("table").removeClass("hidden");
            $(this).siblings(".postedAlgorithms").addClass("hidden");
        } else {
            $(this).siblings("table").addClass("hidden");
            $(this).siblings(".postedAlgorithms").removeClass("hidden");
        }
        $(".publish").unbind().click(function() {
            deletePublishModifier($(this).attr("givenId"),"publish");
        });
        $(".remove").unbind().click(function() {
            deletePublishModifier($(this).attr("givenId"),"remove");
        });
    });
    $(".send-message #sendButton .btn").click(function() {
        var comment = $(".send-message textarea").val().trim();
        if(comment.length>0) {
            $(".send-message textarea").val("");
            jQuery.ajax({
                method: 'post',
                url: "../users/discussprofile",
                dataType: "json",
                data: {id: profileId, comment: comment},
                success: function(data) {
                    comments = data;
                    populateComments();
                },
                fail: function(data) {
                    
                }
            });
        }
    });
    getProfileDetails();
});