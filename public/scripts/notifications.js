$(document).ready(function() {
    var root = "http://localhost:8080",
        notifications = [],
        startAjax = function() {
            jQuery.ajax({
                method: 'get',
                url: root + "/notifications/all",
                success: function (data) {
                    notifications = data.notifications;
                    if(data.messageCount > 0) {
                        $("span.messageCount#messageNotifCount").html((data.messageCount > 99 ? "99+" : data.messageCount));
                        $("span.messageCount#messageNotifCount").removeClass("hidden");
                    }
                    if(data.groupCount > 0) {
                        $("span.messageCount#groupNotifCount").html((data.groupCount > 99 ? "99+" : data.groupCount));
                        $("span.messageCount#groupNotifCount").removeClass("hidden");
                    }
                    populateNotificationsBar();
                    populateNotificationsPage();
                }
            });
        },
        createURL = function(url, reference, type) {
            switch(type) {
                case 'New reply!':
                    return url+"#comment"+reference;
                case 'New comment!':
                    return url+"#comment"+reference;
                case 'New line comment!':
                    return url+"#line"+(parseInt(reference, 10)+1);
                default:
                    return url;
            }
        },
        populateNotificationsBar = function() {
            var toAdd = "",
                count = 0;
            $.each(notifications, function (index, value) {
                if (value.seen === 0) {
                    count += 1;
                }
                if (index<5) {
                    toAdd += 
                        "<li listindex='"+index+"'"+(value.checked_out == 0 ? " class='unread'" :"")+">"+
                        "<a href='"+createURL(root+value.url,value.reference,value.title)+"'><strong>"+value.title + "</strong> <span style='float:right'>"+value.created_at+"</span>"+
                        "<hr><strong>"+ value.name + "</strong> " + value.text +
                        "</a>" +
                        "</li>";
                }
            });
            $("ul .notifications li[listindex]").remove();
            $("ul .notifications").prepend(toAdd);
            $("ul .notifications li[listindex] a").click(function(e) {
                e.preventDefault();
                var clickedNotification = notifications[$(this).closest("li[listindex]").attr("listindex")];
                jQuery.ajax({
                    method: 'put',
                    url: root + "/notifications/checknotification",
                    data: {id:clickedNotification.id},
                    dataType: "json",
                    success: function (data) {
                        window.location.href = createURL(root+clickedNotification.url, clickedNotification.reference, clickedNotification.title);
                    }
                });
            });
            if(count > 0) {
                $("span.messageCount#notifCount").html((count > 99 ? "99+" : count));
                $("span.messageCount#notifCount").removeClass("hidden");
            }
        },
        populateNotificationsPage = function() {
            if($(".allNotifications").length > 0) {
                var toAdd = "";
                $.each(notifications, function (index, value) {
                    toAdd += 
                        "<div listindex='"+index+"'class='boxWrapper extendedNotification "+(value.checked_out == 0 ? "unread" :"")+"'>"+
                        "<p><strong>"+value.title + "</strong> <span style='float:right'>"+value.created_at+"</span>"+
                        "<hr><strong><a href='"+root+value.url+"/users/"+value.user_id+"'>"+ value.name + "</a></strong> " + value.text +
                        "<span class='buttons'>"+
                            "<a href='javascript:void(0)' class='removeNotif'><span class='glyphicon glyphicon-remove'></a>"+
                            "<a href='javascript:void(0)' class='toLink'><span class='glyphicon glyphicon-link'></a>" +
                        "</span>" +
                        (value.what_was_said == "" ?
                         "" :
                         "<p><em>&#8222;"+value.what_was_said+"&#8220;</em></p>")+

                        "</p>" +
                        "</div>";
                });
                $(".allNotifications").html(toAdd);
                $(".removeNotif").click(function() {
                    var listindex = $(this).closest('.extendedNotification').attr("listindex");
                    jQuery.ajax({
                        method: 'delete',
                        url: root + "/notifications/delete",
                        data: {id:notifications[listindex].id},
                        dataType: "json",
                        success: function (data) {
                            startAjax();
                        }
                    });
                });
                $(".toLink").click(function() {
                    var listindex = $(this).closest('.extendedNotification').attr("listindex");
                    jQuery.ajax({
                        method: 'put',
                        url: root + "/notifications/checknotification",
                        data: {id:notifications[listindex].id},
                        dataType: "json",
                        success: function (data) {
                            $(".extendedNotification[listindex='"+listindex+"']").removeClass("unread");
                            window.location.href = createURL(root+notifications[listindex].url, notifications[listindex].reference, notifications[listindex].title);
                        }
                    });
                });
            }
        };
    startAjax();
    $(".seeNotifs").click(function() {
        jQuery.ajax({
            method: 'put',
            url: root + "/notifications/seeall"
        });
    });
    setInterval(function() {
        startAjax();
    }, 30000);
});