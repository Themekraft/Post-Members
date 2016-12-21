<?php

function tk_pm_author_info_box(){
	echo tk_pm_get_author_info_box();
}

function tk_pm_get_author_info_box( ) {

	global $post;


	// Get author's display name
	$display_name = get_the_author_meta( 'display_name', $post->post_author );

	// If display name is not available then use nickname as display name
	if ( empty( $display_name ) ) {
		$display_name = get_the_author_meta( 'nickname', $post->post_author );
	}

	// Get author's biographical information or description
	$user_description = get_the_author_meta( 'user_description', $post->post_author );

	// Get author's website URL
	$user_website = get_the_author_meta('url', $post->post_author);
	$author_details = '';

	$author_details .= '<p class="author_avatar"><a href="' . bp_core_get_user_domain($post->post_author) .'">' . get_avatar( get_the_author_meta( 'user_email' ), 90 ) . '</a></p>';

//	if ( ! empty( $user_description ) ) {
//		$author_details .=  nl2br( $user_description );
//	}

	$author_details .= '<p class="author-contact"><b>' . __('Ihr Kontakt', 'dav') . '</b></p>';
	if ( ! empty( $display_name ) ) {
		$author_details .= '<p class="author_name"> ' . $display_name . '</p>';
	}
	if ( ! empty( $display_tel ) ) {
		$author_details .= '<p class="author_tel"> '. __('Tel ', 'dav') . $display_tel . '</p>';
	}
	if ( ! empty( $display_fax ) ) {
		$author_details .= '<p class="author_fax"> '. __('Fax ', 'dav') . $display_fax . '</p>';
	}
	if ( ! empty( $display_fax ) ) {
		$author_details .= '<p class="author_email"> '. __('E-Mail ', 'dav') . $display_fax . '</p>';
	}

	// Check if author has a website in their profile
	if ( ! empty( $user_website ) ) {
		// Display author website link
		$author_details .= ' <p> <a href="' . $user_website .'" target="_blank" rel="nofollow">Website</a></p>';

	}

	return $author_details;
}

function tk_pm_list_members( $atts = array() ) {
	echo tk_pm_get_list_members($atts);
}