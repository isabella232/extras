<?php

function siteorigin_metaslider_layout_basic_right($layouts){
	$layouts['basic-right'] = array(
		'title' => __('Basic Right', 'siteorigin'),
	);
	ob_start();

	// The slide HTML
	?>
	<div class="layer" data-width="475" data-height="90" style="width: 475px; height: 90px; top: 120px; left: 10px; position: absolute;" data-top="120" data-left="10">
		<div class="animation_in animated disabled" data-animation="disabled" data-animation-delay="0" style="width: 100%; height: 100%;">
			<div class="animation_out animated disabled" data-animation="disabled" data-animation-delay="0" style="height: 100%; width: 100%;">
				<div class="content_wrap" style="height: 100%;">
					<div class="content" id="layer_content_447077974" data-padding="5" style="color: white; padding: 5px; position: relative;">
						<h2>
							This Theme Is Responsive<br/>
							<strong>To The Very Last Pixel</strong>
						</h2>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php

	$layouts['basic-right']['html'] = ob_get_clean();

	return $layouts;
}
add_filter('siteorigin_metaslider_layouts', 'siteorigin_metaslider_layout_basic_right');