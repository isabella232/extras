<?php

define('SO_PAGINATION_SEARCH', 'search');
define('SO_PAGINATION_ITEM', 'item');
define('SO_PAGINATION_SEARCH_SPREAD', 4);



function so_pagination_render($type = self::PAGINATION_SEARCH) {
	global $max_page;
	global $paged;
	global $wp_query;

	if(empty($max_page)) {
		$max_page = $wp_query->max_num_pages;
	}
	if(!$paged) {
		$paged = 1;
	}

	if(empty($max_page)) return;

	if(!file_exists(dirname(__FILE__) . '/tpl/' . $type . '.phtml'))
		return new WP_Error(1, 'Invalid pagination type. Use SO_PAGINATION_* constants.');

	include(dirname(__FILE__) . '/tpl/'.$type.'.phtml');
}

function so_pagination_page_url($page){
	global $wp_rewrite;
	if(!$wp_rewrite->using_permalinks() || is_search()){
		return add_query_arg('paged', $page);
	}
	else{
		if($page > 1) $page_arg = '/page/'.$page.'/';
		else $page_arg = '/';

		$current = $_SERVER['REQUEST_URI'];
		if(preg_match('/\/page\/[0-9]+/', $current)){
			$current = preg_replace('/\/page\/[0-9]+\//', $page_arg, $current);
		}
		elseif($page != 1){
			$current = $current.'page/'.$page.'/';
		}

		return $current;
	}
}