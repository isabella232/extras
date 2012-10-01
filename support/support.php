<?php

/**
 * Add the support page
 * 
 * @action admin_menu
 */
function siteorigin_support_admin_menu(){
	add_theme_page(__('Premium Theme Support', 'siteorigin'), __('Theme Support', 'siteorigin'), 'switch_theme', 'theme_support', 'siteorigin_support_render');
}
add_action('admin_menu', 'siteorigin_support_admin_menu');

/**
 * Callback to render the premium page
 */
function siteorigin_support_render(){
	if(!defined('SITEORIGIN_IS_PREMIUM')) locate_template('extras/support/upgrade.php', true);
	else locate_template('extras/support/page.php', true);
}