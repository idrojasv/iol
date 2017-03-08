<?php

class MailsterForm {

	private $values = array();
	private $scheme = 'http';
	private $object = array(
		'userdata' => array(),
		'lists' => array(),
		'errors' => array(),
	);
	private $lists = array();
	private $message = '';

	private $form = null;
	private $campaignID = null;
	private $cache = true;
	private $honeypot = true;
	private $hash = null;
	private $profile = false;
	private $preview = false;
	private $ajax = true;
	private $embed_style = true;
	private $form_endpoint = 'subscribe';
	private $classes = array( 'mailster-form', 'mailster-form-submit' );
	private $redirect = false;
	private $referer = true;
	private $extern = false;

	static $add_script = false;
	static $add_style = false;

	public function __construct() {
		$this->scheme = is_ssl() ? 'https' : 'http';
		$this->honeypot = ! is_admin();
		$this->form = new StdClass();
	}


	/**
	 *
	 *
	 * @param unknown $message
	 * @param unknown $field   (optional)
	 */
	public function set_error( $message, $field = '_' ) {
		$this->object['errors'][ $field ] = (string) $message;
	}


	/**
	 *
	 *
	 * @param unknown $message
	 * @param unknown $field   (optional)
	 */
	public function set_success( $message, $field = '_' ) {
		$this->object['success'][ $field ] = (string) $message;
	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @return unknown
	 */
	public function id( $id ) {

		$this->ID = $id;
		$this->form = mailster( 'forms' )->get( $this->ID, true, true );
		if ( ! $this->form ) {
			$this->form = $this->form = mailster( 'forms' )->get( 1, true, true );
		}

		$this->ajax();
		return $this;
	}


	/**
	 *
	 *
	 * @param unknown $bool (optional)
	 */
	public function is_preview( $bool = true ) {

		$this->preview = ! ! $bool;
	}


	/**
	 *
	 *
	 * @param unknown $bool (optional)
	 */
	public function ajax( $bool = true ) {

		$this->ajax = ! ! $bool;
		( $bool ) ? $this->add_class( 'mailster-ajax-form' ) : $this->remove_class( 'mailster-ajax-form' );
	}


	/**
	 *
	 *
	 * @param unknown $class
	 */
	public function add_class( $class ) {

		$this->classes[] = esc_attr( $class );
		$this->classes = array_unique( $this->classes );
	}


	/**
	 *
	 *
	 * @param unknown $class
	 */
	public function remove_class( $class ) {

		if ( ( $key = array_search( $class, $this->classes ) ) !== false ) {
			unset( $this->classes[ $key ] );
		}

	}


	/**
	 *
	 *
	 * @param unknown $value
	 */
	public function redirect( $value ) {

		$this->redirect = $value;
	}


	/**
	 *
	 *
	 * @param unknown $key
	 * @param unknown $args
	 */
	public function __call( $key, $args ) {

		$value = empty( $args ) ? true : $args[0];

		if ( isset( $this->form->{$key} ) ) {
			$this->form->{$key} = $value;
		} else {
			$this->{$key} = $value;
		}

	}


	/**
	 *
	 *
	 * @param unknown $key
	 * @return unknown
	 */
	public function __get( $key ) {

		if ( isset( $this->form->{$key} ) ) {
			return $this->form->{$key};
		}

		return null;
	}


	/**
	 *
	 *
	 * @param unknown $echo (optional)
	 * @return unknown
	 */
	public function render( $echo = true ) {

		if ( ! $this->form ) {
			return;
		}

		add_action( 'wp_footer', array( &$this, 'print_script' ) );
		add_action( 'admin_footer', array( &$this, 'print_script' ) );

		if ( $this->unsubscribe ) {
			return $this->unsubscribe_form();
		}

		if ( $this->prefill ) {

			$current_user = wp_get_current_user();
			if ( $current_user->ID != 0 ) {
				$this->object['userdata']['email'] = $current_user->user_email;
				$this->object['userdata']['firstname'] = get_user_meta( $current_user->ID, 'first_name', true );
				$this->object['userdata']['lastname'] = get_user_meta( $current_user->ID, 'last_name', true );
				if ( ! $this->object['userdata']['firstname'] ) {
					$this->object['userdata']['firstname'] = $current_user->display_name;
				}

				$this->cache( false );
			}
		}
		if ( $this->profile ) {
			if ( $subscriber = mailster( 'subscribers' )->get_by_hash( $this->hash, true ) ) {
				$this->object['userdata'] = (array) $subscriber;
			}
		}

		if ( isset( $_GET['userdata'] ) ) {
			$this->object['userdata'] = wp_parse_args( $this->object['userdata'], $_GET['userdata'] );
		}

		if ( isset( $_GET['mailster_error'] ) ) {
			// for non ajax request
			$transient = 'mailster_error_' . esc_attr( $_GET['mailster_error'] );
			$data = get_transient( $transient );
			if ( $data ) {
				$this->object['userdata'] = $data['userdata'];
				$this->object['errors'] = $data['errors'];
				$this->object['lists'] = $data['lists'];
				$this->has_errors( ! ! count( $this->object['errors'] ) );
				delete_transient( $transient );
			}
		}

		$html = '';
		// $html .= '<!-- Begin Mailster Form -->'."\n";
		$html .= '%%STYLES%%';

		$html .= '<form action="%%FORMACTION%%" method="post" class="mailster-form-%%FORMID%% %%CLASSES%%">';
		$html .= '%%INFOS%%';
		$html .= '%%HIDDENFIELDS%%';

		$customfields = mailster()->get_custom_fields();
		$inline = $this->form->inline;
		$asterisk = $this->form->asterisk;

		$fields = array();

		foreach ( $this->form->fields as $field ) {

			$required = $field->required;

			$label = ! empty( $field->name ) ? $field->name : mailster_text( $field->field_id );
			$esc_label = esc_attr( strip_tags( $label ) );

			$value = ( isset( $this->object['userdata'][ $field->field_id ] )
				? esc_attr( $this->object['userdata'][ $field->field_id ] )
				: '' );

			$class = ( isset( $this->object['errors'][ $field->field_id ] ) ? ' error' : '' );

			switch ( $field->field_id ) {

				case 'email':

					$fields['email'] = '<div class="mailster-wrapper mailster-email-wrapper' . $class . '">';
					if ( ! $inline ) {
						$fields['email'] .= '<label for="mailster-email-' . $this->ID . '">' . $label . ' ' . ( $asterisk ? '<span class="mailster-required">*</span>' : '' ) . '</label>';
					}

					$fields['email'] .= '<input id="mailster-email-' . $this->ID . '" name="email" type="email" value="' . $value . '"' . ( $inline ? ' placeholder="' . $esc_label . ( $asterisk ? ' *' : '' ) . '"' : '' ) . ' class="input mailster-email mailster-required" aria-required="' . ( $required ? 'true' : 'false' ) . '" aria-label="' . $esc_label . '" spellcheck="false">';
					$fields['email'] .= '</div>';

				break;

				case 'firstname':
				case 'lastname':

					$fields[ $field->field_id ] = '<div class="mailster-wrapper mailster-' . $field->field_id . '-wrapper' . $class . '">';
					if ( ! $inline ) {
						$fields[ $field->field_id ] .= '<label for="mailster-' . $field->field_id . '-' . $this->ID . '">' . $label . ( $required && $asterisk ? ' <span class="mailster-required">*</span>' : '' ) . '</label>';
					}

					$fields[ $field->field_id ] .= '<input id="mailster-' . $field->field_id . '-' . $this->ID . '" name="' . $field->field_id . '" type="text" value="' . $value . '"' . ( $inline ? ' placeholder="' . $esc_label . ( $required && $asterisk ? ' *' : '' ) . '"' : '' ) . ' class="input mailster-' . $field->field_id . '' . ( $required ? ' mailster-required' : '' ) . '" aria-required="' . ( $required ? 'true' : 'false' ) . '" aria-label="' . $esc_label . '">';
					$fields[ $field->field_id ] .= '</div>';

				break;

				// custom fields
				default:

					if ( ! isset( $customfields[ $field->field_id ] ) ) {
						break;
					}

					$data = $customfields[ $field->field_id ];

					// $label = isset($form->labels[$field->field_id]) ? $form->labels[$field->field_id] : $data['name'];
					// $esc_label = esc_attr(strip_tags($label));
					$fields[ $field->field_id ] = '<div class="mailster-wrapper mailster-' . $field->field_id . '-wrapper' . $class . '">';

					$showlabel = ! $inline;

					switch ( $data['type'] ) {
						case 'dropdown':case 'radio':$showlabel = true;
						break;
						case 'checkbox':$showlabel = false;
						break;
					}

					if ( $showlabel ) {
						$fields[ $field->field_id ] .= '<label for="mailster-' . $field->field_id . '-' . $this->ID . '">' . $label;
						if ( $required && $asterisk ) {
							$fields[ $field->field_id ] .= ' <span class="mailster-required">*</span>';
						}

						$fields[ $field->field_id ] .= '</label>';
					}

					switch ( $data['type'] ) {

						case 'dropdown':

							$fields[ $field->field_id ] .= '<select id="mailster-' . $field->field_id . '-' . $this->ID . '" name="' . $field->field_id . '" class="input mailster-' . $field->field_id . '' . ( $required ? ' mailster-required' : '' ) . '" aria-required="' . ( $required ? 'true' : 'false' ) . '" aria-label="' . $esc_label . '">';
							foreach ( $data['values'] as $v ) {
								if ( ! isset( $data['default'] ) ) {
									$data['default'] = $value;
								}

								$fields[ $field->field_id ] .= '<option value="' . $v . '" ' . selected( $data['default'], $v, false ) . '>' . $v . '</option>';
							}
							$fields[ $field->field_id ] .= '</select>';
						break;

						case 'radio':

							$fields[ $field->field_id ] .= '<ul class="mailster-list">';
							$i = 0;
							foreach ( $data['values'] as $v ) {
								if ( ! isset( $data['default'] ) ) {
									$data['default'] = $value;
								}

								$fields[ $field->field_id ] .= '<li><label><input id="mailster-' . $field->field_id . '-' . $this->ID . '-' . ( $i++ ) . '" name="' . $field->field_id . '" type="radio" value="' . $v . '" class="radio mailster-' . $field->field_id . '' . ( $required ? ' mailster-required' : '' ) . '" ' . checked( $data['default'], $v, false ) . ' aria-label="' . $v . '"> ' . $v . '</label></li>';

							}
							$fields[ $field->field_id ] .= '</ul>';
						break;

						case 'checkbox':

							$fields[ $field->field_id ] .= '<label for="mailster-' . $field->field_id . '-' . $this->ID . '">';
							$fields[ $field->field_id ] .= '<input type="hidden" name="' . $field->field_id . '" value="0"><input id="mailster-' . $field->field_id . '-' . $this->ID . '" name="' . $field->field_id . '" type="checkbox" value="1" ' . checked( $value || ( ! $value && isset( $data['default'] ) ), true, false ) . ' class="mailster-' . $field->field_id . '' . ( $required ? ' mailster-required' : '' ) . '" aria-required="' . ( $required ? 'true' : 'false' ) . '" aria-label="' . $esc_label . '"> ';
							$fields[ $field->field_id ] .= ' ' . $label;
							if ( $required && $asterisk ) {
								$fields[ $field->field_id ] .= ' <span class="mailster-required">*</span>';
							}

							$fields[ $field->field_id ] .= '</label>';

						break;

						case 'date':

							$fields[ $field->field_id ] .= '<input id="mailster-' . $field->field_id . '-' . $this->ID . '" name="' . $field->field_id . '" type="text" value="' . $value . '"' . ( $inline ? ' placeholder="' . $esc_label . ( $required && $asterisk ? ' *' : '' ) . '"' : '' ) . ' class="input input-date datepicker mailster-' . $field->field_id . '' . ( $required ? ' mailster-required' : '' ) . '" aria-required="' . ( $required ? 'true' : 'false' ) . '" aria-label="' . $esc_label . '">';

						break;

						case 'textarea':

							$fields[ $field->field_id ] .= '<textarea id="mailster-' . $field->field_id . '-' . $this->ID . '" name="' . $field->field_id . '"' . ( $inline ? ' placeholder="' . $label . ( $required && $asterisk ? ' *' : '' ) . '"' : '' ) . ' class="input mailster-' . $field->field_id . '' . ( $required ? ' mailster-required' : '' ) . '" aria-required="' . ( $required ? 'true' : 'false' ) . '" aria-label="' . $label . '">' . esc_textarea( $value ) . '</textarea>';

						break;

						default:

							$fields[ $field->field_id ] .= '<input id="mailster-' . $field->field_id . '-' . $this->ID . '" name="' . $field->field_id . '" type="text" value="' . $value . '"' . ( $inline ? ' placeholder="' . $esc_label . ( $required && $asterisk ? ' *' : '' ) . '"' : '' ) . ' class="input mailster-' . $field->field_id . '' . ( $required ? ' mailster-required' : '' ) . '" aria-required="' . ( $required ? 'true' : 'false' ) . '" aria-label="' . $esc_label . '">';

					}

					$fields[ $field->field_id ] .= '</div>';

			}
		}

		if ( $this->form->userschoice ) {
			$lists = mailster( 'forms' )->get_lists( $this->ID );

			if ( ! empty( $lists ) ) {

				if ( $this->profile ) {
					$userlists = mailster( 'subscribers' )->get_lists( $this->object['userdata']['ID'], true );
				}

				$fields['lists'] = '<div class="mailster-wrapper mailster-lists-wrapper' . $class . '"><label>' . mailster_text( 'lists', __( 'Lists', 'mailster' ) ) . '</label>';

				if ( $this->form->dropdown ) {
					$fields['lists'] .= '<select name="lists[]" class="input mailster-lists-dropdown">';
					foreach ( $lists as $list ) {
						$selected = ! empty( $this->object['errors'] ) && in_array( $list->ID, $this->object['lists'] );

						$fields['lists'] .= '<option value="' . $list->ID . '"' . selected( $selected, true, false ) . '> ' . $list->name . '</option>';
					}
					$fields['lists'] .= '</select>';
				} else {
					$fields['lists'] .= '<ul class="mailster-list">';
					foreach ( $lists as $i => $list ) {
						$checked = ( empty( $this->object['errors'] ) && $this->form->precheck )
							|| ( ! empty( $this->object['errors'] ) && in_array( $list->ID, $this->object['lists'] ) )
							|| ( $this->form->precheck && $this->preview );

						if ( $this->profile ) {
							$checked = in_array( $list->ID, $userlists );
						}

						$fields['lists'] .= '<li><label title="' . esc_attr( $list->description ) . '"><input type="hidden" name="lists[' . $i . ']" value="0"><input class="mailster-list mailster-list-' . $list->slug . '" type="checkbox" name="lists[' . $i . ']" value="' . $list->ID . '" ' . checked( $checked, true, false ) . ' aria-label="' . esc_attr( $list->name ) . '"> ' . $list->name;
						if ( $list->description ) {
							$fields['lists'] .= ' <span class="mailster-list-description mailster-list-description-' . $list->ID . '">' . $list->description . '</span>';
						}

						$fields['lists'] .= '</label></li>';

					}
					$fields['lists'] .= '</ul>';
				}

				$fields['lists'] .= '</div>';
			}
		}

		$buttonlabel = esc_attr( strip_tags( $this->form->submit ) );

		$fields['_submit'] = '<div class="mailster-wrapper mailster-submit-wrapper form-submit"><input name="submit" type="submit" value="' . $buttonlabel . '" class="submit-button button" aria-label="' . $buttonlabel . '"></div>';

		// if($cache) set_transient( $transient, $fields );
		$fields = apply_filters( 'mymail_form_fields', apply_filters( 'mailster_form_fields', $fields, $this->ID, $this->form ), $this->ID, $this->form );

		if ( $this->honeypot ) {
			$position = rand( count( $fields ), 0 ) - 1;
			$fields = array_slice( $fields, 0, $position, true ) +
				array( '_honeypot' => '<label style="position:absolute;top:-99999px;' . ( is_rtl() ? 'right' : 'left' ) . ':-99999px;z-index:-99;"><input name="n_' . wp_create_nonce( 'honeypot' ) . '_email" type="email" tabindex="-1" autocomplete="off"></label>' ) +
				array_slice( $fields, $position, null, true );
		}

		$html .= '<div class="mailster-form-fields">';
		$html .= "\n" . implode( "\n", $fields ) . "\n";
		$html .= '</div>' . "\n";

		$html .= '</form>' . "\n";

		// $html .= '<!-- End Mailster Form -->';
		$html = str_replace( '%%FORMACTION%%', $this->get_form_action( $this->profile ? 'mailster_profile_submit' : 'mailster_form_submit' ), $html );
		$html = str_replace( '%%CLASSES%%', esc_attr( implode( ' ', $this->classes ) ), $html );
		$html = str_replace( '%%FORMID%%', $this->ID, $html );
		$html = str_replace( '%%STYLES%%', $this->get_styles(), $html );
		$html = str_replace( '%%HIDDENFIELDS%%', $this->get_hidden_fields(), $html );
		$html = str_replace( '%%INFOS%%', $this->get_info(), $html );

		$html = apply_filters( 'mymail_form', apply_filters( 'mailster_form', $html, $this->ID, $this->form ), $this->ID, $this->form );

		if ( ! $echo ) {
			return $html;
		}

		echo $html;

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	private function get_styles() {

		$html = '';
		if ( ! self::$add_style ) {
			ob_start();
			$this->print_style( $this->embed_style );
			$html .= ob_get_contents();
			ob_end_clean();

		}
		if ( isset( $this->form->stylesheet ) && ! empty( $this->form->stylesheet ) ) {
			$html .= '<style type="text/css" media="screen" class="mailster-custom-form-css">' . "\n" . $this->strip_css( $this->form->stylesheet ) . '</style>' . "\n";
		}

		return $html;
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	private function get_hidden_fields() {

		global $pagenow;

		$html = '';

		$redirect = esc_url( home_url( remove_query_arg( array( 'mailster_error', 'mailster_success' ), $_SERVER['REQUEST_URI'] ) ) );
		$referer = $pagenow == 'form.php' ? ( isset( $_GET['referer'] ) ? $_GET['referer'] : 'extern' ) : $redirect;

		if ( $this->redirect ) {
			$html .= '<input name="_redirect" type="hidden" value="' . esc_attr( is_string( $this->redirect ) ? $this->redirect : $redirect ) . '">' . "\n";
		}

		if ( $this->referer ) {
			$html .= '<input name="_referer" type="hidden" value="' . esc_attr( is_string( $this->referer ) ? $this->referer : $referer ) . '">' . "\n";
		}

		if ( $this->hash ) {
			$html .= '<input name="_hash" type="hidden" value="' . esc_attr( $this->hash ) . '">' . "\n";
		}

		$html .= '<input name="formid" type="hidden" value="' . $this->ID . '">' . "\n";

		return $html;
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	private function get_info() {

		$html = '';

		if ( ! empty( $this->object['success'] ) ) :
			$html .= '<div class="mailster-form-info success">';
			$html .= $this->get_message( 'success' );
			$html .= $this->message;
			$html .= '</div>' . "\n";
		endif;
		if ( ! empty( $this->object['errors'] ) ) :
			$html .= '<div class="mailster-form-info error">';
			$html .= $this->get_message();
			$html .= '</div>' . "\n";
		endif;

		return $html;

	}


	/**
	 *
	 *
	 * @param unknown $bool (optional)
	 */
	public function is_profile( $bool = true ) {

		if ( $bool ) {

			$this->profile = true;
			$this->form_endpoint = 'update';
			$this->form->submit = mailster_text( 'profilebutton', __( 'Update Profile', 'mailster' ) );
			$this->add_class( 'is-profile' );
			$this->set_hash();

		} else {

			$this->profile = false;
			$this->remove_class( 'is-profile' );
			$this->hash = null;
			$this->form_endpoint = 'subscribe';

		}

	}


	/**
	 *
	 *
	 * @param unknown $ID
	 */
	public function campaign_id( $ID ) {
		$this->campaignID = intval( $ID );
	}


	/**
	 *
	 *
	 * @param unknown $bool (optional)
	 */
	public function is_unsubscribe( $bool = true ) {

		if ( $bool ) {

			$this->form_endpoint = 'unsubscribe';
			$this->unsubscribe = true;
			$this->set_hash();

		} else {

			$this->form_endpoint = 'subscribe';
			$this->unsubscribe = false;
			$this->hash = null;

		}

	}


	/**
	 *
	 *
	 * @param unknown $hash (optional)
	 */
	private function set_hash( $hash = null ) {
		$this->hash = is_null( $hash ) ? $this->get_hash() : $hash;
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	private function get_hash() {

		if ( isset( $_COOKIE['mailster'] ) ) {
			return $_COOKIE['mailster'];
		}

		if ( ! $this->unsubscribe && is_user_logged_in() && ( $subscriber = mailster( 'subscribers' )->get_by_wpid( get_current_user_id() ) ) ) {
			return $subscriber->hash;
		}

		return null;

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	private function unsubscribe_form() {

		$campaign = $this->campaignID ? mailster( 'campaigns' )->get( $this->campaignID ) : null;

		$subscriber = $this->hash ? mailster( 'subscribers' )->get_by_hash( $this->hash ) : null;

		$single_opt_out = mailster_option( 'single_opt_out' );

		$infoclass = '';

		// instant unsubscribe
		if ( $subscriber && $single_opt_out ) {

			if ( mailster( 'subscribers' )->unsubscribe( $subscriber->ID, $this->campaignID ) ) {
				$infoclass = ' success';
				$this->message = '<p>' . mailster_text( 'unsubscribe' ) . '</p>';
				$this->form_endpoint = 'resubscribe';
			} else {
				$infoclass = ' error';
				$this->message = '<p>' . mailster_text( 'unsubscribeerror' ) . '</p>';
			}
		}

		global $post;
		$form_id = '';
		if ( preg_match( '#\[newsletter_signup_form id="?(\d+)"?#', $post->post_content, $form_id ) ) {
			$form_id = intval( $form_id[1] );
			$this->id( $form_id );
		}

		$html = '';

		$html .= $this->get_styles();

		$action = 'mailster_form_unsubscribe';

		$html .= '<form action="' . $this->get_form_action( $action ) . '" method="post" class="mailster-form mailster-form-' . $form_id . ' mailster-form-submit mailster-ajax-form" id="mailster-form-unsubscribe">' . "\n";
		$html .= '<div class="mailster-form-info ' . $infoclass . '">';
		// $html .= $this->get_message();
		$html .= $this->message;
		$html .= '</div>';
		$html .= '<input name="hash" type="hidden" value="' . $this->hash . '">';
		$html .= '<input name="campaign" type="hidden" value="' . $this->campaignID . '">';
		$html .= '<div class="mailster-form-fields">';
		if ( ! $this->hash ) {

			$html .= '<div class="mailster-wrapper mailster-email-wrapper"><label for="mailster-email">' . mailster_text( 'email', __( 'Email', 'mailster' ) ) . ' <span class="mailster-required">*</span></label>';
			$html .= '<input id="mailster-email" class="input mailster-email mailster-required" name="email" type="email" value=""></div>';

		}
		if ( $subscriber && $single_opt_out ) {
		} else {
			$buttontext = mailster_text( 'unsubscribebutton', __( 'Unsubscribe', 'mailster' ) );
			$html .= '<div class="mailster-wrapper mailster-submit-wrapper form-submit"><input name="submit" type="submit" value="' . $buttontext . '" class="submit-button button"></div>';
			$html .= '</div>';
		}
		$html .= '</form>';

		return apply_filters( 'mymail_unsubscribe_form', apply_filters( 'mailster_unsubscribe_form', $html, $this->campaignID ), $this->campaignID );
	}


	public function submit() {

		global $wp;

		$_BASE = $_POST;

		if ( empty( $_BASE ) ) {
			wp_die( 'no data' );
		};

		$submissiontype = isset( $wp->query_vars['_mailster'] ) ? $wp->query_vars['_mailster'] : null;

		if ( ! $submissiontype ) {
			wp_die( 'wrong submissiontype' );
		};

		if ( $this->honeypot ) {
			$honeypotnonce = wp_create_nonce( 'honeypot' );
			$honeypot = isset( $_BASE[ 'n_' . $honeypotnonce . '_email' ] ) ? $_BASE[ 'n_' . $honeypotnonce . '_email' ] : null;

			if ( ! empty( $honeypot ) ) {
				die( 1 );
			}
		}

		$baselink = get_permalink( mailster_option( 'homepage' ) );
		if ( ! $baselink ) {
			$baselink = site_url();
		}

		$referer = isset( $_BASE['_referer'] ) ? $_BASE['_referer'] : $baselink;
		if ( $referer == 'extern' || isset( $_GET['_extern'] ) ) {
			$referer = esc_url( wp_get_referer() );
		}

		$now = time();

		$this->id( isset( $_BASE['formid'] ) ? intval( $_BASE['formid'] ) : 1 );

		$double_opt_in = $this->form->doubleoptin;
		$overwrite = $this->form->overwrite;

		$customfields = mailster()->get_custom_fields();

		$formdata = isset( $_BASE['userdata'] ) ? $_BASE['userdata'] : $_BASE;
		$formdata = apply_filters( 'mymail_pre_submit', apply_filters( 'mailster_pre_submit', $formdata, $this->form ), $this->form );

		foreach ( $this->form->fields as $field_id => $field ) {

			$type = isset( $customfields[ $field_id ] ) ? $customfields[ $field_id ]['type'] : 'textfield';

			$value = isset( $formdata[ $field_id ] ) ? $formdata[ $field_id ] : '';

			$this->object['userdata'][ $field_id ] = ( $type == 'textarea' ? stripslashes( $value ) : sanitize_text_field( $value ) );

			if ( ( $field_id == 'email' && ! mailster_is_email( trim( $this->object['userdata'][ $field_id ] ) ) )
				|| ( ! $this->object['userdata'][ $field_id ] && in_array( $field_id, $this->form->required ) ) ) {
				$this->object['errors'][ $field_id ] = $field->error_msg;
			}
		}

		$this->object['userdata']['email'] = trim( $this->object['userdata']['email'] );

		$this->object['lists'] = $this->form->userschoice
			? ( isset( $_POST['lists'] )
			? (array) $_POST['lists'] : array() )
			: $this->form->lists;

		// to hook into the system
		$this->object = apply_filters( 'mymail_submit', apply_filters( 'mailster_submit', $this->object ) );
		$this->object = apply_filters( 'mymail_submit_' . $this->ID, apply_filters( 'mailster_submit_' . $this->ID, $this->object ) );

		if ( $this->valid() ) {

			$email = $this->object['userdata']['email'];

			$entry = wp_parse_args( array(
					'lang' => mailster_get_lang(),
			), $this->object['userdata'] );

			switch ( $submissiontype ) {

				case 'subscribe':

					$entry = wp_parse_args( array(
						'signup' => $now,
						'confirm' => $double_opt_in ? 0 : $now,
						'status' => $double_opt_in ? 0 : 1,
						'lang' => mailster_get_lang(),
						'referer' => $referer,
						'form' => $this->ID,
					), $this->object['userdata'] );

					if ( $overwrite && $subscriber = mailster( 'subscribers' )->get_by_mail( $entry['email'] ) ) {
						$entry = wp_parse_args( array(
							// change status if other than pending, subscribed or unsubscribed
							'status' => $subscriber->status >= 3 ? 2 : $subscriber->status,
							'ID' => $subscriber->ID,
						), $entry );

							$subscriber_id = mailster( 'subscribers' )->update( $entry, true, true );
							$message = $entry['status'] == 0 ? mailster_text( 'confirmation' ) : mailster_text( 'profile_update' );

					} else {

						$subscriber_id = mailster( 'subscribers' )->add( $entry );
						$message = $double_opt_in ? mailster_text( 'confirmation' ) : mailster_text( 'success' );

					}

				break;

				case 'update':

					$this->set_hash();
					$subscriber = mailster( 'subscribers' )->get_by_hash( $this->hash, true );
					$entry = wp_parse_args( array(
						// change status if other than pending, subscribed or unsubscribed
						'status' => $subscriber->status >= 3 ? 2 : $subscriber->status,
						'ID' => $subscriber->ID,
					), $entry );

						$subscriber_id = mailster( 'subscribers' )->update( $entry, true, true );

						$message = $entry['status'] == 0 ? mailster_text( 'confirmation' ) : mailster_text( 'profile_update' );

				break;
			}

			if ( is_wp_error( $subscriber_id ) ) {

				$error_code = $subscriber_id->get_error_code();

				switch ( $error_code ) {

					case 'email_exists':

						if ( $exists = mailster( 'subscribers' )->get_by_mail( $this->object['userdata']['email'] ) ) {

							$this->object['errors']['email'] = mailster_text( 'already_registered' );

							if ( $exists->status == 0 ) {
								$this->object['errors']['confirmation'] = mailster_text( 'new_confirmation_sent' );
								mailster( 'subscribers' )->send_confirmations( $exists->ID, true, true );

							} elseif ( $exists->status == 1 ) {

								// change status to "pending" if user is other than subscribed
							} elseif ( $exists->status != 1 ) {
								if ( $double_opt_in ) {
									$this->object['errors']['confirmation'] = mailster_text( 'new_confirmation_sent' );
									mailster( 'subscribers' )->change_status( $exists->ID, 0, true );
									mailster( 'subscribers' )->send_confirmations( $exists->ID, true, true );
								} else {
									mailster( 'subscribers' )->change_status( $exists->ID, 1, true );
								}
							}

							mailster( 'subscribers' )->assign_lists( $exists->ID, $this->object['lists'], $submissiontype == 'update' );

						}

					break;

					default:

						$field = isset( $entry[ $error_code ] ) ? $error_code : '_';
						$this->object['errors'][ $field ] = $subscriber_id->get_error_message();

					break;

				}
			} else {

				mailster( 'subscribers' )->assign_lists( $subscriber_id, $this->object['lists'], $submissiontype == 'update' );

				$target = add_query_arg( array(
						'subscribe' => '',
				), $baselink );

			}

			$this->object = apply_filters( 'mymail_post_submit', apply_filters( 'mailster_post_submit', $this->object ) );
			$this->object = apply_filters( 'mymail_post_submit_' . $this->ID, apply_filters( 'mailster_post_submit_' . $this->ID, $this->object ) );

			if ( $this->valid() ) {
				$return = array(
					'success' => true,
					'html' => '<p>' . $message . '</p>',
				);
			} else {
				$return = array(
					'success' => false,
					'fields' => $this->object['errors'],
					'html' => '<p>' . $this->get_message( 'errors', true ) . '</p>',
				);
			}

			if ( $this->form->redirect ) {
				$return = wp_parse_args( array( 'redirect' => $this->form->redirect ), $return );
			}

			// an error occurred
		} else {

			$return = array(
				'success' => false,
				'fields' => $this->object['errors'],
				'html' => $this->get_message(),
			);

		}

		// ajax request
		if ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) ) :

			@header( 'Content-type: application/json' );
			echo json_encode( $return );
			exit;

		endif;

		if ( $this->is_extern() ) {

			if ( ! $return['success'] ) {
				wp_die( $return['html'] . '<a href="javascript:history.back()">' . __( 'Go back', 'mailster' ) . '</a>' );
				exit;
			}

			$target = isset( $return['redirect'] ) ? $return['redirect'] : esc_url( wp_get_referer() );

		} else {

			if ( ! $return['success'] ) {
				wp_die( $return['html'] . '<a href="javascript:history.back()">' . __( 'Go back', 'mailster' ) . '</a>' );
				exit;
			}

			$target = isset( $return['redirect'] ) ? $return['redirect'] : esc_url( wp_get_referer() );

		}

		wp_redirect( $target );
		exit;

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function update() {

		$baselink = get_permalink( mailster_option( 'homepage' ) );
		if ( ! $baselink ) {
			$baselink = site_url();
		}

		$_BASE = $_POST;

		if ( empty( $_BASE ) ) {
			wp_die( 'no data' );
		};

		$referer = isset( $_BASE['_referer'] ) ? $_BASE['_referer'] : $baselink;
		$redirect = isset( $_BASE['_redirect'] ) ? $_BASE['_redirect'] : $baselink;

		$now = time();

		$form_id = mailster_option( 'profile_form', 0 );
		$form = mailster( 'forms' )->get( $form_id );

		$customfields = mailster()->get_custom_fields();
		$subscriber = mailster( 'subscribers' )->get_by_hash( $_BASE['hash'], true );

		foreach ( $form->fields as $field ) {

			$value = esc_attr( isset( $_BASE[ $field->field_id ] )
				? $_BASE[ $field->field_id ]
			: ( isset( $_BASE['userdata'][ $field->field_id ] ) ? $_BASE['userdata'][ $field->field_id ] : '' ) );

			$this->object['userdata'][ $field->field_id ] = ( $type == 'textarea' ? stripslashes( $value ) : sanitize_text_field( $value ) );

			if ( ( $field->field_id == 'email' && ! mailster_is_email( trim( $this->object['userdata'][ $field->field_id ] ) ) ) || ( ! $this->object['userdata'][ $field->field_id ] && in_array( $field->field_id, $form->required ) ) ) {
				$this->object['errors'][ $field->field_id ] = mailster_text( $field->field_id, isset( $customfields[ $field->field_id ]['name'] ) ? $customfields[ $field->field_id ]['name'] : $field->name );
			}
		}

		$this->object['userdata']['email'] = trim( $this->object['userdata']['email'] );

		$this->object['userdata'] = $this->object['userdata'];

		$this->object['lists'] = isset( $_BASE['lists'] ) ? (array) $_BASE['lists'] : array();

		$this->object = apply_filters( 'mymail_submit', apply_filters( 'mailster_submit', $this->object ) );
		$this->object = apply_filters( 'mymail_submit_' . $form_id, apply_filters( 'mailster_submit_' . $form_id, $this->object ) );

		$this->object['userdata']['ID'] = $subscriber->ID;

		if ( $this->valid() ) {
			$email = $this->object['userdata']['email'];

			$this->object['userdata']['updated'] = $now;

			// change status if other than pending, subscribed or unsubscribed
			if ( $subscriber->status >= 3 ) {
				$this->object['userdata']['status'] = 2;
			}

			$subscriber_id = mailster( 'subscribers' )->update( $this->object['userdata'], true, true );

			if ( is_wp_error( $subscriber_id ) ) {

				$this->object['errors']['confirmation'] = $subscriber_id->get_error_message();

			} else {

				if ( isset( $form->userschoice ) ) {
					mailster( 'subscribers' )->assign_lists( $subscriber_id, $this->object['lists'], true );
				}

				$target = add_query_arg( array(
						'subscribe' => '',
				), $baselink );

			}

			$this->object = apply_filters( 'mymail_post_submit', apply_filters( 'mailster_post_submit', $this->object ) );
			$this->object = apply_filters( 'mymail_post_submit_' . $form_id, apply_filters( 'mailster_post_submit_' . $form_id, $this->object ) );

			// redirect if no ajax request
			if ( ! isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) ) {

				$target = ( ! empty( $form->redirect ) )
					? $form->redirect
					: add_query_arg( array( 'mailster_success' => $double_opt_in + 1 ), $redirect );

				$target = apply_filters( 'mymail_profile_update_target', apply_filters( 'mailster_profile_update_target', $target, $form_id ), $form_id );
				wp_redirect( $target );
				exit;

			} else {

				if ( $this->valid() ) {
					$return = array(
						'success' => true,
						'html' => '<p>' . mailster_text( 'profile_update' ) . '</p>',
					);
				} else {
					$return = array(
						'success' => false,
						'fields' => $this->object['errors'],
						'html' => '<p>' . $this->get_message( 'errors', true ) . '</p>',
					);
				}

				@header( 'Content-type: application/json' );
				echo json_encode( $return );
				exit;

			}

			// redirect if no ajax request or extern
			return $target;

			// an error occurred
		} else {

			$return = array(
				'success' => false,
				'fields' => $this->object['errors'],
				'html' => $this->get_message(),
			);

			// stop if no ajax request
			if ( ! isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) ) {

				wp_die( $return['html'] . '<a href="javascript:history.back()">' . __( 'Go back', 'mailster' ) . '</a>' );
				exit;

			}

			@header( 'Content-type: application/json' );
			echo json_encode( $return );
			exit;

		}

	}


	public function unsubscribe() {

		$return['success'] = false;

		$_BASE = $_POST;

		if ( empty( $_BASE ) ) {
			wp_die( 'no data' );
		};

		$campaign_id = ! empty( $_BASE['campaign'] ) ? intval( $_BASE['campaign'] ) : null;

		if ( isset( $_BASE['email'] ) ) {
			$return['success'] = mailster( 'subscribers' )->unsubscribe_by_mail( $_BASE['email'], $campaign_id );
		} elseif ( isset( $_BASE['hash'] ) ) {
				$return['success'] = mailster( 'subscribers' )->unsubscribe_by_hash( $_BASE['hash'], $campaign_id );
		} else {
			// wp_redirect(mailster()->get_unsubscribe_link());
			// exit;
		}

		// redirect if no ajax request
		if ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) ) {

			$return['html'] = $return['success']
				? mailster_text( 'unsubscribe' )
				: ( empty( $_POST['email'] )
				? mailster_text( 'enter_email' )
				: mailster_text( 'unsubscribeerror' ) );

			@header( 'Content-type: application/json' );
			echo json_encode( $return );
			exit;

		} else {

			if ( $return['success'] ) {
				wp_die( $return['html'] . '<a href="javascript:history.back()">' . mailster_text( 'unsubscribe' ) . '</a>' );
			} else {
				wp_die( $return['html'] . '<a href="javascript:history.back()">' . ( empty( $_POST['email'] ) ? mailster_text( 'enter_email' ) : mailster_text( 'unsubscribeerror' ) ) . '</a>' );
			}
			exit;

		}

	}


	/**
	 *
	 *
	 * @param unknown $css
	 * @return unknown
	 */
	public function strip_css( $css ) {
		$css = strip_tags( $css );
		$css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );
		$css = trim( str_replace( array( "\r\n", "\r", "\n", "\t", '   ', '  ' ), '', $css ) );
		$css = str_replace( ' {', '{', $css );
		$css = str_replace( '{ ', '{', $css );
		$css = str_replace( ' }', '}', $css );
		$css = str_replace( '}', '}' . "\n", $css );
		return $css;
	}


