<?php
include( dirname( __FILE__ ) . "/functions/core_functions.php" );

require_once( dirname( __FILE__ ) . '/../classes/Psr4AutoloaderClass.php' );

// Registering class autoloader
$loader = new \HotSource\TriggerHappy\Psr4AutoloaderClass;
$loader->register();
$loader->addNamespace( 'HotSource\TriggerHappy', ABSPATH . '/wp-content/plugins/trigger-happy/src/classes' );

function triggerhappy_load_core_nodes( $nodes ) {

	$nodes = [];

	// WordPress
	$nodes['th_core_timer'] = new HotSource\TriggerHappy\Nodes\Triggers\Timer();
	$nodes['th_core_wp_login'] = new HotSource\TriggerHappy\Nodes\Actions\Login();
	$nodes['th_core_wp_logout'] = new HotSource\TriggerHappy\Nodes\Actions\Logout();
	$nodes['th_core_wp_redirect'] = new HotSource\TriggerHappy\Nodes\Actions\Redirect();
	$nodes['th_core_send_email'] = new HotSource\TriggerHappy\Nodes\Actions\CoreSendEmail();
	$nodes['th_core_add_nav_menu_item'] = new HotSource\TriggerHappy\Nodes\Actions\AddNavMenuItem();

	// Users
	$nodes['th_core_user_login'] = new HotSource\TriggerHappy\Nodes\Triggers\UserLogin();
	$nodes['th_core_user_logout'] = new HotSource\TriggerHappy\Nodes\Triggers\UserLogout();
	$nodes['th_core_user_updated'] = new HotSource\TriggerHappy\Nodes\Triggers\UserUpdated();
	$nodes['th_core_user_register'] = new HotSource\TriggerHappy\Nodes\Triggers\UserRegister();
	$nodes['th_core_filter_logged_in'] = new HotSource\TriggerHappy\Nodes\Conditions\LoggedIn();
	$nodes['th_core_filter_not_logged_in'] = new HotSource\TriggerHappy\Nodes\Conditions\NotLoggedIn();

	// Posts
	$nodes['th_core_post_saved'] = new HotSource\TriggerHappy\Nodes\Triggers\PostSaved();
	$nodes['th_core_post_published'] = new HotSource\TriggerHappy\Nodes\Triggers\PostPublished();
	$nodes['th_core_create_post'] = new HotSource\TriggerHappy\Nodes\Actions\CoreCreatePost();
	$nodes['th_core_insert_html_after_post'] = new HotSource\TriggerHappy\Nodes\Actions\CorePostInsertHtml();

	// Sidebar
	$nodes['th_core_insert_html_sidebar'] = new HotSource\TriggerHappy\Nodes\Actions\CoreSidebarInsertHtml();

	// Queries
	$nodes['th_core_any_url_query'] = new HotSource\TriggerHappy\Nodes\Triggers\AnyUrlQuery();
	$nodes['th_core_archive_query'] = new HotSource\TriggerHappy\Nodes\Triggers\ArchiveQuery();
	$nodes['th_core_category_query'] = new HotSource\TriggerHappy\Nodes\Triggers\CategoryQuery();
	$nodes['th_core_taxonomy_query'] = new HotSource\TriggerHappy\Nodes\Triggers\TaxonomyQuery();
	$nodes['th_core_single_post'] = new HotSource\TriggerHappy\Nodes\Triggers\CoreSinglePostViewed();
	$nodes['th_core_single_post_query'] = new HotSource\TriggerHappy\Nodes\Triggers\CoreSinglePostQuery();
	$nodes['th_core_set_query_param'] = new HotSource\TriggerHappy\Nodes\Actions\SetQueryParameter();

	// Comments
	$nodes['th_core_comment_created'] = new HotSource\TriggerHappy\Nodes\Triggers\CommentCreated();
	$nodes['th_core_comment_status_changed'] = new HotSource\TriggerHappy\Nodes\Triggers\CommentStatus();

	// Products
	$nodes['th_core_product_created'] = new \HotSource\TriggerHappy\Nodes\Triggers\ProductCreated();

	return $nodes;
}


function triggerhappy_add_action( $action, $callable ) {
	add_action(
		'all', function ( $args ) use ( $action, $callable ) {
		if ( current_filter() == $action ) {
			call_user_func( $callable, $args );
		}
	}
	);
}

