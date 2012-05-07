<?php

function so_share_enqueue(){
	$settings = array_merge($settings, array(
		'permalink' => get_permalink(),
		'title' => get_the_title(),
	));
	wp_enqueue_script('origin-share', get_template_directory_uri() . '/extras/share/js/share.js', array('jquery'));
	wp_localize_script('origin-share', 'share', $settings);
}

function so_share_twitter_username($twitter){
	$twitter = trim($twitter);
	if($twitter[0] == '@') return substr($twitter,1);

	$url = parse_url($twitter);

	// Check if this is a twitter URL
	if(isset($url['host']) && !in_array($url['host'], array('twitter.com', 'www.twitter.com'))) return false;

	// Check if this is a fragment URL
	if(isset($url['fragment']) && $url['fragment'][0] == '!')
		return substr($url['fragment'],2);

	// And our very last attempt... take it that the username is on the end of the path
	if(isset($url['path'])){
		$parts = explode('/', $url['path']);
		$username = array_pop($parts);
		return $username;
	}

	return false;
}