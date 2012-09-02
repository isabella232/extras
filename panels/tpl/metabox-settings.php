<?php

global $post;

?>

<p>
	<label for="panels-home-checkbox">
		<input type="checkbox" name="panels_home_page" id="panels-home-checkbox" <?php checked($post->ID, get_theme_mod('panels_home_page')) ?>>
		<?php _e('Use as Home Page', 'siteorigin') ?>
	</label>
</p>