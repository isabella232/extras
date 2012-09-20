<div class="wrap">
	<div id="icon-themes" class="icon32"><br></div>
	<h2><?php _e('Theme Settings','siteorigin') ?></h2>
	
	<?php so_settings_change_message(); ?>
	
	<form action="options.php" method="post">
		<?php settings_fields('theme_settings'); ?>
		<?php do_settings_sections('theme_settings') ?>

		<p class="submit"><input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Settings', 'siteorigin'); ?>" /></p>
		<input type="hidden" id="current-tab-field" name="theme_settings_current_tab" value="<?php echo get_theme_mod('_theme_settings_current_tab', 0) ?>" />
	</form>
</div> 