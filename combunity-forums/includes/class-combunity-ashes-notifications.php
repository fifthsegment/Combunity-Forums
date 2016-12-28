<?php

/**
 * Fired during plugin activation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Combunity_Ashes
 * @subpackage Combunity_Ashes/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run the Forums on Combunity.
 *
 * @since      1.0.0
 * @package    Combunity_Ashes
 * @subpackage Combunity_Ashes/includes
 * @author     Abdullah <abdullah@combunity.com>
 */
class Combunity_Ashes_Notifications {
	/**
	 * The parent class instance.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $_p    The parent class instance.
	 */
	protected $_p;

	/**
	 * Register all our hooks, since this runs everytime a plugin is constructed
	 */
	public function __construct( $parent ){
		$this->_p = &$parent;
		$this->_p->loader->add_action('combunity_thread_created', $this, 'send_notification_on_thread_creation' );
		$this->_p->loader->add_action('combunity_reply_created', $this, 
			'send_notification_on_reply_creation' );
		$this->_p->loader->add_action( 'combunity_user_mention', $this, 
			'send_notification_on_user_mention', 10, 3);
		$this->_p->loader->add_action( 'profile_personal_options',$this, 'extra_profile_fields' );
		$this->_p->loader->add_action( 'edit_user_profile', $this, 'extra_profile_fields'  );
		$this->_p->loader->add_action( 'personal_options_update', $this, 'save_extra_user_profile_fields' );
		$this->_p->loader->add_action( 'edit_user_profile_update', $this, 'save_extra_user_profile_fields');
		$this->_p->loader->add_action( 'user_register', $this, 'created_user_add_fields', 10, 1 );

		// add_action('init', array($this, 'send_notification_on_thread_creation') );
		// var_dump( $this->send_notification_on_thread_creation(5031) ); 
	}

	public function created_user_add_fields( $user_id ){
		update_user_meta( $user_id, 'combunity_enable_emails', "on" );
	}

	/**
	 * Loads a template for emails
	 */
	public function get_template( $file ){
		$basedir = $this->_p->basedir;
		return file_get_contents( $basedir . '/emails/'.$file.'.html' );
	}

	/**
	 * Modify content type of wp_mail for our emails
	 */
	public function modify_content_type_of_wpemails(){
		return "text/html";
	}

