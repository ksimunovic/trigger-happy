<?php
/**
 * TriggerHappy Auth
 *
 * Handles th-auth endpoint requests.
 *
 * @author   WooThemes
 * @category API
 * @package  WooCommerce/API
 * @since    2.4.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class TriggerHappy_Auth {
	/**
	 * Version.
	 *
	 * @var int
	 */
	const VERSION = 1;
	/**
	 * Setup class.
	 *
	 * @since 2.4.0
	 */
	public function __construct() {
		// Add query vars
		add_filter( 'query_vars', array( $this, 'add_query_vars' ), 0 );
		// Register auth endpoint
		add_action( 'init', array( __CLASS__, 'add_endpoint' ), 0 );
		// Handle auth requests
		add_action( 'parse_request', array( $this, 'handle_auth_requests' ), 0 );
	}
	/**
	 * Add query vars.
	 *
	 * @since  2.4.0
	 *
	 * @param  array $vars
	 *
	 * @return string[]
	 */
	public function add_query_vars( $vars ) {
		$vars[] = 'th-auth-version';
		$vars[] = 'th-auth-route';
		return $vars;
	}
	/**
	 * Add auth endpoint.
	 *
	 * @since 2.4.0
	 */
	public static function add_endpoint() {

		add_rewrite_rule( '^th-auth/v([1]{1})/(.*)?', 'index.php?th-auth-version=$matches[1]&th-auth-route=$matches[2]', 'top' );
	}
	/**
	 * Return a list of permissions a scope allows.
	 *
	 * @since  2.4.0
	 *
	 * @param  string $scope
	 *
	 * @return array
	 */
	protected function get_permissions_in_scope( $scope ) {
		$permissions = array();
		$permissions[] = __( 'Send Data from Flows', 'woocommerce' );
		$permissions[] = __( 'Execute Flows', 'woocommerce' );
		$permissions[] = __( 'View and manage flows', 'woocommerce' );
		return apply_filters( 'triggerhappy_api_permissions_in_scope', $permissions, $scope );
	}
	/**
	 * Build auth urls.
	 *
	 * @since  2.4.0
	 *
	 * @param  array $data
	 * @param  string $endpoint
	 *
	 * @return string
	 */
	protected function build_url( $data, $endpoint ) {
		$url = wc_get_endpoint_url( 'th-auth/v' . self::VERSION, $endpoint, home_url( '/' ) );
		return add_query_arg( array(
			'app_name'            => sanitize_text_field( $data['app_name'] ),
			'user_id'             => sanitize_text_field( $data['user_id'] ),
			'return_url'          => urlencode( $this->get_formatted_url( $data['return_url'] ) ),
			'callback_url'        => urlencode( $this->get_formatted_url( $data['callback_url'] ) ),
			//'scope'               => sanitize_text_field( $data['scope'] ),
		), $url );
	}
	/**
	 * Decode and format a URL.
	 * @param  string $url
	 * @return string
	 */
	protected function get_formatted_url( $url ) {
		$url = urldecode( $url );
		if ( ! strstr( $url, '://' ) ) {
			$url = 'https://' . $url;
		}
		return $url;
	}
	/**
	 * Make validation.
	 *
	 * @since  2.4.0
	 */
	protected function make_validation() {
		$params = array(
			'app_name',
			'user_id',
			'return_url',
			'callback_url'
		);
		foreach ( $params as $param ) {
			if ( empty( $_REQUEST[ $param ] ) ) {
				/* translators: %s: parameter */
				throw new Exception( sprintf( __( 'Missing parameter %s', 'trigger-happy' ), $param ) );
			}
		}

		foreach ( array( 'return_url', 'callback_url' ) as $param ) {
			$param = $this->get_formatted_url( $_REQUEST[ $param ] );
			if ( false === filter_var( $param, FILTER_VALIDATE_URL ) ) {
				/* translators: %s: url */
				throw new Exception( sprintf( __( 'The %s is not a valid URL', 'trigger-happy' ), $param ) );
			}
		}
		$callback_url = $this->get_formatted_url( $_REQUEST['callback_url'] );
		if ( 0 !== stripos( $callback_url, 'https://' ) ) {
			throw new Exception( __( 'The callback_url needs to be over SSL', 'trigger-happy' ) );
		}
	}
	/**
	 * Create keys.
	 *
	 * @since  2.4.0
	 *
	 * @param  string $app_name
	 * @param  string $app_user_id
	 * @param  string $scope
	 *
	 * @return array
	 */
	protected function create_keys( $app_name, $app_user_id, $scope ) {
		global $wpdb;
		/* translators: 1: app name 2: scope 3: date 4: time */
		$description = sprintf(
			__( '%1$s - API %2$s.', 'trigger-happy' ),
			sanitize_text_field( $app_name ),
			 $scope 
		);
		$user = wp_get_current_user();
		// Created API keys.
		$permissions     = 'all';
		$consumer_key    = 'ck_' . wc_rand_hash();
		$consumer_secret = 'cs_' . wc_rand_hash();
		$wpdb->insert(
			$wpdb->prefix . 'triggerhappy_api_keys',
			array(
				'user_id'         => $user->ID,
				'description'     => $description,
				'permissions'     => $permissions,
				'consumer_key'    => wc_api_hash( $consumer_key ),
				'consumer_secret' => $consumer_secret,
				'truncated_key'   => substr( $consumer_key, -7 ),
			),
			array(
				'%d',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
			)
		);
		return array(
			'key_id'          => $wpdb->insert_id,
			'user_id'         => $app_user_id,
			'consumer_key'    => $consumer_key,
			'consumer_secret' => $consumer_secret,
			'key_permissions' => $permissions,
		);
	}
	/**
	 * Post consumer data.
	 *
	 * @since  2.4.0
	 *
	 * @param  array  $consumer_data
	 * @param  string $url
	 *
	 * @return bool
	 * @throws Exception
	 */
	protected function post_consumer_data( $consumer_data, $url ) {
		$params = array(
			'body'      => json_encode( $consumer_data ),
			'timeout'   => 60,
			'headers'   => array(
				'Content-Type' => 'application/json;charset=' . get_bloginfo( 'charset' ),
			),
		);
		$response = wp_safe_remote_post( esc_url_raw( $url ), $params );
		if ( is_wp_error( $response ) ) {
			throw new Exception( $response->get_error_message() );
		} elseif ( 200 != $response['response']['code'] ) {
			throw new Exception( __( 'An error occurred in the request and at the time were unable to send the consumer data', 'trigger-happy' ) );
		}
		return true;
	}
	/**
	 * Handle auth requests.
	 *
	 * @since 2.4.0
	 */
	public function handle_auth_requests() {
		global $wp;
		if ( ! empty( $_GET['th-auth-version'] ) ) {
			$wp->query_vars['th-auth-version'] = $_GET['th-auth-version'];
		}
		if ( ! empty( $_GET['th-auth-route'] ) ) {
			$wp->query_vars['th-auth-route'] = $_GET['th-auth-route'];
		}
		// wc-auth endpoint requests
		if ( ! empty( $wp->query_vars['th-auth-version'] ) && ! empty( $wp->query_vars['th-auth-route'] ) ) {
			$this->auth_endpoint( $wp->query_vars['th-auth-route'] );
		}
	}
	/**
	 * Auth endpoint.
	 *
	 * @since 2.4.0
	 *
	 * @param string $route
	 */
	protected function auth_endpoint( $route ) {
		ob_start();
		$consumer_data = array();
		try {
			$route = strtolower( sanitize_text_field( $route ) );
			$this->make_validation();
			// Login endpoint
			if ( 'login' == $route && ! is_user_logged_in() ) {
				TH::get_template( 'auth/form-login.php', array(
					'app_name'     => $_REQUEST['app_name'],
					'return_url'   => add_query_arg( array( 'success' => 0, 'user_id' => sanitize_text_field( $_REQUEST['user_id'] ) ), $this->get_formatted_url( $_REQUEST['return_url'] ) ),
					'redirect_url' => $this->build_url( $_REQUEST, 'authorize' ),
				) );
				exit;
			// Redirect with user is logged in
			} elseif ( 'login' == $route && is_user_logged_in() ) {
				wp_redirect( esc_url_raw( $this->build_url( $_REQUEST, 'authorize' ) ) );
				exit;
			// Redirect with user is not logged in and trying to access the authorize endpoint
			} elseif ( 'authorize' == $route && ! is_user_logged_in() ) {
				wp_redirect( esc_url_raw( $this->build_url( $_REQUEST, 'login' ) ) );
				exit;
			// Authorize endpoint
		} elseif ( 'authorize' == $route && current_user_can( 'manage_options' ) ) {
				TH::get_template( 'auth/form-grant-access.php', array(
					'app_name'    => $_REQUEST['app_name'],
					'return_url'  => add_query_arg( array( 'success' => 0, 'user_id' => sanitize_text_field( $_REQUEST['user_id'] ) ), $this->get_formatted_url( $_REQUEST['return_url'] ) ),
					'scope'       => 'all',
					'permissions' => $this->get_permissions_in_scope( 'all' ),
					'granted_url' => wp_nonce_url( $this->build_url( $_REQUEST, 'access_granted' ), 'th_auth_grant_access', 'th_auth_nonce' ),
					'logout_url'  => wp_logout_url( $this->build_url( $_REQUEST, 'login' ) ),
					'user'        => wp_get_current_user(),
				) );
				exit;
			// Granted access endpoint
			} elseif ( 'access_granted' == $route && current_user_can( 'manage_options' ) ) {
				if ( ! isset( $_GET['th_auth_nonce'] ) || ! wp_verify_nonce( $_GET['th_auth_nonce'], 'th_auth_grant_access' ) ) {
					throw new Exception( __( 'Invalid nonce verification', 'trigger-happy' ) );
				}
				$consumer_data = $this->create_keys( $_REQUEST['app_name'], $_REQUEST['user_id'], $_REQUEST['scope'] );
				$response      = $this->post_consumer_data( $consumer_data, $this->get_formatted_url( $_REQUEST['callback_url'] ) );
				if ( $response ) {
					wp_redirect( esc_url_raw( add_query_arg( array( 'success' => 1, 'user_id' => sanitize_text_field( $_REQUEST['user_id'] ) ), $this->get_formatted_url( $_REQUEST['return_url'] ) ) ) );
					exit;
				}
			} else {
				throw new Exception( __( 'You do not have permission to access this page', 'trigger-happy' ) );
			}
		} catch ( Exception $e ) {
			$this->maybe_delete_key( $consumer_data );
			/* translators: %s: error message */
			wp_die( sprintf( __( 'Error: %s.', 'trigger-happy' ), $e->getMessage() ), __( 'Access denied', 'trigger-happy' ), array( 'response' => 401 ) );
		}
	}
	/**
	 * Maybe delete key.
	 *
	 * @since 2.4.0
	 *
	 * @param array $key
	 */
	private function maybe_delete_key( $key ) {
		global $wpdb;
		if ( isset( $key['key_id'] ) ) {
			$wpdb->delete( $wpdb->prefix . 'triggerhappy_api_keys', array( 'key_id' => $key['key_id'] ), array( '%d' ) );
		}
	}
}
new TriggerHappy_Auth();
