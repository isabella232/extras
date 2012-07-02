<?php

/**
 * Add the custom CSS editor to the admin menu.
 */
function so_custom_css_admin_menu(){
	add_theme_page(__('Custom CSS', 'siteorigin'), __('Custom CSS', 'siteorigin'), 'edit_theme_options', 'so_custom_css', 'so_custom_css_page');
	
	if(isset($_POST['so_custom_css_save'])){
		check_admin_referer('custom_css', '_sononce');

		$theme = wp_get_theme();
		update_option('siteorigin_custom_css['.$theme->get_template().']', $_POST['custom_css']);
	}
	
}
add_action('admin_menu', 'so_custom_css_admin_menu');

function so_custom_css_help(){
	$screen = get_current_screen();
	$theme = wp_get_theme(basename(get_template_directory()));
	$screen->add_help_tab(array(
		'id' => 'custom-css',
		'title' => __('Custom CSS', 'siteorigin'),
		'content' => '<p>'
			. sprintf(__("%s adds any custom CSS you enter here into your site's header. ", 'siteorigin'), $theme->get('Name'))
			. __("These changes will persist across updates so it's best to make all your changes here. ", 'siteorigin')
			. sprintf(__("If you purchase <a href='%s'>%s Premium</a>, you'll get access premium support. ", 'siteorigin'), 'http://siteorigin.com/premium/'.$theme->get_template().'/', $theme->get('Name'))
			. __("We'll be able to help you make customizations. ", 'siteorigin')
			. '</p>'
	));
}
add_action('load-appearance_page_so_custom_css', 'so_custom_css_help');

/**
 * 
 * @param $page
 * @return mixed
 */
function so_custom_css_enqueue($page){
	if($page != 'appearance_page_so_custom_css') return;

	wp_enqueue_script('codemirror', get_template_directory_uri().'/extras/css/codemirror/lib/codemirror.js', array(), '2.3');
	wp_enqueue_script('codemirror-mode-css', get_template_directory_uri().'/extras/css/codemirror/mode/css/css.js', array(), '2.3');
	
	wp_enqueue_style('codemirror', get_template_directory_uri().'/extras/css/codemirror/lib/codemirror.css', array(), '2.3');
	wp_enqueue_style('codemirror-theme-neat', get_template_directory_uri().'/extras/css/codemirror/theme/neat.css', array(), '2.3');
	
	wp_enqueue_script('siteorigin-custom-css', get_template_directory_uri().'/extras/css/css.js', array('jquery'));
}
add_action('admin_enqueue_scripts', 'so_custom_css_enqueue');

/**
 * Render the custom CSS page
 */
function so_custom_css_page(){
	$theme = wp_get_theme();
	$custom_css = get_option('siteorigin_custom_css['.$theme->get_template().']', '');
	
	?>
	<div class="wrap">
		<div id="icon-themes" class="icon32"><br></div>
		<h2><?php _e('Custom CSS', 'siteorigin') ?></h2>
		
		<form action="<?php print add_query_arg(null, null) ?>" method="POST" style="margin-top:25px">
			
			<div id="custom-css-container" style="border:1px solid #DFDFDF; padding: 8px;">
				<textarea name="custom_css" id="custom-css-textarea"><?php print esc_textarea($custom_css) ?></textarea>
				<?php wp_nonce_field('custom_css', '_sononce') ?>
			</div>
			<p class="description">
				<?php
				$theme = wp_get_theme(basename(get_template_directory()));
				printf(__('Changes apply to %s and its child themes', 'siteorigin'), $theme->get('Name'));
				?>
			</p>
			
			<p class="submit">
				<input type="submit" name="so_custom_css_save" class="button-primary" value="<?php esc_attr_e('Save CSS', 'siteorigin'); ?>" />
			</p>
			
		</form>
	</div>
	<?php
}

/**
 * Display the custom CSS.
 * 
 * @return mixed
 */
function so_custom_css_print_styles(){
	$theme = wp_get_theme();
	$custom_css = get_option('siteorigin_custom_css['.$theme->get_template().']', '');
	if(empty($custom_css)) return;
	
	?><style type="text/css" media="screen" id="siteorigin-custom-css"><?php print esc_textarea($custom_css) ?></style><?php
}
add_action('wp_print_styles', 'so_custom_css_print_styles', 15);