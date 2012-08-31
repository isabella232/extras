<?php

require_once get_template_directory().'/extras/panels/inc/panel.php';
require_once get_template_directory().'/extras/panels/panels/basic.php';
require_once get_template_directory().'/extras/panels/panels/post.php';

/**
 * Initialize the panels extra
 */
function so_panels_init(){
	register_post_type('panel', array(
		'labels' => array(
			'name' => __('Panels', 'siteorigin'),
			'singular_name' => __('Panel', 'siteorigin'),
			'add_new' => __('Add New', 'siteorigin'),
			'add_new_item' => __('Add New Panel', 'siteorigin'),
			'edit_item' => __('Edit Panel', 'siteorigin'),
			'new_item' => __('New Panel', 'siteorigin'),
			'all_items' => __('All Panels', 'siteorigin'),
			'view_item' => __('View Panel', 'siteorigin'),
			'search_items' => __('Search Panels', 'siteorigin'),
			'not_found' =>  __('No panels found', 'siteorigin'),
			'not_found_in_trash' => __('No panels found in Trash', 'siteorigin'),
			'parent_item_colon' => '',
			'menu_name' => __('Panels', 'siteorigin')
		),
		'public' => true,
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
		'menu_icon' => get_template_directory_uri().'/extras/panels/images/menu-icon.png'
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
 * Render a panel metabox.
 * 
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
		
		wp_localize_script('so-panels-admin', 'panelsLoc', array(
			'buttons' => array(
				'delete' => __('Delete', 'siteorigin'),
				'done' => __('Done', 'siteorigin'),
			),
			'messages' => array(
				'confirmDeletePanel' => __('Are you sure you want to delete this panel?', 'siteorigin'),
			)
		));
		
		global $so_panel_grids;
		wp_localize_script('so-panels-admin-grid', 'panelsGrids', $so_panel_grids);
		
		// Localize the panels with the panels data
		global $post;
		$panels_data = get_post_meta($post->ID, 'panels_data', true); 
		if(!empty($panels_data )){
			wp_localize_script('so-panels-admin', 'panelsData', $panels_data);
		}
		
		// This gives panels a chance to enqueue scripts too, without having to check the screen ID.
		do_action('so_panel_enqueue_admin_scripts');
	}
}
add_action('admin_print_scripts-post-new.php', 'so_panels_admin_enqueue_scripts');
add_action('admin_print_scripts-post.php', 'so_panels_admin_enqueue_scripts');

/**
 * Enqueue the admin panel styles
 */
function so_panels_admin_enqueue_styles(){
	$screen = get_current_screen();
	if($screen->id == 'panel'){
		wp_enqueue_style("wp-jquery-ui-dialog");
		wp_enqueue_style('so-panels-jquery-ui', get_template_directory_uri().'/extras/panels/css/jquery-ui-theme.css');
		wp_enqueue_style('so-panels-admin', get_template_directory_uri().'/extras/panels/css/panels-admin.css');
		wp_enqueue_style('so-panels-icon', get_template_directory_uri().'/extras/panels/css/panels-icon.css');

		do_action('so_panel_enqueue_admin_styles');
	}
}
add_action('admin_print_styles-post-new.php', 'so_panels_admin_enqueue_styles');
add_action('admin_print_styles-post.php', 'so_panels_admin_enqueue_styles');

function so_panels_admin_enqueue_icon_style($hook){
	$screen = get_current_screen();
	if($screen->post_type == 'panel'){
		wp_enqueue_style('so-panels-icon', get_template_directory_uri().'/extras/panels/css/panels-icon.css');
	}
}
add_action('admin_print_styles-edit.php', 'so_panels_admin_enqueue_icon_style');

/**
 * Save the panels data
 * 
 * @param $post_id
 * @param $post
 */
function so_panels_save_post($post_id, $post){
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( empty($_POST['_sopanels_nonce']) || !wp_verify_nonce($_POST['_sopanels_nonce'], 'save') ) return;
	if ( !current_user_can( 'edit_post', $post_id ) ) return;
	
	$panels_data = array();

	$panels_data['panels'] = array_map('stripslashes_deep', isset($_POST['panels']) ? $_POST['panels'] : array());
	$panels_data['panels'] = array_values($panels_data['panels']);
	
	$panels_data['grids'] = array_map('stripslashes_deep', isset($_POST['grids']) ? $_POST['grids'] : array());
	$panels_data['grids'] = array_values($panels_data['grids']);
	
	$panels_data['grid_cells'] = array_map('stripslashes_deep', isset($_POST['grid_cells']) ? $_POST['grid_cells'] : array());
	$panels_data['grid_cells'] = array_values($panels_data['grid_cells']);
	
	update_post_meta($post_id, 'panels_data', $panels_data);
}
add_action('save_post', 'so_panels_save_post', 10, 2);


/**
 * Print the CSS for the current panel
 */
function so_panels_css(){
	global $post;
	if(is_single() && $post->post_type = 'panel'){
		$panels_data = get_post_meta($post->ID, 'panels_data', true);
		
		$css = array();
		$css[1920] = array();
		$css[767] = array(); // This is a mobile resolution
		
		// Add the grid sizing
		$ci = 0;
		foreach($panels_data['grids'] as $gi => $grid){
			$cell_count = intval($grid['cells']);
			for($i = 0; $i < $cell_count; $i++){
				$cell = $panels_data['grid_cells'][$ci++];
				
				if($cell_count > 1){
					$css_new = 'width:'.round($cell['weight']*100,3).'%';
					if(empty($css[1920][$css_new])) $css[1920][$css_new] = array();
					$css[1920][$css_new][] = '#pgc-'.$gi.'-'.$i;
				}
			}
			
			if($cell_count > 1){
				if(empty($css[1920]['float:left'])) $css[1920]['float:left'] = array();
				$css[1920]['float:left'][] = '#pg-'.$gi.' .panel-grid-cell';
			}

			// Mobile Responsive
			if(empty($css[767]['float:left'])) $css[767]['float:none'] = array();
			$css[767]['float:none'][] = '#pg-'.$gi.' .panel-grid-cell';

			if(empty($css[767]['float:left'])) $css[767]['width:auto'] = array();
			$css[767]['width:auto'][] = '#pg-'.$gi.' .panel-grid-cell';
		}
		

		// Build the CSS
		$css_text = '';
		krsort($css);
		foreach($css as $res => $def){
			if($res < 1920){
				$css_text .= '@media (max-width:'.$res.'px)';
				$css_text .= ' { ';
			}

			foreach($def as $property => $selector){
				$selector = array_unique($selector);
				$css_text .= implode(' , ', $selector).' { '.$property.' } ';
			}

			if($res < 1920) $css_text .= ' } ';
		}
		
		print '<style type="text/css">';
		print $css_text;
		print '</style>';
	}
}
add_action('wp_print_styles', 'so_panels_css');

/**
 * Render the panels
 * 
 * @param bool $post_id
 */
function so_panels_render($post_id = false){
	if(empty($post_id)){
		global $post;
	}
	else $post = get_post($post_id);

	$panels_data = get_post_meta($post->ID, 'panels_data', true);
	
	// Create the skeleton of the grids
	$grids = array();
	foreach($panels_data['grids'] as $gi => $grid){
		$gi = intval($gi);
		$grids[$gi] = array();
		for($i = 0; $i < $grid['cells']; $i++){
			$grids[$gi][$i] = array();
		}
	}
	
	foreach($panels_data['panels'] as $panel){
		$grids[intval($panel['info']['grid'])][intval($panel['info']['cell'])][] = $panel;
	}
	
	global $so_panel_grids;
	
	ob_start();
	foreach($grids as $gi => $cells){
		$grid = $panels_data['grids'][$gi];
		$grid_info = $so_panel_grids[$grid['grid_type']];
		
		?><div class="panel-grid" id="pg-<?php print $gi ?>"><?php
		foreach($cells as $ci => $panels){
			?><div class="panel-grid-cell" id="pgc-<?php print $gi.'-'.$ci ?>"><?php
			foreach($panels as $pi => $panel){
				$panel_class = new $panel['info']['class'];
				$info = $panel_class->get_info();
				$classes = array('panel-panel', 'panel-'.$info['group'], 'panel-'.$info['group'].'-'.$info['name']);
				if($pi == 0) $classes[] = 'panel-first-child';
				if($pi == count($panels)-1) $classes[] = 'panel-last-child';
				
				?><div class="<?php print esc_attr(implode(' ',$classes)) ?>" id="panel-<?php print $pi ?>"><?php
				$panel_class->render($panel);
				?></div><?php
			}
			if(empty($panels)) print '&nbsp;';
			?></div><?php
		}
		?><div class="clear"></div></div><?php
	}
	$html = ob_get_clean();
	print apply_filters('panels_render', $html, $post_id, $post);
}