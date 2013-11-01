<?php

$siteorigin_theme_widgets = array();
$siteorigin_theme_widget_teasers = array();

function siteorigin_theme_widgets_admin_menu(){
	add_theme_page( __('Theme Widgets', 'siteorigin'), __('Theme Widgets', 'siteorigin'), 'manage_options', 'siteorigin_theme_widgets', 'siteorigin_theme_widgets_admin_page' );
}
add_action('admin_menu', 'siteorigin_theme_widgets_admin_menu', 12);

function siteorigin_theme_widgets_admin_page(){
	get_template_part('extras/theme-widgets/tpl/admin');
}

/**
 *
 */
function siteorigin_theme_widgets_init(){
	
}
add_action('widgets_init', 'siteorigin_theme_widgets_init');

/**
 * Register a widget that we can enable/disable
 *
 * @param $class
 * @param $title
 * @param bool $image
 */
function siteorigin_theme_widget_register($class, $title, $image = false){
	global $siteorigin_theme_widgets;
	$siteorigin_theme_widgets[$class] = compact('title', 'image');
}

/**
 * Register a widget teaser, probably included in premium version.
 *
 * @param $class
 * @param $title
 * @param bool $image
 */
function siteorigin_theme_widget_register_teaser($class, $title, $image = false){
	global $siteorigin_theme_widget_teasers;
	$siteorigin_theme_widget_teasers[$class] = compact('title', 'image');
}