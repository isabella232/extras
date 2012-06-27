<div class="wrap">
	<h2><?php _e('Premium Theme Support', 'siteorigin') ?></h2>
	<?php $theme = wp_get_theme(basename(get_template_directory())) ?>
	<p><?php printf(__('We offer priority email support to everyone who upgrades to <a href="%s">%s Premium</a>.', 'siteorigin'), admin_url('themes.php?page=premium_upgrade'), $theme->get('Name')); ?></p>
	<p>
		<?php
		printf(
			__("You're can contact us with bug reports and pre-sales questions - <a href='%s'>support@siteorigin.com</a>", 'siteorigin'),
			'mailto:support@siteorigin.com?subject='.urlencode($theme->get('Name').' Questions')
		); ?></p>
</div> 