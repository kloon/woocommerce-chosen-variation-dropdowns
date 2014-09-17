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
				add_action( 'wp_enqueue_scripts', array( $this, 'cv_register_scripts' ) );
				add_filter( 'woocommerce_settings_tabs_array', array( $this, 'cv_add_settings_tab'), 50 );
				add_action( 'woocommerce_settings_tabs_chosen_variations', array( $this, 'cv_settings_tab') );
				add_action( 'woocommerce_update_options_chosen_variations', array( $this, 'cv_update_settings') );
			}

			function cv_add_settings_tab( $settings_tabs ) {
		        $settings_tabs['chosen_variations'] = __( 'Chosen Variations', 'wc-chosen-variations-tab' );
		        return $settings_tabs;
		    }
		    function cv_settings_tab() {
			    woocommerce_admin_fields( self::cv_get_settings() );
			}

			function cv_update_settings() {
			    woocommerce_update_options( self::cv_get_settings() );
			}
			 
			function cv_get_settings() {

				$settings = array(
		            'section_title' => array(
		                'name'     => __( 'Disable Product Chosen Search', 'woocommerce-chosen_variations' ),
		                'type'     => 'title',
		                'desc'     => 'Disable Chosen search field on product variation dropdowns.',
		                'id'       => 'wc_chosen_variations_section_title'
		            ),
		            'title' => array(
		                'name' => __( 'Disable Chosen Search?', 'woocommerce-chosen_variations' ),
		                'id' 		=> 'wc_chosen_variation_search_disabled',
						'default'	=> 'no',
						'type' 		=> 'checkbox',
						'checkboxgroup'		=> 'start'
		            ),
		            'section_end' => array(
		                 'type' => 'sectionend',
		                 'id' => 'wc_chosen_variations_section_end'
		            )
		        );
		 
		        return apply_filters( 'wc_chosen_variations_settings', $settings );
			}

			function cv_register_scripts() {
				if ( apply_filters( 'woocommerce_is_product_chosen_dropdown', is_singular( 'product' ) ) ) {
					global $woocommerce;
					$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
					wp_register_script( 'ajax-chosen', $woocommerce->plugin_url() . '/assets/js/chosen/ajax-chosen.jquery'.$suffix.'.js', array('jquery', 'chosen'), $woocommerce->version );
					wp_register_script( 'chosen', $woocommerce->plugin_url() . '/assets/js/chosen/chosen.jquery'.$suffix.'.js', array('jquery'), $woocommerce->version );
					wp_enqueue_script( 'ajax-chosen' );
					wp_enqueue_script( 'chosen' );
					wp_enqueue_style( 'woocommerce_chosen_styles', $woocommerce->plugin_url() . '/assets/css/chosen.css' );

					// Get options and build options associate array
					if ( get_option( 'wc_chosen_variation_search_disabled' ) == 'yes' ){
						$options = array( 'disable_search' => 'true' );
					} else {
						$options = array( 'disable_search' => 'false' );
					}				

					wp_register_script( 'chosen-variations', plugin_dir_url( __FILE__ ) . "/chosen-variations.js" );
					wp_enqueue_script( 'chosen-variations' );
				    wp_localize_script( 'chosen-variations', 'php_vars', $options );

				}
			}
		}

		$GLOBALS['wc_chosen_variation_dropdowns'] = new WC_Chosen_Variation_Dropdowns();
	}
}
?>