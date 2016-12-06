<?php

function tk_pm_list_members( $atts ) {
	extract(shortcode_atts( array(
		'post_id' => '',
	), $atts ));

	$post_members = get_post_meta( $post_id, '_tk_post_members', true );



	if ( isset( $post_members ) || is_array( $post_members ) ) {
		ob_start(); ?>
		<ul>
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
								                                         class="tk-pm-remove-member">Any Action?</a></div>
							</div>
						</div>
					</div>
					<input type="hidden" value="<?php echo $user_data->ID ?>" name="_tk_post_members[]"></li>

				<?php
			}
			?></ul>
		<?php
		$tmp = ob_get_clean();
	}

	return $tmp;
}
add_shortcode( 'tk_pm_list_members', 'tk_pm_list_members' );