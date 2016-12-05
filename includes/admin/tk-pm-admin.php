<?php


//
// Add the Settings Page to the Settings Menu
//
function tk_pm_settings_menu() {

	add_submenu_page( 'options-general.php', __( 'Post Members', 'tk-pm' ), __( 'Post Members', 'tk-pm' ), 'manage_options', 'tk_pm_settings', 'tk_pm_settings_page' );

}

add_action( 'admin_menu', 'tk_pm_settings_menu' );

//
// Settings Page Content
//
function tk_pm_settings_page() { ?>

	<div class="wrap">
		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">

				<div id="postbox-container-1" class="postbox-container">
					<?php tk_pm_settings_page_sidebar(); ?>
				</div>
				<div id="postbox-container-2" class="postbox-container">
					<?php tk_pm_settings_page_tabs_content(); ?>
				</div>
			</div> <!-- #post-body -->
		</div> <!-- #poststuff -->
	</div> <!-- .wrap -->
	<?php
}

/**
 * Settings Tabs Navigation
 *
 * @param string $current
 */
function tk_pm_admin_tabs( $current = 'homepage' ) {
	$tabs = array( 'general' => 'General Settings' );

	$tabs = apply_filters( 'tk_pm_admin_tabs', $tabs );

	echo '<h2 class="nav-tab-wrapper" style="padding-bottom: 0;">';
		foreach ( $tabs as $tab => $name ) {
			$class = ( $tab == $current ) ? ' nav-tab-active' : '';
			echo "<a class='nav-tab$class' href='?page=tk_pm_settings&tab=$tab'>$name</a>";
		}
	echo '</h2>';
}

/**
 * Register Settings Options
 *
 * @param string $current
 */
function tk_pm_register_option() {
	register_setting( 'tk_pm_post_types', 'tk_pm_post_types', 'tk_pm_post_types_sanitize' );
}

add_action( 'admin_init', 'tk_pm_register_option' );

/**
 * @param $new
 *
 * @return mixed
 */
function tk_pm_post_types_sanitize( $new ) {
	// todo: Sanitize
	return $new;
}

function tk_pm_settings_page_tabs_content() { ?>

	<div id="poststuff">

		<?php

		// Display the Update Message
		if ( isset( $_GET['updated'] ) && 'true' == esc_attr( $_GET['updated'] ) ) {
			echo '<div class="updated" ><p>' . __( 'Settings Saved', 'tk-pm' ) . '</p></div>';
		}

		if ( isset ( $_GET['tab'] ) ) {
			tk_pm_admin_tabs( $_GET['tab'] );
		} else {
			tk_pm_admin_tabs( 'general' );
		}

		if ( $_GET['page'] == 'tk_pm_settings' ) {

			if ( isset ( $_GET['tab'] ) ) {
				$tab = $_GET['tab'];
			} else {
				$tab = 'general';
			}

			switch ( $tab ) {
				case 'general' :
					$tk_pm_post_types = get_option( 'tk_pm_post_types' ); ?>
					<div class="metabox-holder">
						<div class="postbox">
							<h3><span><?php _e( 'Select the Post Types you like to have Post Members enabled', 'tk-pm' ); ?></span></h3>
							<div class="inside">
								<p>Mal Schauen</p>
							</div><!-- .inside -->
						</div><!-- .postbox -->
					</div><!-- .metabox-holder -->
					<?php
					break;
				default:
					do_action( 'tk_pm_settings_page_tab', $tab );
					break;
			}
		}
		?>
	</div> <!-- #poststuff -->
	<?php
}

function tk_pm_settings_page_sidebar() {}