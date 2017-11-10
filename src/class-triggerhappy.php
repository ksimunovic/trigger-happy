<?php
class TriggerHappy {
    private static $instance = null;
    public $nodes = array();
    public $types = array();
    public $types_schema = array();
    public $globals = array();
    public static function get_instance() {
        if (self::$instance !== null)
            return self::$instance;
        self::$instance = new TriggerHappy();
        self::$instance->init_hooks();

        return self::$instance;
    }
    public function init_hooks() {
        add_action( 'rest_api_init', array($this,'rest_api_init') );
        add_action( 'triggerhappy_load_nodes',array($this,'load_nodes'), 20 );
        add_action( 'plugins_loaded', array($this, 'initialize_post_type') );
        add_action( 'plugins_loaded', array($this, 'init_nodes') );

        if (is_admin()) {
            $admin = TriggerHappyAdmin::init();
        }

    }
    public function initialize_post_type() {
            $labels = array(
        		'name'               => _x( 'Flows', 'post type general name', 'trigger-happy' ),
        		'singular_name'      => _x( 'Flow', 'post type singular name', 'trigger-happy' ),
        		'menu_name'          => _x( 'Trigger Happy', 'admin menu', 'trigger-happy' ),
        		'name_admin_bar'     => _x( 'Flow', 'add new on admin bar', 'trigger-happy' ),
        		'add_new'            => _x( 'Add New', 'flow', 'trigger-happy' ),
        		'add_new_item'       => __( 'Add New Flow', 'trigger-happy' ),
        		'new_item'           => __( 'New Flow', 'trigger-happy' ),
        		'edit_item'          => __( 'Edit Flow', 'trigger-happy' ),
        		'view_item'          => __( 'View Flow', 'trigger-happy' ),
        		'all_items'          => __( 'All Flows', 'trigger-happy' ),
        		'search_items'       => __( 'Search Flows', 'trigger-happy' ),
        		'parent_item_colon'  => __( 'Parent Flows:', 'trigger-happy' ),
        		'not_found'          => __( 'No flows found.', 'trigger-happy' ),
        		'not_found_in_trash' => __( 'No flows found in Trash.', 'trigger-happy' )
        	);

        	$args = array(
        		'labels'             => $labels,

        		'public'             => false,
        		'publicly_queryable' => false,
        		'show_ui'            => true,
        		'show_in_menu'       => true,
        		'query_var'          => false,
        		'rewrite'            => false,

        		'has_archive'        => false,
        		'hierarchical'       => false,
        		'menu_position'      => null,
        		'supports'           => array( 'title' )
        	);

        	register_post_type( 'th_flow', $args );


}
    public function rest_api_init() {
        require_once( dirname(__FILE__) ."/api/FlowHooksController.php" );
        require_once( dirname(__FILE__) ."/api/FlowController.php" );
        require_once( dirname(__FILE__) ."/api/FlowPluginsController.php" );
        $controller = new FlowHooksController();
        $controller->register_routes();
        $controller2 = new FlowController();
        $controller2->register_routes();
        $controller2 = new FlowPluginsController();
        $controller2->register_routes();
    }


    public function load_nodes() {
    	require_once( dirname( __FILE__ ) . '/nodes/core.php' );
    	$active_plugins = get_option( 'active_plugins' );
    	$all_plugins = get_plugins();
    	foreach ( $active_plugins as $dir ) {
    		$plugin_info = $all_plugins[ $dir ];
    		$plugin_slug = sanitize_title( $plugin_info['Name'] );

    		$node_file_name = dirname( __FILE__ ) . '/nodes/' . $plugin_slug . '.php';
    		if ( file_exists( $node_file_name ) ) {

    			//require_once( $node_file_name );
    		}
    	}

    }


    function create_field_def( $name, $type = 'flow', $opts = array() ) {
    	$opts = array_merge(
    		array(
    			'dir' => 'in',
    			'desc' => '',
    			'label' => '',
    			'argIndex' => -1,
    		),$opts
    	);
    	$opts['name'] = $name;
    	$opts['type'] = $type;
    	return $opts;
    }
    function fetch_node( $id, $ns = null ) {

    	if ( $ns !== null ) {
    		return $this->nodes[ $ns ][ $id ];
    	}

    	foreach ( $this->nodes as $ns => $v ) {
    		if ( isset( $this->nodes[ $ns ][ $id ] ) ) {
    			return $this->nodes[ $ns ][ $id ];
    		}
    	}
    }


    function get_global( $id  ) {


    	foreach ( $this->globals as $i => $global ) {
            if ($global['name'] == $id) {
                return $global;
            }
    	}
        return null;
    }

    function register_value_type( $id, $parentType, $getOptions = null, $ajax = false ) {

        if ( !isset($this->types[ $id ] )) {
        	$this->types[ $id ] = array(
        		'id' => $id,
        		'base' => $parentType,
        	);
        }
    	if ( $getOptions != null && is_callable( $getOptions ) ) {
    		$this->types[ $id ]['callback'] = $getOptions;
    	} else if ($getOptions != null && is_array($getOptions)) {
    		$this->types[ $id ]['choices'] = $getOptions;

    	}

    	$this->types[ $id ]['ajax'] = $ajax;

    }
    function register_json_schema( $id, $jsonSchema ) {

    	if ( !isset(	$this->types[ $id ] ))
    		$this->types[ $id ] = array(
    			'id' => $id,
    			'base' => 'object'
    		);
    	$schema = array(
    		'$schema' => 'http://json-schema.org/draft-04/schema#',
    		'title' => $jsonSchema['title'],
    		'type' => $jsonSchema['type'],
    		'properties' => $jsonSchema['properties'],
    	);
    	$this->types_schema[ $id ] = apply_filters('triggerhappy_json_schema_' . $id,$schema);

    }
    function register_api_schema( $id, $apiRoute ) {

    	$this->types_schema[ $id ] = function() use ( $apiRoute ) {
    		$wp_rest_server = rest_get_server();
    		$route = $wp_rest_server->get_route_options( $apiRoute );
    		if ( isset( $route ) && isset( $route['schema'] ) && is_callable( $route['schema'] ) ) {
    			return call_user_func( $route['schema'] );
    		}
    		return null;
    	};
    }
    function register_type_schema( $id, $schemaCallback ) {

    	$this->types_schema[ $id ] = $schemaCallback;
    }
    function register_global_field(   $name, $type, $description, $callable = false ) {

        array_push($this->globals, array(
            'id'=>sanitize_title($name),
            'name'=>$name,
            'type'=>$type,
            'description'=>$description
        ));
    	if ($callable && $callable != null && $callable != '' && is_callable($callable)) {
    		add_filter('triggerhappy_global_'. $name, $callable);
    	}
    }

    function register_node( $id, $ns, $options ) {


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
    function init_nodes() {


    	do_action( 'triggerhappy_load_nodes' );
    	do_action( 'triggerhappy_schema' );
    	$this->nodes = apply_filters( 'triggerhappy_nodes', $this->nodes );


        $flows = get_posts(array('post_type'=>'th_flow'));
        foreach ($flows as $flow) {

            $f = new TriggerHappyFlow($flow->ID,json_decode($flow->post_content));

        }
    }

    public static function get_node( $id, $ns = null ) {
        return self::get_instance()->fetch_node($id, $ns);
    }


    public static function create_field( $name, $type = 'flow', $opts = array() ) {
        return self::get_instance()->create_field_def($name,$type,$opts);
    }
}
