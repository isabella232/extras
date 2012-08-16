<?php

so_panels_register_group('post', array(
	'name' => __('Post', 'siteorigin'),
));

class SO_Panel_Post_Title extends SO_Panel{
	function form(){}
	function save($new_values){}
	
	function render(){
		?><h1 class="entry-title"><?php the_title() ?></h1><?php
	}
	
	function get_info(){
		return array(
			'title' => __('Title', 'siteorigin'),
			'description' => __("The current post's title.", 'siteorigin'),
			'group' => 'post',
		);
	}
}
so_panels_register_type('post', 'SO_Panel_Post_Title');

class SO_Panel_Post_Content extends SO_Panel{
	function form(){}
	function save($new_values){}

	function render(){
		?><div class="entry-content"><?php the_content() ?></div><?php
	}

	function get_info(){
		return array(
			'title' => __('Content', 'siteorigin'),
			'description' => __("The current post's content.", 'siteorigin'),
			'group' => 'post',
		);
	}
}
so_panels_register_type('post', 'SO_Panel_Post_Content');

class SO_Panel_Post_Featured_Image extends SO_Panel{
	function form(){}
	function save($new_values){}

	function render(){
		if(!has_post_thumbnail()) return;
		?><div class="entry-featured-image"><?php the_content() ?></div><?php
	}

	function get_info(){
		return array(
			'title' => __('Featured Image', 'siteorigin'),
			'description' => __("The current post's featured image.", 'siteorigin'),
			'group' => 'post',
		);
	}
}
so_panels_register_type('post', 'SO_Panel_Post_Featured_Image');

class SO_Panel_Post_Comments extends SO_Panel{
	function form(){}
	function save($new_values){}

	function render(){
		if(!has_post_thumbnail()) return;
		?><div class="entry-featured-image"><?php the_content() ?></div><?php
	}

	function get_info(){
		return array(
			'title' => __('Post Comments', 'siteorigin'),
			'description' => __("The current post's comments.", 'siteorigin'),
			'group' => 'post',
		);
	}
}
so_panels_register_type('post', 'SO_Panel_Post_Comments');