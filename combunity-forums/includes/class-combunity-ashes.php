<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://combunity.com
 * @since      1.0.0
 *
 * @package    Combunity_Ashes
 * @subpackage Combunity_Ashes/includes
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
 * @package    Combunity_Ashes
 * @subpackage Combunity_Ashes/includes
 * @author     Abdullah <abdullah@combunity.com>
 */
class Combunity_Ashes {

	
	/**
	 * The forums instance responsible for all forum related actions
	 * in the plugin.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      Combunity_Ashes_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	public $forums;

	/**
	 * The forum posts instance responsible for all forum posts related actions
	 * in the plugin.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      Combunity_Ashes_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	public $forum_posts;	

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      Combunity_Ashes_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	public $loader;

	/**
	 * The toolbox that's responsible for creating pages and doing common wp tasks of 
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      Combunity_Ashes_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	public $toolbox;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $Combunity_Ashes    The string used to uniquely identify this plugin.
	 */
	protected $Combunity_Ashes;

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
	public function __construct( $version ) {

		$this->Combunity_Ashes = 'combunity-ashes';
		$this->version = $version;

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->basedir = dirname( dirname(__FILE__) );

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Combunity_Ashes_Loader. Orchestrates the hooks of the plugin.
	 * - Combunity_Ashes_i18n. Defines internationalization functionality.
	 * - Combunity_Ashes_Admin. Defines all hooks for the admin area.
	 * - Combunity_Ashes_Public. Defines all hooks for the public side of the site.
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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-combunity-ashes-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-combunity-ashes-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-combunity-ashes-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-combunity-ashes-public.php';

		/**
		 * The class responsible for defining all forum actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-combunity-ashes-forums.php';

		/**
		 * The class responsible for defining all forum post actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-combunity-ashes-forum-posts.php';

		/**
		 * The class responsible for providing an API to themes and other plugins to interface with
		 * combunity.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-combunity-ashes-api.php';
		
		/**
		 * The class responsible for providing aa REST API for updating comments/posts via the frontend in
		 * combunity.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-combunity-ashes-rest-controller.php';

		/**
		 * The common WP Toolbox class that makes simple tasks easier (like creating pages)
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-fseg-wp-toolbox.php';

		/**
		 * The common WP Toolbox class that makes simple tasks easier (like creating pages)
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-combunity-ashes-notifications.php';		

		$this->loader = new Combunity_Ashes_Loader();

		$this->API = new Combunity_Ashes_API( $this );

		$this->toolbox = new Fifthsegment_WP_Toolbox();

		$this->Notifications = new Combunity_Ashes_Notifications( $this );
		
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Combunity_Ashes_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Combunity_Ashes_i18n();

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

		$plugin_admin = new Combunity_Ashes_Admin( $this->get_Combunity_Ashes(), $this->get_version(), $this );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Combunity_Ashes_Public( $this->get_Combunity_Ashes(), $this->get_version() );
		$forums = new Combunity_Ashes_Forums( $this );
		$forum_posts = new Combunity_Ashes_Forum_Posts( $this );
		$rest = new Combunity_Ashes_REST( $this );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		
		$this->forums = &$forums;
		$this->forum_posts = &$forum_posts;
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
	public function get_Combunity_Ashes() {
		return $this->Combunity_Ashes;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Combunity_Ashes_Loader    Orchestrates the hooks of the plugin.
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

}
