
var tk_pm_select = jQuery.fn.select2;
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



        var repo = new Array();

        repo.id = jQuery(this).attr('data-id');
        repo.display_name = jQuery(this).attr('data-display_name');
        repo.user_email = jQuery(this).attr('data-user_email');
        repo.avatar_url = jQuery(this).attr('data-avatar_url');

        //console.log(repo);

        jQuery("#tk-pm-sortable").append(formatRepo_html(repo));


        return false;
    });














    tk_pm_select.call(jQuery(".js-data-example-ajax").select2({
        placeholder: {
            id: "123",
            placeholder: "Leave blank to ..."
        },
        allowClear: false,
        ajax: {
            type: 'POST',
            dataType: "json",
            url: ajaxurl,
            delay: 250,
            data: function (params) {
                return {
                    "action": "tk_pm_user_search",
                    "term": params.term, // search term
                };
            },
            processResults: function (data, params) {
                var items = new Array();
                items.placeholder =  Array({
                    id: "123",
                    placeholder: "Leave blank to ..."
                });
                items.id = 123;
                jQuery.each(data, function (i, val) {
                        //jQuery.each(val, function (i2, val2) {
                        //    items.push(val2);
                        //});
                    items.push(val);
                });

                params.page = params.page || 1;

                console.log(data);
                console.log(items);
                //items.id = 'id';
                return {
                    results: items,
                    //pagination: {
                    //    more: (params.page * 30) < items
                    //}
                };
            },
            cache: true
        },
        templateResult: function(item) {
            /* FIX */
            if (item.placeholder) return item.placeholder;
            return formatRepo(item);
        },
        templateSelection: function (item) {
            /* FIX */
            if (item.placeholder) return item.placeholder;
            return formatRepoSelection(item);
        },
        escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
        minimumInputLength: 3,
        //templateResult: formatRepo, // omitted for brevity, see the source of this page
    }));

});

function formatRepo (repo) {
    if (repo.loading) return repo.text;

    return formatRepo_html(repo);
}

function formatRepo_html(repo) {

    return '<li class="select2-results__option select2-results__option--highlighted" role="treeitem" aria-selected="false"> ' +
        '<div class="select2-result-user clearfix"> ' +
        '<div class="select2-result-repository__avatar"><img src="' + repo.avatar_url + '"></div> ' +
        '<div class="select2-result-repository__meta"> ' +
        '<div class="select2-result-repository__display_name">' + repo.display_name + '</div> ' +
        '<div class="select2-result-repository__user_email">' + repo.user_email + '</div> ' +
        '<div class="select2-result-repository__actions"> ' +
        '<div class="select2-result-repository__add"><a data-id="' + repo.id + '" data-avatar_url="' + repo.avatar_url + '" data-display_name="' + repo.display_name + '" data-user_email="' + repo.user_email + '" data-id="' + repo.id + '"href="#" class="tk-pm-add-member">Add Member</a> </div> ' +
        '</div> ' +
        '</div> ' +
        '</div> ' +
        '</li>';
}