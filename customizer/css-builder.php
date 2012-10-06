<?php

/**
 * This is used for building custom CSS.
 */
class SiteOrigin_CSS_Builder {
	private $css;
	private $fonts;

	// These are web safe fonts
	static $web_safe = array(
		'Helvetica Neue' => 'Arial, Helvetica, Geneva, sans-serif',
		'Lucida Grande' => 'Lucida, Verdana, sans-serif',
		'Georgia' => '"Times New Roman", Times, serif',
		'Courier New' => 'Courier, mono',
	);

	function __construct() {
		$this->css = array();
		$this->google_fonts = array();
	}

	/**
	 * Add a CSS value
	 *
	 * @param $selector
	 * @param $property
	 * @param $value
	 */
	function add_css( $selector, $property, $value ) {
		if ( empty( $value ) ) return;

		$selector = preg_replace( '/\s+/m', ' ', $selector );

		if ( $property == 'font' ) {
			if ( strpos( $value, ':' ) !== false ) list( $family, $variant ) = explode( ':', $value, 2 );
			else {
				$family = $value;
				$variant = 400;
			}

			if ( !empty( self::$web_safe[ $family ] ) ) $family = '"' . $family . '", ' . self::$web_safe[ $family ];
			else {
				$this->google_fonts[ ] = array( $family, $variant );
				$family = '"' . $family . '"';
			}

			$this->add_css( $selector, 'font-family', $family );
			if ( $variant != 400 ) $this->add_css( $selector, 'font-weight', $variant );

			return;
		}

		if ( empty( $this->css[ $selector ] ) ) $this->css[ $selector ] = array();

		$this->css[ $selector ][ ] = $property . ': ' . $value;
	}

	/**
	 * Echo all the CSS
	 */
	function print_css() {
		// Start by importing Google web fonts
		echo '<style type="text/css" id="customizer-css">';

		$import = array();
		foreach ( $this->google_fonts as $font ) {
			$import[ ] = urlencode( $font[ 0 ] ) . ':' . $font[ 1 ];
		}
		$import = array_unique( $import );
		if ( !empty( $import ) ) {
			echo '@import url(http://fonts.googleapis.com/css?family=' . implode( '|', $import ) . '); ';
		}

		foreach ( $this->css as $selector => $rules ) {
			echo $selector . ' { ' . implode( '; ', $rules ) . ' } ';
		}
		echo '</style>';
	}
}