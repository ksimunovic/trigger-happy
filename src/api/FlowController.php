<?php
class FlowController extends WP_REST_Controller {

	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes() {
		$version = '1';
		$namespace = 'wpflow/v' . $version;
		$base = 'flows';
		register_rest_route(
			$namespace, '/flows/(?P<id>\d+)' , array(
				array(
					'methods'         => WP_REST_Server::READABLE,
					'callback'        => array( $this, 'get_flow' ),
					'permission_callback' => array( $this, 'get_permissions_check' ),
				)
			)
		);
		register_rest_route(
			$namespace, '/globals' , array(
				array(
					'methods'         => WP_REST_Server::READABLE,
					'callback'        => array( $this, 'get_globals' ),
					'permission_callback' => array( $this, 'get_permissions_check' ),
				)
			)
		);
	}

	/**
	 * Get a collection of global variables
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_globals( $request ) {

		$items = TriggerHappy::get_instance()->globals;
		return new WP_REST_Response( $items, 200 );
	}
	/**
	 * Get a collection of items
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_flow( $request ) {

		$flow_id = $request->get_param( 'id' );

		$data = get_post( $flow_id );

		return new WP_REST_Response( $data, 200 );
	}

	public function get_permissions_check( $request ) {
		return current_user_can( 'manage_options' );
	}



}
