<?php

/**
 * This function just gives active widgets a chance to enqueue their scripts
 */
function siteorigin_widgets_enqueue_widget_scripts(){
	global $wp_registered_widgets, $post;
	$active_widgets = array();
	
	if(is_single() && $post->post_type == 'panel'){
		$panel_widget_classes = array();
		$data = get_post_meta($post->ID, 'panels_data', true);
		
		if(!empty($data['widgets'])){
			foreach($data['widgets'] as $widget){
				$panel_widget_classes[] = $widget['info']['class'];
			}
		}
	}
	
	foreach($wp_registered_widgets as $widget){
		if(!empty($widget['callback'][0]) && is_object($widget['callback'][0])){
			if(is_active_widget(false, false, $widget['callback'][0]->id_base)) $active_widgets[] = $widget['callback'][0]->id_base;
			if(!empty($panel_widget_classes) && in_array(get_class($widget['callback'][0]),$panel_widget_classes)) $active_widgets[] = $widget['callback'][0]->id_base;
		}
	}
	
	$active_widgets = array_unique($active_widgets);
	
	foreach($active_widgets as $widget){
		do_action('enqueue_widget_scripts_'.$widget);
	}
}
add_action('wp_enqueue_scripts', 'siteorigin_widgets_enqueue_widget_scripts');

/**
 * A call to action widget. Designed to be used on a home page panel
 */
class SiteOrigin_Widgets_CTA extends WP_Widget {
	function __construct(){
		parent::__construct(
			'call-to-action',
			__('Call To Action', 'siteorigin'),
			array(
				'description' => __('A call to action block, generally for your home page.', 'siteorigin'),
			)
		);
	}

	/**
	 * @param array $args
	 * @param array $instance
	 */
	function widget($args, $instance) {
		
		echo $args['before_widget'];
		if(!empty($instance['headline'])) echo '<h2 class="cta-headline">'.esc_html($instance['headline']).'</h2>';
		if(!empty($instance['text'])) echo '<p class="cta-sub-text">'.esc_html($instance['headline']).'</p>';
		if(!empty($instance['url'])){
			?>
			<a href="<?php echo esc_url($instance['url']) ?>" class="cta-button <?php if(!empty($instance['button_style'])) echo esc_attr('cta-button-'.$instance['button_style']) ?>">
				<span><?php echo esc_html($instance['button']) ?></span>
			</a>
			<?php
		}
		echo $args['after_widget'];
	}

	/**
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array|void
	 */
	function update($new, $old) {
		$new['headline'] = esc_html($new['headline']);
		$new['text'] = esc_html($new['text']);
		$new['button'] = esc_html($new['button']);
		$new['url'] = esc_url_raw($new['url']);
		return $new;
	}

	/**
	 * @param array $instance
	 * @return string|void
	 */
	function form($instance) {
		$instance = wp_parse_args($instance, array(
			'headline' => '',
			'text' => '',
			'button' => '',
			'url' => '',
			'button_style' => false,
		));
		
		/**
		 * This gives themes a chance to add their own button styles
		 */
		$button_styles = apply_filters('siteorigin_button_styles', array());

		?>
		<p><label for="<?php echo $this->get_field_id('headline') ?>"><?php _e('Headline', 'siteorigin') ?></label></p>
		<p><input class="widefat" name="<?php echo $this->get_field_name('headline') ?>" for="<?php echo $this->get_field_id('headline') ?>" value="<?php echo esc_attr($instance['headline']) ?>"></p>
	
		<p><label for="<?php echo $this->get_field_id('text') ?>"><?php _e('Text', 'siteorigin') ?></label></p>
		<p><input class="widefat" name="<?php echo $this->get_field_name('text') ?>" id="<?php echo $this->get_field_id('text') ?>" value="<?php echo esc_attr($instance['text']) ?>"></p>
	
		<p><label for="<?php echo $this->get_field_id('button') ?>"><?php _e('Button Text', 'siteorigin') ?></label></p>
		<p><input class="widefat" name="<?php echo $this->get_field_name('button') ?>" for="<?php echo $this->get_field_id('button') ?>" value="<?php echo esc_attr($instance['button']) ?>"></p>
	
		<p><label for="<?php echo $this->get_field_id('url') ?>"><?php _e('Button URL', 'siteorigin') ?></label></p>
		<p><input class="widefat" name="<?php echo $this->get_field_name('url') ?>" for="<?php echo $this->get_field_id('url') ?>" value="<?php echo esc_attr($instance['url']) ?>"></p>
	
		<?php
		if(!empty($button_styles)){
			?>
			<p><label for="<?php echo $this->get_field_id('button_style') ?>"><?php _e('Button Style', 'siteorigin') ?></label></p>
			<p>
				<select name="<?php echo $this->get_field_name('button_style') ?>" for="<?php echo $this->get_field_id('button_style') ?>">
					<?php foreach($button_styles as $style => $name) : ?>
						<option value="<?php echo esc_attr($style) ?>"><?php echo esc_html($name) ?></option>
					<?php endforeach; ?>
				</select>
			</p>
			<?php
		}
	}
}

