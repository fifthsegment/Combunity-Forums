<?php

/**
 * The file that defines a sort of simple REST API for updating comments/posts
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://combunity.com
 * @since      1.0.0
 *
 * @package    Combunity_Ashes
 * @subpackage Combunity_Ashes_REST/includes
 */


class Combunity_Ashes_REST {
	/**
	 * The parent class instance.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $_p    The parent class instance.
	 */
	protected $_p;

	/**
	 * Constructing the REST Controller.
	 *
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function __construct( $parent ){

		$this->_p  = $parent;
		$this->_p->loader->add_action( 'wp_ajax_combunity_get_comment',$this, 'get_comment_text' );
		$this->_p->loader->add_action( 'wp_ajax_combunity_post_comment',$this, 'post_comment_text' );
		$this->_p->loader->add_action( 'wp_ajax_combunity_post_comment_vote',$this, 'register_comment_vote' );
		$this->_p->loader->add_action( 'wp_ajax_combunity_get_delete_thread',$this, 'get_delete_thread' );
		$this->_p->loader->add_action('wp_ajax_combunity_admin_subscribe', $this, 'ajax_admin_subscribe' );

		/**
		 * Get form structures to be rendered by the js frontend
		 * These are all basically just edit forms 
		 */

		$this->_p->loader->add_action( 'wp_ajax_combunity_get_comment_form_meta', $this, 
			'get_comment_form_meta' );
		$this->_p->loader->add_action( 'wp_ajax_combunity_get_forum_posts_form_meta', $this, 
			'get_forum_posts_form_meta' );
		$this->_p->loader->add_action( 'wp_ajax_combunity_forum_posts_post',$this, 'post_forum_posts' );
		$this->_p->loader->add_action( 'wp_ajax_combunity_get_edit_thread_form_meta',$this, 'get_edit_thread_form_meta' );
		$this->_p->loader->add_action( 'wp_ajax_combunity_post_thread',$this, 'post_thread' );

		/**
		 * Login ajax handlers
		 *  
		 */
		$this->_p->loader->add_action( 'wp_ajax_nopriv_combunity_auth_login',$this, 'auth_login' );
		$this->_p->loader->add_action( 'wp_ajax_nopriv_combunity_auth_signup',$this, 'auth_signup' );
		


		/**
		 * Get all the pages/parts
		 */

