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
 * Render this theme's premium upgrade page
 */
function so_premium_page_render(){
	$theme = wp_get_theme(basename(get_template_directory()));
	$args = apply_filters('so_premium_page', array(
		'title' => sprintf(__('Upgrade To %s Premium', 'siteorigin'), $theme->get('Name')),
		'purchase_url' => '',
		'first_line' => '',
		'below_first_buy' => '', 
		'below_second_buy' => '', 
		'features' => array(),
		'final' => '',
	));
	
	?>
	<div class="wrap" id="theme-upgrade">
		<h2><?php print $args['title'] ?></h2>
		<p><?php print $args['first_line'] ?></p>

		<p class="download">
			<a href="<?php print $args['purchase_url'] ?>" class="download"><?php _e('Download Now', 'siteorigin') ?></a>
			<span><?php print $args['below_first_buy'] ?></span>
		</p>
		
		<?php if(!empty($args['promo_image'])) : ?>
			<div id="promo-image">
				<img src="<?php print esc_attr($args['promo_image'][0]) ?>" width="<?php print esc_attr($args['promo_image'][1]) ?>" height="<?php print esc_attr($args['promo_image'][2]) ?>" class="magnify" />
			</div>
		<?php endif; ?>
		
		<?php foreach($args['features'] as $feature) : ?>
			<h3><?php print $feature['title'] ?></h3>
			<p><?php print $feature['text'] ?></p>
		<?php endforeach ?>

		<p class="download">
			<a href="<?php print $args['purchase_url'] ?>" class="download"><?php _e('Download Now', 'siteorigin') ?></a>
			<span><?php print $args['below_second_buy'] ?></span>
		</p>
		
		<p><?php print $args['final'] ?></p>
	</div>
	<?php
}

/**
 * Enqueue premium scripts
 */
function so_premium_enqueue($prefix){
	if($prefix != 'appearance_page_premium_upgrade') return false;
	
	wp_enqueue_style('siteorigin-premium-upgrade', get_template_directory_uri().'/extras/premium/upgrade.css', array(), SO_THEME_VERSION);
	wp_enqueue_script('siteorigin-magnifier', get_template_directory_uri().'/extras/premium/magnifier.js');
}
add_action('admin_enqueue_scripts', 'so_premium_enqueue');

function so_premium_footer(){
	$screen = get_current_screen();
	if($screen->id != 'appearance_page_premium_upgrade') return false;
	?><div id="magnifier"><div class="image"></div></div><?php
}
add_action('admin_footer', 'so_premium_footer');