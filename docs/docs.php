<?php

/**
 * Display the premium admin menu
 * @return mixed
 */
function so_theme_docs_admin_menu(){
	add_theme_page(__('Theme Documentation', 'siteorigin'), __('Theme Docs', 'siteorigin'), 'switch_themes', 'so_theme_docs', 'so_theme_docs_page_render');
}
add_action('admin_menu', 'so_theme_docs_admin_menu');

function so_theme_docs_enqueue($prefix){
	if($prefix != 'appearance_page_so_theme_docs') return;
	wp_enqueue_style('siteorigin-theme-docs', get_template_directory_uri().'/extras/docs/docs.css', array(), SO_THEME_VERSION);
}
add_action('admin_enqueue_scripts', 'so_theme_docs_enqueue');

/**
 * Render the documentation page
 */
function so_theme_docs_page_render(){
	$theme = basename(get_template_directory());
	if(empty($_GET['current'])) $page = $theme.'/';
	else $page = $_GET['current'];
	
	// Get the document page
	$response = wp_remote_get(SO_THEME_ENDPOINT.'/doc/'.$page.'?format=php');
	$doc = false;
	if(!is_wp_error($response) && !empty($response['body'])){
		$doc = unserialize(urldecode($response['body']));
	}
	
	if(empty($doc)){
		?><div class="wrap" id="siteorigin-theme-docs"><h2><?php _e('Not Found', 'siteorigin') ?></h2></div><?php
		exit();	
	}
	
	?>
	<div class="wrap" id="siteorigin-theme-docs">
		<h2><?php print $doc['post_title'] ?></h2>
		<?php print $doc['post_content'] ?>
		
		<div id="siteorigin-theme-table">
			<?php print $doc['navigation'] ?>
			<ul class="social-follow">
				<strong><?php printf(__('Do you like %s? Follow me for more freebies.', 'siteorigin'), ucfirst($theme)) ?></strong>
				<li class="network facebook">
					<iframe src="//www.facebook.com/plugins/like.php?href=http%3A%2F%2Fwww.facebook.com%2FSiteOrigin&amp;send=false&amp;layout=button_count&amp;width=200&amp;show_faces=true&amp;action=like&amp;colorscheme=light&amp;font&amp;height=21&amp;appId=222225781217824" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:200px; height:21px;" allowTransparency="true"></iframe>
				</li>
				<li class="network twitter">
					<iframe allowtransparency="true" frameborder="0" scrolling="no" src="https://platform.twitter.com/widgets/follow_button.html?screen_name=siteorigin&show_count=false&show_screen_name=true" style="width:122px; height:20px;"></iframe>
				</li>
			</ul>
		</div>
	</div>
	<?php
}