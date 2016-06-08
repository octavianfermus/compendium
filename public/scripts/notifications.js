$(document).ready(function() {
    var root = "http://localhost:8080",
        notifications = [],
        startAjax = function() {
            jQuery.ajax({
                method: 'get',
                url: root + "/users/notifications",
                success: function (data) {
                    notifications = data;
                    populateNotificationsBar();
                    populateNotificationsPage();
                }
            });
        },
        createURL = function(url, reference, type) {
            console.log(url,reference,type);
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
                    if(value.title === "New line comment!" || value.title === "New comment!" || value.title === "New reply!" || value.title ==="Request answered!") {
                        toAdd += 
                            "<li listindex='"+index+"'"+(value.checked_out == 0 ? " class='unread'" :"")+">"+
                            "<a href='"+createURL(root+value.url,value.reference,value.title)+"'><strong>"+value.title + "</strong> <span style='float:right'>"+value.created_at+"</span>"+
                            "<hr><strong>"+ value.name + "</strong> " + value.text +
                            "</a>" +
                            "</li>";
                    }
                }
            });
            $("ul .notifications li[listindex]").remove();
            $("ul .notifications").prepend(toAdd);
            if(count > 0) {
                $("span.messageCount").html((count > 99 ? "99+" : count));
                $("span.messageCount").removeClass("hidden");
            }
        },
        populateNotificationsPage = function() {
            if($(".allNotifications").length > 0) {
                var toAdd = "";
                $.each(notifications, function (index, value) {
                    if(value.title === "New line comment!" || value.title === "New comment!" || value.title === "New reply!" || value.title ==="Request answered!") {
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
                    }
                });
                $(".allNotifications").html(toAdd);
                $(".removeNotif").click(function() {
                    var listindex = $(this).closest('.extendedNotification').attr("listindex");
                    jQuery.ajax({
                        method: 'delete',
                        url: root + "/users/deletenotification",
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
                        url: root + "/users/checknotification",
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
    
    setInterval(function() {
        startAjax();
    }, 10000);
});