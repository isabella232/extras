<?php

so_panels_register_group('home', array(
	'name' => __('Home', 'siteorigin'),
));


class SO_Panel_Home_CTA extends SO_Panel{
	function form(){
		/**
		 * This gives themes a chance to add their own button styles 
		 */
		$button_styles = apply_filters('so_panels_home_cta_button_styles', array());
		
		?>
		<p><strong><?php _e('Headline', 'siteorigin') ?></strong></p>
		<p><input class="widefat" name="<?php echo self::input_name('headline') ?>"></p>
	
		<p><strong><?php _e('Text', 'siteorigin') ?></strong></p>
		<p><input class="widefat" name="<?php echo self::input_name('text') ?>"></p>
	
		<p><strong><?php _e('Button Text', 'siteorigin') ?></strong></p>
		<p><input class="widefat" name="<?php echo self::input_name('button') ?>"></p>
	
		<p><strong><?php _e('Button URL', 'siteorigin') ?></strong></p>
		<p><input class="widefat" name="<?php echo self::input_name('url') ?>"></p>
		
		<?php if(!empty($button_styles)) : ?>
			<p><strong><?php _e('Button Style', 'siteorigin') ?></strong></p>
			<p>
				<select name="<?php echo self::input_name('button_style') ?>">
					<?php foreach($button_styles as $style => $name) : ?>
						<option value="<?php echo esc_attr($style) ?>"><?php echo esc_html($name) ?></option>
					<?php endforeach; ?>
				</select>
			</p>
		<?php endif; ?>
	
		<?php
	}

	function save($new_values){
		return $new_values;
	}

	function render($data){
		?>
		<h2 class="cta-headline"><?php echo esc_html($data['headline']) ?></h2>
		<p class="cta-sub-text"><?php echo esc_html($data['text']) ?></p>
	
		<a href="<?php echo esc_url($data['url']) ?>" class="cta-button <?php if(!empty($data['button_style'])) echo esc_attr('cta-button-'.$data['button_style']) ?>">
			<span><?php echo esc_html($data['button']) ?></span>
		</a>
		<?php
	}
	
	function get_info(){
		return array(
			'title' => __('Call To Action', 'siteorigin'),
			'description' => __('Panel with a title, text and button.', 'siteorigin'),
			'title_field' => 'headline',
			'group' => 'home',
			'name' => 'cta',
		);
	}
}
so_panels_register_type('home', 'SO_Panel_Home_CTA');

class SO_Panel_Home_Headline extends SO_Panel{
	function form(){
		?>
		<p><strong><?php _e('Headline', 'siteorigin') ?></strong></p>
		<p><input class="widefat" name="<?php echo self::input_name('headline') ?>"></p>
		<?php
	}

	function save($new_values){
		return $new_values;
	}

	function render($data){
		?>
		<h2><?php echo esc_html($data['headline']) ?></h2>
		<?php
	}

	function get_info(){
		return array(
			'title' => __('Headline', 'siteorigin'),
			'description' => __('Just a Headline.', 'siteorigin'),
			'title_field' => 'headline',
			'group' => 'home',
			'name' => 'headline',
		);
	}
}
so_panels_register_type('home', 'SO_Panel_Home_Headline');

class SO_Panel_Home_Feature extends SO_Panel {
	function form(){
		$icons = glob(get_template_directory().'/images/feature-icons/*.png');
		
		?>
		<p><strong><?php _e('Headline', 'siteorigin') ?></strong></p>
		<p><input class="widefat" name="<?php echo self::input_name('headline') ?>"></p>
	
		<p><strong><?php _e('Text', 'siteorigin') ?></strong></p>
		<p><textarea class="widefat" rows="3" name="<?php echo self::input_name('text') ?>"></textarea></p>

		<p><strong><?php _e('URL', 'siteorigin') ?></strong></p>
		<p><input class="widefat" name="<?php echo self::input_name('url') ?>"></p>

		<p><strong><?php _e('Headline', 'siteorigin') ?></strong></p>
		<p>
			<select name="<?php echo self::input_name('icon') ?>">
				<?php foreach ($icons as $icon) :  ?>
					<option value="<?php echo esc_attr(basename($icon)) ?>"><?php echo esc_html(basename($icon)) ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		
		<?php
	}
	
	function save($new_values){
		return $new_values;
	}

	function render($data){
		?>
		<h3 class="feature-panel-heading"><?php echo esc_html($data['headline']) ?></h3>
		<?php if(!empty($data['icon'])) : ?>
			<div class="feature-icon">
				<img src="<?php echo esc_attr(get_template_directory_uri().'/images/feature-icons/'.$data['icon']) ?>" />
			</div>
		<?php endif; ?>
		
		<?php if(!empty($data['text'])) : ?>
			<div class="feature-panel-text entry-content">
				<?php echo wpautop(do_shortcode($data['text'])) ?>
			</div>
			<?php endif; ?>
		<?php
	}

	function get_info(){
		return array(
			'title' => __('Product/Service Feature', 'siteorigin'),
			'description' => __('A headline, text and icon.', 'siteorigin'),
			'title_field' => 'headline',
			'group' => 'home',
			'name' => 'feature',
		);
	}
}
so_panels_register_type('home', 'SO_Panel_Home_Feature');