/**
 * A call to action widget. Designed to be used on a home page panel
 */
class SiteOrigin_Widgets_Button extends WP_Widget {
	function __construct(){
		parent::__construct(
			'button',
			__('Button', 'siteorigin'),
			array(
				'description' => __('Display a button.', 'siteorigin'),
			)
		);
	}

	/**
	 * @param array $args
	 * @param array $instance
	 */
	function widget($args, $instance) {
		
		$instance = wp_parse_args($instance, array(
			'url' => '#',
			'button' => __('Click Me', 'siteorigin'),
			'align' => 'center',
		));
		
		echo $args['before_widget'];
		echo '<div class="button-container align-'.esc_attr($instance['align']).'">';
		if(!empty($instance['url'])){
			?>
			<a href="<?php echo esc_url($instance['url']) ?>" class="cta-button button <?php if(!empty($instance['button_style'])) echo esc_attr('cta-button-'.$instance['button_style'].' button-'.$instance['button_style']) ?>">
				<span><?php echo esc_html($instance['button']) ?></span>
			</a>
			<?php
		}
		echo '</div>';
		echo $args['after_widget'];
	}

	/**
	 * @param array $new
	 * @param array $old
	 * @return array
	 */
	function update($new, $old) {
		$new['button'] = strip_tags($new['button']);
		$new['url'] = esc_url_raw($new['url']);
		return $new;
	}

	/**
	 * @param array $instance
	 * @return string|void
	 */
	function form($instance) {
		$instance = wp_parse_args($instance, array(
			'button' => '',
			'url' => '',
			'align' => 'center',
			'button_style' => false,
		));

		/**
		 * This gives themes a chance to add their own button styles
		 */
		$button_styles = apply_filters('siteorigin_button_styles', array());

		?>
		<p><label for="<?php echo $this->get_field_id('button') ?>"><?php _e('Button Text', 'siteorigin') ?></label></p>
		<p><input class="widefat" name="<?php echo $this->get_field_name('button') ?>" for="<?php echo $this->get_field_id('button') ?>" value="<?php echo esc_attr($instance['button']) ?>"></p>
	
		<p><label for="<?php echo $this->get_field_id('url') ?>"><?php _e('Button URL', 'siteorigin') ?></label></p>
		<p><input class="widefat" name="<?php echo $this->get_field_name('url') ?>" for="<?php echo $this->get_field_id('url') ?>" value="<?php echo esc_attr($instance['url']) ?>"></p>

		<p><label for="<?php echo $this->get_field_id('align') ?>"><?php _e('Alignment', 'siteorigin') ?></label></p>
		<p>
			<select name="<?php echo $this->get_field_name('align') ?>" id="<?php echo $this->get_field_id('align') ?>">
				<option value="left" <?php selected('left', $instance['align']) ?>><?php esc_html_e('Left', 'siteorigin') ?></option>
				<option value="center" <?php selected('center', $instance['align']) ?>><?php esc_html_e('Center', 'siteorigin') ?></option>
				<option value="right" <?php selected('right', $instance['align']) ?>><?php esc_html_e('Right', 'siteorigin') ?></option>
			</select>
		</p>
			
		<?php
		if(!empty($button_styles)){
			?>
			<p><label for="<?php echo $this->get_field_id('button_style') ?>"><?php _e('Button Style', 'siteorigin') ?></label></p>
			<p>
				<select name="<?php echo $this->get_field_name('button_style') ?>" for="<?php echo $this->get_field_id('button_style') ?>">
					<?php foreach($button_styles as $style => $name) : ?>
					<option value="<?php echo esc_attr($style) ?>"><?php echo esc_html($name) ?></option>
					<?php endforeach; ?>
				</select>
			</p>
			<?php
		}
	}
}