		$this->_p->loader->add_action( 'wp_ajax_combunity_get_loginsignup', $this, 'get_loginsignup_modal');

	}

	/**
	 * Performs user subscriptions
	 *
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function ajax_admin_subscribe(){
		$subscribed = get_option('combunity_notifications_subscribed', false);
		/**
		 * Check if no is set
		 */
		$data = array(
			"error" => false, 
			"info" => __("You need to be logged in first.")
		);
		if ( isset( $_POST["data"][ "no" ] ) && $_POST["data"][ "no" ] == "true" ){
			
			$data["info"] = __("");

			// update_option('combunity_notifications_subscribed', true );
		}else{
			if ( isset( $_POST["data"][ "email" ] ) ){
				/**
				 * Validate email address
				 */
				$email = $_POST["data"][ "email" ];

				if ( !is_email( $email ) ){

					$data["info"] = sprintf( __('%s is not valid'), $email ) ;

				}else{

					$response = wp_remote_get('http://updatesv2.combunity.com/mailinglist/combunity.php?email='. $email );

					update_option('combunity_notifications_subscribed', true ) ;

					$data["info"] = sprintf( __('%s subscribed!'), $email ) ;

				}
				
			}
		}
		echo json_encode( $data );
		exit;
	}

	/**
	 * Performs basic checks before a GET request is handled.
	 *
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function pre_get_request( $idcheck = true ){
		if ( !is_user_logged_in() ){
			$data = array(
				"error" => true, "info" => __("You need to be logged in first.")
			);
			echo json_encode( $data );
			exit;
		}

		if ( !$idcheck ){
			return;
		}

		if ( !isset( $_POST["data"] ) || !isset( $_POST["data"]["id"] ) ){
			$data = array(
				"error" => true, "info" => __("You need to pass in a an id.")
			);
			echo json_encode( $data );
			exit;
		}
	}

	/**
	 * Prevents non authors to update comments.
	 *
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function comment_protection( $comment_ID ){
		$author_email = get_comment_author_email( $comment_ID );
		$user = wp_get_current_user();
		$data = array(
			"error" => true, "info" => __("You need to be the author of a reply to modify it.")
		);
		$comment_status = wp_get_comment_status( $comment_ID );
		/**
		 * Checks and quitely fails if current user is not the author
		 */
		if ( current_user_can( 'edit_comment', $comment_ID ) ){
			return;
		}else{
			/**
			 * Doing this extra work since we want to allow even subscribers to be able to delete comments
			 */
			if ( $user->user_email != $author_email ){
				echo json_encode( $data );
				exit;
			}
			if ( $comment_status != "approved" ){
				$data["info"] = __("Your reply hasn't been approved yet.");
				echo json_encode( $data );
				exit;
			}
		}
	}

	public function sanitize_and_get_object_id(){
		$object_ID = intval($_POST["data"]["id"]);
		if ( $object_ID < 1 ){
			$data = array( "error" => true, "info"=> __('ID needs to be an integer') );
			echo json_encode($data);
			exit;
		}
		return $object_ID;
	}

	/**
	 * GET a comment edit form structure.
	 *
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function get_comment_form_meta(){

		/**
		 * Perform pre get request checks to make sure it contains the right data (basically 
		 * just the ID) 
		 */
		$this->pre_get_request();

		/**
		 * Check if the user is the owner of this comment
		 * But before doing that we're doing a little santization of the raw input id 
		 */
		$comment_ID = $this->sanitize_and_get_object_id();


		$this->comment_protection( $comment_ID ) ;

		/**
		 * All checks complete the current user IS the author of this comment
		 */
		$text = get_comment_text( $comment_ID );
		$formdata  = array();
		$formdata[] = array(
			'etype' => 'textarea',
			'name' => 'comment_text',
			'value' => $text,
			'rte'=> true
		);
		$formdata[] = array(
			'etype' => 'hidden',
			'name' => 'id',
			'value' => $comment_ID
		);

		$nonce = wp_create_nonce( 'post_comment_text' );
		$data = array( "error" => false, "info"=> $formdata, "security" => $nonce );
		echo json_encode($data);
		exit;
	}

	/**
	 * GET a comment by id.
	 *
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function get_comment_text(){

		/**
		 * Perform pre get request checks to make sure it contains the right data (basically 
		 * just the ID) 
		 */
		$this->pre_get_request();

		/**
		 * Check if the user is the owner of this comment
		 * But before doing that we're doing a little santization of the raw input id 
		 */
		$comment_ID = $this->sanitize_and_get_object_id();


		$this->comment_protection( $comment_ID ) ;

		/**
		 * All checks complete the current user IS the author of this comment
		 */
		$text = get_comment_text( $comment_ID );
		$data = array( "error" => false, "info"=> $text );
		echo json_encode($data);
		exit;
	}

	/**
	 * Parse post data.
	 *	
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function post_data_parser(){
		$form_data = $_POST['data'];
		$form_values = array();
		$return_data = array();
		foreach ($form_data as $row) {
			$form_values[$row["name"]] = $row["value"] ;
		}
		return $form_values;
	}

	/**
	 * Log a user into Combunity.
	 *	
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function auth_login(){
		$data = $this->post_data_parser();

		$msg = Combunity_Api()->login( $data );
		if (strlen($msg)>0){
			echo json_encode(array("error"=>true, "info"=>$msg));
		}
		else{
			$redirect= site_url();
			echo json_encode(array("error"=>false, "info"=>"Logged in", "redirect" => $redirect));
		}
		exit();
	}

	/**
	 * Signs a user up for Combunity.
	 *	
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function auth_signup(){

		$data = $this->post_data_parser();
		// echo json_encode($data);
		// exit();
		$created = Combunity_Api()->signup( $data );

		if ( strlen( $created ) == 0 ){

			$user_name = sanitize_user($data["signup_username"]);
			
			$password = sanitize_text_field($data["signup_password"]);

			$data = array('user_login' => $user_name, 'user_password' => $password);

	    	$msg = Combunity_Api()->login($data);

	    	if (strlen($msg)>0){

				echo json_encode(
					array(
						"error"=>true, 
						"info"=>$msg
					)
				);

				exit;

			}
			else{

				echo json_encode(
					array(
						"error"=>false, 
						"info"=> __("Account created. Now Logging you in.") 
					)
				);
				exit;
			}

		}else{

			echo json_encode(
				array(
					"error"=>true, 
					"info"=> $created 
				)
			);

			exit;

		}
	}

	/**
	 * Check WordPress comment moderation settings.
	 *	
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function moderation_checks( $comment_ID ){
		/**
		 * Lets check the moderation settings
		 * Before all else lets see if the user is a mod
		 */
		if ( current_user_can('moderate_comments') ){
			/**
			 * User is a mod all checks are now pointless
			 */
			return;
		}
		if ( get_option('comment_moderation') == 1 ){
			/**
			 * All comments need to be approved
			 */
			
			wp_set_comment_status( $comment_ID, "hold" );
			

			$data = array(
				"error" => true, "info" => __("Reply held for moderation. It will be posted after a moderator approves it")
			);
			echo json_encode( $data );
			exit;

		}else if ( get_option('comment_whitelist') == 1 ){
			/**
			 * Only new comments need to be approved
			 * Lets see if this user has aleady approved comments
			 */
			$past_comments = get_comments(array(
				'author_email'=>wp_get_current_user()->user_email, 
				'status'=>'approve',
				'count' => true
				)
			);
			/**
			 * We've obtained an array of comments lets see if it isn't zero
			 */
			if ( $past_comments == 0 ){
				
				
				wp_set_comment_status( $comment_ID, "hold" );
				

				$data = array(
					"error" => true, "info" => __("Reply held for moderation. It will be posted after a moderator approves it")
				);
				echo json_encode( $data );
				exit;
			}
		}
	}

	/**
	 * POST a comment by id.
	 *	This should probably have been a PUT.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function post_comment_text(){

		/**
		 * Perform pre get request checks to make sure it contains the right data (basically 
		 * just the ID) 
		 */
		$this->pre_get_request();
		$comment_ID = $this->sanitize_and_get_object_id();

		
		/**
		 * Performing nonce validation
		 */
		$check = check_ajax_referer( 'post_comment_text', 'security', false );
		
		if ( ! $check  ) {
			$data = array(
				"error" => true, "info" => __("Security validation of nonce failed.")
			);
			echo json_encode( $data );
			exit;
		}
		/**
		 * Performing a simple santization of the comment ID
		 */
		$comment_ID = preg_replace('/\D/', '', $comment_ID);
		
		if ( !isset( $_POST[ "data" ][ "comment_text" ] ) )	{
			$data = array(
				"error" => true, "info" => __("Reply text missing.")
			);
			echo json_encode( $data );
			exit;
		}
		/**
		 * Making sure the person performing the request is the author
		 */
		$this->comment_protection( $comment_ID ) ;


		$comment_text = $_POST[ "data" ][ "comment_text" ];

		/**
		 * We have everything we need now lets update the comment
		 */
		// $comment_text = sanitize_text_field( $comment_text  );
		$comment = get_comment( $comment_ID, "ARRAY_A" );

		if ( wp_get_comment_status( $comment_ID ) != "approved" ){
			$data = array(
				"error" => true, "info" => __("This reply has not been approved yet.")
			);
			echo json_encode( $data );
			exit;
		}

		$allowed_tags = wp_kses_allowed_html( 'post' );
		$comment_text = wp_kses( $comment_text, $allowed_tags );

		$comment_text = apply_filters( 'combunity_pre_update_reply' , $comment_text );

		$comment["comment_content"] = "[".__("Edited")."] <br>". $comment_text;

		wp_update_comment( $comment );
		/**
		 * And if moderation settings allow for the comment to be posted
		 */
		$this->moderation_checks($comment_ID );
		/**
		 * Updated! Let's respond to the client with a success message
		 */
		$comment_body = get_comment_text( $comment_ID );
		$data = array(
			"error" => false, "info" =>array( 
				"text" => $comment_body,
				"validation" =>	 $comment_body
			)
		);
		echo json_encode( $data );
		exit;
	}

	/**
	 * GET the submit thread form meta.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function get_forum_posts_form_meta(){
		$selected = '';
		if ( isset($_POST['data']['additional'] ) && isset( $_POST['data']['additional']['cforum'] ) ){
			$selected = sanitize_text_field( $_POST['data']['additional']['cforum'] );
		}

		$fields = $this->_p->forum_posts->get_submit_form( array('selected' => $selected ) );

		$nonce = wp_create_nonce( 'post_forum_posts' );

		$data = array( "error" => false, "info"=> $fields, "security" => $nonce );

		echo json_encode($data);

		exit;
	}

	/**
	 * GET the loginsignup section.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function post_forum_posts(){
		/**
		 * Lets make sure the user is logged in first
		 */
		$this->pre_get_request(false);

		/**
		 * Performing nonce validation
		 */
		$check = check_ajax_referer( 'post_forum_posts', 'security', false );
		
		if ( ! $check  ) {
			$data = array(
				"error" => true, "info" => __("Security validation of nonce failed.")
			);
			echo json_encode( $data );
			exit;
		}

		/**
		 * Check if user is posting too fast
		 */
		$lasttime = get_user_meta( get_current_user_id(), 'combunity_last_post_time', true);
		$waittimesecs = 60;
		if ( !empty($lasttime) ){
			$lastpostdiff = time()-$lasttime;
			if (  $lastpostdiff < $waittimesecs ){
				$lastpostdiff = $waittimesecs - $lastpostdiff;
				$data = array(
					"error" => true, "info" => __("You are posting too fast. Please try again in $lastpostdiff seconds." )
				);
				echo json_encode( $data );
				exit;
			}
		}


		$post_title = $_POST["data"]["post_title"];

		$post_title = sanitize_text_field( $post_title );

		$post_content = $_POST["data"]["post_content"];

		$category = $_POST["data"]["category"];

		$category = sanitize_text_field( $category );

		$forums =  get_terms( array(
		    'taxonomy' => 'cforum',
		    'hide_empty' => false,
		) );
		$forums_array = array();
		$found = false;
		foreach ($forums as $forum) {
			# code...
			if ( $category == $forum->slug ){
				$found = true;
			}
		}

		if ( !$found ){
			/**
			 * The user is trying to send a bad category
			 */
			$data = array( "error" => true, "info"=> __("That forum doesn't exist.") );

			echo json_encode($data);

			exit;

		}

		/**
		 * Check if empty title or content 
		 */

		if ( empty( $post_title ) || empty( $post_content ) ){

			$data = array( 'error' => true, 'info' => __("Please make sure that you have entered both a thread title and thread contents.") );

			echo json_encode($data);

			exit;

		}

		// Create post object
		$build_post = array(
		  'post_title'    => wp_strip_all_tags( $post_title ),
		  'post_content'  => $post_content,
		  'post_status'   => 'publish',
		  'post_type' => 'cpost',
		);
		 
		// Insert the post into the database
		$post_id = wp_insert_post( $build_post );

		if ( is_wp_error( $post_id ) ){

			$data = array( 'error' => true, 'info' => $post_id->get_error_message() );

			echo json_encode($data);

			exit;

		}

		$r = wp_set_object_terms($post_id, $category, 'cforum');

		update_user_meta( get_current_user_id() , 'combunity_last_post_time', time() );

		$fields = $this->_p->forum_posts->get_submit_form();

		$response = get_permalink( $post_id );

		$data = array( 
			"error" => false, 
			"info"=> array( 
				"validation" => sprintf('<a href="%2$s">%1$s</a>', __('Click here to continue...'), $response ) 
				)
		);

		echo json_encode($data);

		exit;
	}

	/**
	 * 
	 */
	public function register_comment_vote(){
		/**
		 * Lets make sure the user is logged in first and has an id to send to us
		 */
		$this->pre_get_request();
		
		$id = (int)$_POST['data']['id'];

		$type = (string)$_POST['data']['vote_type'];

		$latest_vote = 0;

		if ($type=="up"){
			$latest_vote = 1;
		}

		if ($type=="down"){
			$latest_vote = -1;
		}
		$this->_p->forum_posts->comment_vote( $id, $latest_vote );

		$data = array( 
			"error" => false, 
			"info"=> __("Casted comment vote")
		);

		echo json_encode($data);

		exit;

	}

	/**
	 * Get the Edit thread form
	 */
	public function get_edit_thread_form_meta(){
		$this->pre_get_request();
		
		$id = (int)$_POST['data']['id'];

		$content_post = get_post($id);

		// $this->is_author_check( get_post($id) );

		$this->capability_check( $id );

		$content = $content_post->post_content;

		$content = apply_filters('the_content', $content);

		$content = str_replace(']]>', ']]&gt;', $content);

		$formdata  = array();
		$formdata[] = array(
			'etype' => 'textarea',
			'name' => 'post_content',
			'value' => $content,
			'rte'=> true
		);
		$formdata[] = array(
			'etype' => 'hidden',
			'name' => 'id',
			'value' => $id
		);

		$nonce = wp_create_nonce( 'post_thread_text' );
		$data = array( "error" => false, "info"=> $formdata, "security" => $nonce );
		echo json_encode($data);
		exit;
	}

	/**
	 * Checks if the current user is the author of a post, fails if not
	 * .
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function is_author_check( $post ){

		$is_author = Combunity_Api()->is_the_user_author( $post );

		if ( !$is_author ){

			$data = array( "error" => true, "info"=> __('You need to be the author of a thread to edit it.') );

			echo json_encode( $data );

			exit;
		}
	}

	/**
	 * Is used by the Wp_ajax handler to save a thread
	 */
	public function post_thread( ){
		/**
		 * Lets make sure the user is sending an id too
		 */
		$this->pre_get_request();

		/**
		 * Performing nonce validation
		 */
		$check = check_ajax_referer( 'post_thread_text', 'security', false );

		if ( ! $check  ) {
			$data = array(
				"error" => true, "info" => __("Security validation of nonce failed.")
			);
			echo json_encode( $data );
			exit;
		}

		$id = $this->sanitize_and_get_object_id();

		$post = get_post( $id );

		$this->capability_check( $id );

		$post_content = sanitize_post_field( 'post_content', $_POST["data"]["post_content"], $id, 'display' );

		$post->post_content = stripslashes_deep( $post_content );

		$post_id = wp_update_post( $post , true );		

		if ( is_wp_error($post_id) ) {

			$errors_list = '';
			$errors = $post_id->get_error_messages() . '<br/>'; 

			foreach ($errors as $error) {

				$errors_list .= $error;

			}
			$data = array( "error" => true, "info"=> $errors_list );

			echo json_encode( $data );

			exit;

		}else{


			$reponse_content = get_post( $post->ID )->post_content;
			
			$data = array( "error" => false, "info"=> array( "validation" => $reponse_content ) );

			echo json_encode( $data );

			exit;


		}
	}

	private function capability_check( $post_id ){

		if ( !Combunity_Api()->can_user_do("delete", $post_id ) ){
			
			$data = array( 
				"error" => true, 
				"info"=> __("You don't have enough permissions to do that.") 
			);

			echo json_encode( $data );

			exit;
		}

	}

	/**
	 * Used to delete threads via the AJAX API
	 */
	public function get_delete_thread(){
		/**
		 * Lets make sure the user is sending an id too
		 */
		$this->pre_get_request();

		$id = $this->sanitize_and_get_object_id();

		$post = get_post( $id );

		// $this->is_author_check( $post );

		$this->capability_check( $post->ID );

		$categories = Combunity_Api()->get_thread_categories( $post->ID );

		if ( sizeof( $categories ) > 0 ){

			$redirect_url = get_term_link( $categories [ 0 ], 'cforum' );

		}else{
			$redirect_url = site_url();
		}

		wp_delete_post( $id );

		$response_text = sprintf('<a href="%2$s">%1$s</a>', __('Thread Deleted. Click here to continue...'), $redirect_url ) ;

		ob_start();

		get_template_part( 'template-parts/deleted-cpost' , '' );

		$response = ob_get_clean();

		$response .= $response_text;

		$data = array( "error" => false, "info"=> $response );

		echo json_encode( $data );

		exit;
	}


}
