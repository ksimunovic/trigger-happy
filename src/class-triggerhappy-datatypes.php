<?php
class TriggerHappyDataTypes {


    public function register_value_type( $id, $parentType, $getOptions = null, $ajax = false ) {
    	global $triggerhappy;
    	if ( ! isset( $triggerhappy['types'] ) ) {
    		$triggerhappy['types'] = array();
    	}

    	$triggerhappy['types'][ $id ] = array(
    		'id' => $id,
    		'base' => $parentType,
    	);
    	if ( $getOptions != null && is_callable( $getOptions ) ) {
    		$triggerhappy['types'][ $id ]['callback'] = $getOptions;
    	} else if ($getOptions != null && is_array($getOptions)) {
    		$triggerhappy['types'][ $id ]['choices'] = $getOptions;

    	}

    	$triggerhappy['types'][ $id ]['ajax'] = $ajax;

    }
    public function register_json_schema( $id, $jsonSchema ) {
    	global $triggerhappy;
    	if ( ! isset( $triggerhappy['types_schema'] ) ) {
    		$triggerhappy['types_schema'] = array();
    	}
    	if ( ! isset( $triggerhappy['types'] ) ) {
    		$triggerhappy['types'] = array();
    	}
    	if ( !isset(	$triggerhappy['types'][ $id ] ))
    		$triggerhappy['types'][ $id ] = array(
    			'id' => $id,
    			'base' => 'object'
    		);
    	$schema = array(
    		'$schema' => 'http://json-schema.org/draft-04/schema#',
    		'title' => $jsonSchema['title'],
    		'type' => $jsonSchema['type'],
    		'properties' => $jsonSchema['properties'],
    	);
    	$triggerhappy['types_schema'][ $id ] = apply_filters('triggerhappy_json_schema_' . $id,$schema);

    }
    public function register_api_schema( $id, $apiRoute ) {
    	global $triggerhappy;
    	if ( ! isset( $triggerhappy['types_schema'] ) ) {
    		$triggerhappy['types_schema'] = array();
    	}
    	$triggerhappy['types_schema'][ $id ] = function() use ( $apiRoute ) {
    		$wp_rest_server = rest_get_server();
    		$route = $wp_rest_server->get_route_options( $apiRoute );
    		if ( isset( $route ) && isset( $route['schema'] ) && is_callable( $route['schema'] ) ) {
    			return call_user_func( $route['schema'] );
    		}
    		return null;
    	};
    }
    public function register_type_schema( $id, $schemaCallback ) {
    	global $triggerhappy;
    	if ( ! isset( $triggerhappy['types_schema'] ) ) {
    		$triggerhappy['types_schema'] = array();
    	}
    	$triggerhappy['types_schema'][ $id ] = $schemaCallback;
    }
    public function register_global_field(   $name, $type, $description, $callable = false ) {
        global $triggerhappy;
        if ( ! isset( $triggerhappy['globals'] ) ) {
            $triggerhappy['globals'] = array();
        }
        array_push($triggerhappy['globals'], array(
            'id'=>sanitize_title($name),
            'name'=>$name,
            'type'=>$type,
            'description'=>$description
        ));
    	if ($callable && $callable != null && $callable != '' && is_callable($callable)) {
    		add_filter('triggerhappy_global_'. $name, $callable);
    	}
    }

    public function init() {
    	global $triggerhappy;

    	if ( ! isset( $triggerhappy ) ) {
    		$triggerhappy = array(
    			'nodes' => array(),
    		);
    	}

    	do_action( 'triggerhappy_load_nodes' );
    	do_action( 'triggerhappy_schema' );
    	$triggerhappy['nodes'] = apply_filters( 'triggerhappy_nodes', $triggerhappy['nodes'] );
    }
}
