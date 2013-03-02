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

function siteorigin_panels_lite_install_url(){
	return wp_nonce_url(self_admin_url('plugins.php?action=activate&plugin='.$plugin_file), 'activate-plugin_'.$plugin_file);
}

// gives a link to activate the plugin
function activate_link() {
	$plugin_file = $this->get_plugin_file();
	if ($plugin_file) return wp_nonce_url(self_admin_url('plugins.php?action=activate&plugin='.$plugin_file), 'activate-plugin_'.$plugin_file);
	return false;
}

// return a nonced installation link for the plugin. checks wordpress.org to make sure it's there first.
function install_link() {
	include_once ABSPATH . 'wp-admin/includes/plugin-install.php';

	$info = plugins_api('plugin_information', array('slug' => $this->slug ));

	if ( is_wp_error( $info ) )
		return false; // plugin not available from wordpress.org

	return wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=' . $this->slug), 'install-plugin_' . $this->slug);
}