<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://combunity.com
 * @since      1.0.0
 *
 * @package    Combunity_Ashes
 * @subpackage Combunity_Ashes/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Combunity_Ashes
 * @subpackage Combunity_Ashes/admin
 * @author     Your Name <email@example.com>
 */
class Combunity_Ashes_Admin {

	/**
	 * The parent class instance.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      class    $_p    The parent class instance.
	 */
	protected $_p;
	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $Combunity_Ashes    The ID of this plugin.
	 */
	private $Combunity_Ashes;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $Combunity_Ashes       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $Combunity_Ashes, $version, $parent ) {

		$this->Combunity_Ashes = $Combunity_Ashes;
		$this->version = $version;
		$this->_p = $parent;
		$this->_p->loader->add_action( 'admin_menu', $this, 'register_options_page' );
		$this->_p->loader->add_action( 'combunity_load_admin_options_default', $this, 'tab_default_load_options' );
		$this->_p->loader->add_action( 'combunity_load_admin_options_installer', $this, 'tab_installer' );
		$this->_p->loader->add_action( 'combunity_load_admin_options_addons', $this, 'tab_addons' );
		$this->_p->loader->add_action( 'wp_ajax_combunity_install_pages',$this, 'install_pages' );
		$this->_p->loader->add_action( 'admin_notices', $this, 'admin_notice_subscription_form' );
		$this->_p->loader->add_action( 'combunity_load_subscription_form', $this, 'load_subscription_form' );
	}

	/**
	 * Renders the get email subscriptions form
	 */
	public function admin_notice_subscription_form(){
		$screen = get_current_screen();

		$allowed_screens = array('edit-cpost', 'cpost', 'edit-cforum');

		$id = $screen->id;
		if ( !in_array($id, $allowed_screens) )
			return;
		$subscribed = get_option('combunity_notifications_subscribed', false); 
		if ( $subscribed ) 
			return;
		?>
	    <div class="notice notice-success is-dismissible">
	        <?php do_action('combunity_load_subscription_form') ?>
	    </div>
	    <?php
	}

	/**
	 * Print the actual subscription form
	 */
	public function load_subscription_form(){
		?>
		<p><?php _e( 'Would you like to receive email updates of new addons and updates to Combunity? ' ); ?><input type="" name="subscribeemail" id="subscribeemail" placeholder="Your email" >	<button id="combunitysubscribeyes" class="button button-primary">Yes, sign me up</button> <button class="button "  id="combunitysubscribeno">No</button>
		</p>
		<div class="validation"></div>
		<?php
	}

	/**
	 * Install Combunity Pages
	 *
	 * @since    1.0.0
	 */
	public function install_pages(){
		$id = $this->_p->toolbox->create_page(array('title'=>'Post Thread', 'content'=> '[combunity type="submitthread"]'));

		update_option('combunity_adminpage_postthread', $id);

		$id = $this->_p->toolbox->create_page(array('title'=>'Entrance', 'content'=> '[combunity type="entrance"]'));

		update_option('combunity_adminpage_entrance', $id);		

		$data = array(
			"error" => false, "info" => __("Installed pages")
		);
		echo json_encode( $data );
		exit;
	}

	/**
	 * Build Combunity's installer
	 *
	 * @since    1.0.0
	 */
	public function tab_installer(){
		include_once('partials/combunity-ashes-tab-installer.php');
	}

	/**
	 * Build Combunity's Addons tab
	 *
	 * @since    1.0.0
	 */
	public function tab_addons(){
		include_once('partials/combunity-ashes-tab-addons.php');
	}	

	/**
	 * Load Combunity Options
	 *
	 * @since    1.0.0
	 */
	public function tab_default_load_options(){
		include_once( 'combunity-admin-form-helper.php' );
		$options = array();

		$options[] = array(
			'id' 			=> 'site_rules',
			'prefix' 		=> 'combunity_',
			'label'			=> __( 'Site Rules' , 'combunity' ),
			'description'	=> __( 'The Rules shown on the submit post page.', 'combunity' ),
			'type'			=> 'textarea',
			'default'		=> 'Enter your site rules here.',
			'class'			=> 'combunity-textarea',
			'placeholder'	=> __( 'Placeholder text', 'combunity' )
		);

		$options[] = array(
			'id' 			=> 'install_pages',
			'prefix' 		=> 'combunity_',
			'label'			=> __( 'Install pages' , 'combunity' ),
			'description'	=> __( 'Install submit thread and login pages', 'combunity' ),
			'type'			=> 'button',
			'default'		=> 'Enter your site rules here.',
			'class'			=> 'button',
			'placeholder'	=> __( 'Placeholder text', 'combunity' ),
			'nonsubmit' 	=> true
		);

		$action =  ( add_query_arg('', '') );
		$form_actions = array('method'=>'POST', 'action' => $action) ;
		$form = new Combunity_Admin_Form_Helper( $options, $form_actions );
		$form->handle_submit();
		$form->form_start();
		$form->render_form_body();
		$form->submit_button( array() );
		$form->form_end();

		

	}
	/**
	 * Register the Options page for Combunity
	 *
	 * @since    1.0.0
	 */
	public function register_options_page(){
		// add_menu_page( 
		// 	__('Combunity'), __('Combunity 2.0'), 'manage_options', 'combunity_ashes', array($this, 'build_options_page'));
		add_submenu_page( 
			'edit.php?post_type=cpost' ,
			__('Settings'), 
			__('Settings'),
			'manage_options', 
			'combunity_ashes', 
			array($this, 'build_options_page')
		);
	}

	/**
	 * Build the actual options page for Combunity
	 *
	 * @since    1.0.0
	 */
	public function build_options_page(){
		include_once('partials/combunity-ashes-admin-display.php');
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Combunity_Ashes_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Combunity_Ashes_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->Combunity_Ashes, plugin_dir_url( __FILE__ ) . 'css/combunity-ashes-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Combunity_Ashes_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Combunity_Ashes_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->Combunity_Ashes, plugin_dir_url( __FILE__ ) . 'js/combunity-ashes-admin.js', array( 'jquery' ), $this->version, false );

		wp_localize_script( $this->Combunity_Ashes, 
			"combunity",
        	array( 
        		'ajax_url' => admin_url( 'admin-ajax.php' ),
        	) 
        );

		wp_enqueue_script( $this->Combunity_Ashes . '-jscolor', plugin_dir_url( __FILE__ ) . 'js/jscolor.min.js', array( 'jquery' ), $this->version, false );

	}

}
