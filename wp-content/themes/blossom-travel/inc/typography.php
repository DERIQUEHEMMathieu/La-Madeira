<?php
/**
 * Blossom Travel Typography Related Functions
 *
 * @package Blossom_Travel
 */

if( ! function_exists( 'blossom_travel_fonts_url' ) ):
/**
 * 
*/ 
function blossom_travel_fonts_url(){
    $fonts_url = '';
    
    $primary_font       = get_theme_mod( 'primary_font', 'Montserrat' );
    $ig_primary_font    = blossom_travel_is_google_font( $primary_font );    
    $secondary_font     = get_theme_mod( 'secondary_font', 'Cormorant Garamond' );
    $ig_secondary_font  = blossom_travel_is_google_font( $secondary_font );    
        
    /* Translators: If there are characters in your language that are not
    * supported by respective fonts, translate this to 'off'. Do not translate
    * into your own language.
    */
    $primary    = _x( 'on', 'Primary Font: on or off', 'blossom-travel' );
    $secondary  = _x( 'on', 'Secondary Font: on or off', 'blossom-travel' );
    
    
    if ( 'off' !== $primary || 'off' !== $secondary ) {
        
        $font_families = array();
     
        if ( 'off' !== $primary && $ig_primary_font ) {
            $primary_variant = blossom_travel_check_varient( $primary_font, 'regular', true );
            if( $primary_variant ){
                $primary_var = ':' . $primary_variant;
            }else{
                $primary_var = '';    
            }            
            $font_families[] = $primary_font . $primary_var;
        }
         
        if ( 'off' !== $secondary && $ig_secondary_font ) {
            $secondary_variant = blossom_travel_check_varient( $secondary_font, 'regular', true );
            if( $secondary_variant ){
                $secondary_var = ':' . $secondary_variant;    
            }else{
                $secondary_var = '';
            }
            $font_families[] = $secondary_font . $secondary_var;
        }
        
        $font_families = array_diff( array_unique( $font_families ), array('') );
        
        $query_args = array(
            'family' => urlencode( implode( '|', $font_families ) ),            
        );
        
        $fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
    }
     
    return esc_url_raw( $fonts_url );
}
endif;

if( ! function_exists( 'blossom_travel_get_google_fonts' ) ) :
/**
 * Get Google Fonts
*/
function blossom_travel_get_google_fonts(){
    $fonts = include wp_normalize_path( get_template_directory() . '/inc/custom-controls/typography/webfonts.php' );
    $google_fonts = array();
    if ( is_array( $fonts ) ) {
		foreach ( $fonts['items'] as $font ) {
            $google_fonts[ $font['family'] ] = array(
				'variants' => $font['variants'],
			);
		}
	}    
    return $google_fonts;
}
endif;

if( ! function_exists( 'blossom_travel_get_websafe_font' ) ) :
/**
 * Function listing WebSafe Fonts and its attributes
*/
function blossom_travel_get_websafe_font(){
    $standard_fonts = array(
		'georgia-serif' => array(
			'variants' => array( 'regular', 'italic', '700', '700italic' ),
			'fonts' => 'Georgia, serif',
		),
        'palatino-serif' => array(
			'variants' => array( 'regular', 'italic', '700', '700italic' ),
			'fonts' => '"Palatino Linotype", "Book Antiqua", Palatino, serif',
		),
        'times-serif' => array(
			'variants' => array( 'regular', 'italic', '700', '700italic' ),
			'fonts' => '"Times New Roman", Times, serif',
		),
        'arial-helvetica' => array(
			'variants' => array( 'regular', 'italic', '700', '700italic' ),
			'fonts' => 'Arial, Helvetica, sans-serif',
		),
        'arial-gadget' => array(
			'variants' => array( 'regular', 'italic', '700', '700italic' ),
			'fonts' => '"Arial Black", Gadget, sans-serif',
		),
		'comic-cursive' => array(
			'variants' => array( 'regular', 'italic', '700', '700italic' ),
			'fonts' => '"Comic Sans MS", cursive, sans-serif',
		),
		'impact-charcoal'  => array(
			'variants' => array( 'regular', 'italic', '700', '700italic' ),
			'fonts' => 'Impact, Charcoal, sans-serif',
		),
        'lucida' => array(
			'variants' => array( 'regular', 'italic', '700', '700italic' ),
			'fonts' => '"Lucida Sans Unicode", "Lucida Grande", sans-serif',
		),
        'tahoma-geneva' => array(
			'variants' => array( 'regular', 'italic', '700', '700italic' ),
			'fonts' => 'Tahoma, Geneva, sans-serif',
		),
		'trebuchet-helvetica' => array(
			'variants' => array( 'regular', 'italic', '700', '700italic' ),
			'fonts' => '"Trebuchet MS", Helvetica, sans-serif',
		),
		'verdana-geneva'  => array(
			'variants' => array( 'regular', 'italic', '700', '700italic' ),
			'fonts' => 'Verdana, Geneva, sans-serif',
		),
        'courier' => array(
			'variants' => array( 'regular', 'italic', '700', '700italic' ),
			'fonts' => '"Courier New", Courier, monospace',
		),
        'lucida-monaco' => array(
			'variants' => array( 'regular', 'italic', '700', '700italic' ),
			'fonts' => '"Lucida Console", Monaco, monospace',
		)
	);
    
    return apply_filters( 'blossom_travel_standard_fonts', $standard_fonts );
}
endif;

