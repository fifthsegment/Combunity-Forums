<?php

/**
 * Fired during plugin activation
 *
 * @link       http://combunity.com
 * @since      1.0.0
 *
 * @package    Combunity_Ashes
 * @subpackage Combunity_Ashes/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Combunity_Ashes
 * @subpackage Combunity_Ashes/includes
 * @author     Your Name <email@example.com>
 */
class Combunity_Ashes_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-fseg-wp-toolbox.php';

		$toolbox = new Fifthsegment_WP_Toolbox();

		$id = $toolbox->create_page(array('title'=>'Post Thread', 'content'=> '[combunity type="submitthread"]'));

		update_option('combunity_adminpage_postthread', $id);

		$id = $toolbox->create_page(array('title'=>'Entrance', 'content'=> '[combunity type="entrance"]'));

		update_option('combunity_adminpage_entrance', $id);	



	}

}
