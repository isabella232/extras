<?php

/**
 * Create the slider post type
 */
function seven_slider_init(){
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
		'register_meta_box_cb' => 'seven_slider_metaboxes',
	));
}
add_action('after_setup_theme', 'seven_slider_init');

/**
 * Register slider metaboxes
 */
function seven_slider_metaboxes(){
	add_meta_box('slider-slides', __('Slider Slides', 'siteorigin'), 'seven_slider_metabox_slider_slides', 'slider');
}

/**
 * Enqueue scripts for editing the slider
 */
function seven_slider_admin_enqueue(){
	$screen = get_current_screen();
	if($screen->id == 'slider'){
		global $post;
		
		wp_enqueue_script('thickbox');
		wp_enqueue_style('thickbox');

		wp_enqueue_script( 'jquery-ui-draggable' );
		wp_enqueue_script( 'jquery-ui-droppable' );
		wp_enqueue_script( 'jquery-ui-sortable' );

		wp_enqueue_script('seven-slider-admin', get_template_directory_uri().'/extras/slider/admin.js', array('jquery'), SO_THEME_VERSION);
		wp_enqueue_style('seven-slider-admin', get_template_directory_uri().'/extras/slider/admin.css', array(), SO_THEME_VERSION);
		
		$slides = get_post_meta($post->ID, 'seven_slider', true);
		wp_localize_script('seven-slider-admin', 'sevenSlider', array(
			'images' => seven_slider_get_post_images($post->ID),
			'slides' => $slides,
		));
	}
}
add_action('admin_print_scripts-post-new.php', 'seven_slider_admin_enqueue');
add_action('admin_print_scripts-post.php', 'seven_slider_admin_enqueue');

/**
 * Get the images for a given post
 */
function seven_slider_action_images(){
	header('content-type: text/json', true);
	print json_encode(seven_slider_get_post_images(intval($_REQUEST['post_ID'])));
	exit();
}
add_action('wp_ajax_seven_slider_images', 'seven_slider_action_images');

/**
 * Get all the images for the slider
 * @param $post_id
 * @return array
 */
function seven_slider_get_post_images($post_id){
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
function seven_slider_metabox_slider_slides($post, $post_id){
	get_template_part('extras/slider/admin', 'builder');
}

/**
 * Save the slider
 */
function seven_slider_save_post($post_id){
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( empty($_POST['_seven_nonce']) || !wp_verify_nonce($_POST['_seven_nonce'], 'save_slider') ) return;
	
	$slider = array();
	foreach($_REQUEST['seven_slider'] as $field => $vals){
		foreach($vals as $slide => $val){
			if(empty($slider[$slide])) $slider[$slide] = array();
			$slider[$slide][$field] = $val;
		}
	}
	
	$slider = array_map('stripslashes_deep', $slider);
	update_post_meta($post_id, 'seven_slider', $slider);
}
add_action('save_post', 'seven_slider_save_post');

/**
 * The Seven slider panel
 */
class Seven_Panel_Slider extends SO_Panel{
	function form(){
		$sliders = get_posts(array(
			'numberposts' => -1,
			'post_type' => 'slider',
			'post_status' => 'publish',
		));
		
		global $_wp_additional_image_sizes;
		
		?>
		<p><strong><?php _e('Slider', 'siteorigin') ?></strong></p>
		<p>
			<select name="<?php print self::input_name('slider_id') ?>">
				<option value="-1"><?php _e('None', 'siteorigin') ?></option>
				<?php foreach ($sliders as $slider ) : ?>
					<option value="<?php print $slider->ID ?>"><?php print $slider->post_title ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p><strong><?php _e('Slider', 'siteorigin') ?></strong></p>
		<p>
			<select name="<?php print self::input_name('image_size') ?>">
				<option value="large"><?php _e('Large', 'siteorigin') ?></option>
				<option value="medium"><?php _e('Medium', 'siteorigin') ?></option>
				<option value="thumbnail"><?php _e('Thumbnail', 'siteorigin') ?></option>
				<option value="full"><?php _e('Full', 'siteorigin') ?></option>
				<?php foreach ($_wp_additional_image_sizes as $name => $info) : ?>
					<option value="<?php print esc_attr($name) ?>"><?php print esc_html($name) ?></option>
				<?php endforeach ?>
			</select>
		</p>
		<?php
	}

	function save($new_values){
		return $new_values;
	}

	function render($data){
		if($data['slider_id'] == -1){
			_e('Please select a slider.', 'siteorigin');
			return;
		}
		
		$post = get_post($data['slider_id']);
		$slides = get_post_meta($post->ID, 'seven_slider', true);
		
		?><ul class="slides"><?php
		foreach($slides as $slide){
			?>
			<li>
				<?php print wp_get_attachment_image($slide['image'], !empty($data['image_size']) ? $data['image_size'] : 'large'); ?>
			</li>
			<?php
		}
		?></ul><?php
	}

	function get_info(){
		return array(
			'title' => __('Slider', 'siteorigin'),
			'description' => __("Add sliders you've created.", 'siteorigin'),
			//'title_field' => 'headline',
			'group' => 'home',
			'name' => 'slider',
		);
	}
}
so_panels_register_type('home', 'Seven_Panel_Slider');