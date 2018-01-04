<?php
/**
 * Include files and functions in customizer.
 *
 * @package azera-shop
 */

/**
 * Require repeater class.
 *
 * @param WP_Customize_Manager $wp_customize Customizer object.
 */
function azera_shop_repeater_register( $wp_customize ) {
	require_once( get_template_directory() . '/inc/customizer-repeater/class/customizer-repeater-control.php' );

}
add_action( 'customize_register', 'azera_shop_repeater_register' );

if ( ! function_exists( 'azera_shop_sanitize_repeater' ) ) {
	/**
	 * Sanitize repeater field
	 *
	 * @param string $input Control input in json format.
	 */
	function azera_shop_sanitize_repeater( $input ) {
		$input_decoded = json_decode( $input, true );

		if ( ! empty( $input_decoded ) ) {
			foreach ( $input_decoded as $boxk => $box ) {
				foreach ( $box as $key => $value ) {

					$input_decoded[ $boxk ][ $key ] = wp_kses_post( force_balance_tags( $value ) );

				}
			}
			return json_encode( $input_decoded );
		}
		return $input;
	}
}
