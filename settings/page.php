<div class="wrap">
	<div id="icon-options-general" class="icon32"><br></div>
	<?php $theme = wp_get_theme(); ?>
	<h2><?php printf(__( '%s Settings', 'siteorigin' ), $theme->get('Name')) ?></h2>

	<?php if( function_exists('siteorigin_recommended_menu') && current_user_can('activate_plugins') ) : ?>
		<p class="description">
			<?php printf( __('Need more from %1$s? Enhance its functionality with these <a href="%2$s">recommended addons</a>. Created by the same people who created %1$s.'), $theme->get('Name'), admin_url('themes.php?page=siteorigin_recommended_page') ) ?>
		</p>
	<?php endif; ?>
	
	<?php siteorigin_settings_change_message(); ?>
	
	<form action="options.php" method="post">
		<?php settings_fields( 'theme_settings' ); ?>
		<?php do_settings_sections( 'theme_settings' ) ?>

		<p class="submit"><input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Settings', 'siteorigin'); ?>" /></p>
		<input type="hidden" id="current-tab-field" name="theme_settings_current_tab" value="<?php echo intval(get_theme_mod('_theme_settings_current_tab', 0)) ?>" />
	</form>
</div> 