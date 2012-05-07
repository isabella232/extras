<?php

/**
 * Active the First Run extra. This will just display a bar in the admin after a user first installs this theme
 * 
 * @action after_switch_theme
 */
function so_first_run_activate(){
	global $siteorigin_first_run_active;
	$siteorigin_first_run_active = true;
}
add_action('after_switch_theme', 'so_first_run_activate');

/**
 * Enqueue admin scripts.
 * 
 * @param $suffix
 * @return mixed
 */
function so_first_run_enqueue($suffix){
	if($suffix != 'themes.php' && $suffix != 'theme-install.php') return;
	global $siteorigin_first_run_active;
	if(empty($siteorigin_first_run_active)) return;
	
	wp_enqueue_script('siteorigin-firstrun', get_template_directory_uri().'/extras/firstrun/firstrun.js', array('jquery'));
	wp_enqueue_style('siteorigin-firstrun', get_template_directory_uri().'/extras/firstrun/firstrun.css');
}
add_action('admin_enqueue_scripts', 'so_first_run_enqueue');

/**
 * Display the first run bar
 * 
 * @action admin_footer-themes.php
 */
function so_first_run_display(){
	global $siteorigin_first_run_active;
	if(empty($siteorigin_first_run_active)) return;
	
	// TODO update this when WP 3.4 is officially released
	$theme = get_theme_data(get_template_directory().'/style.css');
	include(dirname(__FILE__).'/bar.phtml');
}
add_action('admin_footer-themes.php', 'so_first_run_display');