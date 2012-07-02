<?php

/**
 * Display the premium admin menu
 * @return mixed
 */
function so_premium_admin_menu(){
	if(defined('SO_PREMIUM_VERSION')) return;
	
	add_theme_page(__('Premium Upgrade', 'siteorigin'), __('Premium Upgrade', 'siteorigin'), 'switch_themes', 'premium_upgrade', 'so_premium_page_render');
}
add_action('admin_menu', 'so_premium_admin_menu');

/**
 * Render the premium page
 */
function so_premium_page_render(){
	$theme = wp_get_theme(basename(get_template_directory()));
	
	?>
	<div class="wrap">
		<h2><?php _e('Premium Upgrade', 'siteorigin') ?></h2>
		<p><?php printf(__("If you like %s, you'll love the premium upgrade - <a href='%s' target='_blank'>find out more</a>"), $theme->get('Name'), so_premium_get_url()) ?></p>
	</div>
	<?php
}

/**
 * Gets the URL of the premium theme.
 * @return string The URL where the user can buy the premium upgrade.
 */
function so_premium_get_url(){
	return 'http://siteorigin.com/premium/'.basename(get_template_directory()).'/';
}