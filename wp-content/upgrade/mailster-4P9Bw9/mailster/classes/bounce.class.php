<?php

class MailsterBounce {

	private $mailbox;

	/**
	 *
	 *
	 * @param unknown $service (optional)
	 */
	public function __construct() {

		add_action( 'plugins_loaded', array( &$this, 'init' ), 1 );

	}


	public function init() {

		// add_action( 'init', array( &$this, 'check' ), 1 );
		add_action( 'mailster_cron_worker', array( &$this, 'check' ), 1 );
		add_action( 'mailster_check_bounces', array( &$this, 'check' ), 99 );

	}


	/**
	 *
	 *
	 * @param unknown $bool (optional)
	 */
	public function bounce_lock( $bool = true ) {

		set_transient( 'mailster_check_bounces_lock', $bool, mailster_option( 'bounce_check', 5 ) * 60 );

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function is_bounce_lock() {

		return get_transient( 'mailster_check_bounces_lock' );

	}


	/**
	 *
	 *
	 * @param unknown $force (optional)
	 * @return unknown
	 */
	public function send_test() {

		$identifier = 'mailster_' . md5( uniqid() );

		$mail = mailster( 'mail' );
		$mail->to = mailster_option( 'bounce' );
		$mail->subject = 'Mailster Bounce Test ' . $identifier;

		$replace = array(
			'preheader' => 'You can delete this message!',
			'notification' => 'This message was sent from your WordPress blog to test your bounce server. You can delete this message!',
		);

		if ( $mail->send_notification( $identifier, $mail->subject, $replace ) ) {
			return $identifier;
		}

		return false;
	}


	/**
	 *
	 *
	 * @param unknown $server  (optional)
	 * @param unknown $user    (optional)
	 * @param unknown $pwd     (optional)
	 * @param unknown $port    (optional)
	 * @param unknown $ssl     (optional)
	 * @param unknown $service (optional)
	 * @return unknown
	 */
	public function get_handler( $server = null, $user = null, $pwd = null, $port = null, $ssl = null, $service = null ) {

		$server = ! is_null( $server ) ? $server : mailster_option( 'bounce_server' );
		$user = ! is_null( $user ) ? $user : mailster_option( 'bounce_user' );
		$pwd = ! is_null( $pwd ) ? $pwd : mailster_option( 'bounce_pwd' );
		$port = ! is_null( $port ) ? $port : mailster_option( 'bounce_port', 110 );
		$ssl = ! is_null( $ssl ) ? $ssl : mailster_option( 'bounce_ssl' );
		$service = ! is_null( $service ) ? $service : mailster_option( 'bounce_service' );

		switch ( $service ) {
			case 'pop3':
			case 'imap':
			case 'nntp':
				$handler = new MailsterBounceHandler( $service );
			break;
			default:
				$handler = new MailsterBounceLegacyHandler();
			break;
		}

		$connect = $handler->connect( $server, $user, $pwd, $port, $ssl, $service, 10 );

		if ( is_wp_error( $connect ) ) {

			return $connect;

		}

		return $handler;

	}


	/**
	 *
	 *
	 * @param unknown $identifier
	 * @return unknown
	 */
	public function test( $identifier ) {

		$handler = $this->get_handler();

		if ( is_wp_error( $handler ) ) {

			return $handler;

		}

		return $handler->check_bounce_message( $identifier );

	}


	/**
	 *
	 *
	 * @param unknown $force (optional)
	 * @return unknown
	 */
	public function check( $force = false ) {

		if ( ! mailster_option( 'bounce_active' ) ) {
			return false;
		}

		if ( $this->is_bounce_lock() && ! $force ) {
			return false;
		}

		$handler = $this->get_handler();

		if ( is_wp_error( $handler ) ) {

			mailster_notice( sprintf( __( 'It looks like your bounce server setting is incorrect! Last error: %s', 'mailster' ), '<br><strong>' . $handler->get_error_message() . '</strong>' ), 'error', true, 'bounce_server' );

			return;
		}

		return $handler->process_bounces();

	}


}


class MailsterBounceHandler {

	public $mailbox;
	public $bounce_delete;
	public $MID;
	public $service;

	/**
	 *
	 *
	 * @param unknown $service (optional)
	 */
	public function __construct( $service = 'pop3' ) {

		$this->bounce_delete = mailster_option( 'bounce_delete' );
		$this->MID = mailster_option( 'ID' );
		$this->service = $service;

	}


	public function __destruct() {

		if ( $this->mailbox ) {
			$this->mailbox->expungeDeletedMails();
		}

	}


	/**
	 *
	 *
	 * @param unknown $server
	 * @param unknown $user
	 * @param unknown $pwd
	 * @param unknown $port    (optional)
	 * @param unknown $ssl     (optional)
	 * @param unknown $timeout (optional)
	 * @return unknown
	 */
	public function connect( $server, $user, $pwd, $port = 110, $ssl = false, $timeout = 60 ) {

		$path = '{' . $server . ':' . $port . '/' . $this->service . ( $ssl ? '/ssl' : '' ) . '/novalidate-cert}INBOX';

		require MAILSTER_DIR . 'classes/libs/PhpImap/__autoload.php';

		try {

			@imap_timeout( IMAP_OPENTIMEOUT, $timeout );
			@imap_timeout( IMAP_READTIMEOUT, $timeout );

			$this->mailbox = new PhpImap\Mailbox( $path, $user, $pwd );
			$this->mailbox->checkMailbox();

		} catch ( Exception $e ) {

			return new WP_Error( 'connect_error', $e->getMessage() );

		}

		return true;

	}


