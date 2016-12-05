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

	echo 'es geht los';

	print_r($post_members);

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