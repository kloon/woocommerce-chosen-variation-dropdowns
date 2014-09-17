<?php
/*
Plugin Name: WooCommerce Chosen Variation Dropdowns
Plugin URI: http://gerhardpotgieter.com/tag/woocommerce-chosen-variations
Version: 0.1
Description: Transform the variation dropdowns on your product pages to Chosen dropdowns.
Author: kloon
Tested up to: 3.6
Author URI: http://gerhardpotgieter.com

	License: GNU General Public License v3.0
	License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

/**
 * Check if WooCommerce is active
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	if ( ! class_exists( 'WC_Chosen_Variation_Dropdowns' ) ) {

		class WC_Chosen_Variation_Dropdowns {

			function __construct() {
				add_filter( 'woocommerce_catalog_settings', array( $this, 'register_settings' ) );
				add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );
			}

			function register_settings( $settings ) {
				$newsettings = array();
				foreach ( $settings as $key => $value ) {
					if ( $key == 6 ) {
						$newsettings[] = array(
							'title'		=> __( 'Disable Product Chosen Search', 'woocommerce' ),
							'desc' 		=> __( 'Disable Chosen search field on product variation dropdowns.', 'woocommerce' ),
							'id' 		=> 'woocommerce_chosen_variation_search_disabled',
							'default'	=> 'no',
							'type' 		=> 'checkbox',
							'checkboxgroup'		=> 'start'
						);
						$newsettings[] = $value;
					} else {
						$newsettings[] = $value;
					}
				}
				return $newsettings;
			}

			function register_scripts() {
				if ( apply_filters( 'woocommerce_is_product_chosen_dropdown', is_singular( 'product' ) ) ) {
					global $woocommerce;
					$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
					wp_register_script( 'ajax-chosen', $woocommerce->plugin_url() . '/assets/js/chosen/ajax-chosen.jquery'.$suffix.'.js', array('jquery', 'chosen'), $woocommerce->version );
					wp_register_script( 'chosen', $woocommerce->plugin_url() . '/assets/js/chosen/chosen.jquery'.$suffix.'.js', array('jquery'), $woocommerce->version );
					wp_enqueue_script( 'ajax-chosen' );
					wp_enqueue_script( 'chosen' );
					wp_enqueue_style( 'woocommerce_chosen_styles', $woocommerce->plugin_url() . '/assets/css/chosen.css' );

					// Get options and build options string
					$options = array();
					$new_options = array();
					$js_options = '';
					if ( get_option( 'woocommerce_chosen_variation_search_disabled' ) == 'yes' )
						$options['disable_search'] = 'true';

					if ( ! empty( $options ) ) {
						foreach ( $options as $key => $value ) {
							$new_options[] = $key . ': ' . $value;
						}
						$js_options = '{' . implode( ',', $new_options ) . '}';
					}

					wp_register_script( 'chosen-variations', plugin_dir_url( __FILE__ ) . "/chosen-variations.js" );
					wp_enqueue_script( 'chosen-variations' );
				    wp_localize_script( 'chosen-variations', 'php_vars', $js_options );

				}
			}
		}

		$GLOBALS['wc_chosen_variation_dropdowns'] = new WC_Chosen_Variation_Dropdowns();
	}
}
?>