$(document).ready(function() {
    var profileId = window.location.href.split("#")[0].split("/")[window.location.href.split("/").length-1],
        profileData = undefined,
        root = "http://localhost:8080",
        populate = function() {
            console.log(profileData);
            $("#profileOf").html(profileData.userData.lastName + " " + profileData.userData.firstName);
            $(".commend-star #commendationNumber").html(profileData.userData.commendations.number);
            if(profileData.userData.commendations.commendedByYou == true) {
                $(".commend-star").addClass("green");
            }
        },
        getProfileDetails = function() {
            console.log(profileId);
            jQuery.ajax({
                method: 'post',
                url: root+"/profiledetails",
                dataType: "json",
                data: {id: profileId},
                success: function (data) {
                    profileData = data;
                    populate();
                },
                fail: function(data) {
                    console.log(data);
                }
            });
        };
    $(".commend-star").click(function() {
        jQuery.ajax({
            method: 'put',
            url: root+"/users/commend",
            dataType: "json",
            data: {id: profileId},
            success: function (data) {
                if($(".commend-star").hasClass("green")) {
                    $(".commend-star").removeClass("green");
                    profileData.userData.commendations.commendedByYou == false;
                } else {
                    $(".commend-star").addClass("green");
                    profileData.userData.commendations.commendedByYou == true;
                }
                profileData.userData.commendations.number = data.number;
                $(".commend-star #commendationNumber").html(profileData.userData.commendations.number);
            },
            fail: function(data) {
                console.log(data);
            }
        });
    });
    getProfileDetails();
});