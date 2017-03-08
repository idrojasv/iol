<?php

class MailsterTranslations {

	private $endpoint = 'https://translate.mailster.co';


	public function __construct() {

		add_action( 'plugins_loaded', array( &$this, 'init' ), 1 );

	}


	public function init() {

		if ( is_dir( MAILSTER_UPLOAD_DIR . '/languages' ) ) {
			$custom = MAILSTER_UPLOAD_DIR . '/languages/mailster-' . get_locale() . '.mo';
			if ( file_exists( $custom ) ) {
				load_textdomain( 'mailster', $custom );
			} else {
				load_plugin_textdomain( 'mailster' );
			}
		} else {
			load_plugin_textdomain( 'mailster' );
		}

		add_filter( 'site_transient_update_plugins', array( &$this, 'update_plugins_filter' ), 1 );
		add_action( 'delete_site_transient_update_plugins', array( &$this, 're_check' ) );

	}


	/**
	 *
	 *
	 * @param unknown $value
	 * @return unknown
	 */
	public function update_plugins_filter( $value ) {
		// no translation support
		if ( ! isset( $value->translations ) ) {
			return $value;
		}

		$data = $this->get_translation_data();

		if ( ! empty( $data ) ) {
			$value->translations[] = $data;
		}

		return $value;
	}


	/**
	 *
	 *
	 * @param unknown $force (optional)
	 * @return unknown
	 */
	public function get_translation_data( $force = false ) {

		if ( $force || ( false === ( $data = get_transient( '_mailster_translation' ) ) ) ) {

			// check if a newer version is available once a day
			$recheckafter = 86400;
			$data = false;

			$locale = get_locale();

			if ( 'en_US' == $locale ) {
				set_transient( '_mailster_translation', array(), $recheckafter );
				return false;
			}

			$file = 'mailster-' . $locale;
			$url = $this->endpoint . '/api/projects/mailster';
			$package = $this->endpoint . '/api/get/mailster/' . $locale;

			$location = WP_LANG_DIR . '/plugins';
			$mo_file = trailingslashit( $location ) . $file . '.mo';
			$filemtime = file_exists( $mo_file ) ? filemtime( $mo_file ) : 0;

			$response = wp_remote_get( $url );
			$body = wp_remote_retrieve_body( $response );
			if ( empty( $body ) || 200 != wp_remote_retrieve_response_code( $response ) ) {
				set_transient( '_mailster_translation', array(), 3600 );
				return false;
			}

			$body = json_decode( $body );

			$translation_set = null;

			foreach ( $body->translation_sets as $set ) {
				if ( ! isset( $set->wp_locale ) ) {
					$set->wp_locale = $set->locale;
				}
				if ( $set->wp_locale == $locale ) {
					$translation_set = $set;
					break;
				}
			}

			if ( $translation_set ) {

				$lastmodified = strtotime( $translation_set->last_modified );
				if ( $lastmodified - $filemtime > 0 ) {
					$data = array(
						'type' => 'plugin',
						'slug' => 'mailster',
						'language' => $locale,
						'version' => MAILSTER_VERSION,
						'updated' => date( 'Y-m-d H:i:s', $lastmodified ),
						'current' => $filemtime,
						'package' => $package,
						'autoupdate' => (bool) mailster_option( 'autoupdate' ),
					);
				}
			}

			set_transient( '_mailster_translation', $data, $recheckafter );
		}

		return is_array( $data ) ? ( ! empty( $data ) ? $data : null ) : false;

	}


	/**
	 *
	 *
	 * @param unknown $new
	 */
	public function on_activate( $new ) {

		try {
			$this->download_language();
		} catch ( Exception $e ) {
		}

	}


	public function re_check() {
		$this->get_translation_data( true );
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function download_language() {

		include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		$upgrader = new Language_Pack_Upgrader( new Automatic_Upgrader_Skin() );

		add_filter( 'site_transient_update_plugins', array( &$this, 'site_transient_update_plugins' ) );
		$result = $upgrader->bulk_upgrade();
		remove_filter( 'site_transient_update_plugins', array( &$this, 'site_transient_update_plugins' ) );

		if ( ! empty( $result[0] ) ) {

			return true;

		}

		return false;

	}


	/**
	 *
	 *
	 * @param unknown $value
	 * @return unknown
	 */
	public function site_transient_update_plugins( $value ) {

		// no translation support
		if ( ! isset( $value->translations ) ) {
			return $value;
		}

		$value->translations = array();

		$translation_data = $this->get_translation_data( true );

		if ( ! empty( $translation_data ) ) {
			$value->translations[] = $translation_data;
		}

		return $value;

	}



}
