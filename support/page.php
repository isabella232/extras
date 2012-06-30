<div class="wrap" style="max-width:680px">
	<h2><?php _e('Premium Theme Support', 'siteorigin') ?></h2>
	<?php $theme = wp_get_theme(basename(get_template_directory())) ?>
	<p>
		<?php
		printf(
			__("Please email <a href='%s'>support@siteorigin.com</a> for premium support. ", 'siteorigin'),
			'mailto:support@siteorigin.com?subject='.urlencode($theme->get('Name').' Support [premium]')
		);
		_e("Make sure you include your order number or just mention that you're a premium user so we can prioritize your email.", 'siteorigin')
		?>
	</p>
</div> 