/**
 * A widget that displays some text, a headline and an icon. 
 */
class SiteOrigin_Widgets_IconText extends WP_Widget {
	function __construct(){
		parent::__construct(
			'icon-text',
			__('Icon and Text', 'siteorigin'),
			array(
				'description' => __('A block of text with an icon.', 'siteorigin'),
			)
		);
	}

	/**
	 * @param array $args
	 * @param array $instance
	 */
	function widget($args, $instance) {
		echo $args['before_widget'];
		if(!empty($instance['headline'])){
			echo $args['before_title'].$instance['headline'].$args['after_title'];
		}

		if(!empty($instance['icon'])){
			?><div class="feature-icon"><img src="<?php echo esc_attr(get_template_directory_uri().'/images/feature-icons/'.$instance['icon']) ?>" /></div><?php
		}
		
		if(!empty($instance['text'])){
			?><div class="widget-text entry-content"><?php echo wpautop(do_shortcode($instance['text'])) ?></div><?php
		}

		echo $args['after_widget'];
	}

	/**
	 * @param array $new
	 * @param array $old
	 * @return array|void
	 */
	function update($new, $old) {
		$instance = $new;

		$instance['headline'] = strip_tags($instance['headline']);
		if ( current_user_can('unfiltered_html') )
			$instance['text'] = $instance['text'];
		else
			$instance['text'] = stripslashes( wp_filter_post_kses( addslashes($instance['text']) ) );
		$instance['url'] = esc_url_raw($instance['url']);
		
		return $instance;
	}

	/**
	 * @param array $instance
	 * @return string|void
	 */
	function form($instance) {
		$icons = glob(get_template_directory().'/images/feature-icons/*.png');
		$instance = wp_parse_args($instance, array(
			'headline' => '',
			'text' => '',
			'url' => '',
			'icon' => false,
		))

		?>
		<p><label for="<?php echo $this->get_field_id('headline') ?>"><?php _e('Headline', 'siteorigin') ?></label></p>
		<p><input class="widefat" name="<?php echo $this->get_field_name('headline') ?>" id="<?php echo $this->get_field_id('headline') ?>" value="<?php echo esc_attr($instance['headline']) ?>"></p>
	
		<p><label for="<?php echo $this->get_field_id('text') ?>"><?php _e('Text', 'siteorigin') ?></label></p>
		<p><textarea class="widefat" rows="3" name="<?php echo $this->get_field_name('text') ?>" id="<?php echo $this->get_field_id('headline') ?>"><?php echo esc_textarea($instance['text']) ?></textarea></p>
	
		<p><label for="<?php echo $this->get_field_id('url') ?>"><?php _e('URL', 'siteorigin') ?></label></p>
		<p><input class="widefat" name="<?php echo $this->get_field_name('url') ?>" id="<?php echo $this->get_field_id('url') ?>" value="<?php echo esc_attr($instance['url']) ?>"></p>
	
		<p><label for="<?php echo $this->get_field_id('icon') ?>"><?php _e('Headline', 'siteorigin') ?></label></p>
		<p>
			<select name="<?php echo $this->get_field_name('icon') ?>" id="<?php echo $this->get_field_id('icon') ?>">
				<?php foreach ($icons as $icon) :  ?>
				 <option value="<?php echo esc_attr(basename($icon)) ?>" <?php selected($instance['icon'], $icon) ?>><?php echo esc_html(basename($icon)) ?></option>
				<?php endforeach; ?>
			</select>
		</p>
	
		<?php
	}
}

