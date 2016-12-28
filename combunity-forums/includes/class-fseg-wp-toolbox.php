<?php
class Fifthsegment_WP_Toolbox{
	/**
	 * 
	 */
	public function __construct(){

	}

	/**
	 * Actually create a WP page
	 */
	public function create_pages_fly($pageName, $content) {
        $createPage = array(
          'post_title'    => $pageName,
          'post_content'  => $content,
          'post_status'   => 'publish',
          'post_author'   => 1,
          'post_type'     => 'page',
          'post_name'     => $pageName
        );

        // Insert the post into the database
        wp_insert_post( $createPage );
    } 

	/**
	 * Creates a WordPress page, returns if page exists
	 */
	public function create_page( $args ){

		$defaults = array('title', 'content');

		$opts = array_merge( $defaults, $args );

		if( get_page_by_title( $opts['title'] ) == NULL ){

    		$this->create_pages_fly( $opts['title'], $opts['content'] );	

		}

		$page = get_page_by_title( $opts['title'] );

		if ( $page ){

			return $page->ID;

		}
	}
}