<?php
namespace TheatreWP;

if ( realpath( __FILE__ ) === realpath( $_SERVER["SCRIPT_FILENAME"] ) )
	exit ( 'Do not access this file directly.' );

class Metabox {

	protected array $_meta_boxes;
	protected string $_current_post_type;

	public function __construct($_current_post_type) {

		$this->_current_post_type = $_current_post_type;

        add_action('admin_init', array( $this, 'twp_define_metaboxes' ) );

	}

	/**
	 * Define metaboxes
	 *
	 * @access public
	 * @return void
	 */
	public function twp_define_metaboxes() {
		$this->_meta_boxes['spectacle'] = array(
				'id'       => 'spectacle-meta-box',
				'title'    => __('Spectacle Options', 'theatre-wp' ),
				'pages'    => array( 'spectacle' ),
				'context'  => 'normal',
				'priority' => 'high',
				'fields'   => array(
					array(
						'name' => __( 'Synopsis', 'theatre-wp' ),
						'desc' => __( 'Short description', 'theatre-wp' ),
						'id' => Setup::$twp_prefix . 'synopsis',
						'type' => 'textarea',
						'std' => ''
					),
					array(
						'name' => __( 'Audience', 'theatre-wp' ),
						'desc' => __( 'Intended Audience', 'theatre-wp' ),
						'id' => Setup::$twp_prefix . 'audience',
						'type' => 'select',
						'options' => apply_filters( 'twp_define_audiences', Spectacle::$audience ) /* twp_define_audiences filter */
					),
					array(
						'name' => __( 'Duration', 'theatre-wp' ),
						'desc' => __( 'Duration in minutes', 'theatre-wp' ),
						'id' => Setup::$twp_prefix . 'duration',
						'type' => 'text',
						'std' => ''
					),
					array(
						'name' => __( 'Credits', 'theatre-wp' ),
						'desc' => __( 'Credits Titles', 'theatre-wp' ),
						'id'   => Setup::$twp_prefix . 'credits',
						'type' => 'wysiwyg',
						'std'  => '',
						'options' => ['media_buttons' => false] // wysiwyg editor options
					),
					array(
						'name' => __( 'Sheet', 'theatre-wp' ),
						'desc' => __( 'Technical Sheet', 'theatre-wp' ),
						'id'   => Setup::$twp_prefix . 'sheet',
						'type' => 'textarea',
						'std'  => ''
					),
					array(
						'name' => __( 'Sponsors', 'theatre-wp' ),
						'desc' => __( 'Sponsors', 'theatre-wp' ),
						'id' => Setup::$twp_prefix . 'prod-sponsor',
						'type' => ( Setup::$default_options['twp_single_sponsor'] == 1 ? 'sponsorselect' : 'multicheckbox' ),
						'options' => Sponsor::get_sponsors_titles()
					),
					array(
						'name' => __( 'Video', 'theatre-wp' ),
						'desc' => __( 'Video Code. The code of the video in YouTube or Vimeo', 'theatre-wp' ),
						'id'   => Setup::$twp_prefix . 'video',
						'type' => 'textarea',
						'std'  => ''
					)
				)
			);

		$this->_meta_boxes['performance'] = array (
				'id'       => 'performance-meta-box',
				'title'    => __( 'Performance Options', 'theatre-wp' ),
				'pages'    => array( 'performance' ),
				'context'  => 'normal',
				'priority' => 'high',
				'fields'   => array(
					array(
						'name'    => __( 'Show', 'theatre-wp' ),
						'desc'    => __( 'Performing Show', 'theatre-wp' ),
						'id'      => Setup::$twp_prefix . 'spectacle_id',
						'type'    => 'select',
						'options' => Spectacle::get_spectacles_array()
					),
					array(
						'name' => __( 'First date', 'theatre-wp' ),
						'desc' => __( 'First performing date. [Date selection / Time]', 'theatre-wp' ),
						'id'   => Setup::$twp_prefix . 'date_first',
						'type' => 'text_datetime_timestamp',
						'std'  => '',
						// jQuery date picker options. See here http://jqueryui.com/demos/datepicker
						'js_options' => array(
							'appendText'	=> '(yyyy-mm-dd)',
							'autoSize'		=> true,
							'buttonText'	=> __( 'Select Date', 'theatre-wp' ),
							'dateFormat'	=> __( 'dd-mm-yyyy', 'theatre-wp' ),
							'showButtonPanel' => true
						)
					),
					array(
						'name' => __( 'Last date', 'theatre-wp' ),
						'desc' => __( 'Last performing date. [Date selection / Time]', 'theatre-wp' ),
						'id'   => Setup::$twp_prefix . 'date_last',
						'type' => 'text_datetime_timestamp',
						'std'  => ''
					),
					array(
						'name' => __( 'Event', 'theatre-wp' ),
						'desc' => __( 'Event in which the show is performed (Festival, Arts Program...)', 'theatre-wp' ),
						'id'   => Setup::$twp_prefix . 'event',
						'type' => 'text',
						'std'  => ''
					),
					array(
						'name' => __( 'Stage', 'theatre-wp' ),
						'desc' => __( 'Where is the Show to be played (Theatre)', 'theatre-wp' ),
						'id'   => Setup::$twp_prefix . 'place',
						'type' => 'text',
						'std'  => ''
					),
					array(
						'name' => __( 'Theatre Address', 'theatre-wp' ),
						'desc' => '',
						'id'   => Setup::$twp_prefix . 'address',
						'type' => 'text',
						'std'  => ''
					),
					array(
						'name' => __( 'Postal Code', 'theatre-wp' ),
						'desc' => '',
						'id'   => Setup::$twp_prefix . 'postal_code',
						'type' => 'text',
						'std'  => ''
					),
					array(
						'name' => __( 'Town', 'theatre-wp' ),
						'desc' => __( 'Performing in this Town', 'theatre-wp' ),
						'id'   => Setup::$twp_prefix . 'town',
						'type' => 'text',
						'std'  => ''
					),
					array(
						'name' => __( 'Region', 'theatre-wp' ),
						'desc' => __( 'e.g. Province, County...', 'theatre-wp' ),
						'id'   => Setup::$twp_prefix . 'region',
						'type' => 'text',
						'std'  => ''
					),
					array(
						'name' => __( 'Country', 'theatre-wp' ),
						'desc' => '',
						'id'   => Setup::$twp_prefix . 'country',
						'type' => 'text',
						'std'  => ''
					),
					array(
						'name' => __( 'Display Map', 'theatre-wp' ),
						'desc' => __( 'Check to display map', 'theatre-wp' ),
						'id'   => Setup::$twp_prefix . 'display_map',
						'type' => 'checkbox',
						'std'  => ''
					)
				)
			);
		$this->_meta_boxes['sponsor'] = array(
				'id' => 'sponsor-meta-box',
				'title' => __( 'Sponsor', 'theatre-wp' ),
				'pages' => array( 'sponsor' ),
				'context' => 'normal',
				'priority' => 'high',
				'fields' => array(
					array(
						'name' => __( 'Link', 'theatre-wp' ),
						'desc' => __( 'Sponsor Link', 'theatre-wp' ),
						'id'   => Setup::$twp_prefix . 'sponsor-url',
						'type' => 'text',
						'std'  => 'https://'
					),
					array(
						'name' => __( 'Weight', 'theatre-wp' ),
						'desc' => __( 'A number between 0 and 99 to set the importance. 99 is higher', 'theatre-wp' ),
						'id'   => Setup::$twp_prefix . 'sponsor-weight',
						'type' => 'text',
						'std'  => '0'
					)
				)
			);

		// Add additional performance metaboxes if tickets info option is enabled
		if ( get_option( 'twp_tickets_info' ) == 1 ) {
			$tickets_info_metabox_url = array(
				'name' => __( 'Tickets URL', 'theatre-wp' ),
				'desc' => __( 'Link to tickets sales', 'theatre-wp' ),
				'id'   => Setup::$twp_prefix . 'tickets_url',
				'type' => 'text',
				'std'  => ''
			);

			$tickets_info_metabox_price = array(
				'name' => __( 'Price', 'theatre-wp' ),
				'desc' => __( 'Tickets price', 'theatre-wp' ),
				'id'   => Setup::$twp_prefix . 'tickets_price',
				'type' => 'text',
				'std'  => ''
			);

			$tickets_info_metabox_entrance = array(
				'name' => __( 'Free entrance', 'theatre-wp' ),
				'desc' => __( 'Free entrance', 'theatre-wp' ),
				'id'   => Setup::$twp_prefix . 'free_entrance',
				'type' => 'checkbox',
				'std'  => ''
			);

			$tickets_info_metabox_invitation = array(
				'name' => __( 'Invitation needed', 'theatre-wp' ),
				'desc' => __( 'Invitation needed', 'theatre-wp' ),
				'id'   => Setup::$twp_prefix . 'invitation',
				'type' => 'checkbox',
				'std'  => ''
			);

			$this->_meta_boxes['performance']['fields'][] = $tickets_info_metabox_url;
			$this->_meta_boxes['performance']['fields'][] = $tickets_info_metabox_price;
			$this->_meta_boxes['performance']['fields'][] = $tickets_info_metabox_entrance;
			$this->_meta_boxes['performance']['fields'][] = $tickets_info_metabox_invitation;
		}
	}

