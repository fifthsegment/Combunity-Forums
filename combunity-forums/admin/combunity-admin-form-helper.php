<?php

class Combunity_Admin_Form_Helper{
	/**
	 * All form fields
	 */
	private $fields = array();

	private $form_actions = array();

	public function __construct( $fields, $form_actions ){
		$this->fields = $fields;
		$this->form_actions = $form_actions;
	}

	/**
	 * Generate the form start html tag
	 * @param  array   $opts for button data
	 * @param  boolean $echo  Whether to echo the field HTML or return it
	 * @return void
	 */
	public function form_start( $opts = array() , $echo = true ){

		$defaults = array('method' => 'GET', 'action' => '');

		$data = array_merge( $defaults, $this->form_actions );

		$html = '';

		$html .= '<form method="'.$data['method'].'" action="'.$data['action'].'">';

		$html .= '<br>';

		if( !$echo ){

			return $html;

		}

		echo $html;
	}

	/**
	 * Handles the form submission
	 */
	public function handle_submit(){
		if ( isset( $_REQUEST['Submit'] ) ){
			
			$html = '<div id="" class=""><p><strong>'.__('Settings Saved.').'</strong></p></div>';

			echo $html;

			foreach ($this->fields as $key => $field) {

				if ( isset($field['nonsubmit']) ){

					continue;

				}

				$option_name = '';

				if ( isset($this->fields[$key]['prefix'] )){

					$option_name .= $this->fields[$key]['prefix'];

				}

				$option_name .= $field['id'];

				$value = stripslashes_deep( $_REQUEST[$option_name] );
				
				update_option( $option_name , $value );

			}
		}
	}

	/**
	 * Renders the form fields
	 * @param  array   $opts for the form
	 * @param  boolean $echo  Whether to echo the field HTML or return it
	 * @return void
	 */
	public function render_form_body( $opts = array(), $echo = true ){
		foreach ($this->fields as $option ) {
			# code...
			$this->display_field( $option );

		}
	}

	/**
	 * Generate the form end html tag
	 * @param  array   $opts for button data
	 * @param  boolean $echo  Whether to echo the field HTML or return it
	 * @return void
	 */
	public function form_end( $opts = array() , $echo = true ){
		$html = '';
		$html .= '</form>';
		if( !$echo ){
			return $html;
		}
		echo $html;
	}

	/**
	 * Generate the form save button
	 * @param  array   $opts for button data
	 * @param  boolean $echo  Whether to echo the field HTML or return it
	 * @return void
	 */
	public function submit_button( $opts = array() , $echo = true ){
		$html = '';
		$html .= '<p class="submit">' . "\n";
		// $html .= '<input type="hidden" name="tab" value="' . esc_attr( $tab ) . '" />' . "\n";
		$html .= '<input name="Submit" type="submit" class="button-primary" value="' . esc_attr( __( 'Save Settings' ) ) . '" />' . "\n";
		$html .= '</p>' . "\n";
		if( !$echo ){
			return $html;
		}
		echo $html;

	}