class SiteOrigin_Widgets_PostList extends WP_Widget{
	function __construct(){
		WP_Widget::__construct(
			'postlist',
			__('Post List', 'siteorigin'),
			array(
				'description' => __('Displays a list of posts.', 'siteorigin'),
			)
		);
	}

	/**
	 * 
	 * 
	 * @param array $args
	 * @param array $instance
	 */
	function widget($args, $instance){
		echo $args['before_widget'];
		if(!empty($instance['headline'])){
			echo $args['before_title'] . $instance['headline'] . $args['after_title'];
		}

		$posts = get_posts(array(
			'numberposts' => $instance['numberposts'],
			'orderby' => $instance['orderby'],
			'order' => $instance['order'],
			'post_type' => $instance['post_type'],
		));

		$thumbnail_size = apply_filters('siteorigin_widgets_postlist_thumbnail_size', 'post-thumbnail');

		?><div class="flexslider-carousel"><ul class="posts slides"><?php

		global $post;
		foreach($posts as $post){
			setup_postdata($post);
			?>
			<li id="post-<?php the_ID() ?>" <?php post_class('summary') ?>>
				<div class="thumbnail">
					<a href="<?php the_permalink() ?>">
						<?php if(has_post_thumbnail()) : the_post_thumbnail($thumbnail_size) ?>
						<?php else : ?>
						<!-- Temporary thumbnail -->
						<?php endif ?>
					</a>
				</div>

				<?php if($instance['show_titles']) : ?>
				<div class="post-info">
					<a href="<?php the_permalink() ?>"><?php the_title() ?></a>
				</div>
				<?php endif; ?>
			</li>
			<?php
		}
		wp_reset_postdata();

		?></ul></div><?php
		
		echo $args['after_widget'];
	}

	/**
	 * @param array $new
	 * @param array $old
	 * @return array
	 */
	function update($new, $old){
		$new['headline'] = esc_html($new['headline']);
		$new['show_titles'] = !empty($new['show_titles']);
		
		return $new;
	}

	/**
	 * @param array $instance
	 * @return string|void
	 */
	function form($instance){
		$types = get_post_types(array('public' => true), 'objects');
		unset($types['attachment']);
		
		$instance = wp_parse_args($instance, array(
			'headline' => '',
			'post_type' => 'post',
			'numberposts' => '5',
			'orderby' => 'post_date',
			'order' => 'DESC',
			'show_titles' => true,
		));

		?>
		<p><label for="<?php echo $this->get_field_id('headline') ?>"><?php _e('Headline', 'siteorigin') ?></label></p>
		<p><input type="text" class="widefat" name="<?php echo $this->get_field_name('headline') ?>" id="<?php echo $this->get_field_id('headline') ?>" value="<?php echo esc_attr($instance['headline']) ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('post_type') ?>"><?php _e('Post Type', 'siteorigin') ?></label></p>
		<p>
			<select name="<?php echo $this->get_field_name('post_type') ?>" id="<?php echo $this->get_field_id('post_type') ?>">
				<?php foreach($types as $name => $o) : ?>
					<option value="<?php echo esc_attr($name) ?>" <?php selected($name, $instance['post_type']) ?>>
						<?php echo esc_html(isset($o->labels->name) ? $o->labels->name : ucfirst($name)) ?>
					</option>
				<?php endforeach ?>
			</select>
		</p>
	
		<p><label for="<?php echo $this->get_field_id('numberposts') ?>"><?php _e('Post Count', 'siteorigin') ?></label></p>
		<p><input type="text" class="small-text" name="<?php echo $this->get_field_name('numberposts') ?>" id="<?php echo $this->get_field_id('numberposts') ?>" value="<?php echo esc_attr(intval($instance['numberposts'])) ?>"/></p>
	
		<p><label><?php _e('Order By', 'siteorigin') ?></label></p>
		<p>
			<select name="<?php echo $this->get_field_name('orderby') ?>">
				<option value="post_date" <?php selected('post_date', $instance['orderby']) ?>><?php esc_html_e('Post Date', 'siteorigin') ?></option>
				<option value="title" <?php selected('title', $instance['orderby']) ?>><?php esc_html_e('Post Title', 'siteorigin') ?></option>
				<option value="menu_order" <?php selected('menu_order', $instance['orderby']) ?>><?php esc_html_e('Menu Order', 'siteorigin') ?></option>
				<option value="rand" <?php selected('rand', $instance['orderby']) ?>><?php esc_html_e('Random', 'siteorigin') ?></option>
			</select>
	
			<select name="<?php echo $this->get_field_name('order') ?>">
				<option value="DESC" <?php selected('DESC', $instance['order']) ?>><?php esc_html_e('Descending', 'siteorigin') ?></option>
				<option value="ASC" <?php selected('ASC', $instance['order']) ?>><?php esc_html_e('Ascending', 'siteorigin') ?></option>
			</select>
		</p>
	
		<p>
			<label>
				<input name="<?php echo $this->get_field_name('show_titles') ?>" type="checkbox" <?php checked($instance['show_titles']) ?>>
				<?php _e('Show Post Title', 'siteorigin') ?>
			</label>
		</p>
	
		<?php
	}
}