if( ! function_exists( 'blossom_travel_check_varient' ) ) :
/**
 * Checks for matched varients in google fonts for typography fields
*/
function blossom_travel_check_varient( $font_family = 'serif', $font_variants = 'regular', $body = false ){
    $variant = '';
    $var     = array();
    $google_fonts  = blossom_travel_get_google_fonts(); //Google Fonts
    $websafe_fonts = blossom_travel_get_websafe_font(); //Standard Web Safe Fonts
    
    if( array_key_exists( $font_family, $google_fonts ) ){
        $variants = $google_fonts[ $font_family ][ 'variants' ];
        if( in_array( $font_variants, $variants ) ){
            if( $body ){ //LOAD ALL VARIANTS FOR BODY FONT
                foreach( $variants as $v ){
                    $var[] = $v;
                }
                $variant = implode( ',', $var );
            }else{                
                $variant = $font_variants;
            }
        }else{
            $variant = 'regular';
        }        
    }else{ //Standard Web Safe Fonts
        if( array_key_exists( $font_family, $websafe_fonts ) ){
            $variants = $websafe_fonts[ $font_family ][ 'variants' ];
            if( in_array( $font_variants, $variants ) ){
                if( $body ){ //LOAD ALL VARIANTS FOR BODY FONT
                    foreach( $variants as $v ){
                        $var[] = $v;
                    }
                    $variant = implode( ',', $var );
                }else{  
                    $variant = $font_variants;
                }
            }else{
                $variant = 'regular';
            }    
        }
    }
    return $variant;
}
endif;

/**
 * Returns font weight and font style to use in dynamic styles.
*/
function blossom_travel_get_css_variant( $font_variant ){
    $v_array = array(
		'100'       => array(
            'weight'    => '100',
            'style'     => 'normal'
            ),
		'100italic' => array(
            'weight'    => '100',
            'style'     => 'italic'
            ),
		'200'       => array(
            'weight'    => '200',
            'style'     => 'normal'
            ),
		'200italic' => array(
            'weight'    => '200',
            'style'     => 'italic'
            ),
		'300'       => array(
            'weight'    => '300',
            'style'     => 'normal'
            ),
		'300italic' => array(
            'weight'    => '300',
            'style'     => 'italic'
            ),
		'regular'   => array(
            'weight'    => '400',
            'style'     => 'normal'
            ),
		'italic'    => array(
            'weight'    => '400',
            'style'     => 'italic'
            ),
		'500'       => array(
            'weight'    => '500',
            'style'     => 'normal'
            ),
		'500italic' => array(
            'weight'    => '500',
            'style'     => 'italic'
            ),
		'600'       => array(
            'weight'    => '600',
            'style'     => 'normal'
            ),
		'600italic' => array(
            'weight'    => '600',
            'style'     => 'italic'
            ),
		'700'       => array(
            'weight'    => '700',
            'style'     => 'normal'
            ),
		'700italic' => array(
            'weight'    => '700',
            'style'     => 'italic'
            ),
		'800'       => array(
            'weight'    => '800',
            'style'     => 'normal'
            ),
		'800italic' => array(
            'weight'    => '800',
            'style'     => 'italic'
            ),
		'900'       => array(
            'weight'    => '900',
            'style'     => 'normal'
            ),
		'900italic' => array(
            'weight'    => '900',
            'style'     => 'italic'
            ),
	);
    
    if( array_key_exists( $font_variant, $v_array ) ){
        return $v_array[ $font_variant ];
    }
}

