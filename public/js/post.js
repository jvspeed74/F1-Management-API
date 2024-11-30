/***********************************************************************************************************
 ******                            Show Posts                                                         ******
 **********************************************************************************************************/
//This function shows all posts. It gets called when a user clicks on the Post link in the nav bar.

// Pagination, sorting, and limiting are disabled
function showPosts(offset = 0) {
    console.log('show all messages');

    // const url = baseUrl_API + '/messages';

    //if the selection list exists, retrieve the selected option value; otherwise, set a default value.
    let limit = ($("#post-limit-select").length) ? $('#post-limit-select option:checked').val() : 5;
    let sort = ($("#post-sort-select").length) ? $('#post-sort-select option:checked').val() : "id:asc";

    //construct the request url
    const url = baseUrl_API + '/messages?limit=' + limit + "&offset=" + offset + "&sort=" + sort;

//define AXIOS request
    axios({
        method: 'get',
        url: url,
        cache: true,
        headers: {"Authorization": "Bearer " + jwt}
    })
        .then(function (response) {
            displayPosts(response.data);
        })
        .catch(function (error) {
            handleAxiosError(error);
        });
}

//Callback function: display all posts; The parameter is a promise returned by axios request.
function displayPosts(response) {
    //console.log(response);
    let _html;
    _html =
        "<div class='content-row content-row-header'>" +
        "<div class='post-id'>Message ID</></div>" +
        "<div class='post-body'>Message Body</></div>" +
        "<div class='post-create'>Create Time</div>" +
        "<div class='post-update'>Update Time</div>" +
        "</div>";
    let posts = response.data;
    posts.forEach(function (post, x) {
        let cssClass = (x % 2 == 0) ? 'content-row' : 'content-row content-row-odd';
        _html += "<div class='" + cssClass + "'>" +
            "<div class='post-id'>" +
            "<span class='list-key' onclick=showComments('" + post.id + "') title='Get post details'>" + post.id + "</span>" +
            "</div>" +
            "<div class='post-body'>" + post.body + "</div>" +
            "<div class='post-create'>" + post.created_at + "</div>" +
            "<div class='post-update'>" + post.updated_at + "</div>" +
            "</div>" +
            "<div class='container post-detail' id='post-detail-" + post.id + "' style='display: none'></div>";
    });


    //Add a div block for pagination links and selection lists for limiting and sorting courses
    _html += "<div class='content-row course-pagination'><div>";

//pagination
    _html += paginatePosts(response);

//items per page
    _html += limitPosts(response);

//sorting
    _html += sortPosts(response);

//end the div block
    _html += "</div></div>";

    //Finally, update the page
    updateMain('Messages', 'All Messages', _html);
}


/***********************************************************************************************************
 ******                            Show Comments made for a message                                   ******
 **********************************************************************************************************/

/* Display all comments. It get called when a user clicks on a message's id number in
 * the message list. The parameter is the message id number.
*/
function showComments(number) {
    console.log('get a message\'s all comments');

    let url = baseUrl_API + '/messages/' + number + '/comments';
    axios({
        method: 'get',
        url: url,
        cache: true,
        headers: {"Authorization": "Bearer " + jwt}
    })
        .then(function (response) {
            //console.log(response.data);
            displayComments(number, response);
        })
        .catch(function (error) {
            handleAxiosError(error);
        });
}


// Callback function that displays all details of a course.
// Parameters: course number, a promise
function displayComments(number, response) {
    let _html = "<div class='content-row content-row-header'>Comments</div>";
    let comments = response.data;
    //console.log(number);
    //console.log(comments);
    comments.forEach(function (comment, x) {
        _html +=
            "<div class='post-detail-row'><div class='post-detail-label'>Comment ID</div><div class='post-detail-field'>" + comment.id + "</div></div>" +
            "<div class='post-detail-row'><div class='post-detail-label'>Comment Body</div><div class='post-detail-field'>" + comment.body + "</div></div>" +
            "<div class='post-detail-row'><div class='post-detail-label'>Create Time</div><div class='post-detail-field'>" + comment.created_at + "</div></div>";
    });

    $('#post-detail-' + number).html(_html);
    $("[id^='post-detail-']").each(function () {   //hide the visible one
        $(this).not("[id*='" + number + "']").hide();
    });

    $('#post-detail-' + number).toggle();
}