	/**
	 * Generate HTML for displaying fields
	 * @param  array   $field Field data
	 * @param  boolean $echo  Whether to echo the field HTML or return it
	 * @return void
	 */
	public function display_field ( $data = array(), $post = false, $echo = true ) {

		// Get field info
		if ( isset( $data['field'] ) ) {
			$field = $data['field'];
		} else {
			$field = $data;
		}

		// Check for prefix on option name
		$option_name = '';
		if ( isset( $data['prefix'] ) ) {
			$option_name = $data['prefix'];
		}

		// Get saved data
		$data = '';
		if ( $post ) {

			// Get saved field data
			$option_name .= $field['id'];
			$option = get_post_meta( $post->ID, $field['id'], true );

			// Get data to display in field
			if ( isset( $option ) ) {
				$data = $option;
			}

		} else {

			// Get saved option
			$option_name .= $field['id'];
			$option = get_option( $option_name );

			// Get data to display in field
			if ( isset( $option ) ) {
				$data = $option;
			}

		}

		// Show default data if no option saved and default is supplied
		if ( $data === false && isset( $field['default'] ) ) {
			$data = $field['default'];
		} elseif ( $data === false ) {
			$data = '';
		}

		$html = '';

		$class = '';
		if ( isset( $field['class'] ) ){
			$class = $field['class'];
		}
		
		$html .= '<div>';
		$html .= '<label>' . $field['label'] . '</label>';
		$html .= '</div><div>';	
		

		switch( $field['type'] ) {
			case 'noteditabletext':
				$html .= '<span id="' . esc_attr( $field['id'] ) . '">' . ( $field['placeholder'] ) . '</span>' . "\n";
				break;
			case 'text':
			case 'url':
			case 'email':
				$html .= '<input class="' . $class . '" id="' . esc_attr( $field['id'] ) . '" type="text" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value="' . esc_attr( $data ) . '" />' . "\n";
			break;

			case 'password':
			case 'number':
			case 'hidden':
				$min = '';
				if ( isset( $field['min'] ) ) {
					$min = ' min="' . esc_attr( $field['min'] ) . '"';
				}

				$max = '';
				if ( isset( $field['max'] ) ) {
					$max = ' max="' . esc_attr( $field['max'] ) . '"';
				}
				$html .= '<input class="' . $class . '" id="' . esc_attr( $field['id'] ) . '" type="' . esc_attr( $field['type'] ) . '" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value="' . esc_attr( $data ) . '"' . $min . '' . $max . '/>' . "\n";
			break;

			case 'text_secret':
				$html .= '<input class="' . $class . '" id="' . esc_attr( $field['id'] ) . '" type="text" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value="" />' . "\n";
			break;

			case 'textarea':
				$html .= '<textarea class="' . $class . '" id="' . esc_attr( $field['id'] ) . '" rows="5" cols="50" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '">' . $data . '</textarea><br/>'. "\n";
			break;

			case 'checkbox':
				$checked = '';
				if ( $data && 'on' == $data ) {
					$checked = 'checked="checked"';
				}
				$html .= '<input class="' . $class . '" id="' . esc_attr( $field['id'] ) . '" type="' . esc_attr( $field['type'] ) . '" name="' . esc_attr( $option_name ) . '" ' . $checked . '/>' . "\n";
			break;

			case 'checkbox_multi':
				foreach ( $field['options'] as $k => $v ) {
					$checked = false;
					if ( in_array( $k, $data ) ) {
						$checked = true;
					}
					$html .= '<label for="' . esc_attr( $field['id'] . '_' . $k ) . '" class="checkbox_multi"><input type="checkbox" ' . checked( $checked, true, false ) . ' name="' . esc_attr( $option_name ) . '[]" value="' . esc_attr( $k ) . '" id="' . esc_attr( $field['id'] . '_' . $k ) . '" /> ' . $v . '</label> ';
				}
			break;

			case 'radio':
				foreach ( $field['options'] as $k => $v ) {
					$checked = false;
					if ( $k == $data ) {
						$checked = true;
					}
					$html .= '<label for="' . esc_attr( $field['id'] . '_' . $k ) . '"><input type="radio" ' . checked( $checked, true, false ) . ' name="' . esc_attr( $option_name ) . '" value="' . esc_attr( $k ) . '" id="' . esc_attr( $field['id'] . '_' . $k ) . '" /> ' . $v . '</label> ';
				}
			break;

			case 'select':
				$html .= '<select name="' . esc_attr( $option_name ) . '" id="' . esc_attr( $field['id'] ) . '">';
				foreach ( $field['options'] as $k => $v ) {
					$selected = false;
					if ( $k == $data ) {
						$selected = true;
					}
					$html .= '<option ' . selected( $selected, true, false ) . ' value="' . esc_attr( $k ) . '">' . $v . '</option>';
				}
				$html .= '</select> ';
			break;

			case 'select_multi':
				$html .= '<select name="' . esc_attr( $option_name ) . '[]" id="' . esc_attr( $field['id'] ) . '" multiple="multiple" class="' . $class . '">';
				
				foreach ( $field['options'] as $k => $v ) {
					$selected = false;
					if ( ( $data != "" ) ){
						if ( in_array( $k, $data ) ) {
							$selected = true;
						}
					}
					$html .= '<option ' . selected( $selected, true, false ) . ' value="' . esc_attr( $k ) . '">' . $v . '</option>';
				}
				$html .= '</select> ';
			break;

			case 'image':
				$image_thumb = '';
				if ( $data ) {
					$image_thumb = wp_get_attachment_thumb_url( $data );
				}
				$html .= '<img id="' . $option_name . '_preview" class="image_preview" src="' . $image_thumb . '" /><br/>' . "\n";
				$html .= '<input id="' . $option_name . '_button" type="button" data-uploader_title="' . __( 'Upload an image' , 'combunity' ) . '" data-uploader_button_text="' . __( 'Use image' , 'combunity' ) . '" class="image_upload_button button" value="'. __( 'Upload new image' , 'combunity' ) . '" />' . "\n";
				$html .= '<input id="' . $option_name . '_delete" type="button" class="image_delete_button button" value="'. __( 'Remove image' , 'combunity' ) . '" />' . "\n";
				$html .= '<input id="' . $option_name . '" class="image_data_field" type="hidden" name="' . $option_name . '" value="' . $data . '"/><br/>' . "\n";
			break;

			case 'color':
				?><div class="color-picker" style="position:relative;">
			        <input type="text" name="<?php esc_attr_e( $option_name ); ?>" class="color" value="<?php esc_attr_e( $data ); ?>" />
			        <div style="position:absolute;background:#FFF;z-index:99;border-radius:100%;" class="colorpicker"></div>
			    </div>
			    <?php
			break;

			case 'button':
				ob_start();
				?><div id="div-<?php echo esc_attr( $field['id'] ) ?>" class="">
			        <button id="<?php echo esc_attr( $field['id'] ) ?>" class="<?php echo esc_attr( $field['class'] ) ?>"><?php echo esc_attr( $field['label'] ) ?></button>
			    </div>
			    <?php
			    $html .= ob_get_clean();
			break;

		}

		switch( $field['type'] ) {

			case 'checkbox_multi':
			case 'radio':
			case 'select_multi':
				$html .= '<br/><span class="description">' . $field['description'] . '</span>';
			break;

			default:
				if ( ! $post ) {
					$html .= '<label for="' . esc_attr( $field['id'] ) . '">' . "\n";
				}

				$html .= '<span class="description">' . $field['description'] . '</span>' . "\n";

				if ( ! $post ) {
					$html .= '</label>' . "\n";
				}
			break;
		}
		$html .= '</div>';
		if ( ! $echo ) {
			return $html;
		}

		echo $html;

		}
	
}