<div class="wrap" id="panels-home-page">
	<div id="icon-index" class="icon32"><br></div>
	<h2><?php esc_html_e('Home Page', 'siteorigin') ?></h2>
	
	<?php if(isset($_POST['_sopanels_home_nonce']) && wp_verify_nonce($_POST['_sopanels_home_nonce'], 'save')) : ?>
		<div id="message" class="updated">
			<p><?php printf('Home page updated. <a href="%s">View page</a>', get_home_url()) ?></p>
		</div>
	<?php endif; ?>
	
	<div id="post-body-wrapper">
		<div id="post-body" class="metabox-holder columns-2">
			<form action="<?php echo add_query_arg('page', 'so_panels_home_page') ?>" method="post">
				<div id="post-body-content">
					<?php wp_editor('', 'content') ?>
					<?php do_meta_boxes('appearance_page_so_panels_home_page', 'advanced', false) ?>
					
					<p>
						<input type="submit" class="button button-primary" value="<?php esc_attr_e('Save Home Page', 'siteorigin') ?>" />
					</p>
				</div>
				
				<?php wp_nonce_field('save', '_sopanels_home_nonce') ?>
			</form>
		</div>
	</div>
</div> 