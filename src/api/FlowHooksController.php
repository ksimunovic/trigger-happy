<?php
class FlowHooksController extends WP_REST_Controller {

	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes() {
		$version = '1';
		$namespace = 'wpflow/v' . $version;
		$base = 'nodes';
		register_rest_route(
			$namespace, '/' . $base, array(
				array(
					'methods'         => WP_REST_Server::READABLE,
					'callback'        => array( $this, 'get_available_nodes' ),
					'permission_callback' => array( $this, 'get_permissions_check' ),
					'args'            => array(),
				)
			)
		);
		register_rest_route(
			$namespace, '/' . $base . '/(?P<plugin>[a-zA-Z0-9-_]+)/(?P<nodetype>[a-zA-Z0-9-_]+)/options', array(
				array(
					'methods'         => WP_REST_Server::READABLE,
					'callback'        => array( $this, 'get_available_node_options' ),
					'permission_callback' => array( $this, 'get_permissions_check' ),
					'args'            => array(),
				),
			)
		);

		register_rest_route(
			$namespace, '/types/(?P<typeid>[a-zA-Z0-9-_]+)', array(
				array(
					'methods'         => WP_REST_Server::READABLE,
					'callback'        => array( $this, 'get_available_choices' ),
					'permission_callback' => array( $this, 'get_permissions_check' ),
					'args'            => array(),
				),
			)
		);

		register_rest_route(
			$namespace, '/types/(?P<typeid>[a-zA-Z0-9-_]+)/values/(?P<search>.*)', array(
				array(
					'methods'         => WP_REST_Server::READABLE,
					'callback'        => array( $this, 'search_available_choices' ),
					'permission_callback' => array( $this, 'get_permissions_check' ),
					'args'            => array(),
				),
			)
		);

	}

	/**
	 * Get a collection of items
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_available_choices( $request ) {

		$typeid = $request->get_param( 'typeid' );
		if ( ! isset(  TriggerHappy::get_instance()->types[ $typeid ] ) ) {
			return new WP_Error(
				'triggerhappy_no_datatype', 'Invalid Data Type', array(
					'status' => 404,
				)
			);
		}
		$typeDef = $types = TriggerHappy::get_instance()->types[ $typeid ];

		$values = array();
		if ( isset( $typeDef['callback'] ) && $typeDef['ajax'] == false ) {
			$values = call_user_func( $typeDef['callback'] );
		}

		$schema = $this->get_schema( $typeid );
		return new WP_REST_Response(
			array(
				'base' => $typeDef['base'],
				'choices' => $values,
				'ajax' => isset( $typeDef['ajax'] ) ? $typeDef['ajax'] : false,
				'schema' => $schema,
			),200
		);
	}

	public function search_available_choices( $request ) {

		$typeid = $request->get_param( 'typeid' );
		$search = $request->get_param( 'search' );
		if ( ! isset( TriggerHappy::get_instance()->types[ $typeid ] ) ) {
			return new WP_Error(
				'triggerhappy_no_datatype', 'Invalid Data Type', array(
					'status' => 404,
				)
			);
		}
		$typeDef = $types = TriggerHappy::get_instance()->types[ $typeid ];
		$values = array();

		if ( isset( $typeDef['callback'] ) && $typeDef['ajax'] == true ) {
			$values = call_user_func( $typeDef['callback'],$search );
		}

		return new WP_REST_Response(
			$values
			,200
		);
	}

	public function get_schema( $schemaid ) {
	$types = TriggerHappy::get_instance()->types_schema;
		if ( isset( $types[ $schemaid ] ) && is_callable( $types[ $schemaid ] ) ) {
			return call_user_func( $types[ $schemaid ] );
		}
		if ( isset( $types[ $schemaid ] ) && is_array( $types[ $schemaid ] ) ) {
			return $types[ $schemaid ];
		}
		return null;
	}
	public function get_available_node_options( $request ) {

		$items = TriggerHappy::get_instance()->nodes;
		$nodeType = $request->get_param( 'nodetype' );
		$plugin = $request->get_param( 'plugin' );
		$data = array();
		foreach ( Ninja_Forms()->form()->get_forms() as $form ) {
			$settings = $form->get_settings();
			$settings['id'] = $form->get_id();
			array_push( $data,$settings );
		}
		return new WP_REST_Response(
			array(
				'forms' => $data,
			),200
		);
	}
	public function get_available_nodes( $request ) {

		$items = TriggerHappy::get_instance()->nodes;
		$advanced = $request->get_param( 'advanced' );
		$byplugin = false;
		if ( $request['plugin'] ) {
			$byplugin = sanitize_title($request['plugin']);
		}
		$data = array();
		foreach ( $items as $type => $nodeData ) {
			
			if ( $byplugin && ( ! isset( $nodeData['plugin'] ) || $nodeData['plugin'] == '' || sanitize_title( $nodeData['plugin'] ) != $byplugin ) )  {
				continue;
			}
			if ( ! $advanced && isset($nodeData['advanced']) && $nodeData['advanced'] ) {
				continue;
			}
			$nodeData['type'] = $type;
			$itemdata =  $nodeData;
			$data[] = $this->prepare_response_for_collection( $nodeData );

		}

		return new WP_REST_Response( $data, 200 );
	}

	/**
	 * Check if a given request has access to get items
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|bool
	 */
	public function get_permissions_check( $request ) {
		return current_user_can( 'manage_options' );
	}


}
