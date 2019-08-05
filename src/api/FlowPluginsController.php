<?php
class FlowPluginsController extends WP_REST_Controller {

	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes() {
		$version = '1';
		$namespace = 'wpflow/v' . $version;
		$base = 'plugins';
		register_rest_route(
			$namespace, '/' . $base, array(
				array(
					'methods'         => WP_REST_Server::READABLE,
					'callback'        => array( $this, 'get_available_plugins' ),
					'permission_callback' => array( $this, 'get_plugins_permissions_check' ),
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
	public function get_available_plugins( $request ) {

		$items = TriggerHappy::get_instance()->nodes;
		$data = array();
		$options =  get_option( 'triggerhappy_plugin_data' );
		if ( ! $options ) {
			$options = array();
		}
		$avail = array();
		$skip = array();
		foreach ( $items as $nodeId => $nodeList ) {
			if (!isset($nodeList['plugin'])) {
				continue;
			}
			$pluginName = $nodeList['plugin'];
			$avail[$pluginName] = 1;
			if ( $pluginName && ! isset( $options[ $pluginName ] ) && ! isset( $skip[ $pluginName ] ) ) {

				$pluginDataReq = wp_remote_get( 'https://api.wordpress.org/plugins/info/1.0/' . strtolower($pluginName) . '.json' );
				if ( is_wp_error( $pluginDataReq ) ) {
					$skip[ $pluginName ] = true;
					continue;
				}
				$pluginData = json_decode( wp_remote_retrieve_body( $pluginDataReq ) );
				if ( $pluginData == null ) {
					$skip[ $pluginName ] = true;
					continue;
				}

				$iconUrl = 'https://ps.w.org/' . $pluginName . '/assets/icon-128x128.png';
				if ( is_wp_error( wp_remote_get( $iconUrl ) ) ) {
					$iconUrl = 'https://ps.w.org/' . $pluginName . '/assets/icon-128x128.jpg';
				}
				$options[ $pluginName ] = array(
					'name' => $pluginName,
					'label' => $pluginData->name,
					'icon' => $iconUrl,
				);
			}

		}
		foreach ($options as $plugin=>$plugindata) {
			if ( ! isset( $avail[ $plugin ] ) ) {
				continue;
			}
			array_push( $data, $plugindata );
		}
		update_option( 'triggerhappy_plugin_data',$options );

		return new WP_REST_Response( $data, 200 );
	}

	public function get_plugins_permissions_check( $request ) {
		return current_user_can( 'manage_options' );
	}


}
