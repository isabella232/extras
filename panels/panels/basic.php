<?php

so_panels_register_group('basic', array(
	'name' => __('Basic', 'siteorigin'),
));

class SO_Panel_Basic_Text extends SO_Panel{
	function form(){
		?>
		<p><strong><?php _e('Headline', 'siteorigin') ?></strong></p>
		<p><input class="widefat" name="<?php print self::input_name('headline') ?>"></p>
	
		<p><strong><?php _e('Text', 'siteorigin') ?></strong></p>
		<p><textarea class="widefat" rows="3" name="<?php print self::input_name('text') ?>"></textarea></p>
	
		<?php
	}

	function save($new_values){

	}

	function render(){

	}

	function get_info(){
		return array(
			'title' => __('Text', 'siteorigin'),
			'description' => null,
			'group' => 'post',
		);
	}
}
so_panels_register_type('basic', 'SO_Panel_Basic_Text');