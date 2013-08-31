<?php
global $siteorigin_premium_info;
$theme = basename( get_template_directory() );
?>

<div class="wrap" id="theme-upgrade">
	<form id="theme-upgrade-info" method="post" action="<?php echo esc_url( add_query_arg( 'action', 'enter-order' ) ) ?>">
		<p>
			<?php
			printf(
				__( "After you pay for %s Premium, we'll email you an order number to your <strong>PayPal email address</strong>. ", 'siteorigin' ) ,
				ucfirst( $theme )
			);
			printf(
				__( "Use <a href='%s' target='_blank'>this form</a> if you don't receive your order number in the next 15 minutes. ", 'siteorigin' ) ,
				'http://siteorigin.com/orders/'
			);
			_e( 'To be sure, check your spam folder.', 'siteorigin' );
			?>
		</p>

		<label><strong><?php _e( 'Order Number', 'siteorigin' ) ?></strong></label>
		<input type="text" class="regular-text" name="order_number" />
		<input type="submit" value="<?php esc_attr_e( 'Enable Upgrade', 'siteorigin' ) ?>" />
		<?php wp_nonce_field( 'save_order_number', '_upgrade_nonce' ) ?>
	</form>

	<a href="#" id="theme-upgrade-already-paid" class="button"><?php _e( 'Already Paid?', 'siteorigin' ) ?></a>

	<div id="icon-themes" class="icon32"><br></div>
	<h2><?php echo !empty($siteorigin_premium_info['premium_title']) ? $siteorigin_premium_info['premium_title'] : __('Premium Upgrade', 'siteorigin') ?></h2>

	<?php if( empty($siteorigin_premium_info) ) : ?>

		<p><?php printf(__( "The premium version of this theme is no longer available. If you need any help, please contact <a href='http://siteorigin.com/#support'>SiteOrigin support</a>.", 'siteorigin' ),ucfirst( $theme )); ?></p>

	<?php else:  ?>

		<div class="left-column">

			<?php if( !empty($siteorigin_premium_info['premium_video_poster']) ) : // Only load the video iFrame after the video is clicked ?>
				<div id="video-wrapper" style="background-image: url(<?php echo esc_url($siteorigin_premium_info['premium_video_poster']) ?>)">
					<?php if(!empty($siteorigin_premium_info['premium_video_id'])) : ?>
						<a href="#" id="click-to-play" data-video-id="<?php echo esc_attr($siteorigin_premium_info['premium_video_id']) ?>"></a>
					<?php else : ?>
						<div class="placeholder"></div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<p class="premium-summary">
				<?php echo $siteorigin_premium_info['premium_summary'] ?>
			</p>

			<div id="features">
				<?php foreach ( $siteorigin_premium_info['features'] as $feature ) : ?>
					<div class="feature clearfix">
						<?php if(!empty($feature['image'])) : ?><img src="<?php echo $feature['image'] ?>" /><?php endif; ?>
						<h3><?php echo $feature['heading'] ?></h3>
						<p><?php echo $feature['content'] ?></p>
					</div>
				<?php endforeach; ?>
			</div>

		</div>

		<div class="right-column">
			<p>
				<?php printf( __("We're working on more features for %s Premium, but we need your help.", 'siteorigin'), ucfirst($theme) ) ?>
				<?php printf( __("Every premium purchase will fund new features, helping you get the most out of %s.", 'siteorigin'), ucfirst($theme) ) ?>
			</p>

			<?php if(!empty($siteorigin_premium_info['roadmap'])) : ?>
				<p><?php printf( __("Visit the <a href='%s' target='_blank' class='roadmap'>%s roadmap</a> for progress updates.", 'siteorigin'), esc_url($siteorigin_premium_info['roadmap']), ucfirst($theme) ) ?></p>
			<?php endif; ?>

			<h3>Choose Your Perk</h3>
			<div id="purchase-form">
				<?php foreach($siteorigin_premium_info['rewards'] as $reward) : ?>
					<a class="purchase-option" href="<?php echo esc_url( $siteorigin_premium_info['buy_url'] ) ?>?amount=<?php echo floatval($reward['amount']) ?>" data-amount="<?php echo intval($reward['amount']) ?>">
						<h4><?php printf(__('Pay %s or more', 'siteorigin'), '$'.$reward['amount']) ?></h4>
						<h3><?php echo esc_html($reward['title']) ?></h3>
						<p><?php echo $reward['text'] ?></p>
						<div class="buy-now"><?php _e('Buy', 'siteorigin') ?></div>
					</a>
				<?php endforeach ?>

				<div class="more">
					<p><strong><?php _e('Pay a custom amount.', 'siteorigin') ?></strong> <?php _e("You'll receive a the highest level reward you qualify for.", 'siteorigin') ?></p>
					<form id="custom-price-form" action="<?php echo esc_url( $siteorigin_premium_info['buy_url'] ) ?>" method="get">
						$<input type="number" name="amount" placeholder="5+" min="5" />
						<input type="submit" value="<?php esc_attr_e('Buy Now', 'siteorigin') ?>" />
					</form>
				</div>
			</div>

			<?php if(!empty($siteorigin_premium_info['testimonials'])): ?>
				<h3 class="testimonials-heading"><?php _e('Our User Feedback', 'siteorigin') ?></h3>
				<ul class="testimonials">
					<?php foreach($siteorigin_premium_info['testimonials'] as $testimonial) : ?>
						<li class="testimonial clearfix">
							<div class="avatar" style="background-image: url(http://www.gravatar.com/avatar/<?php echo esc_attr($testimonial['gravatar']) ?>?d=identicon&s=40)"></div>

							<div class="text">
								<div class="name"><?php echo $testimonial['name'] ?></div>
								<div class="content"><?php echo $testimonial['content'] ?></div>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

	<?php endif; ?>

	<div class="clear"></div>
</div>