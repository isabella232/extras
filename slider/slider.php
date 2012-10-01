<?php

/**
 * Create the slider post type
 */
function siteorigin_slider_init(){
	register_post_type('slider', array(
		'labels' => array(
			'name' => __('Sliders', 'siteorigin'),
			'singular_name' => __('Slider', 'siteorigin'),
			'add_new' => __('Add New', 'siteorigin', 'siteorigin'),
			'add_new_item' => __('Add New Slider', 'siteorigin'),
			'edit_item' => __('Edit Slider', 'siteorigin'),
			'new_item' => __('New Slider', 'siteorigin'),
			'all_items' => __('All Sliders', 'siteorigin'),
			'view_item' => __('View Slider', 'siteorigin'),
			'search_items' => __('Search Sliders', 'siteorigin'),
			'not_found' =>  __('No sliders found', 'siteorigin'),
			'not_found_in_trash' => __('No sliders found in Trash', 'siteorigin'),
			'parent_item_colon' => '',
			'menu_name' => __('Sliders', 'siteorigin')

		),
		'public' => false,
		'publicly_queryable' => false,
		'show_ui' => true,
		'show_in_menu' => true,
		'query_var' => false,
		'rewrite' => false,
		'capability_type' => 'post',
		'has_archive' => true,
		'hierarchical' => false,
		'menu_position' => null,
		'supports' => array( 'title'),
		'register_meta_box_cb' => 'siteorigin_slider_metaboxes',
		'menu_icon' => get_template_directory_uri().'/extras/slider/menu-icon.png'
	));
}
add_action('after_setup_theme', 'siteorigin_slider_init');

/**
 * Register slider metaboxes
 */
function siteorigin_slider_metaboxes(){
	add_meta_box('slider-slides', __('Slider Slides', 'siteorigin'), 'siteorigin_slider_metabox_slider_slides', 'slider');
}

/**
 * Add custom slider admin columns
 */
function siteorigin_slider_admin_columns($columns){
	$columns['slides'] = __('Slides', 'siteorigin');
	return $columns;
}
add_filter('manage_slider_posts_columns', 'siteorigin_slider_admin_columns');

/**
 * Render the custom slider admin columns.
 * 
 * @param $column
 * @param $post_id
 */
function siteorigin_slider_admin_column_display($column, $post_id){
	switch($column){
		case 'slides':
			$slides = get_post_meta($post_id, 'siteorigin_slider', true);
			echo count($slides);
			break;
	}
}
add_action('manage_slider_posts_custom_column', 'siteorigin_slider_admin_column_display', 10, 2);

/**
 * Enqueue scripts for editing the slider
 */
function siteorigin_slider_admin_enqueue(){
	$screen = get_current_screen();
	if($screen->id == 'slider'){
		global $post;
		
		wp_enqueue_script('thickbox');
		wp_enqueue_style('thickbox');

		wp_enqueue_script( 'jquery-ui-draggable' );
		wp_enqueue_script( 'jquery-ui-droppable' );
		wp_enqueue_script( 'jquery-ui-sortable' );

		wp_enqueue_script('siteorigin-slider-admin', get_template_directory_uri().'/extras/slider/js/admin.js', array('jquery'), SITEORIGIN_THEME_VERSION);
		wp_enqueue_style('siteorigin-slider-admin', get_template_directory_uri().'/extras/slider/css/admin.css', array(), SITEORIGIN_THEME_VERSION);
		
		$slides = get_post_meta($post->ID, 'siteorigin_slider', true);
		wp_localize_script('siteorigin-slider-admin', 'siteoriginSlider', array(
			'images' => siteorigin_slider_get_post_images($post->ID),
			'slides' => $slides,
		));
	}
}
add_action('admin_print_scripts-post-new.php', 'siteorigin_slider_admin_enqueue');
add_action('admin_print_scripts-post.php', 'siteorigin_slider_admin_enqueue');

/**
 * Get the images for a given post
 */
function siteorigin_slider_action_images(){
	header('content-type: text/json', true);
	echo json_encode(siteorigin_slider_get_post_images(intval($_REQUEST['post_ID'])));
	exit();
}
add_action('wp_ajax_siteorigin_slider_images', 'siteorigin_slider_action_images');

/**
 * Get all the images for the slider
 * @param $post_id
 * @return array
 */
function siteorigin_slider_get_post_images($post_id){
	$attachments = get_children(array(
		'post_type' => 'attachment',
		'post_mime_type' => 'image',
		'post_parent' => $post_id,
		'post_status' => null,
		'numberposts' => -1,
	));
	$images = array();
	foreach($attachments as $attachment){
		$images[$attachment->ID] = $attachment->post_title;
	}
	
	return $images;
}

/**
 * Render the slider meta box
 */
function siteorigin_slider_metabox_slider_slides($post, $post_id){
	get_template_part('extras/slider/tpl/admin', 'builder');
}

