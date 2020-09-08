<?php
/**
 * Blossom Travel functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Blossom_Travel
 */

//define theme version
$blossom_travel_theme_data = wp_get_theme();
if( ! defined( 'BLOSSOM_TRAVEL_THEME_VERSION' ) ) define( 'BLOSSOM_TRAVEL_THEME_VERSION', $blossom_travel_theme_data->get( 'Version' ) );
if( ! defined( 'BLOSSOM_TRAVEL_THEME_NAME' ) ) define( 'BLOSSOM_TRAVEL_THEME_NAME', $blossom_travel_theme_data->get( 'Name' ) );

/**
 * Custom Functions.
 */
require get_template_directory() . '/inc/custom-functions.php';

/**
 * Standalone Functions.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Template Functions.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Custom functions for selective refresh.
 */
require get_template_directory() . '/inc/partials.php';

/**
 * Custom Controls
 */
require get_template_directory() . '/inc/custom-controls/custom-control.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer/customizer.php';

/**
 * Widgets
 */
require get_template_directory() . '/inc/widgets.php';

/**
 * Metabox
 */
require get_template_directory() . '/inc/metabox.php';

/**
 * Typography Functions
 */
require get_template_directory() . '/inc/typography.php';

/**
 * Dynamic Styles
 */
require get_template_directory() . '/css/style.php';

/**
 * Plugin Recommendation
*/
require get_template_directory() . '/inc/tgmpa/recommended-plugins.php';

/**
 * Getting Started
*/
require get_template_directory() . '/inc/getting-started/getting-started.php';

/**
 * Toolkit Filters
*/
if( blossom_travel_is_bttk_activated() ) {
	require get_template_directory() . '/inc/toolkit-functions.php';
}

/**
 * Add theme compatibility function for woocommerce if active
*/
if( blossom_travel_is_woocommerce_activated() ){
    require get_template_directory() . '/inc/woocommerce-functions.php';    
}