/**
 * Simply displays a headline
 */
class SiteOrigin_Widgets_Headline extends WP_Widget {
	function __construct(){
		parent::__construct(
			'headline',
			__('Headline', 'siteorigin'),
			array(
				'description' => __('Displays a simple headline.', 'siteorigin'),
			)
		);
	}

	function widget($args, $instance){
		if(empty($instance['headline'])) return;
		
		echo $args['before_widget'];
		echo $args['before_title'].'<span class="size-'.$instance['size'].' align-'.$instance['align'].'">'.$instance['headline'].'</span>'.$args['after_title'];
		echo $args['after_widget'];
	}
	
	function form($instance){
		$instance = wp_parse_args($instance, array(
			'headline' => '',
			'size' => 'large',
			'align' => 'center'
		));
		
		?>
		<p><label for="<?php echo $this->get_field_id('headline') ?>"><?php _e('Headline Text', 'siteorigin') ?></label></p>
		<p><input type="text" class="widefat" name="<?php echo $this->get_field_name('headline') ?>" id="<?php echo $this->get_field_id('headline') ?>" value="<?php echo esc_attr($instance['headline']) ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('size') ?>"><?php _e('Size', 'siteorigin') ?></label></p>
		<p>
			<select name="<?php echo $this->get_field_name('size') ?>" id="<?php echo $this->get_field_id('size') ?>">
				<option value="small" <?php selected('small', $instance['size']) ?>><?php esc_html_e('Small', 'siteorigin') ?></option>
				<option value="medium" <?php selected('medium', $instance['size']) ?>><?php esc_html_e('Medium', 'siteorigin') ?></option>
				<option value="large" <?php selected('large', $instance['size']) ?>><?php esc_html_e('Large', 'siteorigin') ?></option>
				<option value="extra-large" <?php selected('extra-large', $instance['size']) ?>><?php esc_html_e('Extra Large', 'siteorigin') ?></option>
			</select>
		</p>

		<p><label for="<?php echo $this->get_field_id('align') ?>"><?php _e('Alignment', 'siteorigin') ?></label></p>
		<p>
			<select name="<?php echo $this->get_field_name('align') ?>" id="<?php echo $this->get_field_id('align') ?>">
				<option value="left" <?php selected('left', $instance['align']) ?>><?php esc_html_e('Left', 'siteorigin') ?></option>
				<option value="center" <?php selected('center', $instance['align']) ?>><?php esc_html_e('Center', 'siteorigin') ?></option>
				<option value="right" <?php selected('right', $instance['align']) ?>><?php esc_html_e('Right', 'siteorigin') ?></option>
			</select>
		</p>
		<?php
	}
	
	
}

/**
 * Intialize the SiteOrigin widgets
 */
function siteorigin_widgets_init(){
	register_widget('SiteOrigin_Widgets_CTA');
	register_widget('SiteOrigin_Widgets_Button');
	register_widget('SiteOrigin_Widgets_IconText');
	register_widget('SiteOrigin_Widgets_PostList');
	register_widget('SiteOrigin_Widgets_Headline');
}
add_action('widgets_init', 'siteorigin_widgets_init');