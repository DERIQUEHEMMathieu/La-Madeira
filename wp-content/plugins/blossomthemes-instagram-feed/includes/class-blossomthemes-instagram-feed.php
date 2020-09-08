<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://blossomthemes.com
 * @since      1.0.0
 *
 * @package    Blossomthemes_Instagram_Feed
 * @subpackage Blossomthemes_Instagram_Feed/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Blossomthemes_Instagram_Feed
 * @subpackage Blossomthemes_Instagram_Feed/includes
 * @author     blossomthemes <info@blossomthemes.com>
 */
class Blossomthemes_Instagram_Feed {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Blossomthemes_Instagram_Feed_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'BTIF_PLUGIN_VERSION' ) ) {
			$this->version = BTIF_PLUGIN_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'blossomthemes-instagram-feed';
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->init_shortcodes();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Blossomthemes_Instagram_Feed_Loader. Orchestrates the hooks of the plugin.
	 * - Blossomthemes_Instagram_Feed_i18n. Defines internationalization functionality.
	 * - Blossomthemes_Instagram_Feed_Admin. Defines all hooks for the admin area.
	 * - Blossomthemes_Instagram_Feed_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-blossomthemes-instagram-feed-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-blossomthemes-instagram-feed-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-blossomthemes-instagram-feed-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-blossomthemes-instagram-feed-public.php';

		/**
		 * The class responsible for defining all actions in the settings page
		 */
		require_once BTIF_BASE_PATH . '/includes/class-blossomthemes-instagram-feed-settings.php';
		
		/**
		 * The class responsible for defining shortcode for the plugin
		 */
		require_once BTIF_BASE_PATH . '/includes/class-blossomthemes-instagram-feed-shortcodes.php';

		/**
		 * The class responsible for defining all actions in the settings page
		 */
		require_once BTIF_BASE_PATH . '/includes/widgets/widget-blossomthemes-instagram-feed.php';

		/**
		 * The class responsible for defining shortcode for the plugin
		 */
		require_once BTIF_BASE_PATH . '/includes/class-blossomthemes-instagram-feed-api.php';

		/**
		 * The class responsible for handling ajax requests.
		 */
		require_once BTIF_BASE_PATH . '/includes/class-blossomthemes-instagram-feed-ajax.php';

		$this->loader = new Blossomthemes_Instagram_Feed_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Blossomthemes_Instagram_Feed_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Blossomthemes_Instagram_Feed_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Blossomthemes_Instagram_Feed_Admin( $this->get_plugin_name(), $this->get_version() );
		$plugin_ajax  = new Blossomthemes_Instagram_Feed_Ajax( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'blossomthemes_instagram_feed_settings_page' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'blossomthemes_instagram_feed_register_settings' );
		$this->loader->add_action('admin_notices', $plugin_admin, 'blossomthemes_instagram_feed_admin_notice');
		$this->loader->add_action('admin_init', $plugin_admin, 'blossomthemes_instagram_feed_ignore_admin_notice');

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Blossomthemes_Instagram_Feed_Public( $this->get_plugin_name(), $this->get_version() );
		$settings = BlossomThemes_Instagram_Feed_Settings::get_instance();
		$api = Blossomthemes_Instagram_Feed_API::get_instance($settings);
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_filter( 'script_loader_tag', $plugin_public, 'btif_js_defer_files', 10 );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Blossomthemes_Instagram_Feed_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
   	* Init shortcodes.
   	*
   	* @since    1.0.0
   	*/
	public function init_shortcodes(){
	    $plugin_shortcode = new BlossomThemes_Instagram_Feed_Shortcodes();
	    $plugin_shortcode->init();
	}

}
