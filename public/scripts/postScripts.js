$(document).ready(function() {
    var postId = window.location.href.split("/")[window.location.href.split("/").length-1],
        yourVote = undefined,
        comments = undefined,
        populateComments = function() {
            var toAppend = "";
            $.each(comments, function(index, value) {
                console.log(value);
                value.text = value.text.split("\n").join("<br>");
                toAppend += '<div class="reply" tabindex="'+index+'">'+
                    '<p><span class="person"><a href="../users/"'+value.user_id+'">'+value.name+'</a></span> <span class="created"> on '+value.created_at+'</span></p>'+
                    '<p>'+value.text+'</p><hr>'+
                    '<p><a href="javascript:void(0)" class="likeComment">Like <span class="green">('+value.upvotes+')</span></a> | <a href="javascript:void(0)" class="dislikeComment">Dislike <span class="red">('+value.downvotes+')</span></a> | <a href="javascript:void(0)">Reply</a> | <a href="javascript:void(0)">Report</a> </p>'+
                    '</div>';
            });
            $("#algorithmComments").html(toAppend);
            $(".dislikeComment").click(function() {
                tabindex = $(this).closest(".reply").attr("tabindex");
                voteCommentAjax(0,comments[tabindex].id, postId, tabindex);
            });
            $(".likeComment").click(function() {
                tabindex = $(this).closest(".reply").attr("tabindex");
                voteCommentAjax(1,comments[tabindex].id, postId, tabindex);
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
    voteCommentAjax = function(vote, comment_id, algorithm_id, tabindex) {
        jQuery.ajax({
            method: 'post',
            url: "../users/votecomment",
            dataType: "json",
            data: {comment_id: comment_id, vote: vote, algorithm_id: algorithm_id},
            success: function (data) {
                $(".reply[tabindex='"+tabindex+"'] .green").html("("+data.upvotes+")");
                $(".reply[tabindex='"+tabindex+"'] .red").html("("+data.downvotes+")");
                console.log(data);
            },
            fail: function(data) {
                console.log(data);
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
    $("#sendButton .btn").click(function() {
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