$(document).ready(function () {
    var getApproval = function (upvotes, downvotes) {
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
});