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
 * This class defines all code necessary to run the Forums on Combunity.
 *
 * @since      1.0.0
 * @package    Combunity_Ashes
 * @subpackage Combunity_Ashes/includes
 * @author     Abdullah <abdullah@combunity.com>
 */
class Combunity_Ashes_Forum_Posts {
	/**
	 * The parent class instance.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $_p    The parent class instance.
	 */
	protected $_p;

	/**
	 * The wp post type of forums.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $_wp_post_type    The post type of forums.
	 */
	protected $_wp_post_type = "cpost";

	/**
	 * An array of ids of comments the current user has voted on
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $_wp_post_type    The post type of forums.
	 */
	protected $comments_voted_on = array();

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $parent ){
		$this->_p = &$parent;
		$this->_p->loader->add_action( 'init', $this, 'register_post_type' );
		$this->_p->loader->add_action( 'init', $this, 'comments_add_filter_for_enabling_embeds' );
		$this->_p->loader->add_filter('manage_posts_columns', $this, 'manage_posts_columns' );
		$this->_p->loader->add_filter( 'wp_kses_allowed_html', $this, 'comments_kses_allowed_html_hook', 20, 2 );
		$this->_p->loader->add_filter( 'preprocess_comment', $this, 'allow_images_in_replies', 99, 1);
		$this->_p->loader->add_filter( 'combunity_pre_update_reply', $this, 'pre_update_reply',99, 1);
		$this->_p->loader->add_shortcode( 'combunity', $this, 'combunity_shortcode_handler', 10, 2 );

		$this->_p->loader->add_action('save_post',$this, 'pre_process_posts_before_saving',10,1);
		$this->_p->loader->add_action( 'comment_post', $this, 'run_after_reply_posted', 10, 2 );
		$this->_p->loader->add_action('save_post',$this, 'run_after_thread_posted',10,3);

	}

	/**
	 * Renders Combunity's stuff (login forms/post thread form)
	 * 
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function combunity_shortcode_handler( $atts ){
		if ( $atts[ "type" ] == "submitthread" ){
			return "<div class='submitthread'></div>";
		}
		if ( $atts[ "type" ] == "entrance" ){
			return "<div class='entrance'></div>";
		}
	}

	/**
	 * Pre processes a post before saving
	 * 
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function pre_process_posts_before_saving( $post_id ){
		// First lets make sure this post is of our type
		if ( 'cpost' === get_post_type( $post_id ) ){
			$content = get_post( $post_id )->post_content;
			$content = $this->resizeImages( $content );

			remove_action( 'save_post', array( $this, 'pre_process_posts_before_saving') );

			wp_update_post( array( 'ID' => $post_id, 'post_content' => $content ) );

			add_action( 'save_post', array( $this, 'pre_process_posts_before_saving') );

		}

	}

	/**
	 * Allow images/lists/and underlines in comments
	 * of Combunity.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function comments_kses_allowed_html_hook( $tags, $context = null ){
		// var_dump( $context );
		if ( 'pre_comment_content' == $context && ! isset( $tags['img'] ) ) {
			$tags['img'] = array(
				'src' => 1,
				'height' => 1,
				'width' => 1,
				'alt' => 1,
				'title' => 1
			);
			$tags['u'] = array();
			$tags['ul'] = array();
			$tags['li'] = array();
			$tags['ol'] = array();
			$tags['span'] = array(
				'style' => 'text-decoration: none !important'
			);

		}
		return $tags;
	}

	/**
	 * Resizes images in comments to make sure nothing large sized passes through
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function allow_images_in_replies( $commentdata ) {
		 $commentdata['comment_content'] = $this->resizeImages($commentdata['comment_content']);
		 return $commentdata;
	}

	/**
	 * Pre update reply to allow for images in it
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function pre_update_reply( $comment_text ){
		return $this->resizeImages( $comment_text );
	}

	

	/**
	 * Resizes images in comments to make sure nothing large sized passes through
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function resizeImages($comment_content) {

		

        $image_tags = Combunity_Api()->getImageTags( $comment_content );

        if (count($image_tags) === 0) {
            return $comment_content;
        }

        foreach ($image_tags as $img_tag) {

            $original_img_tag = $img_tag;
            $img_tag = stripslashes($img_tag);
            preg_match_all('/(width|height|src|alt)=("[^"]*")/i', $img_tag, $attributes);

            $width = false;
            $height = false;

            $max_width = 200;
            $max_height = 300;
            $src = false;
            $alt = '';
            foreach ($attributes[0] as $attribute_string) {

                $attribute = explode('=', str_replace("\"", '', $attribute_string));

                if ($attribute[0] === 'width') {
                    $width = filter_var($attribute[1], FILTER_SANITIZE_NUMBER_INT);
                }

                if ($attribute[0] === 'height') {
                    $height = filter_var($attribute[1], FILTER_SANITIZE_NUMBER_INT);
                }

                if ($attribute[0] === 'src') {
                    $src = filter_var($attribute[1], FILTER_SANITIZE_STRIPPED);
                }

                if ($attribute[0] === 'alt') {
                    $alt = filter_var($attribute[1], FILTER_SANITIZE_STRIPPED);
                }
            }

            // if (empty($width)) {
            //     $width = $max_width;
            // }

            // if (empty($height)) {
            //     $height = $max_height;
            // }

            // if ($width > $max_width) {
            //     $ratio = $max_width / $width;
            //     $height *= $ratio;
            //     $width *= $ratio;
            // }

            // if ($height > $max_height) {
            //     $ratio = $max_height / $height;
            //     $height *= $ratio;
            //     $width *= $ratio;
            // }

            // $height = ceil($height);
            // $width = ceil($width);

            $style= "max-width: 100%; height:auto;";

            $nonemoticonclass = "combunity-user-posted-image";

            $combunity_img_class = $nonemoticonclass;

            if ( $src ){
            	if( strpos( $src, "plugins/emoticons/img/smiley" ) !== false ) {
            		$combunity_img_class = "";
            	}
            }

            $new_image_tag = addslashes("<a class=\"combunity-comment-image-link\" href=\"{$src}\" target=\"_blank\"><img class=\"tmcecf-comment-image ".$combunity_img_class."\" alt=\"{$alt}\" src=\"{$src}\" width=\"{$width}\" height=\"{$height}\" style=\"{$style}\" /></a>");

            $comment_content = str_replace($original_img_tag, $new_image_tag, $comment_content);
        }

       

        return $comment_content;
    }

	/**
	 * Register post type of threads that power the forums
	 * of Combunity.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function register_post_type(){
		$args = array(
			'show_ui' => true,
			'public' => true,
			'labels' => array(
				'name' => 'Threads',
				'singular_name' => 'Thread',
				'add_new_item' => 'Add new Thread',
				'menu_name' => __('Combunity'),
				'all_items' => __('Threads')
			),
			'map_meta_cap' => true,
			'supports' => array('comments', 'title', 'editor' )
		);
		register_post_type( $this->_wp_post_type, $args );
	}

	/**
	 * Register post type of threads that power the forums
	 * of Combunity.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	public function manage_posts_columns( $columns ){
		// $additonal = array('tags' => __('Tag'));
		$additional = array(
			'comments'=>_('Replies'),
		);
 		return array_merge( $columns, 
              $additional );
	}

	/**
	 * Gets the latest reply sent on a thread 
	 * of Combunity.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function the_last_comment(  ){
		$num = get_comments_number();
		if ( $num == 0 ){
			return false;
		}
		$recent_comments = get_comments( array(
		    'number'    => 1,
		    'post_id' => get_the_ID(),
		    'status'    => 'approve'
		) );
		// var_dump( $recent_comments );
		if ( !sizeof( $recent_comments ) > 0 ){
			return false;
		}
		return $recent_comments[0];
	}

	/**
	 * Gets the latest reply sent on a thread 
	 * of Combunity.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function is_sticky(  ){
		// TODO: Add actual code here
		return false;
	}	

	/**
	 * Gets the time of a comment
	 * of Combunity.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function comment_time( $comment ){
		$comment_date = $comment->comment_date;
		$date = mysql2date( 'U', $comment_date, true );
		return $date;
	}	

	/**
	 * Gets the meta of a post
	 * of Combunity.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function the_fp_meta( $key, $default ){
		$value = get_post_meta( get_the_ID(), $key, true );
		if ( empty( $value ) ){
			return $default;
		}
		return $value;
	}

	/**
	 * Gets the structure of the submit post form 
	 * of Combunity.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function get_submit_form( $data=array() ){
		$selected_forum = '';
		if ( isset($data['selected']) ){

			$selected_forum = $data['selected'];

		}
		// var_dump( $selected_forum );
		$forums =  get_terms( array(
		    'taxonomy' => 'cforum',
		    'hide_empty' => false,
		) );
		$forums_array = array();
		$i = 0;
		foreach ($forums as $forum) {
			# code...
			$forums_array[] = array( "name" => $forum->name, "slug" => $forum->slug );

			if ( $selected_forum == $forum->slug ){

				$forums_array[ $i ]["selected"] = true;

			}
			$i++;
		}
		$fields = array();
		$fields[] = array(
			'etype' => 'text',
			'name' => 'post_title',
			'value' => '',
			'label' => __('Thread title'),
			'rte'=> false
		);
		$fields[] = array(
			'etype' => 'textarea',
			'name' => 'post_content',
			'value' => '',
			'label' => __('Content'),
			'rte'=> true
		);
		$fields[] = array(
			'etype' => 'notice',
			'name' => 'rules',
			'class'=>'combunity-submit-thread-rules',
			'value' => Combunity_Api()->get_option('site_rules'),
			'label' => __('Rules'),
			'rte'=> true
		);

		
		$fields[] = array(
			'etype' => 'select',
			'name' => 'category',
			'value' => $selected_forum,
			'options' => $forums_array,
			'label' => __('Forum'),
			'rte'=> false
		);
		return $fields;
	}

	/**
	 * Checks and gets a user vote
	 * of Combunity.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function check_and_get_user_vote( $post_id_param ){
		if (!is_user_logged_in()){
			return false;
		}
		if ( $this->comments_voted_on == false){
			if (is_user_logged_in()){
				$user_id = get_current_user_id();
				$comments_voted_on = get_user_meta( $user_id, "combunity_comments_voted_on", true);
				if ($comments_voted_on == false){
					$comments_voted_on = array();
				}
				$this->comments_voted_on = $comments_voted_on;
			}
		}
		foreach ($this->comments_voted_on as $post_id ) {
			$pos = strpos($post_id , "-");

			if ($pos != false){

				$id = substr($post_id, 0 , $pos);
				
				if ((int)$id == (int)$post_id_param){
					$type = substr($post_id, $pos+1 , strlen($post_id));
					return $type;
				}
			}
		}
		return false;
	}

	/**
	 * Performs a vote on a comment in 
	 * Combunity.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function comment_vote( $comment_id, $latest_vote ){
		$meta_key = 'combunity_votes';
		do_action('combunity__pre_cast_comment_vote', $comment_id );

		$user_ID = get_current_user_id();
		$comments = get_user_meta( $user_ID, "combunity_comments_voted_on", true);
		foreach ($comments as $commentid) {
			if ($commentid == $comment_id ){
				echo json_encode(array("error"=>true, "info"=> "You've already voted on this comment"));
				exit();
			}
		}
		$prevPoints = get_comment_meta($comment_id, $meta_key, true);
		if ($prevPoints == ""){
			$prevPoints = 0;
		}
		$newPoints = $prevPoints+$latest_vote;

		$type = "";
		if ($latest_vote > 0 ){
			$type = "up";
		}else{
			$type = "down";
		}
		// if ($newPoints<0){
		// 	$newPoints = 0;
		// }
		update_comment_meta( $comment_id, $meta_key, $newPoints );

		

		$posts = get_user_meta( $user_ID, "combunity_comments_voted_on", true);

		if ($posts==""){
			$posts = array();
		}

		$posts[] = $comment_id. "-" . $type;

		update_user_meta( $user_ID, "combunity_comments_voted_on" , $posts );

		do_action('combunity__cast_comment_vote', $comment_id, $user_ID, $type );
		
		do_action('combunity__post_cast_comment_vote', $comment_id, $user_ID, $type );

	}

	/**
	 * Setup filter with correct priority to do oEmbed in comments
	 */
	public function comments_add_filter_for_enabling_embeds() {
		if ( is_admin() )
			return;
		// make_clickable breaks oEmbed regex, make sure we go earlier
		$clickable = has_filter( 'get_comment_text', 'make_clickable' );
		$priority = ( $clickable ) ? $clickable - 1 : 10;
		add_filter( 'get_comment_text', array( $this, 'comments_oembed_filter' ), $priority );
	}

	/**
	 * Safely add oEmbed media to a comment
	 */
	public function comments_oembed_filter( $comment_text ) {
		global $wp_embed;

		// Automatic discovery would be a security risk, safety first
		add_filter( 'embed_oembed_discover', '__return_false', 999 );
		$comment_text = $wp_embed->autoembed( $comment_text );

		// ...but don't break your posts if you use it
		remove_filter( 'embed_oembed_discover', '__return_false', 999 );

		return $comment_text;
	}

	/**
	 * Finds user mentions
	 */
	public function find_user_mentions( $content , $object_type , $object_id ){
		 /**
         * Find any user mentions
         */

        $mentions = array();

        preg_match_all("/@[\w]+/", $content, $mentions );

        if ( sizeof($mentions[0]) > 0 ){
        	$mentions = $mentions[0];
        	foreach ( $mentions as $possible_username ) {
        		# code...

        		$possible_username = str_replace('@', '',  $possible_username);

        		$user = get_user_by('slug', $possible_username);

        		if ( $user ){
				
        			do_action( 'combunity_user_mention', $user , $object_type, $object_id );

        		}
        	}
        }
	}


	/**
	 * Runs after a reply has been posted
	 * Adds reply time to the post object
	 */
	public function run_after_reply_posted( $comment_ID, $comment_approved ){

		if( 1 === $comment_approved ){
			$comment = get_comment( $comment_ID );
			$post_id = $comment->comment_post_ID;
			if ( 'cpost' == get_post_type( $post_id ) ){
				update_post_meta( $post_id, 'combunity_comment_time', time() );

				do_action('combunity_reply_created', $comment_ID );

				$comment = get_comment( $comment_ID );

				$this->find_user_mentions( $comment->comment_content, 'comment', $comment_ID );

			}
		}
	}

	/**
	 * Runs after a thread has been posted
	 * Adds reply time to the post object
	 */
	public function run_after_thread_posted( $post_id, $post, $updated ){
		if ( $updated )
			return;
		if ( 'cpost' == get_post_type( $post_id ) ){
			update_post_meta( $post_id, 'combunity_comment_time', 0 );
			update_post_meta( $post_id, 'combunity_post_views', 0 );
			if ( did_action('combunity_thread_created') === 0 ){
				do_action('combunity_thread_created', $post_id );
			}
			$post = get_post( $post_id );

			$this->find_user_mentions( $post->post_content, 'post', $post_id );
			
		}
	}

	public function shortcode_function(){
        return 'Test the plugin';
    }

}