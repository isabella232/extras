<?php
/**
 * Tight integration with Metaslider
 */

/**
 * Enqueue scripts and styles for meta slider
 */
function siteorigin_metaslider_register_admin_scripts(){
	wp_enqueue_script('siteorigin-metaslider', get_template_directory_uri().'/extras/metaslider/js/metaslider.js',  array('jquery', 'media-views', 'metaslider-admin-script'), SITEORIGIN_THEME_VERSION);
	wp_enqueue_style('siteorigin-metaslider', get_template_directory_uri().'/extras/metaslider/css/metaslider.css',  array(), SITEORIGIN_THEME_VERSION);
	wp_localize_script('siteorigin-metaslider', 'siteoriginMetaslider', array(
		'prebuilt' => __('Prebuilt Slide Layouts', 'siteorigin')
	) );

	// Check if this theme has a metaslider editor style
	if(file_exists(get_template_directory().'/slider/metaslider-editor-style.css')) {
		wp_enqueue_style('siteorigin-metaslider-editor-style', get_template_directory_uri().'/slider/metaslider-editor-style.css',  array(), SITEORIGIN_THEME_VERSION);
	}
}
add_action('metaslider_register_admin_scripts', 'siteorigin_metaslider_register_admin_scripts');

function siteorigin_metaslider_prebuilt_window(){
	if(isset($_GET['page']) && $_GET['page'] == 'metaslider') {
		$layouts = siteorigin_metaslider_prebuilt_layouts();

		?>
		<div id="siteorigin-metaslider-prebuilt-layouts-overlay"></div>
		<div id="siteorigin-metaslider-prebuilt-layouts">
			<a href="#" class="close">x</a>
			<h2><?php _e('Prebuilt Layouts', 'siteorigin') ?></h2>

			<ul class="layouts">
				<?php foreach($layouts as $id => $layout) : ?>
					<li class="layout" data-html="<?php echo esc_attr($layout['html']) ?>">
						<img src="<?php echo get_template_directory_uri().'/extras/metaslider/img/layouts/'.$id.'.png' ?>" />
						<h4><?php echo $layout['title'] ?></h4>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php
	}
}
add_action('admin_footer', 'siteorigin_metaslider_prebuilt_window');

function siteorigin_metaslider_prebuilt_layouts(){
	static $layouts = null;
	if(is_null($layouts)){
		do_action('siteorigin_metaslider_load_prebuilt_layout_files');
		foreach(glob(dirname(__FILE__).'/layouts/*.php') as $lf) {
			include_once($lf);
		}

		$layouts = apply_filters('siteorigin_metaslider_layouts', array());
	}

	return $layouts;
}