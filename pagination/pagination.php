<?php

class OriginPaginationController extends Origin_Controller {
	const PAGINATION_SEARCH = 'search';
	const PAGINATION_ITEM = 'item';
	
	const PAGINATION_SEARCH_SPREAD = 4;
	
	static function single(){
		return parent::single(__CLASS__);
	} 
	
	/**
	 * @static
	 * 
	 * @param string $type
	 * 
	 * @url http://developer.yahoo.com/ypatterns/navigation/pagination/item.html
	 * @url http://developer.yahoo.com/ypatterns/navigation/pagination/search.html
	 * 
	 * @throws Exception
	 * @return mixed
	 */
	static public function render($type = self::PAGINATION_SEARCH) {
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
			throw new Exception('Invalid pagination type. Use PAGINATION_* constants.');
		
		include(dirname(__FILE__) . '/tpl/'.$type.'.phtml');
	}
	
	/**
	 * Get the URL for a page
	 * 
	 * @static
	 * @param $page
	 * @return string
	 */
	static function page_url($page){
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
}

OriginPaginationController::single();