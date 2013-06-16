<?php
global $siteorigin_premium_info;
$premium = $siteorigin_premium_info;
$theme = basename( get_template_directory() );
?>

<div class="wrap" id="theme-upgrade">
	<form id="theme-upgrade-info" method="post" action="<?php echo esc_url( add_query_arg( 'action', 'enter-order' ) ) ?>">
		<p>
			<?php
			printf(
				__( "After you pay for %s Premium, we'll email you a download link and an order number to your <strong>PayPal email address</strong>. ", 'siteorigin' ) ,
				ucfirst( $theme )
			);
			printf(
				__( "Use <a href='%s' target='_blank'>this form</a> if you don't receive your order number in the next 15 minutes. ", 'siteorigin' ) ,
				'http://siteorigin.com/orders/'
			);
			_e( 'Be sure to check your spam folder.', 'siteorigin' );
			?>
		</p>

		<label><strong><?php _e( 'Order Number', 'siteorigin' ) ?></strong></label>
		<input type="text" class="regular-text" name="order_number" />
		<input type="submit" value="<?php esc_attr_e( 'Enable Upgrade', 'siteorigin' ) ?>" />
		<?php wp_nonce_field( 'save_order_number', '_upgrade_nonce' ) ?>
	</form>

	<a href="#" id="theme-upgrade-already-paid" class="button"><?php _e( 'Already Paid?', 'siteorigin' ) ?></a>
	<h2><?php echo $premium['premium_title'] ?></h2>
	<p><?php echo $premium['premium_summary'] ?></p>

	<a name="buy_information"></a>
	<?php if( empty( $premium['variable_pricing'] ) ) : ?>
		<p class="download">
			<span class="buy-button-wrapper">
				<a href="<?php echo esc_url( $premium['buy_url'] ) ?>" class="buy-button">
					<span><?php _e('Upgrade Now', 'siteorigin') ?></span><em><?php echo '$'.$premium['buy_price'] ?></em>
				</a>
			</span>
			<?php if ( isset( $premium['buy_message_1'] ) ) : ?><span class="info"><?php echo $premium['buy_message_1'] ?></span><?php endif; ?>
		</p>
	<?php else : ?>
		<form method="get" action="<?php echo esc_url( $premium['buy_url'] ) ?>" id="variable-pricing-form" target="_blank">

			<h3><?php _e('You Choose The Price', 'siteorigin') ?></h3>

			<?php if(is_array($premium['variable_pricing'])): ?>
				<div class="options hide-if-no-js">
					<?php foreach($premium['variable_pricing'] as $price) : ?>
						<label><input type="radio" name="variable_pricing_option" value="<?php echo floatval($price[0]) ?>" <?php checked($price[0], $premium['buy_price']) ?>> <strong>$<?php echo floatval($price[0]) ?></strong> <?php echo esc_html($price[1]) ?></label>
					<?php endforeach ?>
					<label><input type="radio" name="variable_pricing_option" value="custom" class="custom-price" > <strong><?php _e('Custom', 'siteorigin') ?></strong> <input type="text" name="variable_pricing_custom" value="" placeholder="$5+"> </label>
				</div>
				<div class="options hide-if-js">
					<p><?php _e('Please enable Javascript to change pricing', 'siteorigin') ?></p>
				</div>
			<?php endif; ?>

			<p class="download">
				<span class="buy-button-wrapper">
					<a href="#buy_information" class="buy-button variable-pricing-submit">
						<span><?php _e('Upgrade Now', 'siteorigin') ?></span><em><?php echo '$'.$premium['buy_price'] ?></em>
					</a>
				</span>
				<?php if ( isset( $premium['buy_message_1'] ) ) : ?><span class="info"><?php echo $premium['buy_message_1'] ?></span><?php endif; ?>
			</p>

			<input type="hidden" name="amount" value="<?php echo esc_attr($premium['buy_price']) ?>" >
		</form>
	<?php endif; ?>

	<?php if ( !empty( $premium['featured'] ) ) : ?>
		<p id="promo-image">
			<img src="<?php echo esc_url( $premium['featured'][ 0 ] ) ?>" width="<?php echo intval( $premium['featured'][ 1 ] ) ?>" height="<?php echo intval( $premium['featured'][ 2 ] ) ?>" class="magnify" />
		</p>
	<?php endif; ?>
	<div class="content">
		<?php if ( !empty( $premium['features'] ) ) : foreach ( $premium['features'] as $feature ) : ?>
			<?php if(!empty($feature['image'])) echo '<div class="feature-image-wrapper"><img src="'.esc_url($feature['image']).'" width="220" height="120" class="feature-image" /></div>' ?>
			<h3><?php echo $feature['heading'] ?></h3>
			<p><?php echo $feature['content'] ?></p>
			<div class="clear"></div>
		<?php endforeach; endif; ?>
	</div>

	<?php if( empty( $premium['variable_pricing'] ) ) : ?>
		<p class="download">
			<span class="buy-button-wrapper">
				<a href="<?php echo esc_url( $premium['buy_url'] ) ?>" class="buy-button">
					<span><?php _e('Upgrade Now', 'siteorigin') ?></span><em><?php echo '$'.$premium['buy_price'] ?></em>
				</a>
			</span>
			<?php if ( isset( $premium['buy_message_2'] ) ) : ?><span class="info"><?php echo $premium['buy_message_2'] ?></span><?php endif; ?>
		</p>
	<?php else : ?>
		<p class="download">
			<span class="buy-button-wrapper">
				<a href="#buy_information" class="buy-button variable-pricing">
					<span><?php _e('Purchase', 'siteorigin') ?></span>
				</a>
			</span>
			<?php if ( isset( $premium['buy_message_2'] ) ) : ?><span class="info"><?php echo $premium['buy_message_2'] ?></span><?php endif; ?>
		</p>
	<?php endif; ?>

	<?php if(!empty($premium['testimonials'])): ?>
		<h3 class="testimonials-heading"><?php _e('Some of our User Comments', 'siteorigin') ?></h3>
		<ul class="testimonials">
			<?php foreach($premium['testimonials'] as $testimonial) : ?>
				<li>
					<div class="avatar" style="background-image: url(http://www.gravatar.com/avatar/<?php echo esc_attr($testimonial['gravatar']) ?>?d=identicon&s=55)"></div>
					<div class="text">
						<div class="content"><?php echo $testimonial['content'] ?></div>
						<div class="name"><?php echo $testimonial['name'] ?></div>
					</div>
					<div class="clear"></div>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

</div>
<div id="magnifier">
	<div class="image"></div>
</div>