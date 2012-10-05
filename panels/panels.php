<?php

/**
 * Initialize the panels extra
 * 
 * @action after_setup_theme
 */
function siteorigin_panels_init(){
	$panels_support = get_theme_support('siteorigin-panels');
	if(empty($panels_support)) return;
	
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
		'register_meta_box_cb' => 'siteorigin_panels_metaboxes',
		'menu_icon' => get_template_directory_uri().'/extras/panels/images/menu-icon.png'
	));
}
add_action('after_setup_theme', 'siteorigin_panels_init');

/**
 * Callback to register the Panels Metaboxes
 */
function siteorigin_panels_metaboxes(){
	add_meta_box('so-panels-panels', __('Panels', 'siteorigin'), 'siteorigin_panels_metabox_render', 'panel', 'advanced', 'default', array('panels'));
	add_meta_box('so-panels-settings', __('Panel Settings', 'siteorigin'), 'siteorigin_panels_metabox_render', 'panel', 'side', 'default', array('settings'));
}

/**
 * Render a panel metabox.
 * 
 * @param $post
 * @param $args
 */
function siteorigin_panels_metabox_render($post, $args){
	get_template_part('extras/panels/tpl/metabox', $args['args'][0]);
}

/**
 * Enqueue the panels admin scripts
 * 
 * @action admin_print_scripts-post-new.php
 * @action admin_print_scripts-post.php
 */
function siteorigin_panels_admin_enqueue_scripts(){
	$screen = get_current_screen();
	if($screen->id == 'panel'){
		wp_enqueue_script('jquery-ui-resizable');
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script('jquery-ui-tabs');
		wp_enqueue_script('jquery-ui-dialog');
		wp_enqueue_script('jquery-ui-button');

		wp_enqueue_script('so-panels-admin-grid', get_template_directory_uri().'/extras/panels/js/panels.admin.grid.js', array('jquery'), SITEORIGIN_THEME_VERSION);
		wp_enqueue_script('so-panels-admin', get_template_directory_uri().'/extras/panels/js/panels.admin.js', array('jquery'), SITEORIGIN_THEME_VERSION);
		wp_enqueue_script('so-panels-admin-tooltip', get_template_directory_uri().'/extras/panels/js/panels.admin.tooltip.js', array('jquery'), SITEORIGIN_THEME_VERSION);
		
		wp_localize_script('so-panels-admin', 'panelsLoc', array(
			'buttons' => array(
				'delete' => __('Delete', 'siteorigin'),
				'done' => __('Done', 'siteorigin'),
			),
			'messages' => array(
				'confirmDeletePanel' => __('Are you sure you want to delete this panel?', 'siteorigin'),
			)
		));
		
		global $siteorigin_panel_grids;
		wp_localize_script('so-panels-admin-grid', 'panelsGrids', $siteorigin_panel_grids);
		
		// Localize the panels with the panels data
		global $post;
		$panels_data = get_post_meta($post->ID, 'panels_data', true);
		if(empty($panels_data)) $panels_data = array();
		
		// Remove any panels that no longer exist.
		if(!empty($panels_data['panels'])) {
			foreach($panels_data['panels'] as $i => $panel){
				if(!class_exists($panel['info']['class'])) unset($panels_data['panels'][$i]);
			}
		}
		
		if(!empty($panels_data )){
			wp_localize_script('so-panels-admin', 'panelsData', $panels_data);
		}
		
		// This gives panels a chance to enqueue scripts too, without having to check the screen ID.
		do_action('siteorigin_panel_enqueue_admin_scripts');
	}
}
add_action('admin_print_scripts-post-new.php', 'siteorigin_panels_admin_enqueue_scripts');
add_action('admin_print_scripts-post.php', 'siteorigin_panels_admin_enqueue_scripts');

/**
 * Enqueue the admin panel styles
 * 
 * @action admin_print_styles-post-new.php
 * @action admin_print_styles-post.php
 */
function siteorigin_panels_admin_enqueue_styles(){
	$screen = get_current_screen();
	if($screen->id == 'panel'){
		wp_enqueue_style("wp-jquery-ui-dialog");
		wp_enqueue_style('so-panels-jquery-ui', get_template_directory_uri().'/extras/panels/css/jquery-ui-theme.css');
		wp_enqueue_style('so-panels-admin', get_template_directory_uri().'/extras/panels/css/panels-admin.css');
		wp_enqueue_style('so-panels-icon', get_template_directory_uri().'/extras/panels/css/panels-icon.css');

		do_action('siteorigin_panel_enqueue_admin_styles');
	}
}
add_action('admin_print_styles-post-new.php', 'siteorigin_panels_admin_enqueue_styles');
add_action('admin_print_styles-post.php', 'siteorigin_panels_admin_enqueue_styles');

/**
 * This is the style for the panel icon.
 *
 * @action admin_print_styles-edit.php 
 */
function siteorigin_panels_admin_enqueue_icon_style(){
	$screen = get_current_screen();
	if($screen->post_type == 'panel'){
		wp_enqueue_style('so-panels-icon', get_template_directory_uri().'/extras/panels/css/panels-icon.css');
	}
}
add_action('admin_print_styles-edit.php', 'siteorigin_panels_admin_enqueue_icon_style');

/**
 * Save the panels data
 * 
 * @param $post_id
 * 
 * @action save_post
 */
