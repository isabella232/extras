<?php

if ( class_exists( 'WP_Customize_Control' ) ) :
/**
 * A customizer control for choosing web fonts
 */
class SiteOrigin_Customize_Fonts_Control extends WP_Customize_Control {

	function __construct( $manager, $id, $args = array() ) {
		$google_web_fonts = include get_template_directory() . '/extras/customizer/google-web-fonts.php' ;

		// Let other themes and plugins process the web fonts array
		$google_web_fonts = apply_filters( 'siteorigin_google_web_fonts', $google_web_fonts );

		// Add the default fonts
		$choices = array(
			'Helvetica Neue' => 'Helvetica Neue',
			'Lucida Grande' => 'Lucida Grande',
			'Georgia' => 'Georgia',
			'Courier New' => 'Courier New',
		);

		foreach ( $google_web_fonts as $font => $variants ) {
			foreach ( $variants as $variant ) {
				if ( $variant == 'regular' || $variant == 400 ) {
					$choices[ $font ] = $font;
				}
				else {
					$choices[ $font . ':' . $variant ] = $font . ' (' . $variant . ')';
				}
			}
		}

		$args = wp_parse_args( $args, array(
			'type' => 'select',
			'choices' => $choices,
		) );
		parent::__construct( $manager, $id, $args );
	}

	/**
	 * Render the control. Renders the control wrapper, then calls $this->render_content().
	 */
	protected function render() {
		$id = 'customize-control-' . str_replace( '[', '-', str_replace( ']', '', $this->id ) );
		$class = 'customize-control customize-control-' . $this->type . ' customize-control-font';

		?>
		<li id="<?php echo esc_attr( $id ); ?>" class="<?php echo esc_attr( $class ); ?>">
			<?php $this->render_content(); ?>
		</li>
		<?php
	}
}
endif;


/**
 * Adds web fonts to the given CSS array and adds the web font to the given web fonts array reference.
 *
 * @param string $selector The selector we want to use the web font with.
 * @param string $font The web font.
 * @param array $css The current CSS array.
 * @param array $web_fonts A list of web fonts.
 */
function siteorigin_customize_font_add_web_font( $selector, $font, &$css, &$web_fonts ) {
	if ( empty( $css[ $selector ] ) ) $css[ $selector ] = array();

	if ( strpos( $font, ':' ) !== false ) list( $family, $variant ) = explode( ':', $font, 2 );
	else $family = $font;

	$css[ $selector ][ ] = 'font-family: "' . urldecode( $family ) . '"';

	$web_fonts[ ] = $font;

	if ( !empty( $variant ) ) {
		if ( $variant == 'regular' ) $variant = '400';
		$css[ $selector ][ ] = 'font-weight: ' . $variant;
	}
}