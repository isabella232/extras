<?php

require_once get_template_directory().'/extras/panels/inc/panel.php';
require_once get_template_directory().'/extras/panels/panels/basic.php';
require_once get_template_directory().'/extras/panels/panels/post.php';

function so_panels_init(){
	register_post_type('panel', array(
		'labels' => array(
			'name' => __('Panels', 'siteorigin'),
			'singular_name' => __('Panel', 'siteorigin'),
			'add_new' => __('Add New', 'seven', 'siteorigin'),
			'add_new_item' => __('Add New Panel', 'siteorigin'),
			'edit_item' => __('Edit Panel', 'siteorigin'),
			'new_item' => __('New Panel', 'siteorigin'),
			'all_items' => __('All Panels', 'siteorigin'),
			'view_item' => __('View Panel', 'siteorigin'),
			'search_items' => __('Search Panels', 'siteorigin'),
			'not_found' =>  __('No panels found', 'siteorigin'),
			'not_found_in_trash' => __('No panels found in Trash', 'siteorigin'),
			'parent_item_colon' => '',
			'menu_name' => __('Panels', 'seven')

		),
		'public' => false,
		'publicly_queryable' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'query_var' => false,
		'rewrite' => array('slug' => 'page'),
		'capability_type' => 'post',
		'has_archive' => true,
		'hierarchical' => false,
		'menu_position' => null,
		'supports' => array( 'title'),
		'register_meta_box_cb' => 'so_panels_metaboxes',
	));
}
add_action('after_setup_theme', 'so_panels_init');

/**
 * Register the Panels Metaboxes
 */
function so_panels_metaboxes(){
	add_meta_box('so-panels-panels', __('Panels', 'siteorigin'), 'so_panels_metabox_render', 'panel', 'advanced', 'default', array('panels'));
	add_meta_box('so-panels-settings', __('Panel Settings', 'siteorigin'), 'so_panels_metabox_render', 'panel', 'side', 'default', array('settings'));
}

/**
 * Render a panel metabox
 * @param $post
 * @param $args
 */
function so_panels_metabox_render($post, $args){
	get_template_part('extras/panels/tpl/metabox', $args['args'][0]);
}

/**
 * Enqueue the panels admin scripts
 */
function so_panels_admin_enqueue_scripts(){
	$screen = get_current_screen();
	if($screen->id == 'panel'){
		wp_enqueue_script('jquery-ui-resizable');
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script('jquery-ui-tabs');
		wp_enqueue_script('jquery-ui-dialog');
		wp_enqueue_script('jquery-ui-button');

		wp_enqueue_script('so-panels-admin-grid', get_template_directory_uri().'/extras/panels/js/panels.admin.grid.js', array('jquery'), SO_THEME_VERSION);
		wp_enqueue_script('so-panels-admin', get_template_directory_uri().'/extras/panels/js/panels.admin.js', array('jquery'), SO_THEME_VERSION);
		
		// This gives panels a chance to enqueue scripts too, without having to check the screen ID.
		do_action('so_panel_enqueue_scripts');
	}
}
add_action('admin_print_scripts-post-new.php', 'so_panels_admin_enqueue_scripts');
add_action('admin_print_scripts-post.php', 'so_panels_admin_enqueue_scripts');

function so_panels_admin_enqueue_styles(){
	$screen = get_current_screen();
	if($screen->id == 'panel'){
		wp_enqueue_style("wp-jquery-ui-dialog");
		wp_enqueue_style('so-panels-jquery-ui', get_template_directory_uri().'/extras/panels/css/jquery-ui-theme.css');
		wp_enqueue_style('so-panels-admin', get_template_directory_uri().'/extras/panels/css/panels-admin.css');

		do_action('so_panel_enqueue_styles');
	}
}
add_action('admin_print_styles-post-new.php', 'so_panels_admin_enqueue_styles');
add_action('admin_print_styles-post.php', 'so_panels_admin_enqueue_styles');