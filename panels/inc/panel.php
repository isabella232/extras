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
 * Register a new panel type
 * 
 * @param string $group The name of the group
 * @param string|object $class Either the name of a class or a panel object
 */
function so_panels_register_type($group, $class){
	global $so_panel_types;
	if(empty($so_panel_types)) $so_panel_types = array();
	if(empty($so_panel_types[$group])) $so_panel_types[$group] = array();

	$so_panel_types[$group][] = $class;
	array_unique($so_panel_types[$group]);
}

/**
 * This function allows for dynamically created panel objects
 * 
 * @param string $group
 * @param string $alias
 * @param object $object
 */
function so_panels_register_panel_object($group, $alias, $object){
	global $so_panel_types, $so_panel_objects;
	if(empty($so_panel_types)) $so_panel_types = array();
	if(empty($so_panel_types[$group])) $so_panel_types[$group] = array();

	$so_panel_types[$group][] = $alias;
	$so_panel_objects[$alias] = $object;
}

/**
 * @param $id
 * @param $info
 */
function so_panels_register_group($id, $info){
	global $so_panel_groups;
	if(empty($so_panel_groups)) $so_panel_groups = array();
	
	$so_panel_groups[$id] = $info;
}

/**
 * Register a layout.
 * 
 * @param $id
 * @param $info
 */
function so_panels_register_grid($id, $info = array()){
	global $so_panel_grids;
	if(empty($so_panel_grids)) $so_panel_grids = array();

	$so_panel_grids[$id] = $info;
}