	/**
	 *
	 *
	 * @param unknown $action (optional)
	 * @return unknown
	 */
	private function get_form_action( $action = '' ) {

		$is_permalink = mailster( 'helper' )->using_permalinks();

		$prefix = ! mailster_option( 'got_url_rewrite' ) ? '/index.php' : '/';

		return $is_permalink
			? home_url( $prefix . '/mailster/' . $this->form_endpoint )
			: add_query_arg( array( 'action' => $action ), admin_url( 'admin-ajax.php', $this->scheme ) );

	}


	/**
	 *
	 *
	 * @param unknown $type   (optional)
	 * @param unknown $simple (optional)
	 * @return unknown
	 */
	private function get_message( $type = 'errors', $simple = false ) {

		$html = '';
		if ( ! empty( $this->object[ $type ] ) ) {
			if ( ! $simple && $type == 'errors' ) {
				$html .= '<p>' . mailster_text( 'error' ) . '</p>';
			}

			$html .= '<ul>';
			foreach ( $this->object[ $type ] as $field => $name ) {
				$html .= '<li>' . apply_filters( 'mymail_error_output_' . $field, apply_filters( 'mailster_error_output_' . $field, $name, $this->object ), $this->object ) . '</li>';
			}
			$html .= '</ul>';
		}

		return $html;
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	private function is_extern() {
		return parse_url( wp_get_referer(), PHP_URL_HOST ) != parse_url( home_url(), PHP_URL_HOST );
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	private function valid() {
		return empty( $this->object['errors'] );
	}


	public static function print_script() {

		if ( self::$add_script ) {
			return;
		}

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_register_script( 'mailster-form', MAILSTER_URI . 'assets/js/form' . $suffix . '.js', apply_filters( 'mymail_no_jquery', apply_filters( 'mailster_no_jquery', array( 'jquery' ) ), MAILSTER_VERSION, true ), MAILSTER_VERSION, true );
		wp_register_script( 'mailster-form-placeholder', MAILSTER_URI . 'assets/js/placeholder-fix' . $suffix . '.js', apply_filters( 'mymail_no_jquery', apply_filters( 'mailster_no_jquery', array( 'jquery' ) ), MAILSTER_VERSION, true ), MAILSTER_VERSION, true );

		global $is_IE;

		if ( $is_IE ) {
			wp_print_scripts( 'jquery' );
			echo '<!--[if lte IE 9]>';
			wp_print_scripts( 'mailster-form-placeholder' );
			echo '<![endif]-->';
		}

		wp_print_scripts( 'mailster-form' );

		self::$add_script = true;

	}


	/**
	 *
	 *
	 * @param unknown $embed (optional)
	 */
	public static function print_style( $embed = true ) {

		if ( self::$add_style ) {
			return;
		}

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_register_style( 'mailster-form-default', MAILSTER_URI . 'assets/css/form-default-style' . $suffix . '.css', array(), MAILSTER_VERSION );

		( $embed )
			? mailster( 'helper' )->wp_print_embedded_styles( 'mailster-form-default' )
			: wp_print_styles( 'mailster-form-default' );

		self::$add_style = true;

	}


	/**
	 * deprecated methods
	 *
	 * @param unknown $form_id (optional)
	 * @return unknown
	 */
	public function get( $form_id = 0 ) {

		_deprecated_function( __FUNCTION__, '2.1', "mailster('forms')->get()" );

		$return = mailster( 'helper' )->object_to_array( mailster( 'forms' )->get( $form_id ) );

		$return['id'] = $return['ID'];

		return $return;

	}


	/**
	 *
	 *
	 * @param unknown $option (optional)
	 * @return unknown
	 */
	public function get_all( $option = null ) {

		_deprecated_function( __FUNCTION__, '2.1', "mailster('forms')->get_all()" );

		$forms = mailster( 'helper' )->object_to_array( mailster( 'forms' )->get_all() );
		foreach ( $forms as $i => $form ) {
			$forms[ $i ]['id'] = $form['ID'];
		}

		return $forms;

	}


	/**
	 *
	 *
	 * @param unknown $form_id (optional)
	 * @param unknown $key
	 * @param unknown $value
	 * @return unknown
	 */
	public function set( $form_id = 0, $key, $value ) {

		_deprecated_function( __FUNCTION__, '2.1', "mailster('forms')->update_field()" );

		$return = mailster( 'forms' )->update_field( $form_id, $key, $value );

		$return['id'] = $return['ID'];

		return $return;

	}


	/**
	 *
	 *
	 * @param unknown $form_id
	 * @param unknown $list_id
	 * @return unknown
	 */
	public function assign_list( $form_id, $list_id ) {

		_deprecated_function( __FUNCTION__, '2.1', "mailster('forms')->assign_lists()" );

		$return = mailster( 'forms' )->assign_lists( $form_id, $list_id );

		$return['id'] = $return['ID'];

		return $return;

	}


	/**
	 *
	 *
	 * @param unknown $form_id
	 * @param unknown $list_id
	 * @return unknown
	 */
	public function unassign_list( $form_id, $list_id ) {

		_deprecated_function( __FUNCTION__, '2.1', "mailster('forms')->unassign_lists()" );

		$return = mailster( 'forms' )->unassign_lists( $form_id, $list_id );

		$return['id'] = $return['ID'];

		return $return;

	}


}
