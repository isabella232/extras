<?php $theme = wp_get_theme(basename(get_template_directory())) ?>

<div class="wrap">
	<div id="icon-themes" class="icon32"><br></div>
	<h2><?php _e('Theme Settings','siteorigin') ?></h2>
	<p style="margin-bottom: 30px">
		<a href="<?php print esc_attr('http://siteorigin.com/theme/'.$theme->get_template().'/') ?>" target="_blank">
			<?php _e('Download Documentation', 'siteorigin') ?>
		</a>
	</p>

	<form action="options.php" method="post">
		<?php settings_fields('theme_settings'); ?>
		<?php do_settings_sections('theme_settings') ?>

		<p class="submit"><input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Settings', 'siteorigin'); ?>" /></p>
	</form>
</div> 