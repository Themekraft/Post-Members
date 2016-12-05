
var myOwnSelect2 = jQuery.fn.select2;
delete jQuery.fn.select2;

jQuery(document).ready(function () {

































    jQuery( "#tk-pm-sortable" ).sortable({
            revert: true
        });
    jQuery( "#draggable" ).draggable({
            connectToSortable: "#sortable",
            helper: "clone",
            revert: "invalid"
        });
    jQuery( "ul, li" ).disableSelection();

    jQuery(document).on('click', '.tk-pm-add-member', function () {

        alert('da');

        jQuery("#tk-pm-sortable").append(formatRepo2());

        alert('da');
        return false;
    });

























    myOwnSelect2.call(jQuery(".js-data-example-ajax").select2({
        ajax: {
            type: 'POST',
            dataType: "json",
            url: ajaxurl,
            delay: 250,
            data: function (params) {

                console.log(params);

                return {
                    "action": "tk_pm_user_search",
                    "term": params.term, // search term
                };
            },
            processResults: function (data, params) {


                jQuery.each(data, function (i, val) {
                    console.log(i, val);
                });


                // parse the results into the format expected by Select2
                // since we are using custom formatting functions we do not need to
                // alter the remote JSON data, except to indicate that infinite
                // scrolling can be used
                params.page = params.page || 1;



                return {
                    results: data.items,
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            },
            cache: true
        },
        escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
        minimumInputLength: 1,
        templateResult: formatRepo, // omitted for brevity, see the source of this page
        templateSelection: formatRepoSelection, // omitted for brevity, see the source of this page
        formatResult: formatResult,
        formatSelection: formatSelection
    }));





});

function formatResult(state, container) {
    if (!state.id) return state.text; // optgroup
    container.append(state.text);
    $('<i class="info">link</i>')
        .appendTo(container)
        .mouseup(function(e) {
            e.stopPropagation();
        })
        .click(function(e) {
            e.preventDefault();
            alert('Click!');
            $('#log').text('clicked');
        });
}

function formatSelection(state, container) {
    alert('dsd');
    container.append($('<span class="selected-state"></span>').text(state.text));
    $('<i class="selected-info">link</i>')
        .appendTo(container)
        .mousedown(function(e) {
            e.stopPropagation();
        })
        .click(function(e) {
            e.preventDefault();
            alert('Selection Click!');
            $('#log').text('selection clicked');
        });
}

function formatRepo (repo) {
    if (repo.loading) return repo.text;

    var markup = "<div class='select2-result-repository clearfix'>" +
        "<div class='select2-result-repository__avatar'><img src='" + repo.owner.avatar_url + "' /></div>" +
        "<div class='select2-result-repository__meta'>" +
        "<div class='select2-result-repository__title'>" + repo.full_name + "</div>";

    if (repo.description) {
        markup += "<div class='select2-result-repository__description'>" + repo.description + "</div>";
    }

    markup += "<div class='select2-result-repository__statistics'>" +
        "<div class='select2-result-repository__forks'><i class='fa fa-flash'></i> " + repo.forks_count + " Forks</div>" +
        "<div class='select2-result-repository__stargazers'><i class='fa fa-star'></i> " + repo.stargazers_count + " Stars</div>" +
        "<div class='select2-result-repository__watchers'><i class='fa fa-eye'></i> " + repo.watchers_count + " Watchers</div>" +
        "<a href='#' class='tk-pm-add-member'>Add this User</a>" +
        "</div>" +
        "</div></div>";

    return markup;
}

function formatRepoSelection (repo) {
    return repo.full_name || repo.text;
}
function formatRepo2 () {

    return '<li class="select2-results__option select2-results__option--highlighted" role="treeitem" aria-selected="false"> ' +
        '<div class="select2-result-repository clearfix"> ' +
        '<div class="select2-result-repository__avatar"><img src="https://avatars.githubusercontent.com/u/1609975?v=3"></div> ' +
        '<div class="select2-result-repository__meta"> ' +
        '<div class="select2-result-repository__title">dart-lang/test</div> ' +
        '<div class="select2-result-repository__description">A library for writing unit tests in Dart.</div> ' +
        '<div class="select2-result-repository__statistics"> ' +
        '<div class="select2-result-repository__forks"><i class="fa fa-flash"></i> 73 Forks</div> ' +
        '<div class="select2-result-repository__stargazers"><i class="fa fa-star"></i> 73 Stars</div> ' +
        '<div class="select2-result-repository__watchers"><i class="fa fa-eye"></i> 73 Watchers</div> ' +
        '</div> ' +
        '</div> ' +
        '</div> ' +
        '</li>';


}