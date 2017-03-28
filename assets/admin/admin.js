jQuery(document).ready(function () {

    // User Accordion
    jQuery("#tk-pm-sortable").sortable({
        revert: true
    });
    jQuery("#draggable").draggable({
        connectToSortable: "#sortable",
        helper: "clone",
        revert: "invalid"
    });
    jQuery("ul, li").disableSelection();

    // Add new Member from select2 to the Sortable list
    jQuery(document).on('click', '.tk-pm-add-member', function () {

        var user = new Array();

        user.id = jQuery(this).attr('data-id');
        user.display_name = jQuery(this).attr('data-display_name');
        user.user_email = jQuery(this).attr('data-user_email');
        user.avatar_url = jQuery(this).attr('data-avatar_url');

        jQuery("#tk-pm-sortable").append(formatUser_html(user, 'in'));


        jQuery('#tk-pm-search').bind('mousedown');
        do_the_search();
        return false;
    });
    jQuery(document).on('click', '.tk-pm-remove-member', function () {
        var id = jQuery(this).attr('data-id');
        jQuery('#' + id).remove();
    });

    do_the_search();

    jQuery('#tk-pm-search').unbind('mouseenter mouseleave');
    jQuery('#tk-pm-search').off('hover');
});

function formatUser(user) {
    if (user.loading) return user;

    return formatUser_html(user);
}

function formatUser_html(user, sel2 ) {

    markup = '<li style="height:60px;" id="' + user.id + '" class="select2-results__option select2-results__option--highlighted" role="treeitem" aria-selected="false"> ' +
        '<div class="select2-result-user clearfix"> ' +
        '<div class="select2-result-user__avatar"><img src="' + user.avatar_url + '"></div> ' +
        '<div class="select2-result-user__meta"> ' +
        '<div class="select2-result-user__display_name">' + user.display_name + '</div> ' +
        '<div class="select2-result-user__user_email">' + user.user_email + '</div> ' +
        '<div class="select2-result-user__actions"> ' +
        '<div class="select2-result-user__add"> ';
        if(sel2 != 'in'){
            markup = markup + '<a data-id="' + user.id + '" data-avatar_url="' + user.avatar_url + '" data-display_name="' + user.display_name + '" data-user_email="' + user.user_email + '" href="#" class="tk-pm-add-member">Add Member</a> </div> ';
        }
    markup = markup + '</div> ' +
        '</div> ' +
        '</div> ' +
        '<input type="hidden" value="' + user.id + '" name="_tk_post_members[]">' +
        '</li>';
    return markup;
}

function do_the_search(){
    jQuery("#tk-pm-search").pumSelect2({

        placeholder: "Search for user",
        allowClear: true,
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

                jQuery.each(data, function (i, val) {
                    items.push(val);
                });

                params.page = params.page || 1;

                return {
                    results: items,
                    pagination: {
                        more: (params.page * 30) < items
                    }
                };
            }
        },
        templateResult: function (item) {
            return formatUser(item);
        },
        formatSelection: function (item) {
            return item.id;
        },
        escapeMarkup: function (m) {
            return m;
        },
        minimumInputLength: 1,
    });
}