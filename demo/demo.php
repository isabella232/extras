<?php
/**
 * A demo mode plugin. This shows temporary content until demo mode is disabled.
 *
 * Gives a theme the chance to show off its features and give installation instructions to users.
 *
 * @package SiteOrigin Extras
 */

/**
 * Initialize the demo mode.
 *
 * @param WP_Query $query
 * @action pre_get_posts
 */
function siteorigin_demo_init( $query ) {
	if ( !$query->is_main_query() ) return;
	if ( !get_theme_mod( 'is_demo_mode', true ) ) return;

	$demo_pages = apply_filters( 'siteorigin_demo_pages', array() );
	global $siteorigin_is_demo, $siteorigin_demo_page;

	$siteorigin_is_demo = false;
	$siteorigin_demo_page = null;

	global $wp_query;
	if ( isset( $_GET['demo_page'] ) && isset( $demo_pages[ $_GET['demo_page'] ] ) ) {
		$siteorigin_is_demo = true;
		$siteorigin_demo_page = $demo_pages[ $_GET['demo_page'] ];
		get_template_part( $demo_pages[ $_GET['demo_page'] ] );
		exit();
	} else if ( $wp_query->is_home() && isset( $demo_pages['index'] ) ) {
		$siteorigin_is_demo = true;
		$siteorigin_demo_page = $demo_pages['index'];
		get_template_part( $demo_pages['index'] );
		exit();
	}
}
add_action( 'pre_get_posts', 'siteorigin_demo_init', 1 );


/**
 * Display a footer element.
 *
 * @action wp_footer
 */
function siteorigin_demo_footer() {
	if ( !get_theme_mod( 'is_demo_mode', true ) || !current_user_can( 'edit_theme_options' ) ) return;

	?>
	<div id="so-demo-mode-bar">
		<div id="so-demo-mode-bar-wrapper">
			<?php printf( __( "You're using %s in demo mode. <a href='%s'>Click here</a> to disable demo mode and start building your own site.", 'siteorigin' ), ucfirst( get_option( 'stylesheet' ) ), admin_url( 'themes.php?page=siteorigin_demo_mode_disable' ) ) ?>
		</div>
	</div>
	<?php
}
add_action( 'wp_footer', 'siteorigin_demo_footer' );


/**
 * Filter the title for the demo mode
 *
 * @param $title
 * @param $sep
 * @param $sep_location
 * @return string
 * @filter wp_title
 */
function siteorigin_demo_page_title( $title, $sep, $sep_location ) {
	global $siteorigin_is_demo, $siteorigin_demo_page;
	if ( !$siteorigin_is_demo ) return $title;
	else {
		$titles = apply_filters( 'siteorigin_demo_page_titles', array() );

		if ( isset( $_GET['demo_page'] ) && isset( $titles[ $_GET['demo_page'] ] ) )
			$title = $titles[ $_GET['demo_page'] ] . ' ' . $sep;
	}

	if ( empty( $sep ) ) return $title;
	else return $title . ' ' . get_bloginfo( 'name' );
}
add_filter( 'wp_title', 'siteorigin_demo_page_title', 15, 3 );


/**
 * Initialize the demo mode admin functionality.
 *
 * @action admin_init
 */
function siteorigin_demo_admin_init() {
	if ( !isset( $_POST['_wpdemo_nonce'] ) || !wp_verify_nonce( $_POST['_wpdemo_nonce'], 'save' ) ) return;

	set_theme_mod( 'is_demo_mode', $_POST['siteorigin_demo_disable_confirm'] != 'on' );
	if ( $_POST['siteorigin_demo_disable_confirm'] == 'on' ) {
		header( 'location: ' . admin_url( 'themes.php' ) );
	}

}
add_action( 'admin_init', 'siteorigin_demo_admin_init' );


/**
 * Enqueue scripts for demo mode.
 *
 * @action wp_enqueue_scripts
 */
function siteorigin_demo_enqueue_scripts() {
	if ( !get_theme_mod( 'is_demo_mode', true ) || !current_user_can( 'edit_theme_options' ) ) return;

	wp_enqueue_style( 'so-demo-mode', get_template_directory_uri() . '/extras/demo/bar.css', array(), SITEORIGIN_THEME_VERSION );
}
add_action( 'wp_enqueue_scripts', 'siteorigin_demo_enqueue_scripts' );


/**
 * Add the disable admin menu item to the menu
 *
 * @action admin_menu
 */
function siteorigin_demo_admin_menu() {
	if ( !get_theme_mod( 'is_demo_mode', true ) ) return;

	add_theme_page( __( 'Disable Demo Mode', 'siteorigin' ), __( 'Disable Demo Mode', 'siteorigin' ), 'edit_theme_options', 'siteorigin_demo_mode_disable', 'siteorigin_demo_admin_disable' );
}
add_action( 'admin_menu', 'siteorigin_demo_admin_menu' );


/**
 * Render the "disable demo mode" admin page
 */
function siteorigin_demo_admin_disable() {
	?>
	<div class="wrap">
		<h2><?php _e( 'Disable Demo Mode', 'siteorigin' ) ?></h2>
	
		<form action="<?php echo add_query_arg( 'page', 'siteorigin_demo_mode_disable' ) ?>" method="post">
			<p>
				<label>
					<input type="checkbox" name="siteorigin_demo_disable_confirm" />
					<?php _e( 'Disable', 'siteorigin' ) ?>
				</label>
	
			<div class="description">
				<?php _e( "Check this box to disable demo mode. You can't re-enable the demo mode.", 'siteorigin' ) ?>
			</div>
			</p>
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php esc_attr_e( 'Save Settings', 'siteorigin' ) ?>" />
			</p>
			<?php wp_nonce_field( 'save', '_wpdemo_nonce' ) ?>
		</form>
	</div>
	<?php
}