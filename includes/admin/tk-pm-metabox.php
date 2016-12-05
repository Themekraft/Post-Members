<?php

//
// Post metabox to display members management in the post type edit screen
//
function tk_pm_post_metabox() {
	global $post;

	// sanity check
	if ( ! ( $post instanceof WP_Post ) ) {
		return;
	}

	add_meta_box( 'tk_pm_metabox', __('Post Members', 'tk-pm'), 'tk_pm_post_edit_metabox', 'post', 'normal', 'high' );

}

add_action( 'add_meta_boxes', 'tk_pm_post_metabox' );

//
// Metabox content
//
function tk_pm_post_edit_metabox() {
	global $post;


	$post_members = get_post_meta( $post->ID, '_tk_post_members', true );
	print_r($post_members);
	?>

	<select class="js-data-example-ajax" style="width:100%"></select>


	<ul id="tk-pm-sortable">


		<li class="select2-results__option select2-results__option--highlighted" role="treeitem" aria-selected="false">
			<div class="select2-result-repository clearfix">
				<div class="select2-result-repository__avatar"><img
						src="https://avatars.githubusercontent.com/u/1609975?v=3"></div>
				<div class="select2-result-repository__meta">
					<div class="select2-result-repository__title">dart-lang/test</div>
					<div class="select2-result-repository__description">A library for writing unit tests in Dart.</div>
					<div class="select2-result-repository__statistics">
						<div class="select2-result-repository__forks"><i class="fa fa-flash"></i> 73 Forks</div>
						<div class="select2-result-repository__stargazers"><i class="fa fa-star"></i> 73 Stars</div>
						<div class="select2-result-repository__watchers"><i class="fa fa-eye"></i> 73 Watchers</div>
					</div>
				</div>
			</div>
		</li>
		<li class="select2-results__option select2-results__option--highlighted" role="treeitem" aria-selected="false">
			<div class="select2-result-repository clearfix">
				<div class="select2-result-repository__avatar"><img
						src="https://avatars.githubusercontent.com/u/1609975?v=3"></div>
				<div class="select2-result-repository__meta">
					<div class="select2-result-repository__title">dart-lang/test</div>
					<div class="select2-result-repository__description">A library for writing unit tests in Dart.</div>
					<div class="select2-result-repository__statistics">
						<div class="select2-result-repository__forks"><i class="fa fa-flash"></i> 73 Forks</div>
						<div class="select2-result-repository__stargazers"><i class="fa fa-star"></i> 73 Stars</div>
						<div class="select2-result-repository__watchers"><i class="fa fa-eye"></i> 73 Watchers</div>
					</div>
				</div>
			</div>
		</li>


	</ul>
	<?php


}

//
// Save the metabox data
//
/**
 * @param $post_id
 */
function tk_pm_post_edit_metabox_save( $post_id ) {

	if ( ! is_admin() || defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		return;
	}

	if(!isset($_POST['tk_post_members'])){
		return;
	}

	$post_members = $_POST['tk_post_members'];

	if($post_members)
		update_post_meta( $post_id, $post_members );
}

add_action( 'save_post', 'tk_pm_post_edit_metabox_save' );

function tk_pm_user_search() {


	$term = $_POST['term'];

	$users = new WP_User_Query( array(
		'search'         => '*'.esc_attr( $term ).'*',
		'search_columns' => array(
			'user_login',
			'user_nicename',
			'user_email',
			'user_url',
		),
	) );
	$users_found = $users->get_results();

	foreach( $users_found as $user_id => $user ){
		$json[]['archive_url'] = $user->ID;
	}


	echo json_encode( $json );
	die();
}

add_action( 'wp_ajax_tk_pm_user_search', 'tk_pm_user_search' );