<?php

/**
 * Add the admin menu entries
 */
function siteorigin_panels_lite_admin_menu(){
	add_theme_page(
		__('Custom Home Page Builder', 'so-panels'),
		__('Home Page', 'so-panels'),
		'edit_theme_options',
		'so_panels_home_page',
		'siteorigin_panels_lite_render_admin_home_page'
	);
}
add_action('admin_menu', 'siteorigin_panels_lite_admin_menu');

/**
 * Render the page used to build the custom home page.
 */
function siteorigin_panels_lite_render_admin_home_page(){
	add_meta_box( 'so-panels-panels', __( 'Page Builder', 'so-panels' ), 'siteorigin_panels_metabox_render', 'appearance_page_so_panels_home_page', 'advanced', 'high' );
	get_template_part('extras/panels-lite/tpl/admin', 'home-page');
}

function siteorigin_panels_lite_enqueue_admin($prefix){
	if($prefix == 'appearance_page_so_panels_home_page'){
		wp_enqueue_style('siteorigin-panels-lite-teaser', get_template_directory_uri().'/extras/panels-lite/css/panels-admin.css');
	}
}
add_action('admin_enqueue_scripts', 'siteorigin_panels_lite_enqueue_admin');

/**
 * Add the Edit Home Page item to the admin bar.
 *
 * @param WP_Admin_Bar $admin_bar
 * @return WP_Admin_Bar
 */
function siteorigin_panels_lite_admin_bar_menu($admin_bar){
	/**
	 * @var WP_Query $wp_query
	 */
	global $wp_query;

	if( $wp_query->is_home() && $wp_query->is_main_query() ){
		// Check that we support the home page
		if ( !current_user_can('edit_theme_options') ) return $admin_bar;

		$admin_bar->add_node(array(
			'id' => 'edit-home-page',
			'title' => __('Edit Home Page', 'siteorigin'),
			'href' => admin_url('themes.php?page=so_panels_home_page')
		));
	}

	return $admin_bar;
}
add_action('admin_bar_menu', 'siteorigin_panels_lite_admin_bar_menu', 100);