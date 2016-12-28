<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://combunity.com
 * @since      1.0.0
 *
 * @package    combunity
 */
?>
<?php
	function combunity_is_active( $pre , $check, $active ){
		if ( $pre == $check ){
			echo $active;
		}
	}

	$page = 'default';

	$allowed_pages = array('addons', 'installer');

	if ( isset( $_GET['combunity_apage'] ) && strlen( $_GET['combunity_apage'] ) > 0  ){
		
		$apage = sanitize_text_field( $_GET['combunity_apage'] );

		if ( in_array( $apage, $allowed_pages ) ) {

			$page = $apage;

		}

	}

?>
<style type="text/css">
	.combunity-notice{
		background: #fff;
	    border-left: 4px solid #fff;
	    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
	    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
	    margin: 5px 15px 2px;
	    padding: 1px 12px;
	    margin: 5px 0 15px;
	}
	.combunity-notice-success{
		border-left-color: #46b450;
	}
	.validation {
		color: #4286f4;;
	}
</style>

<div class="wrap about-wrap">
	<h1>Welcome to Combunity 2.0</h1>
		<p class="about-text">Simple. Beautiful Forums for WordPress.</p>
		<?php 
			$subscribed = get_option('combunity_notifications_subscribed', false); 
			if ( !$subscribed ) :
		?>
		<div class="combunity-notice combunity-notice-success">
			<?php do_action('combunity_load_subscription_form') ?>
		</div>
		<?php endif ; ?>
	<h2 class="nav-tab-wrapper wp-clearfix">
		<a href="<?php echo admin_url('edit.php?post_type=cpost&page=combunity_ashes') ?>" class="nav-tab  <?php combunity_is_active($page, 'default', 'nav-tab-active') ?>">
			Settings
		</a>
		<a href="http://support.combunity.com" class="nav-tab">
			Help
		</a>
		<a href="<?php echo admin_url('edit.php?post_type=cpost&page=combunity_ashes&combunity_apage=addons') ?>" class="nav-tab <?php combunity_is_active($page, 'addons', 'nav-tab-active') ?>">
			Addons
		</a>
	</h2>

	<?php

		do_action( 'combunity_load_admin_options_' . $page );

	?>
</div>