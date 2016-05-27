$(document).ready(function () {
    var getApproval = function (upvotes, downvotes) {
            if (upvotes === 0) {
                return 0;
            }
            return upvotes / (upvotes + downvotes) * 100;
        },
        createList = function (data) {
            $(".postedAlgorithms").html("");
            $.each(data, function (index, value) {
                var toAppend = '<div class="postedAlgorithm">' +
                    (value.template ? '<h2><a target="_blank" href="users/editalgorithm/' + value.id + '">' + value.name + '</a> (<span>Language</span>: ' + value.language + ')</h2>': 
                    '<h2><a target="_blank" href="posts/' + value.id + '">' + value.name + '</a> (<span>Language</span>: ' + value.language + ')</h2>') +
                    
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
                    (value.template ? '<td><a target="_blank" href="users/editalgorithm?id=' + value.id + '">' + value.name + '</a></td>' : 
                    '<td><a target="_blank" href="algorithm?id=' + value.id + '">' + value.name + '</a></td>') +
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
        getLists = function (data) {
            jQuery.ajax({
                method: 'post',
                url: "post/searchalgorithm",
                dataType: "json",
                data: data,
                success: function (data) {
                    console.log(data);
                    //createList(data.data);
                    //createTable(data.data);
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
            tags: $("#keywords").val(),
            language: $("select[name='language']").val(),
            ratio: $("input[name='ratio']:checked").length ? true : false
        };
        console.log(data);
        getLists(data);
    });
});