/****************************************************************************************************
 *********                  This function handles errors occurred by an AXIOS request.     **********
 ***************************************************************************************************/

function handleAxiosError(error) {
    let errMessage;
    if (error.response) {
        // The request was made and the server responded with a status code of 4xx or 5xx
        errMessage = {"Code": error.response.status, "Status": error.response.data.status};
    } else if (error.request) {
        // The request was made but no response was received
        errMessage = {"Code": error.request.status, "Status": error.request.data.status};
    } else {
        // Something happened in setting up the request that triggered an error
        errMessage = JSON.stringify(error.message, null, 4);
    }

    showMessage('Error', errMessage);
}

/****************************************************************************************************
 *********                  Paginating, sorting, and limiting courses                      **********
 ***************************************************************************************************/
//paginate all messages
function paginatePosts(response) {
    //calculate the total number of pages
    let limit = response.limit;
    let totalCount = response.totalCount;
    let totalPages = Math.ceil(totalCount / limit);

    //determine the current page showing
    let offset = response.offset;
    let currentPage = offset / limit + 1;

    //retrieve the array of links from response json
    let links = response.links;

    //convert an array of links to JSON document. Keys are "self", "prev", "next", "first", "last"; values are offsets.
    let pages = {};

    //extract offset from each link and store it in pages
    links.forEach(function (link) {
        let href = link.href;
        let offset = href.substr(href.indexOf('offset') + 7);
        pages[link.rel] = offset;
    });

    if (!pages.hasOwnProperty('prev')) {
        pages.prev = pages.self;
    }
    if (!pages.hasOwnProperty('next')) {
        pages.next = pages.self;
    }

    //generate HTML code for links
    let _html = `Showing Page ${currentPage} of ${totalPages}&nbsp;&nbsp;&nbsp;&nbsp;
                <a href='#post' title="first page" onclick='showPosts(${pages.first})'> << </a>
                <a href='#post' title="previous page" onclick='showPosts(${pages.prev})'> < </a>
                <a href='#post' title="next page" onclick='showPosts(${pages.next})'> > </a>
                <a href='#post' title="last page" onclick='showPosts(${pages.last})'> >> </a>`;

    return _html;
}

//limit messages
function limitPosts(response) {
    //define an array of courses per page options
    let postsPerPageOptions = [5, 10, 20];

    //create a selection list for limiting courses
    let _html = `&nbsp;&nbsp;&nbsp;&nbsp; Items per page:<select id='post-limit-select' onChange='showPosts()'>`;
    postsPerPageOptions.forEach(function (option) {
        let selected = (response.limit == option) ? "selected" : "";
        _html += `<option ${selected} value="${option}">${option}</option>`;
    });

    _html += "</select>";

    return _html;
}

//sort messages
function sortPosts(response) {
    //create selection list for sorting
    let sort = response.sort;
    //sort field and direction: convert json to a string then remove {, }, and "
    let sortString = JSON.stringify(sort).replace(/["{}]+/g, "");
    console.log(sortString);

    //define a JSON containing sort options
    let sortOptions = {
        "id:asc": "First Message ID -> Last Message ID",
        "id:desc": "Last Message ID -> First Message ID",
        "body:asc": "Message body A -> Z",
        "body:desc": "Message body Z -> A"
    };

    //create the selection list
    let _html = "&nbsp;&nbsp;&nbsp;&nbsp; Sort by: <select id='post-sort-select'" + "onChange='showPosts()'>";

    for (let option in sortOptions) {
        let selected = (option == sortString) ? "selected" : "";
        _html += `<option ${selected} value='${option}'>${sortOptions[option]}</option>`;
    }
    _html += "</select>";
    return _html;
}

