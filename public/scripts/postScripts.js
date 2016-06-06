$(document).ready(function() {
    var postId = window.location.href.split("/")[window.location.href.split("/").length-1],
        yourVote = undefined,
        comments = undefined,
        voteCommentAjax = function(vote, comment_id, algorithm_id, tabindex) {
            jQuery.ajax({
                method: 'post',
                url: "../users/votecomment",
                dataType: "json",
                data: {comment_id: comment_id, vote: vote, algorithm_id: algorithm_id},
                success: function (data) {
                    $(".reply[tabindex='"+tabindex+"'] .likeComment .green").html("("+data.upvotes+")");
                    $(".reply[tabindex='"+tabindex+"'] .dislikeComment .red").html("("+data.downvotes+")");
                },
                fail: function(data) {
                    console.log(data);
                }
            });
        },
        voteReplyAjax = function(vote, comment_id, algorithm_id, tabindex, sectabindex) {
            jQuery.ajax({
                method: 'post',
                url: "../users/votereply",
                dataType: "json",
                data: {comment_id: comment_id, vote: vote, algorithm_id: algorithm_id},
                success: function (data) {
                    $(".reply[tabindex='"+tabindex+"'] .reply[sectabindex='"+sectabindex+"'] .likeReply .green").html("("+data.upvotes+")");
                    $(".reply[tabindex='"+tabindex+"']  .reply[sectabindex='"+sectabindex+"'] .dislikeReply .red").html("("+data.downvotes+")");
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
                toAppend += '<div class="reply" tabindex="'+index+'" parent="parent">'+
                    '<p><span class="person"><a href="../users/"'+value.user_id+'">'+value.name+'</a></span> <span class="created"> on '+value.created_at+'</span></p>'+
                    '<p>'+value.text+'</p>';
                $.each(value.replies, function(secIndex, secValue) {
                    toAppend +='<div class="reply" sectabindex="'+secIndex+'" parent="noparent">' +
                    '<p><span class="person"><a href="../users/"'+secValue.user_id+'">'+secValue.name+'</a></span> <span class="created"> on '+secValue.created_at+'</span></p>';
                    secValue.text = secValue.text.split("\n").join("<br>");
                    toAppend += '<p>'+secValue.text+'</p>' +
                    ' <hr>'+
                    '<p><a href="javascript:void(0)" class="likeReply">Like <span class="green">('+secValue.upvotes+')</span></a> | <a href="javascript:void(0)" class="dislikeReply">Dislike <span class="red">('+secValue.downvotes+')</span></a> | <a href="javascript:void(0)">Report</a> </p>'+
                    '</div>';
                
                });
                toAppend+=' <hr>'+
                    '<p><a href="javascript:void(0)" class="likeComment">Like <span class="green">('+value.upvotes+')</span></a> | <a href="javascript:void(0)" class="dislikeComment">Dislike <span class="red">('+value.downvotes+')</span></a> | <a href="javascript:void(0)" class="replyComment">Reply</a> | <a href="javascript:void(0)">Report</a> </p>'+
                    '</div>';
            });
            $("#algorithmComments").html(toAppend);
            $(".dislikeComment").click(function() {
                var tabindex = $(this).closest(".reply[parent='parent']").attr("tabindex");
                voteCommentAjax(0,comments[tabindex].id, postId, tabindex);
            });
            $(".likeComment").click(function() {
                var tabindex = $(this).closest(".reply[parent='parent']").attr("tabindex");
                voteCommentAjax(1,comments[tabindex].id, postId, tabindex);
            });
            $(".dislikeReply").click(function() {
                var tabindex = $(this).closest(".reply[parent='parent']").attr("tabindex"),
                    sectabindex = $(this).closest(".reply[parent='noparent']").attr("sectabindex");
                console.log("disliked reply " + tabindex + " " + sectabindex);
                voteReplyAjax(0,comments[tabindex].replies[sectabindex].id, postId, tabindex, sectabindex);
            });
            $(".likeReply").click(function() {
                var tabindex = $(this).closest(".reply[parent='parent']").attr("tabindex"),
                    sectabindex = $(this).closest(".reply[parent='noparent']").attr("sectabindex");
                console.log("liked reply " + tabindex + " " + sectabindex);
                console.log(comments[tabindex].replies[sectabindex]);
                voteReplyAjax(1,comments[tabindex].replies[sectabindex].id, postId, tabindex, sectabindex);
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
                                url: "../users/respondtocomment",
                                dataType: "json",
                                data: {id: postId, commentid: comments[tabindex].id, comment: comment},
                                success: function(data) {
                                    console.log(data);
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
        },
        getPostData = function () {
        jQuery.ajax({
            method: 'get',
            url: "../post/postdata",
            dataType: "json",
            data: {id: postId},
            success: function (data) {
                comments = data.comments;
                populateComments();
                $("#creatorUsername").html(data.username);
                $("#creatorUsername").attr("href","../users/"+data.user_id);
                $("#upvoteSpan").html(data.upvotes);
                $("#downvoteSpan").html(data.downvotes);
                $("#viewSpan").html(data.views);
                $("#algorithmName").html(data.name);
                $("#originalLink").attr("href",data.original_link);
                $("#algorithmDescription").append(data.description);
                $("#language").append(data.language);
                if(data.request_id!=0) {
                    $("#thisRequest").removeClass("hidden");
                }
                $('#postedAlgorithmArea').ace({ 
                    theme: 'monokai',
                    height: 140
                });

                var x = null;
                var y = null;

                document.addEventListener('mousemove', onMouseUpdate, false);
                document.addEventListener('mouseenter', onMouseUpdate, false);

                function onMouseUpdate(e) {
                    x = e.pageX;
                    y = e.pageY;
                }

                function getMouseX() {
                    return x;
                }

                function getMouseY() {
                    return y;
                }

                var decorator = $('#postedAlgorithmArea').data('ace');
                editor = decorator.editor.ace;

                editor.setReadOnly(true);
                editor.setValue(data.content);
                setTimeout(function() {
                    $($(".ace_gutter .ace_layer .ace_gutter-cell")[0]).append("<span class='commentSection' style='position: absolute; left: 5px'>2 comments </span>");
                    $(".ace_gutter .ace_layer .ace_gutter-cell").click(function(e) {
                        if($(this).children('.commentSection').length>0) {
                            $(".lineComments").remove();
                            $("body").append('<div class="lineComments"></div>');
                            $(".lineComments").css({
                                position: 'absolute',
                                top: getMouseY() + 10,
                                left: getMouseX() + 10,
                                width: "30%",
                                'z-index': 1000,
                                border: '1px solid #ccc',
                                background: 'white',
                                padding: '0 10px',
                                'overflow-y': 'scroll',
                                'resize': 'both'
                            });
                            $(".lineComments").append('<div class="boxWrapper"><label>Your Message</label><textarea class="form-control"></textarea><div class="text-right"><button class="btn" id="commentCloser" style="margin-top: 10px; margin-right: 3px">Close</button><button class="btn" style="margin-top: 10px">Submit</button></div></div>');
                            $(".lineComments").append('<div class="conversation"><div class="reply"><p><span class="person"><a href="javascript:void(0)">Richard Sluder</a></span></p><p>Great! just what I needed. Thumbs up</p></div></div>');
                            $(".lineComments").append('<div class="conversation"><div class="reply"><p><span class="person"><a href="javascript:void(0)">Emilia</a></span></p><p>Easy and concise, but is there a faster version?</p></div></div>');
                        }
                        $(".lineComments").draggable();
                        $("#commentCloser").unbind().click(function() {
                            $(".lineComments").remove();
                        });
                    });
                }, 2000);
            },
            error: function (data) {
                console.log(window.location.href.split("/")[window.location.href.split("/").length-1]);
            }
        });
    },
    voteAlgorithmAjax = function(vote) {
        jQuery.ajax({
            method: 'post',
            url: "../users/votealgorithm",
            dataType: "json",
            data: {id: postId, vote: vote},
            success: function (data) {
                $("#upvoteSpan").html(data.upvotes);
                $("#downvoteSpan").html(data.downvotes);
            },
            fail: function(data) {
                console.log(data);
            }
        });
    };
    getPostData();
    $(".upvote a").click(function() {
        voteAlgorithmAjax(1);
    });
    $(".downvote a").click(function() {
        voteAlgorithmAjax(0);
    });
    $(".send-message #sendButton .btn").click(function() {
        var comment = $(".send-message textarea").val().trim();
        if(comment.length>0) {
            $(".send-message textarea").val("");
            jQuery.ajax({
                method: 'post',
                url: "../users/discussalgorithm",
                dataType: "json",
                data: {id: postId, comment: comment},
                success: function(data) {
                    comments = data;
                    populateComments();
                },
                fail: function(data) {
                    
                }
            });
        }
    });
});