/**
 * Function to check if it's a google font
*/
function blossom_travel_is_google_font( $font ){
    $return = false;
    $websafe_fonts = blossom_travel_get_websafe_font();
    if( $font ){
        if( array_key_exists( $font, $websafe_fonts ) ){
            //Web Safe Font
            $return = false;
        }else{
            //Google Font
            $return = true;
        }
    }
    return $return; 
}

/**
 * Function to get valid font, weight and style
*/
function blossom_travel_get_fonts( $font_family, $font_variant ){
    
    $fonts = array();
    $websafe_fonts = blossom_travel_get_websafe_font(); //Web Safe Font
    
    if( $font_family ){
        if( blossom_travel_is_google_font( $font_family ) ){
            $fonts['font'] = esc_attr( $font_family ); //Google Font
            if( $font_variant ){
                $weight_style    = blossom_travel_get_css_variant( blossom_travel_check_varient( $font_family, $font_variant ) );
                $fonts['weight'] = $weight_style['weight'];
                $fonts['style']  = $weight_style['style'];
            }else{
                $fonts['weight'] = '400';
                $fonts['style']  = 'normal';
            }
        }else{
            if( array_key_exists( $font_family, $websafe_fonts ) ){
                $fonts['font'] = $websafe_fonts[ $font_family ]['fonts']; //Web Safe Font
                if( $font_variant ){
                    $weight_style    = blossom_travel_get_css_variant( blossom_travel_check_varient( $font_family, $font_variant ) );
                    $fonts['weight'] = $weight_style['weight'];
                    $fonts['style']  = $weight_style['style'];
                }else{
                    $fonts['weight'] = '400';
                    $fonts['style']  = 'normal';
                }
            }
        }   
    }else{
        $fonts['font']   = '"Times New Roman", Times, serif';
        $fonts['weight'] = '400';
        $fonts['style']  = 'normal';
    }
    
    return $fonts;
}

if( ! function_exists( 'blossom_travel_get_all_fonts' ) ) :
/**
 * Return Web safe font and google font without variants
*/
function blossom_travel_get_all_fonts(){
    $google = array();        
    $standard = apply_filters( 'blossom_travel_standard_font', array(
		'georgia-serif'       => __( 'Georgia', 'blossom-travel' ),
        'palatino-serif'      => __( 'Palatino Linotype, Book Antiqua, Palatino', 'blossom-travel' ),
        'times-serif'         => __( 'Times New Roman, Times', 'blossom-travel' ),
        'arial-helvetica'     => __( 'Arial, Helvetica', 'blossom-travel' ),
        'arial-gadget'        => __( 'Arial Black, Gadget', 'blossom-travel' ),
		'comic-cursive'       => __( 'Comic Sans MS, cursive', 'blossom-travel' ),
		'impact-charcoal'     => __( 'Impact, Charcoal', 'blossom-travel' ),
        'lucida'              => __( 'Lucida Sans Unicode, Lucida Grande', 'blossom-travel' ),
        'tahoma-geneva'       => __( 'Tahoma, Geneva', 'blossom-travel' ),
		'trebuchet-helvetica' => __( 'Trebuchet MS, Helvetica', 'blossom-travel' ),
		'verdana-geneva'      => __( 'Verdana, Geneva', 'blossom-travel' ),
        'courier'             => __( 'Courier New, Courier', 'blossom-travel' ),
        'lucida-monaco'       => __( 'Lucida Console, Monaco', 'blossom-travel' ),
	) );
    
    $fonts = include wp_normalize_path( get_template_directory() . '/inc/custom-controls/typography/webfonts.php' );
    
    foreach( $fonts['items'] as $font ){
        $google[$font['family']] = $font['family'];
    }
    $all_fonts = array_merge( $standard, $google );
    return $all_fonts; 
}
endif;