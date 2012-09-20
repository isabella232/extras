<?php

require_once get_template_directory().'/extras/panels/inc/panel.php';

require_once get_template_directory().'/extras/panels/panels/basic.php';
require_once get_template_directory().'/extras/panels/panels/home.php';
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
 * Give all the panels a chance to enqueue their scripts and styles.
 * 
 * @action wp_enqueue_scripts
 */
function so_panels_enqueue_scripts(){
	global $post;
	if(is_single() && $post->post_type == 'panel'){
		$classes = array();
		$data = get_post_meta($post->ID, 'panels_data', true);

		if(empty($data['panels'])) return;
		
		// Find which panels we're using
		foreach($data['panels'] as $panel){
			if(isset($panel['info']['class']) && class_exists($panel['info']['class'])) $classes[] = $panel['info']['class'];
		}
		$classes = array_unique($classes);
		
		foreach($classes as $class){
			if(method_exists($class, 'enqueue')){
				// Let the panels themselves enqueue any scripts they need
				call_user_func(array($class, 'enqueue'));
			}
			
			// Give the themes a chance to enqueue scripts for a given panel
			do_action('so_panels_enqueue_scripts-'.$class);
		}
		
	}
}
add_action('wp_enqueue_scripts', 'so_panels_enqueue_scripts');

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
		
		// Remove any panels that no longer exist.
		foreach($panels_data['panels'] as $i => $panel){
			if(!class_exists($panel['info']['class'])) unset($panels_data['panels'][$i]);
		}
		
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

/**
 * This is the style for the panel icon. 
 */
function so_panels_admin_enqueue_icon_style(){
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
	
	if(isset($_POST['panels_home_page'])){
		set_theme_mod('panels_home_page', $post_id);
	}
	elseif(get_theme_mod('panels_home_page') == $post_id){
		remove_theme_mod('panels_home_page');
	}
}
add_action('save_post', 'so_panels_save_post', 10, 2);


/**
 * echo the CSS for the current panel
 */
function so_panels_css(){
	global $post;
	global $so_panels_margin_bottom;
	if(empty($so_panels_margin_bottom)) $so_panels_margin_bottom = 20;
	
	if(is_single() && $post->post_type == 'panel'){
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
			$mobile_css = array('float:none','width:auto','margin-bottom:'.$so_panels_margin_bottom.'px');
			foreach($mobile_css as $c){
				if(empty($css[767][$c])) $css[767][$c] = array();
				$css[767][$c][] = '#pg-'.$gi.' .panel-grid-cell';
			}
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

		echo '<style type="text/css">';
		echo $css_text;
		echo '</style>';
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
	
	ob_start();
	foreach($grids as $gi => $cells){
		$grid = $panels_data['grids'][$gi];
		
		?><div class="panel-grid" id="pg-<?php echo $gi ?>"><?php
		foreach($cells as $ci => $panels){
			?><div class="panel-grid-cell" id="pgc-<?php echo $gi.'-'.$ci ?>"><?php
			foreach($panels as $pi => $panel){
				// Skip this if the class no longer exists
				if(!class_exists($panel['info']['class'])) continue;
				
				$panel_class = new $panel['info']['class'];
				$info = $panel_class->get_info();
				$classes = array('panel-panel', 'panel-'.$info['group'], 'panel-'.$info['group'].'-'.$info['name']);
				if($pi == 0) $classes[] = 'panel-first-child';
				if($pi == count($panels)-1) $classes[] = 'panel-last-child';
				
				?><div class="<?php echo esc_attr(implode(' ',$classes)) ?>" id="panel-<?php echo $gi . '-' . $ci. '-' . $pi ?>"><?php
				
				// Give child themes or plugins a chance to render this panel
				ob_start();
				do_action('so_panels_render-'.$panel['info']['class']);
				$c = ob_get_clean();
					
				if(empty($c)) $panel_class->render($panel);
				else print $c;
				?></div><?php
			}
			if(empty($panels)) echo '&nbsp;';
			?></div><?php
		}
		?><div class="clear"></div></div><?php
	}
	$html = ob_get_clean();
	echo apply_filters('panels_render', $html, $post_id, $post);
}

/**
 * Change the template we're using for the home page to the panels template if we've set a panel home page.
 * 
 * @param $template
 * @return string
 */
function so_panels_set_home_template($template){
	if(get_theme_mod('panels_home_page')){
		$post = get_post(get_theme_mod('panels_home_page'));
		if(!empty($post) && $post->post_status == 'publish')
			$template = locate_template('single-panel.php'); 
	}
	
	return $template;
}
add_filter('home_template', 'so_panels_set_home_template');

/**
 * Filter the query for the home page panel so we just load the home panel
 * 
 * @param WP_Query $query
 * @return WP_Query
 */
function so_panels_filter_home_query($query){
	if(is_home() && $query->is_main_query() && get_theme_mod('panels_home_page')){
		$post = get_post(get_theme_mod('panels_home_page'));
		if(empty($post) || $post->post_status != 'publish') return $query;
		
		$query->set('post_type', 'panel');
		$query->set('numberposts', 1);
		$query->set('post__in', array(get_theme_mod('panels_home_page')));
		$query->is_single = true;
		$query->is_singular = true;
	}
	
	return $query;
}
add_filter('pre_get_posts', 'so_panels_filter_home_query');

/**
 * Change the permalink of the home page panel
 * 
 * @param $permalink
 * @param $post
 * @return string
 */
function so_panels_filter_post_link($permalink, $post){
	if($post->post_type == 'panel' && $post->ID == get_theme_mod('panels_home_page')){
		$permalink = home_url('/');
	}
	
	return $permalink;
}
add_filter('post_type_link', 'so_panels_filter_post_link', 10, 2);