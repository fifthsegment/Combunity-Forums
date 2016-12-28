<?php

/**
 * Combunity's bootstrap file
 *
 * @link              http://combunity.com
 * @since             1.0.0
 * @package           combunity_ashes
 *
 * @wordpress-plugin
 * Plugin Name:       Combunity Forums
 * Plugin URI:        http://combunity.com/
 * Description:       Combunity allows you to build powerful online social communities right on top of WordPress.  You can use Combunity to build the next Reddit, StackOverflow, Voat.co, a support Forum or simply a community of like minded people. Combunity is extremely light weight, seo friendly, supports social sharing, loads almost instantly and looks beautiful.
 * Version:           2.0.17
 * Author:            Combunity
 * Author URI:        http://combunity.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       combunity-ashes
 * Domain Path:       /languages
 */

$COMBUNITY_VERSION = '2.0.17';

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}



/**
 * 
 */
function combunity_detect_plugin_activation( $plugin ){
	if ( $plugin == "combunity-forums/combunity-ashes.php"  ){
	    // $this->utils->log("Combunity Plugin", "Activated Plugin");
	    $path = "edit.php?post_type=cpost&page=combunity_ashes&combunity_apage=installer";
		$location = admin_url( $path );
		wp_redirect( $location);
		exit;
	}
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-combunity-ashes-activator.php
 */
function activate_combunity_ashes() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-combunity-ashes-activator.php';
	combunity_ashes_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-combunity-ashes-deactivator.php
 */
function deactivate_combunity_ashes() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-combunity-ashes-deactivator.php';
	combunity_ashes_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_combunity_ashes' );
register_deactivation_hook( __FILE__, 'deactivate_combunity_ashes' );
add_action( 'activated_plugin',  'combunity_detect_plugin_activation' , 10, 2 );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-combunity-ashes.php';

$Combunity_Runtime = null;
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_combunity_ashes() {
	global $COMBUNITY_VERSION;
	global $Combunity_Runtime;
	$plugin = new combunity_ashes($COMBUNITY_VERSION);
	$plugin->run();
	$Combunity_Runtime = $plugin;
}
run_combunity_ashes();

function Combunity_Api(){
	global $Combunity_Runtime;
	return $Combunity_Runtime->API;
}
