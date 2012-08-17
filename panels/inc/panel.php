<?php

/**
 * An abstract panel class. Panels that the theme  
 */
abstract class SO_Panel {
	/**
	 * 
	 * @abstract
	 * @return mixed
	 */
	abstract function form();

	/**
	 * Filter the form values
	 * @abstract
	 * @param $new_values
	 * @return mixed
	 */
	abstract function save($new_values);

	/**
	 * Render the panel
	 *
	 * @abstract
	 * @param array $data The data for this panel
	 * @return mixed
	 */
	abstract function render($data);

	/**
	 * Get the panel information
	 * @return array
	 */
	function get_info(){
		return array(
			'title' => __('Untitled Panel Type', 'siteorigin'),
			'description' => null,
			'group' => 'default',
		);
	}
	
	static function input_name($name, $sub = false){
		return esc_attr('panels[{%id}]['.$name.']'.(!empty($sub) ? '['.$sub.']' : ''));
	}
}

/**
 * @param $class
 */
function so_panels_register_type($group, $class){
	global $so_panel_types;
	if(empty($so_panel_types)) $so_panel_types = array();
	if(empty($so_panel_types[$group])) $so_panel_types[$group] = array();

	$so_panel_types[$group][] = $class;
	array_unique($so_panel_types[$group]);
}

function so_panels_register_group($id, $info){
	global $so_panel_groups;
	if(empty($so_panel_groups)) $so_panel_groups = array();
	
	$so_panel_groups[$id] = $info;
}