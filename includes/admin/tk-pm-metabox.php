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

	add_meta_box( 'tk_pm_metabox', __( 'Post Members', 'tk-pm' ), 'tk_pm_post_edit_metabox', 'post', 'normal', 'high' );

}

add_action( 'add_meta_boxes', 'tk_pm_post_metabox' );

//
// Metabox content
//
function tk_pm_post_edit_metabox() {
	global $post;


	$post_members = get_post_meta( $post->ID, '_tk_post_members', true );
	?>

	<select id="tk-pm-search" style="width:100%">
		<option></option>
	</select>

	<?php if ( isset( $post_members ) || is_array( $post_members ) ) { ?>
		<ul id="tk-pm-sortable">
			<?php foreach ( $post_members as $member ) {
				$user_data = get_userdata( $member )
				?>


				<li id="<?php echo $user_data->ID ?>"
				    class="select2-results__option select2-results__option--highlighted" role="treeitem"
				    aria-selected="false">
					<div class="select2-result-user clearfix">
						<div class="select2-result-user__avatar"><img
								src="<?php echo get_avatar_url( $member ) ?>"></div>
						<div class="select2-result-user__meta">
							<div class="select2-result-user__display_name"><?php echo $user_data->display_name ?></div>
							<div class="select2-result-user__user_email"><?php echo $user_data->user_email ?></div>
							<div class="select2-result-user__actions">
								<div class="select2-result-user__add"><a data-id="<?php echo $user_data->ID ?>"
								                                         class="tk-pm-remove-member">Remove</a></div>
							</div>
						</div>
					</div>
					<input type="hidden" value="<?php echo $user_data->ID ?>" name="_tk_post_members[]"></li>

				<?php
			}
			?></ul>
		<?php
	}

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

	if ( ! isset( $_POST['_tk_post_members'] ) ) {
		return;
	}

	$post_members = $_POST['_tk_post_members'];

	if ( $post_members ) {
		update_post_meta( $post_id, '_tk_post_members', $post_members );
	}
}

add_action( 'save_post', 'tk_pm_post_edit_metabox_save' );

function tk_pm_user_search() {

	// Get search term
	$term = $_POST['term'];

	// Search user
	$users = new WP_User_Query( array(
		'search'         => '*' . esc_attr( $term ) . '*',
		'search_columns' => array(
			'user_login',
			'ID',
			'user_nicename',
			'user_email',
			'user_url',
		),
	) );

	// User Loop
	if ( ! empty( $users->results ) ) {
		foreach ( $users->results as $user ) {
			$json[ $user->ID ]['id']           = $user->ID;
			$json[ $user->ID ]['display_name'] = $user->display_name;
			$json[ $user->ID ]['user_email']   = $user->user_email;
			$json[ $user->ID ]['avatar_url']   = get_avatar_url( $user->ID );
		}
	}

	echo json_encode( $json );
	die();
}

add_action( 'wp_ajax_tk_pm_user_search', 'tk_pm_user_search' );