function siteorigin_panels_save_post($post_id){
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( empty($_POST['_sopanels_nonce']) || !wp_verify_nonce($_POST['_sopanels_nonce'], 'save') ) return;
	if ( !current_user_can( 'edit_post', $post_id ) ) return;
	
	$panels_data = array();
	
	$panels_data['widgets'] = array_map('stripslashes_deep', isset($_POST['widgets']) ? $_POST['widgets'] : array());
	$panels_data['widgets'] = array_values($panels_data['widgets']);
	
	foreach($panels_data['widgets'] as $i => $widget){
		$info = $widget['info'];
		if(!class_exists($widget['info']['class'])) continue;
		
		$the_widget = new $widget['info']['class'];
		if(method_exists($the_widget, 'update')){
			unset($widget['info']);
			$widget = $the_widget->update($widget, $widget);
		}
		$widget['info'] = $info;
		$panels_data['widgets'][$i] = $widget;
	}
	
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
add_action('save_post', 'siteorigin_panels_save_post');


/**
 * Echo the CSS for the current panel
 * 
 * @action wp_print_styles
 */
function siteorigin_panels_css(){
	global $post;
	
	$panels_support = get_theme_support('siteorigin-panels');
	if(empty($panels_support)) return;
	$panels_support = $panels_support[0];
	
	$panels_margin_bottom = $panels_support['margin-bottom'];
	
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
			
			if($panels_support['responsive']){
				// Mobile Responsive
				$mobile_css = array('float:none','width:auto','margin-bottom:'.$panels_margin_bottom.'px');
				foreach($mobile_css as $c){
					if(empty($css[767][$c])) $css[767][$c] = array();
					$css[767][$c][] = '#pg-'.$gi.' .panel-grid-cell';
				}
			}
		}

		/**
		 * Filter the unprocessed CSS array
		 */
		$css = apply_filters('siteorigin_panels_css', $css);

		// Build the CSS
		$css_text = '';
		krsort($css);
		foreach($css as $res => $def){
			if(empty($def)) continue;
			
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
add_action('wp_print_styles', 'siteorigin_panels_css');

/**
 * Filter the content of the panel, adding all the widgets.
 * 
 * @param $content
 * 
 * @filter the_content
 */
function siteorigin_panels_content_filter($content){
	global $post;
	if($post->post_type == 'panel'){
		$panel_content = siteorigin_panels_render($post->ID);
		if(!empty($panel_content)) $content = $panel_content;
	}
	
	return $content;
}
add_filter('the_content', 'siteorigin_panels_content_filter');

/**
 * Render the panels
 * 
 * @param bool $post_id
 */
function siteorigin_panels_render($post_id = false){
	if(empty($post_id)){
		global $post;
	}
	else $post = get_post($post_id);

	$panels_data = get_post_meta($post->ID, 'panels_data', true);
	$panels_data = apply_filters('siteorigin_panels_data', $panels_data, $post_id);
	
	// Create the skeleton of the grids
	$grids = array();
	foreach($panels_data['grids'] as $gi => $grid){
		$gi = intval($gi);
		$grids[$gi] = array();
		for($i = 0; $i < $grid['cells']; $i++){
			$grids[$gi][$i] = array();
		}
	}
	
	foreach($panels_data['widgets'] as $widget){
		$grids[intval($widget['info']['grid'])][intval($widget['info']['cell'])][] = $widget;
	}
	
	ob_start();
	foreach($grids as $gi => $cells){
		?><div class="panel-grid" id="pg-<?php echo $gi ?>"><?php
		foreach($cells as $ci => $widgets){
			?><div class="panel-grid-cell" id="pgc-<?php echo $gi.'-'.$ci ?>"><?php
			foreach($widgets as $pi => $widget_info){
				// Skip this if the class no longer exists
				if(!class_exists($widget_info['info']['class'])) continue;

				$the_widget = new $widget_info['info']['class'];
				
				$data = $widget_info;
				unset($data['info']);
				
				$classes = array('panel', 'widget');
				if(!empty($the_widget->id_base)) $classes[] = 'widget_'.$the_widget->id_base; 
				if($pi == 0) $classes[] = 'panel-first-child';
				if($pi == count($widgets)-1) $classes[] = 'panel-last-child';
				
				$the_widget->widget(array(
					'before_widget' => '<div class="'.esc_attr(implode(' ',$classes)).'" id="panel-'.$gi . '-' . $ci. '-' . $pi.'">',
					'after_widget' => '</div>',
					'before_title' => '<h3 class="widget-title">',
					'after_title' => '</h3>',
					'widget_id' => 'widget-'.$gi.'-'.$ci.'-'.$pi
				), $data);
			}
			if(empty($widgets)) echo '&nbsp;';
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
 * 
 * @filter home_template
 */
function siteorigin_panels_set_home_template($template){
	if(get_theme_mod('panels_home_page')){
		$post = get_post(get_theme_mod('panels_home_page'));
		if(!empty($post) && $post->post_status == 'publish')
			$template = locate_template('single-panel.php'); 
	}
	
	return $template;
}
add_filter('home_template', 'siteorigin_panels_set_home_template');

/**
 * Filter the query for the home page panel so we just load the home panel
 * 
 * @param WP_Query $query
 * @return WP_Query
 * 
 * @filter pre_get_posts
 */
function siteorigin_panels_filter_home_query($query){
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
add_filter('pre_get_posts', 'siteorigin_panels_filter_home_query');

/**
 * Change the permalink of the home page panel
 * 
 * @param $permalink
 * @param $post
 * @return string
 * 
 * @filter post_type_link
 */
function siteorigin_panels_filter_post_link($permalink, $post){
	if($post->post_type == 'panel' && $post->ID == get_theme_mod('panels_home_page')){
		$permalink = home_url('/');
	}
	
	return $permalink;
}
add_filter('post_type_link', 'siteorigin_panels_filter_post_link', 10, 2);