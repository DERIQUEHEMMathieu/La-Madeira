<?php 
/**
* Blossom Travel Metabox for Sidebar Layout
*
* @package Blossom_Travel
*
*/ 

function blossom_travel_add_sidebar_layout_box(){
    $post_id   = isset( $_GET['post'] ) ? $_GET['post'] : '';
    $shop_id   = get_option( 'woocommerce_shop_page_id' );
    $template  = get_post_meta( $post_id, '_wp_page_template', true );
    $templates = array( 'templates/blossom-portfolio.php' );
    
    //for post
    add_meta_box( 
        'blossom_travel_sidebar_layout',
        __( 'Sidebar Layout', 'blossom-travel' ),
        'blossom_travel_sidebar_layout_callback', 
        'post',
        'normal',
        'high'
    );

    if( ! in_array( $template, $templates ) && ( $shop_id != $post_id ) ){
        add_meta_box( 
            'blossom_travel_sidebar_layout',
            __( 'Sidebar Layout', 'blossom-travel' ),
            'blossom_travel_sidebar_layout_callback', 
            'page',
            'normal',
            'high'
        );
    }
}
add_action( 'add_meta_boxes', 'blossom_travel_add_sidebar_layout_box' );

$blossom_travel_sidebar_layout = array(    
    'default-sidebar'=> array(
    	 'value'     => 'default-sidebar',
    	 'label'     => __( 'Default Sidebar', 'blossom-travel' ),
    	 'thumbnail' => esc_url( get_template_directory_uri() . '/images/default-sidebar.png' ),
   	),
    'no-sidebar'     => array(
    	 'value'     => 'no-sidebar',
    	 'label'     => __( 'Full Width', 'blossom-travel' ),
    	 'thumbnail' => esc_url( get_template_directory_uri() . '/images/full-width.png' ),
   	),
    'centered'     => array(
    	 'value'     => 'centered',
    	 'label'     => __( 'Full Width Centered', 'blossom-travel' ),
    	 'thumbnail' => esc_url( get_template_directory_uri() . '/images/full-width-centered.png' ),
   	),    
    'left-sidebar' => array(
         'value'     => 'left-sidebar',
    	 'label'     => __( 'Left Sidebar', 'blossom-travel' ),
    	 'thumbnail' => esc_url( get_template_directory_uri() . '/images/left-sidebar.png' ),         
    ),
    'right-sidebar' => array(
         'value'     => 'right-sidebar',
    	 'label'     => __( 'Right Sidebar', 'blossom-travel' ),
    	 'thumbnail' => esc_url( get_template_directory_uri() . '/images/right-sidebar.png' ),         
     )    
);

function blossom_travel_sidebar_layout_callback(){
    global $post , $blossom_travel_sidebar_layout;
    wp_nonce_field( basename( __FILE__ ), 'blossom_travel_nonce' ); ?> 
    <table class="form-table">
        <tr>
            <td colspan="4"><em class="f13"><?php esc_html_e( 'Choose Sidebar Template', 'blossom-travel' ); ?></em></td>
        </tr>
        <tr>
            <td>
                <?php  
                    foreach( $blossom_travel_sidebar_layout as $field ){  
                        $layout = get_post_meta( $post->ID, '_blossom_travel_sidebar_layout', true ); ?>
                        <div class="hide-radio radio-image-wrapper" style="float:left; margin-right:30px;">
                            <input id="<?php echo esc_attr( $field['value'] ); ?>" type="radio" name="blossom_travel_sidebar_layout" value="<?php echo esc_attr( $field['value'] ); ?>" <?php checked( $field['value'], $layout ); if( empty( $layout ) ){ checked( $field['value'], 'default-sidebar' ); }?>/>
                            <label class="description" for="<?php echo esc_attr( $field['value'] ); ?>">
                                <img src="<?php echo esc_url( $field['thumbnail'] ); ?>" alt="<?php echo esc_attr( $field['label'] ); ?>" />
                            </label>
                        </div>
                        <?php 
                    } // end foreach 
                ?>
                <div class="clear"></div>
            </td>
        </tr>
    </table> 
<?php 
}

function blossom_travel_save_sidebar_layout( $post_id ){
    global $blossom_travel_sidebar_layout , $post;

    // Verify the nonce before proceeding.
    if ( !isset( $_POST[ 'blossom_travel_nonce' ] ) || !wp_verify_nonce( $_POST[ 'blossom_travel_nonce' ], basename( __FILE__ ) ) )
        return;
    
    // Stop WP from clearing custom fields on autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE)  
        return;

    if ('page' == $_POST['post_type']) {  
        if (!current_user_can( 'edit_page', $post_id ) )  return $post_id;  
    } elseif (!current_user_can( 'edit_post', $post_id ) ) {  
        return $post_id;  
    }  

    $layout = isset( $_POST['blossom_travel_sidebar_layout'] ) ? sanitize_key( $_POST['blossom_travel_sidebar_layout'] ) : 'default-sidebar';

    if ( array_key_exists( $layout, $blossom_travel_sidebar_layout ) ) {
        update_post_meta( $post_id, '_blossom_travel_sidebar_layout', $layout );
    } else {
        delete_post_meta( $post_id, '_blossom_travel_sidebar_layout' );
    } 
}
add_action( 'save_post' , 'blossom_travel_save_sidebar_layout' );