<div class="wrap" id="panels-home-page">
	<div id="icon-edit-pages" class="icon32 icon32-posts-page"><br></div>
	<h2><?php esc_html_e('Home Page', 'siteorigin') ?></h2>
	
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