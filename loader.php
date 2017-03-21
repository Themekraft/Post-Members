<?php

/*
 Plugin Name: Post Members
 Plugin URI: https://github.com/Themekraft/post-members
 Description: Add Members to Posts and display the Member's via Shortcode
 Version: 0.1
 Author: Sven Lehnert
 Author URI: https://profiles.wordpress.org/svenl77
 License: GPLv2 or later
 Network: false
 Text Domain: tk_pm
 Domain Path: /languages

 *****************************************************************************
 *
 * This script is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 ****************************************************************************
 */


class TK_Post_Members {

	public function __construct() {

		// Add an init hook to allow other plugins to hook into the init
		$this->init_hook();

		// Some constance needed to get files and templates and make them overwrite in the theme possible. TK_PM_INSTALL_PATH and TK_PM_INCLUDES_PATH
		$this->load_constants();

		// Load all needed files
		add_action( 'init', array( $this, 'includes' ), 1 );

		// Load the plugin translation files
		add_action( 'init', array( $this, 'load_plugin_textdomain' ), 10, 1 );

		// Create Taxonomies
		add_action( 'init', array( $this, 'create_taxonomies' ), 0 );

		// Admin js
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_js' ), 1, 1 );

		// Front css
		add_action( 'wp_enqueue_scripts', array( $this, 'front_js' ), 1002, 1 );

	}


	/**
	 * Defines tk_post_members action
	 *
	 * @package post members
	 * @since 0.1
	 */

	public function init_hook() {

		do_action( 'tk_pm_init' );

	}

	/**
	 * Defines constants needed throughout the plugin.
	 *
	 * @package post members
	 * @since 0.1
	 */

	public function load_constants() {

		if ( ! defined( 'TK_PM_INSTALL_PATH' ) ) {
			define( 'TK_PM_INSTALL_PATH', dirname( __FILE__ ) . '/' );
		}

		if ( ! defined( 'TK_PM_INCLUDES_PATH' ) ) {
			define( 'TK_PM_INCLUDES_PATH', TK_PM_INSTALL_PATH . 'includes/' );
		}
	}

	/**
	 * Includes files needed by post members
	 *
	 * @package post members
	 * @since 0.1
	 */

	public function includes() {

		require_once( TK_PM_INCLUDES_PATH . '/admin/tk-pm-metabox.php' );
		require_once( TK_PM_INCLUDES_PATH . '/admin/tk-pm-admin.php' );
		require_once( TK_PM_INCLUDES_PATH . '/shortcodes.php' );
		require_once( TK_PM_INCLUDES_PATH . '/functions.php' );

	}

	/**
	 * Loads the textdomain for the plugin
	 *
	 * @package post members
	 * @since 0.1
	 */

	public function load_plugin_textdomain() {

		load_plugin_textdomain( 'tk_pm', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	}

	/**
	 * Enqueue the needed JS for the admin screen
	 *
	 * @package post members
	 * @since 0.1
	 *
	 * @param $hook_suffix
	 */
	function admin_js( $hook_suffix ) {
		global $post;


		wp_enqueue_script( 'jQuery' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'jquery-ui-accordion' );
		wp_enqueue_script( 'jquery-ui-draggable' );
		wp_enqueue_script( 'jquery-ui-droppable' );


		wp_enqueue_script( 'tk-pm-admin-js', plugins_url( 'assets/admin/admin.js', __FILE__ ), array( 'jquery' ) );
		wp_enqueue_style( 'tk-pm-admin-css', plugins_url( 'assets/admin/admin.css', __FILE__ ) );

		wp_dequeue_style( 'select2' );
		wp_deregister_style( 'select2' );

		wp_dequeue_script( 'select2' );
		wp_deregister_script( 'select2' );

		wp_enqueue_script( 'tk-pm-select2-js', plugins_url( 'assets/resources/select2/dist/js/select2.full.min.js', __FILE__ ), array( 'jquery' ), '4.0.3' );
		wp_enqueue_style( 'tk-pm-select2-css', plugins_url( 'assets/resources/select2/dist/css/select2.min.css', __FILE__ ) );
//		}

	}

	/**
	 * Enqueue the needed JS for the front
	 *
	 * @package post members
	 * @since 0.1
	 *
	 */
	function front_js() {
		global $post;

		// check the post content for the short code
			wp_enqueue_style( 'tk-pm-admin-css', plugins_url( 'assets/admin/admin.css', __FILE__ ) );


	}

	/**
	 * Create Taxonomy for teh post user relationship
	 *
	 * @package post members
	 * @since 0.1
	 *
	 */
	function create_taxonomies() {

		$tk_pm_post_types = get_option( 'tk_pm_post_types', true );

		register_taxonomy( 'tk_pm_relation', $tk_pm_post_types, array(
			'hierarchical'      => true,
			'label'             => 'Post Members',
			'query_var'         => 'tk_pm_relation',
			'public'            => false,
			'show_ui'           => false,
			'show_admin_column' => false,
		) );

	}

}

new TK_Post_Members;