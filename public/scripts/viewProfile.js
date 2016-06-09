$(document).ready(function() {
    var profileId = window.location.href.split("#")[0].split("/")[window.location.href.split("/").length-1],
        profileData = undefined,
        root = "http://localhost:8080",
        algorithms = undefined,
        getApproval = function (upvotes, downvotes) {
            if (upvotes === 0) {
                return 0;
            }
            return upvotes / (upvotes + downvotes) * 100;
        },
        createList = function(data) {
           $(".postedAlgorithms").html("");
            $.each(algorithms, function (index, value) {
                var toAppend = '<div class="postedAlgorithm">' +
                    '<h2><a target="_blank" href="../posts/' + value.id + '">' + value.name + '</a> (<span>Language</span>: ' + value.language + ')</h2>' +          
                    '<p><span>Description</span>: ' + (value.description.trim().length ? value.description : "None") + '</p>' +
                    (!value.template ? '<p><span>Ratings</span>: ' + value.upvotes + ' upvotes, ' + value.downvotes + ' downvotes with an aproval of ' + getApproval(value.upvotes, value.downvotes) + '%</p>' +
                    '<p>' + value.views + ' views, 0 comments</p>' :"") +
                    '</div>';
                $(".postedAlgorithms").append(toAppend);
            }); 
        },
        createTable = function(data) {
            $(".postedAlgorithmsTable tbody").html("");
            $.each(algorithms, function (index, value) {
                var toAppend = '<tr>' +
                    '<td><a target="_blank" href="../posts/' + value.id + '">' + value.name + '</a></td>' +
                    '<td>' + value.language + '</td>' +
                    '<td>' + value.upvotes + '</td>' +
                    '<td>' + value.downvotes + '</td>' +
                    '<td>' + getApproval(value.upvotes, value.downvotes) + '% </td>' +
                    '<td>' + value.views + '</td>' +
                    '<td> 0 </td>' +
                    '</tr>';
                $(".postedAlgorithmsTable tbody").append(toAppend);
            });
        },
        populate = function() {
            $("#profileOf").html(userData.lastName + " " + userData.firstName);
            $(".commend-star #commendationNumber").html(userData.commendations.number);
            if(userData.commendations.commendedByYou == true) {
                $(".commend-star").addClass("green");
            }
            if(algorithms.length > 0) {
                $(".switcher#postsSwitcher").removeClass("hidden");
                createList(algorithms);
                createTable(algorithms);
            } else {
                $("#postedErrorMessage").removeClass("hidden");
                $("#postedErrorMessage").html("This user doesn't currently have any posted algorithms..</p>");    
                $(".postedAlgorithms").html("");
                $(".postedAlgorithmsTable tbody").html("");
                $(".switcher#postsSwitcher").addClass("hidden");
                $(".postedAlgorithmsTable").addClass("hidden");
            }
            
        },
        getProfileDetails = function() {
            jQuery.ajax({
                method: 'post',
                url: root+"/profiledetails",
                dataType: "json",
                data: {id: profileId},
                success: function (data) {
                    userData = data.userData;
                    algorithms = data.algorithms;
                    populate();
                },
                fail: function(data) {
                    console.log(data);
                }
            });
        };
    $(".commend-star").click(function() {
        if(profileData.userData.commendations.youCantCommend == false) {
            jQuery.ajax({
                method: 'put',
                url: root+"/users/commend",
                dataType: "json",
                data: {id: profileId},
                success: function (data) {
                    if($(".commend-star").hasClass("green")) {
                        $(".commend-star").removeClass("green");
                        userData.commendations.commendedByYou == false;
                    } else {
                        $(".commend-star").addClass("green");
                        userData.commendations.commendedByYou == true;
                    }
                    userData.commendations.number = data.number;
                    $(".commend-star #commendationNumber").html(userData.commendations.number);
                },
                fail: function(data) {
                    console.log(data);
                }
            });
        }
    });
    getProfileDetails();
    $(".switcher#postsSwitcher").click(function() {
        if($(this).siblings("table").hasClass("hidden")) {
            $(this).siblings("table").removeClass("hidden");
            $(this).siblings(".postedAlgorithms").addClass("hidden");
        } else {
            $(this).siblings("table").addClass("hidden");
            $(this).siblings(".postedAlgorithms").removeClass("hidden");
        }
        $(".publish").unbind().click(function() {
            deletePublishModifier($(this).attr("givenId"),"publish");
        });
        $(".remove").unbind().click(function() {
            deletePublishModifier($(this).attr("givenId"),"remove");
        });
    });
});