function triggerhappy_load_core_schema() {
	add_filter(
		'triggerhappy_args_wp_post', function ( $p ) {
		return get_post( $p );
	}
	);

	triggerhappy_register_global_field(
		'currentUser',
		'wp_user',
		'The logged in user',
		function () {

			$user = wp_get_current_user();
			$data = [
				'id'       => $user->ID,
				'username' => $user->user_login,
				'fullName' => $user->first_name . ' ' . $user->last_name,
			];

			return $data;
		}
	);

	triggerhappy_register_global_field(
		'wp',
		'wp_query_functions',
		'Built-in WordPress functions',
		function () {

			return [
				'__triggerhappy_call_functions' => 'true',
			];
		}
	);

	triggerhappy_register_global_field(
		'wpPageFunctions',
		'wp_page_functions',
		'Built-in WordPress page/query conditions',
		function () {

			return [
				'__triggerhappy_call_functions' => 'true',
			];
		}
	);


	triggerhappy_register_value_type(
		'wp_post', 'number', function ( $search ) {
		$req = new WP_REST_Request( 'GET', '/wp/v2/posts' );
		$req->set_param( 'search', $search );
		$response = rest_do_request( $req );
		$data = ( $response->get_data() );

		return array_map(
			function ( $d ) {
				return [
					'id'   => $d['id'],
					'text' => $d['name'],
				];
			}, $data
		);
	}, true
	);


	triggerhappy_register_value_type(
		'wp_comment', 'number', function ( $search ) {
		$req = new WP_REST_Request( 'GET', '/wp/v2/comments' );
		$req->set_param( 'search', $search );
		$response = rest_do_request( $req );
		$data = ( $response->get_data() );

		return array_map(
			function ( $d ) {
				return [
					'id'   => $d['id'],
					'text' => $d['name'],
				];
			}, $data
		);
	}, true
	);

	triggerhappy_register_value_type(
		'wp_post_type', 'string', function ( $search ) {
		$result = get_post_types();
		$post_types = [];
		foreach ( $result as $post_type ) {
			array_push( $post_types, [
				'id'   => $post_type,
				'text' => $post_type,
			] );
		}
	}, false
	);

	triggerhappy_register_value_type(
		'wp_post_type', 'string', function () {
		$result = get_post_types();
		$post_types = [];
		foreach ( $result as $post_type ) {
			array_push( $post_types, [
				'id'   => $post_type,
				'text' => $post_type,
			] );
		}

		return $post_types;
	}, false
	);

	triggerhappy_register_value_type(
		'wp_post_status', 'string', function () {
		$result = get_post_stati();
		$post_statuses = [];
		foreach ( $result as $poststatus ) {
			array_push( $post_statuses, [
				'id'   => $poststatus,
				'text' => $poststatus,
			] );
		}

		return $post_statuses;
	}, false
	);


	triggerhappy_register_value_type(
		'triggerhappy_hook_type', 'string', function () {
		return [
			[ 'id' => 'filter', 'text' => 'Filter' ],
			[ 'id' => 'action', 'text' => 'Action' ],
		];
	}, false
	);

	triggerhappy_register_value_type(
		'wp_nav_location', 'string', function () {
		$results = [];
		foreach ( get_registered_nav_menus() as $id => $text ) {
			array_push( $results, [ 'id' => $id, 'text' => $text ] );
		}

		return $results;

	}, false
	);
	triggerhappy_register_value_type(
		'wp_nav_menu', 'number', function () {
		$results = [];
		$menus = get_terms( 'nav_menu' );
		$menus = array_combine( wp_list_pluck( $menus, 'term_id' ), wp_list_pluck( $menus, 'name' ) );
		foreach ( $menus as $id => $text ) {
			array_push( $results, [ 'id' => $id, 'text' => $text ] );
		}

		return $results;

	}, false
	);

	triggerhappy_register_value_type(
		'wp_query', 'array', function () {
	}, false
	);

	triggerhappy_register_value_type(
		'wp_nav_menu_settings', 'array', function () {
	}, false
	);

	triggerhappy_register_value_type(
		'wp_user', 'number', function ( $search ) {
		$req = new WP_REST_Request( 'GET', '/wp/v2/users' );
		$req->set_param( 'search', $search );
		$response = rest_do_request( $req );
		$data = ( $response->get_data() );

		return array_map(
			function ( $d ) {
				return [
					'id'   => $d['id'],
					'text' => $d['name'],
				];
			}, $data
		);
	}, true
	);
	triggerhappy_register_json_schema(
		'wp_user',
		[
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'user',
			'type'       => 'object',
			'properties' =>
				[
					'id'       => [
						'description' => 'Unique identifier for the user.',
						'type'        => 'integer',
						'readonly'    => true,
					],
					'username' => [
						'description' => 'User Name',
						'type'        => 'string',
					],
					'fullName' => [
						'description' => 'Full Name',
						'type'        => 'string',
					],
				],
		]
	);
	triggerhappy_register_json_schema(
		'wp_query_functions',
		[
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'WP Query',
			'type'       => 'object',
			'properties' =>
				[
					'is_main_query'     => [
						'description' => 'Is currently executing the main query on the page',
						'type'        => 'boolean',
						'readonly'    => true,
					],
					'in_the_loop'       => [
						'description' => 'Is currently in the loop',
						'type'        => 'boolean',
						'readonly'    => true,
					],
					'is_user_logged_in' => [
						'description' => 'Is the user currently logged in',
						'type'        => 'boolean',
						'readonly'    => true,
					],
				],

		]
	);
	triggerhappy_register_json_schema(
		'wp_query',
		[
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'WP Query',
			'type'       => 'object',
			'properties' =>
				[
					'is_main_query' => [
						'description' => 'Is currently executing the main query on the page',
						'type'        => 'boolean',
						'readonly'    => true,
					],
					'in_the_loop'   => [
						'description' => 'Is currently in the loop',
						'type'        => 'boolean',
						'readonly'    => true,
					],
				],
		]
	);

	triggerhappy_register_json_schema(
		'wp_nav_menu_settings',
		[
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'WP Nav Menu Settings',
			'type'       => 'object',
			'properties' =>
				[
					'menu'           => [
						'description' => 'Desired Menu (ID, name)',
						'type'        => 'wp_nav_menu',
						'readonly'    => true,
					],
					'theme_location' => [
						'description' => 'The theme location',
						'type'        => 'wp_nav_location',
						'readonly'    => true,
					],
				],
		]
	);
	triggerhappy_register_json_schema(
		'wp_page_functions',
		[
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'WP Functions',
			'type'       => 'object',
			'properties' =>
				[
					'is_single'            => [
						'description' => 'Is a single → being displayed (any post type, not included Pages)',
						'type'        => 'boolean',
						'readonly'    => true,
					],
					'is_post_type_archive' => [
						'description' => 'Is a post archive being displayed',
						'type'        => 'boolean',
						'readonly'    => true,
					],

					'is_singular'    => [
						'description' => 'Is a single post being displayed (including Pages)',
						'type'        => 'boolean',
						'readonly'    => true,
					],
					'is_single_page' => [
						'description' => 'Is a single Page being displayed ',
						'type'        => 'boolean',
						'readonly'    => true,
					],
					'is_home'        => [
						'description' => 'Is the Home Page (When home is set to blog archive)',
						'type'        => 'boolean',
						'readonly'    => true,
					],
					'is_front_page'  => [
						'description' => 'Is the Front Page (When home is set to a static page)',
						'type'        => 'boolean',
						'readonly'    => true,
					],
					'is_admin'       => [
						'description' => 'Is a page in the admin dashboard',
						'type'        => 'boolean',
						'readonly'    => true,
					],

					'is_archive' => [
						'description' => 'Is archive page being displayed',
						'type'        => 'boolean',
						'readonly'    => true,
					],

					'is_category' => [
						'description' => 'Is a tag archive page being displayed',
						'type'        => 'boolean',
						'readonly'    => true,
					],
					'is_tax'      => [
						'description' => 'Is a taxonomy archive page being displayed',
						'type'        => 'boolean',
						'readonly'    => true,
					],

					'is_404'        => [
						'description' => 'When a 404 page is displayed',
						'type'        => 'boolean',
						'readonly'    => true,
					],
					'get_post_type' => [
						'description' => 'Get the post type being displayed',
						'type'        => 'string',
						'readonly'    => true,
					],
				],
		]
	);
	triggerhappy_register_json_schema(
		'wp_post',
		[
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'post',
			'type'       => 'object',
			'properties' =>
				[
					'id'           => [
						'description' => 'Unique identifier for the resource.',
						'type'        => 'integer',
						'readonly'    => true,
					],
					'post_title'   => [
						'description' => 'Post Title.',
						'type'        => 'string',
						'context'     =>
							[
								0 => 'view',
								1 => 'edit',
							],
					],
					'post_name'    =>
						[
							'description' => 'Post slug.',
							'type'        => 'string',
							'context'     =>
								[
									0 => 'view',
									1 => 'edit',
								],
						],
					'permalink'    =>
						[
							'description' => 'Post URL.',
							'type'        => 'string',
							'format'      => 'uri',
							'context'     =>
								[
									0 => 'view',
									1 => 'edit',
								],
							'readonly'    => true,
						],
					'date_created' =>
						[
							'description' => 'The date the post was created, in the sites timezone.',
							'type'        => 'date-time',
							'context'     =>
								[
									0 => 'view',
									1 => 'edit',
								],
							'readonly'    => true,
						],
					'meta_data'    =>
						[
							'description' => 'Meta data.',
							'type'        => 'array',
							'context'     =>
								[
									0 => 'view',
									1 => 'edit',
								],
							'items'       =>
								[
									'type'       => 'object',
									'properties' =>
										[
											'id'    =>
												[
													'description' => 'Meta ID.',
													'type'        => 'integer',
													'context'     =>
														[
															0 => 'view',
															1 => 'edit',
														],
													'readonly'    => true,
												],
											'key'   =>
												[
													'description' => 'Meta key.',
													'type'        => 'string',
													'context'     =>
														[
															0 => 'view',
															1 => 'edit',
														],
												],
											'value' =>
												[
													'description' => 'Meta value.',
													'type'        => 'string',
													'context'     =>
														[
															0 => 'view',
															1 => 'edit',
														],
												],
										],
								],
						],
				],
		]
	);

	triggerhappy_register_json_schema(
		'wp_comment',
		[
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'comment',
			'type'       => 'object',
			'properties' =>
				[
					'id'                   => [
						'description' => 'Unique identifier for the resource.',
						'type'        => 'integer',
						'readonly'    => true,
					],
					'post_id'              => [
						'description' => 'Post ID',
						'type'        => 'string',
					],
					'post'                 => [
						'description' => 'The associated post',
						'type'        => 'wp_post',
					],
					'comment_author'       => [
						'description' => 'The comment authors name',
						'type'        => 'string',
					],
					'comment_author_email' => [
						'description' => 'The comment authors email',
						'type'        => 'string',
					],

					'comment_author_url' => [
						'description' => 'The comment authors webpage',
						'type'        => 'string',
					],

					'comment_author_ip' => [
						'description' => 'The comment authors IP',
						'type'        => 'string',
					],
					'comment_date'      => [
						'description' => 'The date the comment was created (in server\'s timezone)',
						'type'        => 'string',
					],
					'comment_content'   => [
						'description' => 'The content of the comment',
						'type'        => 'string',
					],
					'comment_type'      => [
						'description' => 'The type of the comment (pingback, trackback or empty for normal comments)',
						'type'        => 'string',
					],

					'user_id' => [
						'description' => 'The ID of the author if registered (0 otherwise)',
						'type'        => 'number',
					],

					'user' => [
						'description' => 'The author (if registered)',
						'type'        => 'wp_user',
					],
				],
		]
	);
}

