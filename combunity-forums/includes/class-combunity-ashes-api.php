<?php

/**
 * The file that defines the Combunity API available to WordPress plugins
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
 * The core Combunity plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 *
 * @since      1.0.0
 * @package    Combunity_Ashes
 * @subpackage Combunity_Ashes/includes
 * @author     Abdullah <abdullah@combunity.com>
 */
class Combunity_Ashes_API {
	/**
	 * The parent class instance.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $_p    The parent class instance.
	 */
	protected $_p;

	/**
	 * Constructing the API.
	 *
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function __construct( $parent ){
		$this->_p  = $parent;
	}

	/**
	 * Gets the latest comment on a post.
	 *
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function the_last_comment(){
		return $this->_p->forum_posts->the_last_comment();
	}

	/**
	 * Checks if the current post is sticky.
	 *
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function is_sticky(){
		// TODO: Add definition here
		return $this->_p->forum_posts->is_sticky();
	}

	/**
	 * Gets a link to the current users profile.
	 *
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function get_version(){
		return $this->_p->get_version();
	}

	/**
	 * Gets a link to the current users profile.
	 *
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function the_userprofile_link(){
		// TODO: Add definition here
		// Use WordPress authors page if theme is active
		return get_author_posts_url( get_the_author_meta( 'ID' ), get_the_author_meta( 'user_nicename' ) );
		// return get_the_author_link() ;
	}	

	/**
	 * Returns a Comment time in U format.
	 *
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function get_comment_time( $c ){
		return $this->_p->forum_posts->comment_time( $c );
	}

	/**
	 * Returns a the post meta.
	 *
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function the_fp_meta( $key, $default='' ){
		return $this->_p->forum_posts->the_fp_meta( $key, $default );
	}

	/**
	 * Returns the current user's vote
	 *
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function check_and_get_user_vote( $id ){
		return $this->_p->forum_posts->check_and_get_user_vote( $id );
	}


	/**
	 * Get forum meta
	 * .
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function get_forum_meta( $term, $key, $default = '' ){
		$t_id = $term->term_id;
		$term_meta = get_option( "taxonomy_$t_id" , array() );
		if ( isset( $term_meta[ $key ] ) ){
			return $term_meta[ $key ];
		}
		return $default;
	}

	/**
	 * Get forum meta
	 * .
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function get_option( $key, $default = '' ){
		return get_option( 'combunity_'. $key, $default );
	}

	/**
	 * Get forum meta
	 * .
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function user_emails_enabled( $user_id ){
		if ( get_user_meta( $user_id, 'combunity_enable_emails', "on" ) == "on" ){
			return true;
		}
		return false;
	}

	/**
	 * 
	 */
	public function can_user_do( $action, $post_id ){
		if ( !current_user_can( 'delete_post', $post_id ) ){

			$is_author = Combunity_Api()->is_the_user_author( get_post( $post_id ) );

			if ( !$is_author ){

				return false;

				
			}else{
				return true;
			}
			
		}else{
			return true;
		}
		return false;
	}

