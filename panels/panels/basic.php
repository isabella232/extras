<?php

so_panels_register_group('basic', array(
	'name' => __('Basic', 'siteorigin'),
));

/**
 * 
 */
class SO_Panel_Basic_Text extends SO_Panel{
	function form(){
		?>
		<p><strong><?php _e('Headline', 'siteorigin') ?></strong></p>
		<p><input class="widefat" name="<?php echo self::input_name('headline') ?>"></p>
	
		<p><strong><?php _e('Text', 'siteorigin') ?></strong></p>
		<p><textarea class="widefat" rows="3" name="<?php echo self::input_name('text') ?>"></textarea></p>
	
		<?php
	}
	
	function save($new_values){
		return $new_values;
	}

	/**
	 * Render the text panel.
	 * 
	 * @param array $data
	 * @return mixed|void
	 */
	function render($data){
		if($data['headline']) : ?><h3 class="text-panel-heading"><?php echo esc_html($data['headline']) ?></h3><?php endif;
		if(!empty($data['text'])) :
			?>
			<div class="text-panel-text entry-content">
				<?php echo wpautop(do_shortcode($data['text'])) ?>
			</div>
			<?php
		endif;
	}

	/**
	 * @return array Information about the panel
	 */
	function get_info(){
		return array(
			'title' => __('Text', 'siteorigin'),
			'description' => __('A simple text and headline', 'siteorigin'),
			'title_field' => 'headline',
			'group' => 'basic',
			'name' => 'text',
		);
	}
}
so_panels_register_type('basic', 'SO_Panel_Basic_Text');

/**
 * The basic image panel 
 */
class SO_Panel_Basic_Image extends SO_Panel {
	function form(){
		?>
		<p><strong><?php _e('Image URL', 'siteorigin') ?></strong></p>
		<p><input type="text" class="widefat" name="<?php echo self::input_name('image') ?>"></p>
		<?php
	}

	function save($new_values){
		return $new_values;
	}

	/**
	 * Render the text panel.
	 *
	 * @param array $data
	 * @return mixed|void
	 */
	function render($data){
		?><img src="<?php echo esc_url($data['image']) ?>" /> <?php
	}
	
	function get_info(){
		return array(
			'title' => __('Image', 'siteorigin'),
			'description' => __('A simple image', 'siteorigin'),
			'title_field' => 'image',
			'group' => 'basic',
			'name' => 'image',
		);
	}
}
so_panels_register_type('basic', 'SO_Panel_Basic_Image');

/**
 * This panel just displays the contents of a page
 */
class SO_Panel_Basic_Page extends SO_Panel {
	function form(){
		$pages = get_posts(array(
			'numberposts' => -1,
			'post_type' => 'page',
			'post_status' => 'publish'
		));
		
		?>
		<p><strong><?php _e('Page', 'siteorigin') ?></strong></p>
		<p>
			<select name="<?php echo self::input_name('page') ?>">
				<?php foreach ($pages as $page) : ?>
					<option value="<?php echo esc_attr($page->ID) ?>"><?php echo esc_attr($page->post_title) ?></option>
				<?php endforeach ?>
			</select>
		</p>
		<?php
	}
	
	function save($new_values){
		return $new_values;
	}
	
	function render($data){
		$page = get_post($data['page']);
		if(empty($page)) return;
		
		var_dump($page);
	}

	function get_info(){
		return array(
			'title' => __('Page', 'siteorigin'),
			'description' => __('Display contents of a page', 'siteorigin'),
			'title_field' => 'page',
			'group' => 'basic',
			'name' => 'page',
		);
	}
}
so_panels_register_type('basic', 'SO_Panel_Basic_Page');

/**
 * Display a carousel list of posts
 */
class SO_Panel_Basic_Posts extends SO_Panel {
	function form(){
		$types = get_post_types(array('public' => true), 'objects');
		unset($types['attachment']);
		
		?>
		<p><strong><?php _e('Headline', 'siteorigin') ?></strong></p>
		<p>
			<input type="text" class="widefat" name="<?php echo self::input_name('headline') ?>" />
		</p>
			
		<p><strong><?php _e('Post Type', 'siteorigin') ?></strong></p>
		<p>
			<select name="<?php echo self::input_name('post_type') ?>">
				<?php foreach($types as $name => $o) : ?>
					<option value="<?php echo esc_attr($name) ?>"><?php echo esc_html(isset($o->labels->name) ? $o->labels->name : ucfirst($name)) ?></option>
				<?php endforeach ?>
			</select>
		</p>

		<p><strong><?php _e('Post Count', 'siteorigin') ?></strong></p>
		<p><input type="text" class="small-text" name="<?php echo self::input_name('numberposts') ?>" /></p>

		<p><strong><?php _e('Order By', 'siteorigin') ?></strong></p>
		<p>
			<select name="<?php echo self::input_name('orderby') ?>">
				<option value="post_date"><?php esc_html_e('Post Date', 'siteorigin') ?></option>
				<option value="title"><?php esc_html_e('Post Title', 'siteorigin') ?></option>
				<option value="menu_order"><?php esc_html_e('Menu Order', 'siteorigin') ?></option>
				<option value="rand"><?php esc_html_e('Random', 'siteorigin') ?></option>
			</select>

			<select name="<?php echo self::input_name('order') ?>">
				<option value="DESC"><?php esc_html_e('Descending', 'siteorigin') ?></option>
				<option value="ASC"><?php esc_html_e('Ascending', 'siteorigin') ?></option>
			</select>
		</p>

		<p>
			<strong><?php _e('Show Post Title', 'siteorigin') ?></strong>
			<select name="<?php echo self::input_name('show_titles') ?>">
				<option value="yes"><?php esc_html_e('Yes', 'siteorigin') ?></option>
				<option value="no"><?php esc_html_e('No', 'siteorigin') ?></option>
			</select>
		</p>

		<?php
	}
	
	function save($new){
		$new['count'] = isset($new['count']) ? intval($new['count']) : 10;
		return $new;
	}
	
	function render($data){
		$posts = get_posts(array(
			'numberposts' => $data['numberposts'],
			'orderby' => $data['orderby'],
			'order' => $data['order'],
			'post_type' => $data['post_type'],
		));
		
		$thumbnail_size = apply_filters('so_panels_basic_posts_thumbnail_size', 'post-thumbnail');

		if($data['headline']) : ?><h3 class="text-panel-heading posts-panel-heading"><?php echo esc_html($data['headline']) ?></h3><?php endif;
		
		?><div class="flexslider-carousel post-list-layout-horizontal"><ul class="posts slides"><?php
		
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
				
				<?php if($data['show_titles'] == 'yes') : ?>
					<div class="post-info">
						<a href="<?php the_permalink() ?>"><?php the_title() ?></a>
					</div>
				<?php endif; ?>
			</li>
			<?php
		}
		wp_reset_postdata();
		
		?></ul></div><?php
	}
	
	function get_info(){
		return array(
			'title' => __('Post List', 'siteorigin'),
			'description' => __('Display a list of posts', 'siteorigin'),
			'title_field' => 'headline',
			'group' => 'basic',
			'name' => 'posts',
		);
	}
}
so_panels_register_type('basic', 'SO_Panel_Basic_Posts');