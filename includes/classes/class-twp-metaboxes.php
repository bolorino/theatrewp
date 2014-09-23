<?php
if ( realpath( __FILE__ ) === realpath( $_SERVER["SCRIPT_FILENAME"] ) )
	exit ( 'Do not access this file directly.' );

class TWP_Metaboxes {

	protected $_meta_box;

	public function __construct( $meta_box ) {

		$this->_meta_box = $meta_box;

		add_action( 'admin_menu', array( $this, 'add' ) );
    	add_action( 'save_post', array( $this, 'save' ) );

	}

	function add() {
        foreach ( $this->_meta_box['pages'] as $page ) {
            add_meta_box( $this->_meta_box['id'], $this->_meta_box['title'], array( $this, 'show' ), $page, $this->_meta_box['context'], $this->_meta_box['priority'] );
        }
	}

	function show() {
        global $post;

        // Use nonce for verification
        echo '<input type="hidden" name="twp_meta_box_nonce" id="twp_meta_box_nonce" value="', wp_create_nonce( basename(__FILE__) ), '" />';

        echo '<table class="form-table twp_metabox">';

        foreach ( $this->_meta_box['fields'] as $field ) {
            // get current post meta data
            $meta = get_post_meta( $post->ID, $field['id'], true );

            echo '<tr>',
                    '<th style="width:20%"><label for="', $field['id'], '">', $field['name'], '</label></th>',
                    '<td>';
            switch ( $field['type'] ) {
                case 'text':
                    echo '<input type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:97%" />',
                        '<br />', $field['desc'];
                    break;
                case 'textarea':
                    echo '<textarea name="', $field['id'], '" id="', $field['id'], '" cols="60" rows="4" style="width:97%">', $meta ? $meta : $field['std'], '</textarea>',
                        '<br />', $field['desc'];
                    break;
                case 'select':
                	if ( $field['options'] ) {
                		echo '<select name="', $field['id'], '" id="', $field['id'], '">';
	                    foreach ( $field['options'] as $option ) {
	                        echo '<option', $meta == $option['value'] ? ' selected="selected"' : '', ' value="', $option['value'], '">', __( $option['label'], 'theatrewp' ), '</option>';
	                    }
	                    echo '</select>';
                	} else {
                		echo __('You have no shows registered yet.', 'theatrewp');
                	}
                    break;
                case 'radio':
                    foreach ($field['options'] as $option) {
                        echo '<input type="radio" name="', $field['id'], '" value="', $option['value'], '"', $meta == $option['value'] ? ' checked="checked"' : '', ' />', $option['name'];
                    }
                    break;
                case 'checkbox':
                    echo '<input type="checkbox" name="', $field['id'], '" id="', $field['id'], '"', $meta ? ' checked="checked"' : '', ' />';
                    break;
                case 'multicheckbox':
                    $n = 0;
                    foreach( $field['options'] as $option ) {
                        echo '<input type="checkbox" name="', $field['id'], '[]', '" id="', $field['id'], '"', 'value="', $option['id'], '" ', ( is_array($meta) && in_array( $option['id'], $meta ) ) ? ' checked="checked"' : '', ' /> ', $option['title'], '<br />';
                        $n++;
                    }
                    break;
                	case 'text_datetime_timestamp':
                        $valid_dateformat = TWP_Setup::date_format_php_to_form( get_option( 'date_format') );
						echo '<input class="twp_text_small twp_datepicker" type="text" name="', $field['id'], '[date]" id="', $field['id'], '_date" value="', $meta ? date( $valid_dateformat , $meta ) : $field['std'], '" />';
						echo '<input class="twp_timepicker text_time" type="text" name="', $field['id'], '[time]" id="', $field['id'], '_time" value="', $meta ? date( 'h:i A', $meta ) : $field['std'], '" /><span class="twp_metabox_description" >', $field['desc'], '</span>';
						break;
					case 'wysiwyg':
						wp_editor( $meta ? $meta : $field['std'], $field['id'], isset( $field['options'] ) ? $field['options'] : array() );
				        echo '<p class="cmb_metabox_description">', $field['desc'], '</p>';
						break;
            }
            echo     '<td>',
                '</tr>';
        }

        echo '</table>';

        return;
    }

    function save( $post_id ) {
        // verify nonce
        if ( ! isset( $_POST['twp_meta_box_nonce'] ) ) {
        	return $post_id;
        }

    	if ( ! wp_verify_nonce( $_POST['twp_meta_box_nonce'], basename( __FILE__ ) ) ) {
    		return $post_id;
    	}

        // check autosave
    	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
    		return $post_id;
    	}

        // check permissions
    	if ( 'page' == $_POST['post_type'] ) {
    		if ( ! current_user_can( 'edit_page', $post_id ) ) {
    			return $post_id;
    		}
    	} elseif ( ! current_user_can( 'edit_post', $post_id ) ) {
    		return $post_id;
    	}

    	foreach ( $this->_meta_box['fields'] as $field ) {
    		$old = get_post_meta( $post_id, $field['id'], true );
    		$new = ( isset( $_POST[$field['id']] ) ? $_POST[$field['id']] : false );

    		if ( $field['type'] == 'text_datetime_timestamp' ) {
    			if ( ! empty( $new['date'] ) ) {
                    if ( empty( $new['time'] ) ) {
                        $new['time'] = '00:00';
                    }

    				$string = $new['date'] . ' ' . $new['time'];
					$new = strtotime( $string );
    			} else {
    				$new = '';
    			}
			}

    		if ( $new && $new != $old ) {
    			update_post_meta( $post_id, $field['id'], $new );
    		} elseif ( '' == $new && $old ) {
    			delete_post_meta( $post_id, $field['id'], $old );
    		}
    	}
    }

}