add_filter( 'triggerhappy_nodes', 'triggerhappy_load_core_nodes' );

add_action( 'triggerhappy_schema', 'triggerhappy_load_core_schema' );
add_filter( 'triggerhappy_resolve_field_wp_post__meta_data', 'triggerhappy_resolve_field_wp_post__meta_data', 10, 3 );


add_filter( 'triggerhappy_expression_call_getItem', function ( $result, $obj, $methodName, $args ) {
	if ( count( $args ) == 0 ) {
		return null;
	}

	if ( is_array( $obj ) && isset( $obj[ $args[0] ] ) ) {
		return $obj[ $args[0] ];
	}
	if ( is_object( $obj ) && isset( $obj->{$args[0]} ) ) {
		return $obj->{$args[0]};
	}

	return null;
}, 4, 4 );

function triggerhappy_resolve_field_wp_post__meta_data( $result, $obj, $fieldName ) {
	if ( $obj == null ) {
		return null;
	}

	$meta = get_post_meta( $obj->ID );
	$formattedMeta = [];
	foreach ( $meta as $i => $data ) {
		$formattedMeta[ $i ] = $data[0];
	}

	return [
		'type'  => 'object',
		'value' => $formattedMeta,
	];
}

function triggerhappy_assoc_to_choices( $results ) {
	$choices = [];
	foreach ( $results as $key => $val ) {
		array_push( $choices, [ 'id' => $key, 'text' => $val ] );
	}

	return $choices;
}
