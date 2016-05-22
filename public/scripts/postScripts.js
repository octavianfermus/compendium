$(document).ready(function() {
    var postId = window.location.href.split("/")[window.location.href.split("/").length-1],
        getPostData = function () {
        jQuery.ajax({
            method: 'get',
            url: "../post/postdata",
            dataType: "json",
            data: {id: postId},
            success: function (data) {
                console.log(data);
                $("#creatorUsername").html(data.username);
                $("#upvoteSpan").html(data.upvotes);
                $("#downvoteSpan").html(data.downvotes);
                $("#viewSpan").html(data.views);
                $("#algorithmName").html(data.name);
                $("#originalLink").attr("href",data.original_link);
                $("#algorithmDescription").append(data.description);
                $("#language").append(data.language);
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

    };
    getPostData();
});