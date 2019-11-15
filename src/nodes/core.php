<?php
include( dirname( __FILE__ ) . "/functions/core_functions.php" );
include( dirname( __FILE__ ) . "/core_advanced.php" );

require_once( dirname( __FILE__ ) . '/../classes/Psr4AutoloaderClass.php' );

// Registering class autoloader
$loader = new \HotSource\TriggerHappy\Psr4AutoloaderClass;
$loader->register();
$loader->addNamespace( 'HotSource\TriggerHappy', 'D:/_htdocs/hotsource/content/plugins/trigger-happy/src/classes' );

function triggerhappy_load_core_nodes( $nodes ) {

	$nodes = triggerhappy_load_core_nodes_advanced( $nodes );

	// Wordpress
	$nodes['th_core_send_email'] = new HotSource\TriggerHappy\Nodes\Actions\CoreSendEmail();
	$nodes['th_core_user_updated'] = new HotSource\TriggerHappy\Nodes\Triggers\UserUpdated();
	$nodes['th_core_user_login'] = new HotSource\TriggerHappy\Nodes\Triggers\UserLogin();
	$nodes['th_core_user_logout'] = new HotSource\TriggerHappy\Nodes\Triggers\UserLogout();
	$nodes['th_core_add_nav_menu_item'] = new HotSource\TriggerHappy\Nodes\Actions\AddNavMenuItem();
	$nodes['th_core_timer'] = new HotSource\TriggerHappy\Nodes\Triggers\Timer();

	// Posts
	$nodes['th_core_insert_html_after_post'] = new HotSource\TriggerHappy\Nodes\Actions\CorePostInsertHtml();
	$nodes['th_core_create_post'] = new HotSource\TriggerHappy\Nodes\Actions\CoreCreatePost();

	// Sidebar
	$nodes['th_core_insert_html_sidebar'] = new HotSource\TriggerHappy\Nodes\Actions\CoreSidebarInsertHtml();

	// Queries
	$nodes['th_core_single_post'] = new HotSource\TriggerHappy\Nodes\Triggers\CoreSinglePostViewed();
	$nodes['th_core_single_post_query'] = new HotSource\TriggerHappy\Nodes\Triggers\CoreSinglePostQuery();
	$nodes['th_core_set_query_param'] = new HotSource\TriggerHappy\Nodes\Actions\SetQueryParameter();


	$nodes['th_core_archive_query'] = [
		'description' => 'When data for a post archive is being queried',
		'name'        => 'When Post Archive data is loaded',
		'plugin'      => '',
		'nodeType'    => 'trigger',
		'hook'        => 'pre_get_posts',
		'callback'    => 'triggerhappy_action_hook',
		'cat'         => 'Queries',
		'triggerType' => 'query',
		'globals'     => [ 'query' => 'wp_query' ],
		'fields'      => [
			triggerhappy_field( 'query', 'wp_query', [ 'dir' => 'start' ] ),
		],
		'nodeFilters' => [
			[
				TH::Filter( TH::Expression( "_N1.query.is_archive" ), 'equals', true ),
			],
		],
		'filters'     => [
			[
				TH::Filter( TH::Expression( "_self.query.is_main_query" ), 'equals', true ),
			],
		],
	];


	$nodes['th_core_cat_archive_query'] = [
		'description' => 'When data for a category is being queried',
		'name'        => 'When Post Category data is loaded',
		'plugin'      => '',
		'nodeType'    => 'trigger',
		'hook'        => 'pre_get_posts',
		'callback'    => 'triggerhappy_action_hook',
		'cat'         => 'Queries',
		'triggerType' => 'query',
		'fields'      => [
			triggerhappy_field( 'query', 'wp_query', [ 'dir' => 'start' ] ),
		],
		'nodeFilters' => [
			[
				TH::Filter( TH::Expression( "_N1.query.is_category" ), 'equals', true ),
			],
		],
		'filters'     => [
			[
				TH::Filter( TH::Expression( "_self.query.is_main_query" ), 'equals', true ),
			],
		],
	];


	$nodes['th_core_tax_archive_query'] = [
		'description' => 'When loading data for a Taxonomy Archive',
		'name'        => 'When Taxonomy Archive data is loaded',
		'plugin'      => '',
		'nodeType'    => 'trigger',
		'hook'        => 'pre_get_posts',
		'callback'    => 'triggerhappy_action_hook',
		'cat'         => 'Queries',
		'triggerType' => 'query',
		'fields'      => [
			triggerhappy_field( 'query', 'wp_query', [ 'dir' => 'start' ] ),
		],
		'nodeFilters' => [
			[
				TH::Filter( TH::Expression( "_N1.query.is_tax" ), 'equals', true ),
			],
		],
		'filters'     => [
			[
				TH::Filter( TH::Expression( "_self.query.is_main_query" ), 'equals', true ),
			],
		],
	];


	$nodes['th_core_any_url_query'] = [
		'description' => 'When any page, post or archive data is being queried',
		'name'        => 'When loading data for any front-end URL',
		'plugin'      => '',
		'triggerType' => 'query',
		'nodeType'    => 'trigger',
		'hook'        => 'template_redirect',
		'callback'    => 'triggerhappy_action_hook',
		'cat'         => 'Queries',
		'fields'      => [
			triggerhappy_field( 'query', 'wp_query', [ 'dir' => 'start' ] ),
		],
		'filters'     => [
			[
				TH::Filter( TH::Expression( "_self.query.is_main_query" ), 'equals', true ),
			],
		],
	];

	$nodes['th_core_commentcreated'] = [
		'name'     => 'When a Comment is created',
		'plugin'   => '',
		'cat'      => 'Comments',
		'nodeType' => 'trigger',
		'callback' => 'triggerhappy_action_hook',
		'hook'     => 'wp_insert_comment',

		'description' => 'When a comment is created and saved',
		'fields'      => [
			triggerhappy_field( 'commentId', 'number', [ 'description' => 'The comment ID', 'dir' => 'start' ] ),
			triggerhappy_field( 'comment', 'wp_comment', [
				'description' => 'The added comment',
				'dir'         => 'start',
			] ),
		],

	];

	$nodes['th_core_commentapproved'] = [
		'name'     => 'When a Comment is approved',
		'plugin'   => '',
		'cat'      => 'Comments',
		'nodeType' => 'trigger',
		'callback' => 'triggerhappy_filter_hook',
		'hook'     => 'wp_insert_comment',

		'description' => 'When a comment is created and saved',
		'fields'      => [
			triggerhappy_field( 'commentId', 'number', [ 'description' => 'The comment ID', 'dir' => 'start' ] ),
			triggerhappy_field( 'comment', 'wp_comment', [
				'description' => 'The added comment',
				'dir'         => 'start',
			] ),
		],

	];

	$nodes['th_core_postsaved'] = [
		'name'     => 'When a Post is saved',
		'plugin'   => '',
		'cat'      => 'Posts',
		'nodeType' => 'trigger',
		'callback' => 'triggerhappy_action_hook',
		'hook'     => 'save_post',

		'description' => 'When a post is created or updated',
		'fields'      => [
			triggerhappy_field( 'ID', 'number', [ 'description' => 'The comment ID', 'dir' => 'start' ] ),
			triggerhappy_field( 'post', 'wp_post', [ 'description' => 'The added comment', 'dir' => 'start' ] ),
		],

	];

	$nodes['th_core_postpublished'] = [
		'name'     => 'When a Post is published',
		'plugin'   => '',
		'cat'      => 'Posts',
		'nodeType' => 'trigger',
		'callback' => 'triggerhappy_action_hook',
		'hook'     => 'publish_post',

		'description' => 'When a post is saved as Published',
		'fields'      => [
			triggerhappy_field( 'id', 'number', [ 'description' => 'The post ID', 'dir' => 'start' ] ),
			triggerhappy_field( 'post', 'wp_post', [ 'description' => 'The saved post', 'dir' => 'start' ] ),
		],

	];

	$nodes['th_core_user_register'] = [
		'name'     => 'When a User profile is created',
		'plugin'   => '',
		'cat'      => 'Users',
		'nodeType' => 'trigger',
		'callback' => 'triggerhappy_action_hook',
		'hook'     => 'user_register',

		'description' => 'When a User Profile is created/registered',
		'fields'      => [
			triggerhappy_field( 'id', 'number', [ 'description' => 'The user ID', 'dir' => 'start' ] ),
			triggerhappy_field( 'user', 'wp_user', [ 'description' => 'The user data', 'dir' => 'start' ] ),
		],

	];
	$nodes['th_core_wp_redirect'] = [
		'name'     => 'Redirect to URL',
		'plugin'   => '',
		'cat'      => 'WordPress',
		'nodeType' => 'action',
		'callback' => 'triggerhappy_wp_redirect',
		'fields'   => [
			triggerhappy_field( 'url', 'array' ),
		],
	];


	$nodes['th_core_wp_logout'] = [
		'name'        => 'Logout the current user',
		'description' => 'Forces the user to log out',
		'plugin'      => '',
		'cat'         => 'WordPress',
		'nodeType'    => 'action',
		'callback'    => 'triggerhappy_wp_logout',
		'fields'      => [],
	];

	$nodes['th_core_wp_login'] = [
		'name'        => 'Log in as user',
		'description' => 'Logs in as the specified user',
		'plugin'      => '',
		'cat'         => 'WordPress',
		'nodeType'    => 'action',
		'callback'    => 'triggerhappy_wp_login',
		'fields'      => [
			triggerhappy_field( 'username', 'string', [
				'label'       => 'User Name',
				'description' => 'The user name of the user to log in as',
			] ),
			triggerhappy_field( 'password', 'string', [
				'label'       => 'Password',
				'description' => 'The password to use when logging in ',
			] ),
		],
	];


	$nodes['th_core_filter_logged_in'] = [
		'name'        => 'If the user is logged in',
		'plugin'      => '',
		'cat'         => 'Users',
		'nodeType'    => 'condition',
		'callback'    => 'triggerhappy_condition',
		'fields'      => [],
		'nodeFilters' => [
			[

				TH::Filter( TH::Expression( "_N.wp.is_user_logged_in" ), 'equals', true ),
			],
		],
	];

	$nodes['th_core_filter_not_logged_in'] = [
		'name'        => 'If the user is not logged in',
		'plugin'      => '',
		'cat'         => 'Users',
		'nodeType'    => 'condition',
		'callback'    => 'triggerhappy_condition',
		'fields'      => [],
		'nodeFilters' => [
			[

				TH::Filter( TH::Expression( "_N.wp.is_user_logged_in" ), 'equals', false ),
			],
		],

	];

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
						'description' => 'Is a single post being displayed (any post type, not included Pages)',
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

					'is_category' => [
						'description' => 'Is a category archive page being displayed',
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
