<?php

/**
 * Initialise the recommended extra
 */
function siteorigin_recommended_menu(){
	$theme = wp_get_theme();

	add_theme_page(
		sprintf(__('%s Addons', 'siteorigin'), $theme->get('Name')),
		sprintf(__('%s Addons', 'siteorigin'), $theme->get('Name')),
		'activate_plugins',
		'siteorigin_recommended_page',
		'siteorigin_recommended_display_page'
	);

}
add_action('admin_menu', 'siteorigin_recommended_menu');

function siteorigin_recommended_display_page(){
	locate_template('extras/recommended/tpl/admin.php', true);
}

function siteorigin_recommended_enqueue_scripts($prefix){
	if($prefix == 'appearance_page_siteorigin_recommended_page') {
		wp_enqueue_style('siteorigin-recommended-admin', get_template_directory_uri().'/extras/recommended/css/admin.css', array(), SITEORIGIN_THEME_VERSION);
	}
}
add_action('admin_enqueue_scripts', 'siteorigin_recommended_enqueue_scripts');

class SiteOrigin_Recommended_Customizer {
	static $customizer_url;

	/**
	 * @param The URL where the user can find the customizer plugin $customizer_url
	 */
	function __construct($customizer_url){
		self::$customizer_url = $customizer_url;

		// Enqueue if the customizer plugin isn't already active
		add_action('customize_controls_enqueue_scripts', array($this, 'customizer_enqueue'));
		add_action('customize_controls_init', array($this, 'customizer_init'), 100);
	}

	function customizer_enqueue(){
		// Only enqueue if the customizer plugin isn't already active
		if(is_plugin_active(get_option('template').'-customizer')) return;
		wp_enqueue_style( 'siteorigin-customizer-teaser', get_template_directory_uri() . '/extras/recommended/css/teaser.css', array(), SITEORIGIN_THEME_VERSION );
	}

	function customizer_init(){
		// Only activate if the customizer plugin isn't already active
		if(is_plugin_active(get_option('template').'-customizer')) return;

		/**
		 * @var WP_Customize_Manager
		 */
		global $wp_customize;
		$teaser_customizer = new SiteOrigin_Premium_Teaser_Customizer($wp_customize, 'siteorigin-premium-teaser');
		$wp_customize->add_section($teaser_customizer);
	}
}

if(class_exists('WP_Customize_Section')) :
	class SiteOrigin_Premium_Teaser_Customizer extends WP_Customize_Section{
		function render() {
			$theme = get_option('template');

			?>
			<div class="siteorigin-premium-teaser-customizer-wrapper">
				<a class="siteorigin-premium-teaser" href="<?php echo esc_url(SiteOrigin_Recommended_Customizer::$customizer_url) ?>" target="_blank">
					<em></em>
					<?php echo sprintf(__('Get More Options', 'siteorigin'), $theme) ?>
				</a>
			</div>
			<?php
		}
	}
endif;