$(document).ready(function() {
    var root = "http://localhost:8080";
    jQuery.ajax({
        method: 'get',
        url: root+"/users/notifications",
        success: function (data) {
            var toAdd = "",
                count = 0;
            $.each(data, function(index,value) {
                if(value.seen === 0) {
                    count +=1;
                }
                console.log(value);
                if(index<5) {
                    if(value.title === "New comment!" || value.title === "New reply!" || value.title ==="Request answered!") {
                        toAdd = 
                            "<li"+(value.seen == 0 ? " class='unread'" :"")+">"+
                            "<a href='"+root+value.url+"'><strong>"+value.title + "</strong>"+
                            "<hr><strong>"+ value.name + "</strong> " + value.text +
                            "<p class='text-right'>"+value.created_at+"</p>"+
                            "</a>" +
                            "</li>" + toAdd;
                    }
                }
            });
            $("ul .notifications").prepend(toAdd);
            if(count > 0) {
                $("span.messageCount").html((count > 99 ? "99+" : count));
                $("span.messageCount").removeClass("hidden");
            }
        }
    });
});