	public function process_bounces() {

		$messages = $this->get_messages();

		require MAILSTER_DIR . 'classes/libs/bounce/bounce_driver.class.php';

		foreach ( $messages as $id => $message ) {

			preg_match( '#X-(Mailster|MyMail): ([a-f0-9]{32})#i', $message, $hash );
			preg_match( '#X-(Mailster|MyMail)-Campaign: (\d+)#i', $message, $camp );

			$bouncehandler = new Bouncehandler();
			$bounceresult = $bouncehandler->parse_email( $message );
			$bounceresult = (object) $bounceresult[0];

			$subscriber = mailster( 'subscribers' )->get_by_hash( $hash[2], false );
			$campaign = ! empty( $camp ) ? mailster( 'campaigns' )->get( intval( $camp[2] ) ) : null;

			if ( $subscriber ) {

				$campaign_id = $campaign ? $campaign->ID : 0;
				switch ( $bounceresult->action ) {
					case 'success':
					break;

					case 'failed':
						// hardbounce
						mailster( 'subscribers' )->bounce( $subscriber->ID, $campaign_id, true, $bounceresult->status );
					break;

					case 'transient':
					default:
						// softbounce
						mailster( 'subscribers' )->bounce( $subscriber->ID, $campaign_id, false, $bounceresult->status );

				}
			}

			$this->delete_message( $id );

		}

	}


	/**
	 *
	 *
	 * @param unknown $id
	 */
	protected function delete_message( $id ) {

		$this->mailbox->deleteMail( $id );

	}


	/**
	 *
	 *
	 * @param unknown $all (optional)
	 * @return unknown
	 */
	protected function get_messages( $all = false ) {

		$mailsIds = $this->mailbox->searchMailbox();

		$messages = array();

		foreach ( $mailsIds as $i => $id ) {

			$mail = $this->mailbox->getMail( $id );

			$message = $mail->textPlain;

			if ( $all || preg_match( '#X-(Mailster|MyMail)-ID: ' . preg_quote( $this->MID ) . '#i', $message ) ) {

				$messages[ $id ] = $this->mailbox->getRawMail( $id, false );

			} elseif ( $bounce_delete ) {

				$this->delete_message( $id );

			}
		}

		return $messages;

	}


	/**
	 *
	 *
	 * @param unknown $identifier
	 * @return unknown
	 */
	public function check_bounce_message( $identifier ) {

		$messages = $this->get_messages( true );

		foreach ( $messages as $id => $message ) {

			if ( false !== strpos( $message, $identifier ) ) {
				$this->delete_message( $id );
				return true;
				break;
			}
		}

		return false;

	}


}


class MailsterBounceLegacyHandler extends MailsterBounceHandler {

	public $msgcount = 0;

	/**
	 *
	 *
	 * @param unknown $server
	 * @param unknown $user
	 * @param unknown $pwd
	 * @param unknown $port    (optional)
	 * @param unknown $ssl     (optional)
	 * @param unknown $timeout (optional)
	 * @return unknown
	 */
	public function connect( $server, $user, $pwd, $port = 110, $ssl = false, $timeout = 60 ) {

		require ABSPATH . WPINC . '/class-pop3.php';
		$this->mailbox = new POP3();
		$this->mailbox->TIMEOUT = $timeout;

		if ( $ssl ) {
			$server = 'ssl://' . $server;
		}

		$this->mailbox->connect( $server, $port );

		if ( ! empty( $this->mailbox->ERROR ) ) {
			return new WP_Error( 'connect_error', $this->mailbox->ERROR );
		}

		$this->mailbox->user( $user );

		if ( ! empty( $this->mailbox->ERROR ) ) {
			return new WP_Error( 'connect_error_user', $this->mailbox->ERROR );
		}

		$this->msgcount = $this->mailbox->pass( $pwd );

		if ( ! empty( $this->mailbox->ERROR ) ) {
			return new WP_Error( 'connect_error_user', $this->mailbox->ERROR );
		}

		if ( false === $this->msgcount ) {

			$this->msgcount = 0;
		}

	}


	public function __destruct() {
		$this->mailbox->quit();
	}


	/**
	 *
	 *
	 * @param unknown $id
	 */
	protected function delete_message( $id ) {
		$this->mailbox->delete( $id );
	}


	/**
	 *
	 *
	 * @param unknown $all (optional)
	 * @return unknown
	 */
	protected function get_messages( $all = false ) {

		$messages = array();

		for ( $i = 1; $i <= $this->msgcount; $i++ ) {

			$message = $this->mailbox->get( $i );

			if ( ! $message ) {
				if ( $this->bounce_delete ) {
					$this->delete_message( $i );
				}

				continue;
			}

			$message = implode( $message );

			if ( $all || preg_match( '#X-(Mailster|MyMail)-ID: ' . preg_quote( $this->MID ) . '#i', $message ) ) {

				$messages[ $i ] = $message;

			} elseif ( $this->bounce_delete ) {

				$this->delete_message( $i );

			}
		}

		return $messages;

	}


}
