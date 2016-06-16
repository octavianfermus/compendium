/*global
    $
*/

var globalSettings = (function globalSettings() {
    
    'use strict';
    
    var root = "http://localhost:8080",
        user_data;
    
    $.ajax({
        method: "get",
        url: root + "/users/userdata",
        success: function (data) {
            if (data.state === "success") {
                user_data = data.data;
            }
        }
    });
    
    return {
        getRoot : function () {
            var toReturn = root;
            return toReturn;
        },
        getUserData : function () {
            var toReturn = $.extend({}, user_data);
            return toReturn;
        }
    };
    
}());