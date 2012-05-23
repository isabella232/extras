<?php

/**
 * Active the First Run extra. This will just display a bar in the admin after a user first installs this theme
 * 
 * @action after_switch_theme
 */
function so_adminbar_first_run_activate(){
	define('SO_FIRST_RUN_ACTIVE', true);
}
add_action('after_switch_theme', 'so_adminbar_first_run_activate');

/**
 * Enqueue admin scripts.
 * 
 * @param $suffix
 * @return mixed
 */
function so_adminbar_enqueue($suffix){
	if(!so_adminbar_is_display()) return;
	
	wp_enqueue_script('siteorigin-admin-bar', get_template_directory_uri().'/extras/admin/assets/bar.js', array('jquery'));
	wp_enqueue_style('siteorigin-admin-bar', get_template_directory_uri().'/extras/admin/assets/bar.css');
}
add_action('admin_enqueue_scripts', 'so_adminbar_enqueue');

/**
 * Check if we're displaying the admin bar
 * 
 * @return bool|string The name of the admin bar to display or false for none.
 */
function so_adminbar_is_display(){
	$screen = get_current_screen();
	
	if($screen->id == 'appearance_page_custom-background') $bar = 'background';
	else if($screen->id == 'themes' && defined('SO_FIRST_RUN_ACTIVE')) $bar = 'firstrun';
	
	if(empty($bar)) return false;
	
	// Check if this bar has been dismissed
	$dismissed = get_user_meta(get_current_user_id(), 'so_admin_bars_dismissed', true);
	if(empty($dismissed) || empty($dismissed[$bar])) return $bar;
	return false;
	
}

/**
 * Display the first run bar
 * 
 * @action in_admin_header
 */
function so_adminbar_render(){
	$bar = so_adminbar_is_display();
	if(!$bar) return;

	if(!file_exists(dirname(__FILE__).'/icons/'.$bar.'.png')) $icon = 'http://www.gravatar.com/avatar/'.md5('greg@siteorigin.com').'?s=44';
	else $icon = get_template_directory_uri().'/extras/admin/icons/'.$bar.'.png';

	?>
	<div id="siteorigin-admin-bar" data-type="<?php print esc_attr($bar) ?>">
		<div class="inner">
			<img src="<?php print esc_attr($icon) ?>" class="icon" width="44" height="44" />
			<a href="#dismiss" class="dismiss"><?php _e('dismiss', 'siteorigin') ?></a>
			<strong><?php get_template_part('extras/admin/messages/message', $bar) ?></strong>
		</div>
	</div>
	<?php
}
add_action('in_admin_header', 'so_adminbar_render');

/**
 * An ajax callback to dismiss the admin bar.
 */
function so_adminbar_dismiss_bar(){
	$dismiss = $previous = get_user_meta(get_current_user_id(), 'so_admin_bars_dismissed', true);
	if(empty($dismiss)) $dismiss = array();
	
	$bar = stripslashes($_POST['bar']);
	$dismiss[$bar] = true;
	
	update_user_meta(get_current_user_id(), 'so_admin_bars_dismissed', $dismiss, $previous);
	
	exit();
}
add_action('wp_ajax_so_admin_dismiss_bar', 'so_adminbar_dismiss_bar');