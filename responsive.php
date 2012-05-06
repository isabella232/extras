<?php
/**
 * Makes widgets responsive and gives you nifty responsive column shortcodes.
 *
 * @author Greg Priday <greg@siteorigin.com>
 * @copyright Copyright (c) 2012, SiteOrigin
 * @license GPL <siteorigin.com/gpl>
 */

/**
 * Filter a sidebar.
 * 
 * @action register_sidebar
 */
function so_responsive_register_sidebar($sidebar){
	global $so_responsive_sidebars;
	
	if(!empty($sidebar['responsive'])){
		if(empty($so_responsive_sidebars)) $so_responsive_sidebars = array();
		
		$sidebar = array_merge(array(
			'grid_responds' => '640=50%&420=1',
			'cell_margin' => 15,
			'cell_padding' => 10,
		), $sidebar);
		$so_responsive_sidebars[$sidebar['id']] = $sidebar;
	}
}
add_action('register_sidebar', 'so_responsive_register_sidebar');

/**
 * Display the CSS styles.
 * 
 * @action wp_print_styles
 */
function siteorigin_responsive_print_styles(){
	// Create the dynamic CSS
	global $_wp_sidebars_widgets, $so_responsive_sidebars;
	if(empty($so_responsive_sidebars)) return;

	$css = array();
	foreach($so_responsive_sidebars as $id => $sidebar){
		$columns = count($_wp_sidebars_widgets[$id]);
		if($columns == 0) continue;

		$responsive_resolutions = array(1920);
		$responsive = array(
			1920 => $columns,
		);

		if(!empty($sidebar['grid_responds'])){
			parse_str($sidebar['grid_responds'], $rs);
			foreach($rs as $res => $scale){
				$responsive_resolutions[] = intval($res);
				if(substr($scale,-1,1) == '%'){
					// Percentage based
					$responsive[$res] = intval(ceil($columns / 100 * intval(substr($scale,0,strlen($scale)-1))));
				}
				else{
					// Integer based
					$responsive[$res] = intval($scale);
				}
			}
		}

		krsort($responsive);
		$responsive_resolutions = array_unique($responsive_resolutions);
		rsort($responsive_resolutions);

		// Last clear stores the last clear that this element had
		$last = array();
		foreach($_wp_sidebars_widgets[$id] as $widget){
			$last[$widget] = array(
				'clear' => 'none',
				'margin-bottom' => 0,
			);
		}

		// Set up all the default stuff
		$css[1920] = array();
		$defaults = implode('; ', array(
			'-webkit-box-sizing: border-box',
			'-moz-box-sizing: border-box',
			'box-sizing: border-box',
			'float: left',
		));
		$css[1920][$defaults] = array(
			trim($sidebar['grid_selector']).' > .widget'
		);

		// And now the stuff that's specific to this grid
		$padding = $sidebar['cell_padding'];
		$css[1920]["padding: 0 {$padding}px"] = array(trim($sidebar['grid_selector']).' > .widget');
		$css[1920]["margin-left: -{$padding}px; margin-right: -{$padding}px;"] = array(trim($sidebar['grid_selector']));


		foreach($responsive_resolutions as $resolution){
			if(!isset($css[$resolution])) $css[$resolution] = array();
			$columns = so_responsive_get_nearest_res_value($responsive, $resolution);

			// Set the width of the column
			$width_rule = "width:". round(100/$columns ,4)  ."%;";
			$css[$resolution][$width_rule][] =  trim($sidebar['grid_selector']).' > .widget';

			if($columns == 1) continue;

			// Reset the clearing
			$css[$res]['clear:none;'][] = trim($sidebar['grid_selector']).' > .widget';

			// Add the clearing
			foreach($_wp_sidebars_widgets[$id] as $i => $widget){
				if($i % $columns == 0){
					if($last[$widget]['clear'] != 'left') {
						if(!isset($css[$resolution]['clear:left;'])) $css[$resolution]['clear:left;'] = array();
						$css[$resolution]['clear:left;'][] = trim($sidebar['grid_selector']).' #'.$widget;
					}
					$last[$widget]['clear'] = 'left';
				}
				else{
					if($last[$widget]['clear'] != 'none') {
						if(!isset($css[$resolution]['clear:none;'])) $css[$resolution]['clear:none;'] = array();
						$css[$resolution]['clear:none;'][] = trim($sidebar['grid_selector']).' #'.$widget;
					}
					$last[$widget]['clear'] = 'none';
				}

				if(floor($i/$columns) == ceil(count($_wp_sidebars_widgets[$id])/$columns)-1){
					if($last[$widget]['margin-bottom'] != 0) {
						if(!isset($css[$resolution]['margin-bottom:0;'])) $css[$resolution]['margin-bottom:0;'] = array();
						$css[$resolution]['margin-bottom:0;'][] = trim($sidebar['grid_selector']).' #'.$widget;
					}
					$last[$widget]['margin-bottom'] = 0;
				}
				else{
					$margin = intval($sidebar['cell_margin']);
					if($last[$widget]['margin-bottom'] != $margin) {
						if(!isset($css[$resolution]["margin-bottom:{$margin}px;"])) $css[$resolution]["margin-bottom:{$margin}px;"] = array();
						$css[$resolution]["margin-bottom:{$margin}px;"][] = trim($sidebar['grid_selector']).' #'.$widget;
					}
					$last[$widget]['margin-bottom'] = $margin;
				}
			}
		}
	}

	// Build the CSS
	$css_text = '';
	krsort($css);
	foreach($css as $res => $def){
		if($res < 1920){
			$css_text .= '@media (max-width:'.$res.'px)';
			$css_text .= '{';
		}

		foreach($def as $property => $selector){
			$selector = array_unique($selector);
			$css_text .= implode(',', $selector).'{'.$property.'}';
		}

		if($res < 1920) $css_text .= '}';
	}

	?><style type="text/css" media="screen"><?php print $css_text ?></style><?php
}
add_action('wp_print_styles', 'siteorigin_responsive_print_styles');

function so_responsive_get_nearest_res_value($input, $res){
	$k = array_keys($input);
	$v = array_values($input);

	foreach($k as $i => $this_res){
		if($res == $this_res) return $v[$i];
		if($res >= $this_res) return $v[$i-1];
	}

	return $v[count($v)-1];
}