	/**
	 * Add notification settings to user page
	 */
	public function extra_profile_fields( $user ){
		?>
		<h3><?php _e("Combunity Email notifications", "combunity"); ?></h3>
		<?php
			$commentreply = get_the_author_meta( 'combunity_enable_emails', $user->ID );
		?>
		<table class="form-table">
			<tr>
				<th><label for="combunity_enable_emails"><?php _e("Enabled", "combunity"); ?></label></th>
				<td>
					<input type="checkbox" name="combunity_enable_emails" id="combunity_enable_emails" value="on" <?php checked( $commentreply , "on" ); ?> />
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Save notification settings
	 */
	public function save_extra_user_profile_fields( $user_id ){
		if ( !current_user_can( 'edit_user', $user_id ) ) { return false; }
		$enable = sanitize_text_field( $_POST['combunity_enable_emails'] );
		update_user_meta( $user_id, 'combunity_enable_emails', $enable );
	}

	/**
	 * Sends the actual notification email
	 */
	public function send_email( $email, $subject, $content ){
		$site_title = get_bloginfo ( 'name');

		$subject = "[$site_title] $subject";

		$arr = array(
			"subject" => $subject,
			"content" => $content,
		);

		$arr = apply_filters( "combunity_modify_email_template", $arr );

		$subject = $arr["subject"];

		$content = $arr["content"];
		
		add_filter( 'wp_mail_content_type',array($this, 'modify_content_type_of_wpemails') );
		wp_mail( $email, $subject, $content );
		remove_filter( 'wp_mail_content_type',array($this, 'modify_content_type_of_wpemails') );
	}

	/**
	 * Send notification to a user on mention
	 * user is the WP_User object of the user being mentioned
	 * object type can be post or comment
	 */
	public function send_notification_on_user_mention( $user , $object_type, $object_id ){

		// print 'mnetioned user ';

		// var_dump( $user );

		$template = $this->get_template( 'template' );

		$button = $this->get_template( 'partials/button' );

		$site_title = get_bloginfo ( 'name');

		$unsubscribelink = get_admin_url(null, 'profile.php');

		$email = $user->user_email;

		if ( $object_type == 'post' ){

			ob_start();

			$post = get_post( $object_id );

			$author_id = $post->post_author;

			$author = get_user_by('ID', $author_id);

			$author_username = $author->user_login;

			$thread_title = get_the_title( $object_id );

			$subject = sprintf( __('%s mentioned you in their thread titled : %s' ) , $author_username, 
								$thread_title ) ;

			include(locate_template('template-parts/emails/newmentionthread.php'));


			$body = ob_get_clean();

			$content = str_replace("<%preview%>", $subject , $template );

			$content = str_replace("<%title%>", $subject , $content );

			$content = str_replace("<%content%>", $body , $content );

			$content = str_replace("<%button%>", $button , $content );

			$content = str_replace("<%button%>", $button , $content );

			$content = str_replace("<%unsubscribelink%>", $unsubscribelink, $content );

			$content = str_replace("<%buttontitle%>", sprintf( __('View %s\'s thread'), $author_username) , 
				$content );

			$content = str_replace("<%buttonlink%>", get_permalink($object_id) , $content );


		}else if ( $object_type == 'comment' ){

			ob_start();

			$comment = get_comment( $object_id );

			$author_email = get_comment_author_email( $object_id );

			$author = get_user_by('email', $author_email);

			$author_username = $author->user_login;

			$thread_title = get_the_title( $object_id );

			$subject = sprintf( __('%s mentioned you in their reply' ) , $author_username ) ;

			include(locate_template('template-parts/emails/newmentionreply.php'));

			$body = ob_get_clean();

			$content = str_replace("<%preview%>", $subject , $template );

			$content = str_replace("<%title%>", $subject , $content );

			$content = str_replace("<%content%>", $body , $content );

			$content = str_replace("<%button%>", $button , $content );

			$content = str_replace("<%button%>", $button , $content );

			$content = str_replace("<%unsubscribelink%>", $unsubscribelink, $content );

			$content = str_replace("<%buttontitle%>", sprintf( __('View %s\'s reply'), $author_username) , 
				$content );

			$content = str_replace("<%buttonlink%>", get_comment_link($object_id) , $content );

		}

		$this->send_email( $email, $subject, $content );

	}

	/**
	 * Send a notification to the site admin on new thread creation
	 */
	public function send_notification_on_reply_creation( $comment_id ){

		/**
		 * TODO: Get category moderator and send them an email
		 */

		$comment_object = get_comment( $comment_id );

		$comment_parent = $comment_object->comment_parent;

		$thread_id = $comment_object->comment_post_ID;

		$thread = get_post( $thread_id );

		$author_username = $comment_object->comment_author;

		$author_username = get_user_by( 'email', get_comment_author_email( $comment_id ) )->user_login;

		$thread_title = get_the_title( $thread_id );

		$thread_title_link = '<a href="'.get_permalink($thread_id).'">'.$thread_title.'</a>';

		$thread_author_id  = $thread->post_author;

		$user = get_user_by( 'id', $thread_author_id );

		$author_name = $user->user_login;

		$template = $this->get_template( 'template' );

		$button = $this->get_template( 'partials/button' );

		$site_title = get_bloginfo ( 'name');

		$users = array();

		$users["thread_author"] = $user;

		if ( $comment_parent != 0 ){

			$comment_parent_object = get_comment( $comment_parent );

			$author_email = $comment_parent_object->comment_author_email;

			$users["parent_author"] = get_user_by( "email", $author_email );

		}

		$unsubscribelink = get_admin_url(null, 'profile.php');

		foreach ($users as $key => $user ) {

			$email = $user->user_email;

			if ( !Combunity_Api()->user_emails_enabled( $user->ID ) )
				continue;

			ob_start();

			if ( $key == "thread_author" ){

				$subject = sprintf( __('%s replied to your thread titled : %s' ) , $author_username, 
								$thread_title ) ;

				include(locate_template('template-parts/emails/newreplyonthread.php'));

			}

			if ( $key == "parent_author" ){

				$subject = sprintf( __('%s replied to you on the thread titled : %s' ) , $author_username, 
								$thread_title ) ;

				include(locate_template('template-parts/emails/newreplyonreply.php'));

			}			
			
			$body = ob_get_clean();

			$content = str_replace("<%preview%>", $subject , $template );

			$content = str_replace("<%title%>", $subject , $content );

			$content = str_replace("<%content%>", $body , $content );

			$content = str_replace("<%button%>", $button , $content );

			$content = str_replace("<%button%>", $button , $content );

			$content = str_replace("<%unsubscribelink%>", $unsubscribelink, $content );

			$content = str_replace("<%buttontitle%>", sprintf( __('View %s\'s reply'), $author_username) , 
				$content );

			$content = str_replace("<%buttonlink%>", get_comment_link($comment_object) , $content );

			$this->send_email( $email, $subject, $content );
		}

	}

	/**
	 * Send a notification to the site admin on new thread creation
	 */
	public function send_notification_on_thread_creation( $thread_id ){

		/**
		 * TODO: Get category moderator and send them an email
		 */

		$status = get_post_status( $thread_id );

		if ( $status != 'publish' )
			return;

		$args = array(
			'role' => 'administrator'
		);

		$thread = get_post( $thread_id );

		$thread_title = get_the_title( $thread_id );

		$thread_title_link = '<a href="'.get_permalink($thread_id).'">'.$thread_title.'</a>';

		$thread_author_id  = $thread->post_author;

		$users = get_users( $args );

		$author = get_user_by( 'id', $thread_author_id );

		$author_name = $author->user_login;

		$unsubscribelink = get_admin_url(null, 'profile.php');

		foreach ($users as $user) {

			if ( !Combunity_Api()->user_emails_enabled( $user->ID ) )
				continue;

			$email = $user->user_email;

			$subject_with_link = sprintf( __('A new thread was created by %s titled %s' ) , $author_name, $thread_title_link ) ;

			$subject = sprintf( __('A new thread was created by %s' ) , $author_name ) ;

			$template = $this->get_template( 'template' );

			$button = $this->get_template( 'partials/button' );

			$site_title = get_bloginfo ( 'name');

			ob_start();

			include(locate_template('template-parts/emails/newthread.php'));

			$body = ob_get_clean();

			$content = str_replace("<%preview%>", $subject_with_link , $template );

			$content = str_replace("<%title%>", $subject_with_link , $content );

			$content = str_replace("<%content%>", $body , $content );

			$content = str_replace("<%button%>", $button , $content );

			$content = str_replace("<%button%>", $button , $content );

			$content = str_replace("<%buttontitle%>", __('Click here to view') , $content );

			$content = str_replace("<%unsubscribelink%>", $unsubscribelink, $content );

			$content = str_replace("<%buttonlink%>", get_permalink($thread_id) , $content );

			$this->send_email( $email, $subject, $content );
			# code...
		}
	}
}