/**
 * Save the slider
 */
function siteorigin_slider_save_post($post_id){
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( empty($_POST['_siteorigin_slider_nonce']) || !wp_verify_nonce($_POST['_siteorigin_slider_nonce'], 'save_slider') ) return;
	
	$slider = array();
	foreach($_REQUEST['siteorigin_slider'] as $field => $vals){
		foreach($vals as $slide => $val){
			if(empty($slider[$slide])) $slider[$slide] = array();
			$slider[$slide][$field] = $val;
		}
	}
	
	$slider = array_map('stripslashes_deep', $slider);
	update_post_meta($post_id, 'siteorigin_slider', $slider);
}
add_action('save_post', 'siteorigin_slider_save_post');

function siteorigin_slider_display_upgrade(){
	if(!defined('SITEORIGIN_IS_PREMIUM')){
		
		?>
		<tr valign="top">
			<th scope="row"><label><?php _e('URL', 'siteorigin') ?></label></th>
			<td>
				<?php
				printf(
					__('Upgrade to <a href="%s">%s Premium</a> to link slides to URLS', 'siteorigin'),
					admin_url('themes.php?page=premium_upgrade'),
					ucfirst(get_option('stylesheet'))
				);
				?>
			</td>
		</tr>
		<?php
	}
}
add_action('siteorigin_slider_after_builder_form', 'siteorigin_slider_display_upgrade');

/**
 * The SiteOrigin Slider Widget
 */
class SiteOrigin_Slider_Widget extends WP_Widget {
	function __construct(){
		parent::__construct(
			'so-slider',
			__('Slider', 'siteorigin'),
			array(
				'description' => __('Insert a slider.', 'siteorigin'),
			)
		);
	}

	function widget($args, $instance){
		
		echo $args['before_widget'];
		
		if($instance['slider_id'] == -1){
			_e('Please select a slider.', 'siteorigin');
			echo $args['after_widget'];
			return;
		}

		$post = get_post($instance['slider_id']);
		$slides = get_post_meta($post->ID, 'siteorigin_slider', true);

		?><ul class="slides"><?php
		foreach($slides as $slide){
			?>
			<li>
				<?php do_action('siteorigin_slide_before', $slide) ?>
					
				<?php echo wp_get_attachment_image($slide['image'], !empty($data['image_size']) ? $data['image_size'] : 'large'); ?>
				<?php if(!empty($slide['title']) || !empty($slide['extra'])) : ?>
					<div class="slide-text">
						<h3><?php print esc_html($slide['title']) ?></h3>
						<p><?php print esc_html($slide['extra']) ?></p>
					</div>
				<?php endif; ?>
					
				<?php do_action('siteorigin_slide_after', $slide) ?>
			</li>
			<?php
		}
		?></ul><?php
		print $args['after_widget'];
	}
	
	/**
	 * @param array $instance
	 * @return string|void
	 */
	function form($instance){
		$instance = wp_parse_args($instance, array(
			'slider_id' => false,
			'image_size' => false,
		));
		
		
		$sliders = get_posts(array(
			'numberposts' => -1,
			'post_type' => 'slider',
			'post_status' => 'publish',
		));
		
		global $_wp_additional_image_sizes;
		
		?>
		<p><label for="<?php echo $this->get_field_id('slider_id') ?>"><?php _e('Slider', 'siteorigin') ?></label></p>
		<p>
			<select name="<?php echo $this->get_field_name('slider_id') ?>" id="<?php echo $this->get_field_id('slider_id') ?>">
				<option value="-1"><?php esc_html_e('None', 'siteorigin') ?></option>
				<?php foreach ($sliders as $slider ) : ?>
					<option value="<?php echo $slider->ID ?>"><?php echo $slider->post_title ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p><label for="<?php echo $this->get_field_id('image_size') ?>"><?php _e('Image Size', 'siteorigin') ?></label></p>
		<p>
			<select name="<?php echo $this->get_field_name('image_size') ?>" id="<?php echo $this->get_field_id('image_size') ?>">
				<option value="large"><?php esc_html_e('Large', 'siteorigin') ?></option>
				<option value="medium"><?php esc_html_e('Medium', 'siteorigin') ?></option>
				<option value="thumbnail"><?php esc_html_e('Thumbnail', 'siteorigin') ?></option>
				<option value="full"><?php esc_html_e('Full', 'siteorigin') ?></option>
				<?php foreach ($_wp_additional_image_sizes as $name => $info) : ?>
					<option value="<?php echo esc_attr($name) ?>"><?php echo esc_html($name) ?></option>
				<?php endforeach ?>
			</select>
		</p>
		<?php
	}

	function update($new, $old){
		return $new;
	}
}

/**
 * Register the slider widgets
 */
function siteorigin_slider_register_widgets(){
	register_widget('SiteOrigin_Slider_Widget');
}
add_action('widgets_init', 'siteorigin_slider_register_widgets');