	/**
	 * Get edit thread link
	 * .
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function get_edit_thread_link(){
		if ( Combunity_Api()->can_user_do("delete", get_the_ID() ) ){
			$link = '#';
			$text = __('Edit');
			$id = get_the_ID();
			$ahref = sprintf( '<a href="%1$s" class="combunity-edit-thread-link" data-id="%3$s">%2$s</a>', $link, $text, $id );
			return $ahref;
		}

	}

	/**
	 * Get edit thread link
	 * .
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function get_delete_thread_link(){
		if ( Combunity_Api()->can_user_do("delete", get_the_ID() ) ){
			$link = '#';
			$text = __('Delete');
			$id = get_the_ID();
			$ahref = sprintf( '<a href="%1$s" class="combunity-delete-thread-link" data-id="%3$s">%2$s</a>', $link, $text, $id );
			return $ahref;
		}
	}

	/**
	 * Returns true of the current user is author of a post
	 * .
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function is_the_user_author( $post ){
		if ( !is_user_logged_in() )
			return false;
	
		$id = get_current_user_id();
	
		$author_id = $post->post_author;

		if ( $id == $author_id )
			return true;
	
		return false;
	}

	/**
	 * Returns the categories of a thread(wp custom post) given the id
	 * .
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function get_thread_categories( $post_id ){
		$terms = wp_get_post_terms( $post_id, 'cforum' );
		return $terms;
	}

	/**
	 * Returns the profile link of a WordPress user
	 * .
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function get_userprofile_link( $author ){
		if ( is_object( $author ) ){
			var_dump( $author );
			return $author->user_url;
		}
	}

	/**
	 * Increments the views of a post id
	 * .
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function increment_thread_views( $id ){
		$views = get_post_meta( $id, "combunity_post_views", true);
		if (!$views){
			$views = 0;
		}
		$views++;
		update_post_meta( $id, "combunity_post_views", $views );
	}

	/**
	 * Get all forums ever created
	 * .
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function get_all_forums( $output = 'wp_terms' ){
		$forums =  get_terms( array(
		    'taxonomy' => 'cforum',
		    'hide_empty' => false,
		) );

		// var_dump( $forums );
		switch ( $output ) {
			case 'wp_terms':

				return $forums;

				break;
			case 'simple':
				$forums_array = array();

				foreach ($forums as $forum) {
				
					$forums_array[] = $forum->slug ;

				}

				return $forums_array;

				break;
			default:
				# code...
				break;
		}
	}

	/**
	 * Get WP_Query arguments for the front page
	 * .
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function get_query_args_for_front_page(){
		$forums = $this->get_all_forums( 'simple' );
		// var_dump( $forums );
		$args = array (
			'post_type' => 'cpost',
			// 'paged' => 1,
			// 'order' => 'DESC',
			// 'orderby' => 'meta_value_num',
			// 'meta_key' => $this->_instance->_token.'votes',
			'tax_query' => array(
			    array(
			      'taxonomy' => 'cforum',
			      'field' => 'slug',
			      'terms' => $forums // Where term_id of Term 1 is "1".
			      // 'include_children' => false
			    ),
			 )
		);
		return $args;
	}

	/**
	 * Logs a user into Combunity
	 * returns string of 0 length if succesful
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function login( $data ){
		$creds = array();
		$creds['user_login'] = $data['user_login'];
		$creds['user_password'] = $data['user_password'];
		$creds['remember'] = false;
		if ( strlen($creds['user_login']) == 0 && strlen($creds['user_password'])==0 ){
			return __('Login or Password was missing.');
		}
		$user = wp_signon( $creds, false );
		if ( is_wp_error($user) ){
			return $user->get_error_message();
		}
		return "";
	}

	/**
	 * Signs up a user for Combunity
	 * returns string of 0 length if succesful
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function signup( $data ){

		$user_name = sanitize_user($data["signup_username"]);

		$password = sanitize_text_field($data["signup_password"]);

		$email = sanitize_email($data["signup_email"]);

		// $response = $this->_instance->users->create( $data );

		$user_name = filter_var($user_name, FILTER_SANITIZE_STRING);
		$password = filter_var($password, FILTER_SANITIZE_STRING);
		$email = filter_var($email, FILTER_SANITIZE_EMAIL);

		// Validate e-mail
		if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
		    
		} else {
			// return $email;
		    return $email . __( " is not a valid email address");

		}
		do_action('combunity_pre_create_user', $data );
		$user = wp_create_user( $user_name, $password, $email );
		if (is_wp_error($user)){

			return $user->get_error_message();

		}
		return "";
	}

	/**
	 * Get a users avatar via the object passed
	 * .object can be either post or comment
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function get_avatar( $object ){
		// if ( get_class( $object ) == "WP_Post" || get_class($object) == "WP_Comment" ){
			$avatar = get_avatar( 
				$object, 32,"", 
	    		"Avatar Image", 
	    		array("class"=>"combunity-forum-postpage-avatar") 
	        );
	        return $avatar;
		// }

	}

	/**
	 * Returns an image if the current post contains one
	 * .
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function get_the_post_image(){
		$images = $this->getImageTags( get_post()->post_content );
		if ( sizeof( $images ) < 1 )
			return;
		$src = '';
		$i = 0;
		foreach ($images as $image ) {
			if ( ++$i > 1 )
				break;
			$img_tag = stripslashes($image);
            preg_match_all('/(width|height|src|alt)=("[^"]*")/i', $img_tag, $attributes);
			foreach ($attributes[0] as $attribute_string) {

                $attribute = explode('=', str_replace("\"", '', $attribute_string));

                if ($attribute[0] === 'src') {
                    $src = filter_var($attribute[1], FILTER_SANITIZE_STRIPPED);
                }

            }
		}
		return $src;
	}

	/**
	 * Gets all image tags
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function getImageTags($comment_content) {

        preg_match_all('/<img[^>]+>/i', $comment_content, $result);

        return $result[0];
    }

	/**
	 * Trims a string for use in widgets
	 *
	 * @since 1.0.0
	 * @access public
	 */
    public function string_trim($string,$length=100,$append="&hellip;"){
    	// function truncate() {
    	// $small = strlen($string) > $length ? substr($string,0,$length)."..." : $string;
    	// $image_tags = $this->getImageTags( $small );
    	// if ( sizeof( $image_tags) > 0 ){

    	// }
    	$string = strip_tags( $string, '<img>' );

    	$image_tags = $this->getImageTags( $string );

    	if ( sizeof( $image_tags ) > 0 ){

    		$out = wp_trim_words( $string, 10 );

    		if ( $out == '' ){

    			$out = __('[IMAGE]');

    		}

    		return $out;
    	}else{

    		$out = strlen($string) > $length ? substr($string,0,$length)."..." : $string;

    		return $out;

    	}

    	


		  $string = trim($string);

		  if(strlen($string) > $length) {
		    $string = wordwrap($string, $length);
		    $string = explode("\n", $string, 2);
		    $string = $string[0] . $append;
		  }

		  return $string;
		// }
    }
}
	
