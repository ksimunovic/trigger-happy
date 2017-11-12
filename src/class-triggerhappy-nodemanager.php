<?php
/**
 * TriggerHappyNodeManager class
 * Keeps track of the registered nodes
 * Can be overridden to store nodes in the database
 */
class TriggerHappyNodeManager {
    public $nodes = array();
    public function __construct() {
        $this->triggerhappy_init_nodes();
    }

	/**
	 * Gets the definition for the specified node
	 */
    public function get_node( $id, $ns = null ) {
    	if ( $ns !== null ) {
    		return $this->nodes[ $ns ][ $id ];
    	}

    	foreach ( $this->nodes as $ns => $v ) {
    		if ( isset( $this->nodes[ $ns ][ $id ] ) ) {
    			return $this->nodes[ $ns ][ $id ];
    		}
    	}
    }
	/***
	 * Registers a custom node for use within the Trigger Happy editor
	 */
    public function register_node( $id, $ns, $options ) {

    	if ( ! isset( $this->nodes[ $ns ] ) ) {
    		$this->nodes[ $ns ] = array();
    	}

    	$options = array_merge(
    		array(
    			'name' => $id,
    			'id' => $id,
    			'description' => '',
    			'cat' => '',
    			'plugin' => '',
    		),$options
    	);
    	$this->nodes[ $ns ][ $id ] = $options;
    	$new_node = $this->nodes[ $ns ][ $id ];
    }
    public function init() {
        do_action( 'triggerhappy_load_nodes' );
    	do_action( 'triggerhappy_schema' );
    	$this->nodes = apply_filters( 'triggerhappy_nodes', $this->nodes );
    }
}
