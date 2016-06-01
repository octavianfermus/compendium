$(document).ready(function () {
    var requests = null,
        getApproval = function (upvotes, downvotes) {
            if (upvotes === 0) {
                return 0;
            }
            return upvotes / (upvotes + downvotes) * 100;
        },
        createList = function (data) {
            $(".searchedAlgorithms").html("");
            $.each(data, function (index, value) {
                var toAppend = '<div class="postedAlgorithm">' +
                    '<h2><a target="_blank" href="posts/' + value.id + '">' + value.name + '</a> (<span>Language</span>: ' + value.language + ')</h2>' +            
                    '<p><span>By</span>: <a href="users/' + value.user_id + '">' + value.username + '</a></p>' +
                    '<p><span>Description</span>: ' + value.description + '</p>' +
                    '<p><span>Ratings</span>: ' + value.upvotes + ' upvotes, ' + value.downvotes + ' downvotes with an aproval of ' + getApproval(value.upvotes, value.downvotes) + '%</p>' +
                    '<p>' + value.views + ' views, 0 comments</p>' +
                    '</div>';
                $(".searchedAlgorithms").append(toAppend);
            });
        },
        createTable = function (data) {
            $(".searchedAlgorithmsTable tbody").html("");
            $.each(data, function (index, value) {
                var toAppend = '<tr>' +
                    '<td><a target="_blank" href="posts/' + value.id + '">' + value.name + '</a></td>' +
                    '<td>' + value.language + '</td>' +
                    '<td>' + value.upvotes + '</td>' +
                    '<td>' + value.downvotes + '</td>' +
                    '<td>' + getApproval(value.upvotes, value.downvotes) + '% </td>' +
                    '<td>' + value.views + '</td>' +
                    '<td> 0 </td>' +
                    '<td>' + value.username+ '</td>' +
                    '</tr>';
                $(".searchedAlgorithmsTable tbody").append(toAppend);
            });
        },
        getRequestList = function(data) {
            var data = data,
                    toAppend="";
                $.each(data,function(index,value) {
                    toAppend +="<div>" +
                        "<h2>"+value.name+"</h2>" +
                        "<p><span>Language</span>: "+ value.language + "</p>"+
                        "<p><span>Description</span>: "+ value.description + "</p>"+
                        "<p><button req_id="+value.id+"><span class='glyphicon glyphicon-plus "+(value.userVote == 1 ? "green" : "gray")+"'></span></button>"+value.upvotes+" people upvoted this request.</p>" +
                        "</div>";
                });
                $("#requestModal .requested-box").html(toAppend);
                $("#requestModal .requested-box button").click(function() {
                    var data = {
                        data: {
                            id: $(this).attr("req_id")
                        }
                    };
                    jQuery.ajax({
                        method: 'put',
                        url: "users/voterequest",
                        dataType: "json",
                        data: data,
                        success: function (data) {
                            getRequests();
                        },
                        error: function (data) {
                            console.log(data);
                        } 
                    });
                });
        },
        getRequests = function() {
            jQuery.ajax({
                method: 'get',
                url: "users/viewrequests",
                success: function (data) {
                    requests = data.data;
                    getRequestList(requests);
                    filterRequests();
                },
                error: function (data) {
                    console.log(data);
                } 
            });
        },
        filterRequests = function() {
            var newList = [],
            currentSearch = $("#requestModal #searchRequests").val();
            $.each(requests,function(index,value) {
                if(value.name.toLowerCase().indexOf(currentSearch.toLowerCase()) >-1 || 
                   value.description.toLowerCase().indexOf(currentSearch.toLowerCase())>-1 ||
                  value.language.toLowerCase().indexOf(currentSearch.toLowerCase())>-1) {
                    newList.push(value);
                }
            });
            getRequestList(newList);
        },
        getLists = function (data) {
            jQuery.ajax({
                method: 'post',
                url: "post/searchalgorithm",
                dataType: "json",
                data: data,
                success: function (data) {
                    if(data.data.length > 0) {
                        $("#searchErrorMessage").addClass("hidden");
                        $(".searchedAlgorithms").removeClass("hidden");
                        $(".searchedAlgorithmsTable").addClass("hidden");
                        $(".switcher#searchPostsSwitcher").removeClass("hidden");
                        createList(data.data);
                        createTable(data.data);
                    } else {
                        $(".searchedAlgorithms").html("");
                        $(".searchedAlgorithmsTable tbody").html("");
                        $(".searchedAlgorithmsTable").addClass("hidden");
                        $(".searchedAlgorithms").addClass("hidden");
                        $(".switcher#searchPostsSwitcher").addClass("hidden");
                        $("#searchErrorMessage").removeClass("hidden").html("No results found based on your search arguments..");
                    } 
                },
                error: function (data) {
                    console.log(data);
                } 
            });
        };
    $("[data-target='#requestModal']").click(function() {
        $(".requestedAlgorithms").slideDown();
        $("#submit_algorithm_form").slideUp();
        $("#submitRequest").addClass("hidden");
        getRequests();
    });
    $("#keywords").tagit();
    $("#search_algorithms_form .btn").click(function(e) {
        e.preventDefault();
        var data = {
            tags: $("#search_algorithms_form #keywords").val(),
            language: $("#search_algorithms_form input[name='language']").val(),
            ratio: $("#search_algorithms_form input[name='ratio']:checked").length ? true : false
        };
        getLists(data);
    });
    $(".switcher#searchPostsSwitcher").click(function() {
        if($(this).siblings("table").hasClass("hidden")) {
            $(this).siblings("table").removeClass("hidden");
            $(this).siblings(".searchedAlgorithms").addClass("hidden");
        } else {
            $(this).siblings("table").addClass("hidden");
            $(this).siblings(".searchedAlgorithms").removeClass("hidden");
        }
    });
    $("#submitRequest").click(function() {
        $("#submit_algorithm_form input[type='submit']").click();
    });
    $("#letMeRequest").click(function() {
        $(".requestedAlgorithms").slideUp();
        $("#submit_algorithm_form").slideDown();
        $("#submitRequest").removeClass("hidden");
    });
    
    $("#existentRequests").click(function() {
        $(".requestedAlgorithms").slideDown();
        $("#submit_algorithm_form").slideUp();
        $("#submitRequest").addClass("hidden");
    });
    
    $("#requestModal #searchRequests").keyup(function(e) {
        filterRequests();
    });
    $("#submit_algorithm_form").slideUp();
});