	public function add() {
		add_meta_box(
			$this->_meta_boxes[$this->_current_post_type]['id'], $this->_meta_boxes[$this->_current_post_type]['title'], array( $this, 'show' ), $this->_meta_boxes[$this->_current_post_type]['pages'][0],
			$this->_meta_boxes[$this->_current_post_type]['context'], $this->_meta_boxes[$this->_current_post_type]['priority']
		);
	}

	public function show() {
		global $post;

		// Use nonce for verification
		echo '<input type="hidden" name="twp_meta_box_nonce" id="twp_meta_box_nonce" value="', wp_create_nonce( basename(__FILE__) ), '" />';

		echo '<table class="form-table twp_metabox">';

		foreach ( $this->_meta_boxes[$this->_current_post_type]['fields'] as $field ) {
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
							echo '<option', $meta == $option['value'] ? ' selected="selected"' : '', ' value="', $option['value'], '">', __( $option['label'], 'theatre-wp' ), '</option>';
						}
						echo '</select>';
					} else {
						echo __('You have no shows registered yet.', 'theatre-wp');
					}
					break;
				case 'sponsorselect':
					if ( $field['options'] ) {
						echo '<select name="', $field['id'], '[]', '" id="', $field['id'], '" size="3" multiple="multiple">';
						foreach ( $field['options'] as $option ) {
							echo '<option', ( is_array( $meta ) && in_array( $option['id'], $meta ) || $meta == $option['id'] ) ? ' selected="selected"' : '', ' value="', $option['id'], '">', __( $option['title'], 'theatre-wp' ), '</option>';
						}
						echo '</select>';
					} else {
						echo __('You have no Sponsors registered yet.', 'theatre-wp');
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
						echo '<input type="checkbox" name="', $field['id'], '[]', '" id="', $field['id'], '"', 'value="', $option['id'], '" ', ( is_array( $meta ) && in_array( $option['id'], $meta ) || $meta == $option['id'] ) ? ' checked="checked"' : '', ' /> ', $option['title'], '<br />';
						$n++;
					}
					break;
                case 'text_datetime_timestamp':
					$valid_dateformat = Setup::date_format_php_to_form( get_option( 'date_format') );
					echo '<input class="twp_text_small twp_datepicker" type="text" name="', $field['id'], '[date]" id="', $field['id'], '_date" value="', $meta ? date( $valid_dateformat , $meta ) : $field['std'], '" />';
					echo '<input class="twp_timepicker text_time" type="text" name="', $field['id'], '[time]" id="', $field['id'], '_time" value="', $meta ? date( 'h:i A', $meta ) : $field['std'], '" /><span class="twp_metabox_description" >', $field['desc'], '</span>';
					break;
				case 'wysiwyg':
					wp_editor( $meta ? $meta : $field['std'], $field['id'], $field['options'] ?? array() );
					echo '<p class="cmb_metabox_description">', $field['desc'], '</p>';
					break;
			}
			echo     '<td>',
				'</tr>';
		}

		echo '</table>';
	}

	public function save( $post_id ) {
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

		foreach ( $this->_meta_boxes[get_post_type($post_id)]['fields'] as $field ) {
			$old = get_post_meta( $post_id, $field['id'], true );
			$new = ( $_POST[ $field['id'] ] ?? false );

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

			if ( $field['type'] === 'wysiwyg' ) {
				$new =  $_POST[ $field['id'] ];
			}

			if ( $new && $new != $old ) {
				update_post_meta( $post_id, $field['id'], $new );
			} elseif ( '' == $new && $old ) {
				delete_post_meta( $post_id, $field['id'], $old );
			}
		}
	}

}
