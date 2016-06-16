/*global
    $,
    console,
    globalSettings
*/

$(document).ready(function () {
    
    'use strict';
    
    var requests = null,
        foundAlgorithmList = [],
        lastReportIndex,
        reportType,
        getApproval = function (upvotes, downvotes) {
            if (upvotes === 0) {
                return 0;
            }
            return upvotes / (upvotes + downvotes) * 100;
        },
        createList = function () {
            $(".searchedAlgorithms").html("");
            $.each(foundAlgorithmList, function (index, value) {
                if ($("[name='owned']:checked").length === 0 || ($("[name='owned']:checked").length === 1 && parseInt(value.user_id, 10) !== globalSettings.getUserData().id)) {
                    var toAppend = '<div class="postedAlgorithm" listindex="' + index + '">' +
                        '<h2><a target="_blank" href="posts/' + value.id + '">' + value.name + '</a> (<span>Language</span>: ' + value.language + ')</h2>' +
                        '<p><span>By</span>: <a href="profile/' + value.user_id + '">' + value.username + '</a></p>' +
                        '<p><span>Description</span>: ' + value.description + '</p>' +
                        '<p><span>Ratings</span>: ' + value.upvotes + ' upvotes, ' + value.downvotes + ' downvotes with an aproval of ' + getApproval(value.upvotes, value.downvotes) + '%</p>' +
                        '<p>' + value.views + ' views, 0 comments</p>' +
                        (globalSettings.getUserData().id !== parseInt(value.user_id, 10) ?
                                (value.reported === 0 ?
                                        '<p><button class="transparent reportAlgorithm">Report this algorithm</button></p>' :
                                        '<p><strong><em>Your report was successfully submitted. Thank you!</em></strong></p>')
                        : "") +
                        '</div>';
                    $(".searchedAlgorithms").append(toAppend);
                }
            });
            
            $(".reportAlgorithm").click(function () {
                lastReportIndex = $(this).closest(".postedAlgorithm").attr("listindex");
                reportType = "algorithms";
                $("#reportModal").modal("toggle");
            });
        },
        getRequestList = function (data) {
            var list = data,
                toAppend = "";
            $.each(list, function (index, value) {
                console.log(value);
                toAppend += "<div class='postedRequest' listindex='" + index + "'>" +
                    "<h2>" +
                        value.name +
                        "<span style='float: right; font-size: 16px; font-weight: 600; line-height: 26px; margin-right: 7px;'>by <a href='" + globalSettings.getRoot() + "/profile/" + value.user_id + "'>" + value.username + "</a></span>" +
                    "</h2>" +
                    "<p><span>Language</span>: " + value.language + "</p>" +
                    "<p><span>Description</span>: " + value.description + "</p>" +
                    "<p><button class='vote' req_id=" + value.id + "><span class='glyphicon glyphicon-plus " + (parseInt(value.userVote, 10) === 1 ? "green" : "gray") + "'></span></button>" + value.upvotes + " people upvoted this request.</p>" +
                    (globalSettings.getUserData().id !== parseInt(value.user_id, 10) ?
                                (value.reported === 0 ?
                                        '<p><button class="transparent reportRequest">Report this request</button></p>' :
                                        '<p><strong><em>Your report was successfully submitted. Thank you!</em></strong></p>')
                        : "") +
                    "</div>";
            });
            $("#requestModal .requested-box").html(toAppend);
            
            $(".reportRequest").click(function () {
                lastReportIndex = $(this).closest(".postedRequest").attr("listindex");
                reportType = "requests";
                $("#requestModal").modal("toggle");
                $("#reportModal").modal("toggle");
            });
            
            $("#requestModal .requested-box button.vote").click(function () {
                var data = {
                    data: {
                        id: $(this).attr("req_id")
                    }
                };
                $.ajax({
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
        filterRequests = function () {
            var newList = [],
                currentSearch = $("#requestModal #searchRequests").val();
            $.each(requests, function (index, value) {
                if (value.name.toLowerCase().indexOf(currentSearch.toLowerCase()) > -1 ||
                        value.description.toLowerCase().indexOf(currentSearch.toLowerCase()) > -1 ||
                        value.language.toLowerCase().indexOf(currentSearch.toLowerCase()) > -1) {
                    newList.push(value);
                }
            });
            getRequestList(newList);
        },
        getRequests = function () {
            $.ajax({
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
        getLists = function (data) {
            $.ajax({
                method: 'post',
                url: "post/searchalgorithm",
                dataType: "json",
                data: data,
                success: function (data) {
                    if (data.data.length > 0) {
                        $("#searchErrorMessage").addClass("hidden");
                        $(".searchedAlgorithms").removeClass("hidden");
                        foundAlgorithmList = data.data;
                        createList();
                    } else {
                        $(".searchedAlgorithms").html("");
                        $("#searchErrorMessage").removeClass("hidden").html("No results found based on your search arguments..");
                    }
                },
                error: function (data) {
                    console.log(data);
                }
            });
        };
    $("[data-target='#requestModal']").click(function () {
        $(".requestedAlgorithms").slideDown();
        $("#submit_algorithm_form").slideUp();
        $("#submitRequest").addClass("hidden");
        getRequests();
    });
    $("#keywords").tagit();
    $("#search_algorithms_form .btn").click(function (e) {
        e.preventDefault();
        var data = {
            tags: $("#search_algorithms_form #keywords").val(),
            language: $("#search_algorithms_form input[name='language']").val(),
            ratio: $("#search_algorithms_form input[name='ratio']:checked").length ? true : false
        };
        getLists(data);
    });
    $("#search_algorithms_form .btn").click();
    
    $("#submitRequest").click(function () {
        $("#submit_algorithm_form input[type='submit']").click();
    });
    $("#letMeRequest").click(function () {
        $(".requestedAlgorithms").slideUp();
        $("#submit_algorithm_form").slideDown();
        $("#submitRequest").removeClass("hidden");
    });
    
    $("#existentRequests").click(function () {
        $(".requestedAlgorithms").slideDown();
        $("#submit_algorithm_form").slideUp();
        $("#submitRequest").addClass("hidden");
    });
    
    $("#requestModal #searchRequests").keyup(function (e) {
        filterRequests();
    });
    $("#submit_algorithm_form").slideUp();
    
    $("#submitReport").click(function () {
        var data;
        switch(reportType) {
            case "algorithms":
                data = {
                    user_id: globalSettings.getUserData().id,
                    reported_id: foundAlgorithmList[lastReportIndex].id,
                    table: reportType,
                    reported_user_id: foundAlgorithmList[lastReportIndex].user_id,
                    user_reason: $("#reportModal select").val(),
                    user_description: $("#reportModal textarea").val()
                };
                break;
            default:
                data = {
                    user_id: globalSettings.getUserData().id,
                    reported_id: requests[lastReportIndex].id,
                    table: reportType,
                    reported_user_id: requests[lastReportIndex].user_id,
                    user_reason: $("#reportModal select").val(),
                    user_description: $("#reportModal textarea").val()
                };
        }
        console.log(data);
        
        $.ajax({
            method: 'post',
            url: globalSettings.getRoot() + "/users/report",
            dataType: "json",
            data: data,
            success: function (data) {
                if(reportType == "algorithms") {
                    foundAlgorithmList[lastReportIndex].reported = 1;
                    createList();
                } else {
                    getRequests();
                }
                $("#reportModal").modal("toggle");
            },
            error: function (data) {
            }
        });
    });
});