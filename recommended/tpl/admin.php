<div class="wrap">
	<?php $theme = wp_get_theme(); ?>
	<div id="icon-plugins" class="icon32"><br></div>
	<h2><?php printf(__('%s Addons', 'siteorigin'), $theme->get('Name')) ?></h2>

	<p class="description">
		<?php printf(__('These are some plugins that you can use to enhance the functionality of %s.', 'siteorigin'), $theme->get('Name')) ?>
		<?php printf(__("They were all built to work perfectly with %s.", 'siteorigin'), $theme->get('Name')) ?>
	</p>

	<?php $recommended = apply_filters('siteorigin_recommended_plugins', array()); ?>
	<ul id="theme-recommended">
		<?php foreach($recommended as $r) : ?>
			<li class="plugin">
				<div class="wrapper">
					<h3><a href="<?php echo esc_url($r['url']) ?>" target="_blank"><?php echo $r['name'] ?></a></h3>
					<div class="description"><?php echo $r['description'] ?></div>
					<a href="<?php echo esc_url($r['url']) ?>" class="thumbnail" target="_blank"><img src="<?php echo $r['image'] ?>" width="320" height="225" /></a>
				</div>
			</li>

		<?php endforeach; ?>
	</ul>
</div>