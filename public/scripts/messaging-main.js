$(document).ready(function () {
    var root = globalSettings.getRoot(),
        messageHistory = undefined,
        crumbs = undefined,
        initiated = false,
        timestamp = "",
        userlist = undefined,
        populateCrumbs = function(data) {
            var toAppend ="";
            crumbs = data;
            $.each(crumbs, function(index, value) {

                toAppend +='<li '+ (value.seen == 0 ? 'class="notSeen"': "")+'>'+
                    '<a href="'+root+'/messages/'+value.link +'">'+
                    '<span>'+value.name+'</span>'+
                    '<span style="float:right; margin-right: 4px" class="newSpan">'+(value.seen == 0 ? 'New!' : "")+'</span>' +
                    '<p>'+value.from + ": " + value.message+'</p>'+
                    '</a>'+
                    '</li>';
            });
            $(".sidebar ul.messageList").html(toAppend);
        },
        getPostData = function () {
            jQuery.ajax({
                method: 'post',
                url: root+"/messaging/messagecrumb",
                data: {
                    timestamp: timestamp
                },
                success: function (data) {
                    if(data.timestamp !== timestamp) {
                        timestamp = data.timestamp; 
                        populateCrumbs(data.crumb);
                    }
                },
                error: function (data) {
                    console.log("error");
                }
            });

        };

    getPostData();
    setInterval(function() {
        getPostData();
    }, 1500);
    $.ajax({
        method: 'get',
        url: root+'/userlist',
        success: function(data) {
           userlist = data;
        }
    });
    $(".search-user #searchUserButton .btn").click(function() {
        var comment = $(".search-user input").val().trim(),
            toAppend = "";
        $.each(userlist,function(index,value) {
            if(comment === "" || comment.toLowerCase().split(" ").indexOf(value.first_name.toLowerCase())>-1 ||
               comment.toLowerCase().split(" ").indexOf(value.last_name.toLowerCase())>-1) {
                toAppend +='<div class="boxWrapper">'+
                    '<p><span>Name: </span>'+value.last_name+ " " +value.first_name + '</p>'+
                    '<a href="'+root+'/profile/'+value.id+'">See profile</a> | <a href="'+root+'/messages/'+value.id+'">Send message</a>'+
                    '</div>';
            }
        });
        if(toAppend == "") {
            toAppend = "<div class='boxwrapper'><p>No results based on your queries. Please try again.</p></div>";
        }
        $(".searchResults").html(toAppend);
    });
});