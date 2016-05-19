$(document).ready(function () {
    var givenId = undefined,
        givenAction = undefined,
        deletePublishModifier = function(id,action) {
            givenId = id;
            givenAction = action;
            $('#confirmationModal').modal('toggle');
        }
        publishOrDelete = function (template, id) {
            if (template === 0) {
                $(".remove").unbind().click(function() {
                    deletePublishModifier($(this).attr("givenId"),"remove");
                });
                return '<a href="javascript:void(0)" class="remove" givenid=' + id + '><span class="glyphicon glyphicon-remove"></span> Delete</a>';
            }
            $(".publish").unbind().click(function() {
            deletePublishModifier($(this).attr("givenId"),"publish");
        });
            return '<a href="javascript:void(0)" class="publish" givenid=' + id + '><span class="glyphicon glyphicon-pencil"></span> Publish</a>';
            
        },
        getApproval = function (upvotes, downvotes) {
            if (upvotes === 0) {
                return 0;
            }
            return upvotes / (upvotes + downvotes) * 100;
        },
        createList = function (data) {
            $(".postedAlgorithms").html("");
            $.each(data, function (index, value) {
                var toAppend = '<div class="postedAlgorithm">' +
                    '<h2><a target="_blank" href="algorithm?id=' + value.id + '">' + value.name + '</a> (<span>Language</span>: ' + value.language + ')</h2>' +
                    '<p><span>Description</span>: ' + value.description + '</p>' +
                    '<p><span>Ratings</span>: ' + value.upvotes + ' upvotes, ' + value.downvotes + ' downvotes with an aproval of ' + getApproval(value.upvotes, value.downvotes) + '%</p>' +
                    '<p>' + value.views + ' views, 0 comments</p>' +
                    '<p>' + publishOrDelete(value.template, value.id) + '</p>' +
                    '</div>';
                $(".postedAlgorithms").append(toAppend);
            });
            
        },
        createTable = function (data) {
            $(".postedAlgorithmsTable tbody").html("");
            $.each(data, function (index, value) {
                var toAppend = '<tr>' +
                    '<td><a target="_blank" href="algorithm?id=' + value.id + '">' + value.name + '</a></td>' +
                    '<td>' + value.language + '</td>' +
                    '<td>' + value.upvotes + '</td>' +
                    '<td>' + value.downvotes + '</td>' +
                    '<td>' + getApproval(value.upvotes, value.downvotes) + '% </td>' +
                    '<td>' + value.views + '</td>' +
                    '<td> 0 </td>' +
                    '<td>' + publishOrDelete(value.template, value.id) + '</td>' +
                    '</tr>';
                $(".postedAlgorithmsTable tbody").append(toAppend);
            });
        },
        getLists = function () {
            jQuery.ajax({
                method: 'get',
                url: "users/postedalgorithms",
                dataType: "json",
                success: function (data) {
                    console.log(data);
                    createList(data.data);
                    createTable(data.data);
                },
                error: function (data) {

                }
                
            });
            
        },
        deletePost = function(id) {
            var data = {
                data: {
                    id: id
                }
            };
            jQuery.ajax({
                method: 'PUT',
                url: "users/deletealgorithm",
                dataType: "json",
                data: data,
                success: function (data) {
                    console.log("success");
                    console.log(data);
                },
                error: function (data) {
                    console.log("error");
                    console.log(data);
                }
            });
            getLists();
        },
        publishPost = function(id) {
            var data = {
                data: {
                    id: id
                }
            };
            jQuery.ajax({
                method: 'PUT',
                url: "users/publishalgorithm",
                dataType: "json",
                data: data,
                success: function (data) {
                    console.log("success");
                    console.log(data);
                },
                error: function (data) {
                    console.log("error");
                    console.log(data);
                }
            });
            getLists();
        };
    $(".switcher").click(function() {
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
    $("#executeCommand").click(function() {
        if(givenAction=="publish") {
            publishPost(givenId);
        }
        if(givenAction=="remove") {
            deletePost(givenId);
        }
    });
    getLists();
});