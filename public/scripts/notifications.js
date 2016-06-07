$(document).ready(function() {
    var root = "http://localhost:8080",
        notifications = [],
        populateNotificationsPage = function() {
            if($(".allNotifications").length > 0) {
                var toAdd = "";
                $.each(notifications, function (index, value) {
                    if(value.title === "New comment!" || value.title === "New reply!" || value.title ==="Request answered!") {
                        toAdd += 
                            "<div tabindex='"+index+"'class='boxWrapper extendedNotification "+(value.checked_out == 0 ? "unread" :"")+"'>"+
                            "<a href='"+root+value.url+"'><strong>"+value.title + "</strong> <span style='float:right'>"+value.created_at+"</span>"+
                            "<hr><strong>"+ value.name + "</strong> " + value.text +
                            "</a>" +
                            "<span class='buttons'>"+
                            "<a href='javascript:void(0)'><span class='glyphicon glyphicon-chevron-down'></a>  "+
                            "<a href='javascript:void(0)'><span class='glyphicon glyphicon-link'></a>"+
                            "</span>"+
                            "</div>";
                    }
                });
                $(".allNotifications").html(toAdd);
            }
        };
    jQuery.ajax({
        method: 'get',
        url: root + "/users/notifications",
        success: function (data) {
            var toAdd = "",
                count = 0;
            notifications = data;
            $.each(data, function (index, value) {
                if (value.seen === 0) {
                    count += 1;
                }
                console.log(value);
                if (index<5) {
                    if(value.title === "New comment!" || value.title === "New reply!" || value.title ==="Request answered!") {
                        toAdd += 
                            "<li"+(value.checked_out == 0 ? " class='unread'" :"")+">"+
                            "<a href='"+root+value.url+"'><strong>"+value.title + "</strong> <span style='float:right'>"+value.created_at+"</span>"+
                            "<hr><strong>"+ value.name + "</strong> " + value.text +
                            "</a>" +
                            "</li>";
                    }
                }
            });
            $("ul .notifications").prepend(toAdd);
            if(count > 0) {
                $("span.messageCount").html((count > 99 ? "99+" : count));
                $("span.messageCount").removeClass("hidden");
            }
            populateNotificationsPage();
        }
    });
    
});