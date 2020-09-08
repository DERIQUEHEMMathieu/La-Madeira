<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://blossomthemes.com
 * @since      2.0.0
 *
 * @package    Blossomthemes_Email_Newsletter
 * @subpackage Blossomthemes_Email_Newsletter/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Blossomthemes_Email_Newsletter
 * @subpackage Blossomthemes_Email_Newsletter/public
 * @author     blossomthemes <blossomthemes.com>
 */
class Blossomthemes_Email_Newsletter_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    2.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = BLOSSOMTHEMES_EMAIL_NEWSLETTER_VERSION;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    2.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Blossomthemes_Email_Newsletter_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Blossomthemes_Email_Newsletter_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/blossomthemes-email-newsletter-public.min.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    2.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Blossomthemes_Email_Newsletter_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Blossomthemes_Email_Newsletter_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/blossomthemes-email-newsletter-public.min.js', array( 'jquery' ), $this->version, true );
		
		wp_localize_script(
			$this->plugin_name,
			'bten_ajax_data',
			array( 'ajaxurl' => admin_url('admin-ajax.php') )
		);

		$all = apply_filters('bten_all_enqueue',true);
		if( $all == true )
		{
			wp_enqueue_script( 'all', plugin_dir_url( __FILE__ ) . 'js/all.min.js', array( 'jquery' ), '5.6.3', true );
		}


	}

	function blossom_email_newsletter_js_defer_files($tag)
	{
		$bten_assets = apply_filters('bten_public_assets_enqueue',true);

		if( is_admin() || $bten_assets == true ) return $tag;

		$async_files = apply_filters( 'blossom_email_newsletter_js_defer_files', array( 
	        plugin_dir_url( __FILE__ ) . 'js/blossomthemes-email-newsletter-public.min.js',
	        plugin_dir_url( __FILE__ ) . 'js/all.min.js'
		 ) );
		
		$add_async = false;
		foreach( $async_files as $file ){
			if( strpos( $tag, $file ) !== false ){
				$add_async = true;
				break;
			}
		}

		if( $add_async ) $tag = str_replace( ' src', ' defer="defer" src', $tag );

		return $tag;
	}
}
