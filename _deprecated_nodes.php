<?php

function deprecatedFrontendNodes() {
	$nodes['th_core_tax_archive'] = [
		'description' => 'When a Taxonomy Archive is being viewed on the front-end',
		'name'        => 'When a Taxonomy Archive is viewed',
		'plugin'      => '',
		'nodeType'    => 'trigger',
		'hook'        => 'template_redirect',
		'callback'    => 'triggerhappy_action_hook',
		'cat'         => 'Front-end',
		'triggerType' => 'render',
		'fields'      => [],
		'nodeFilters' => [
			[
				TH::Filter( TH::Expression( "_N.wpPageFunctions.is_tax" ), 'equals', true ),
			],
		],
	];
	$nodes['th_core_set_title'] = [
		'description' => 'Set the post title',
		'name'        => 'Set Post Title',
		'plugin'      => '',
		'actionType'  => 'render',
		'nodeType'    => 'action',
		'cat'         => 'Front-end',
		'callback'    => 'triggerhappy_filter_hook',
		'hook'        => 'the_title',
		'filters'     => [
			[
				TH::Filter( TH::Expression( "_N.wp.in_the_loop" ), 'equals', true ),
			],
		],
		'expressions' => [
			'title' => "{{.current_title}}",
		],
		'fields'      => [
			triggerhappy_field( 'current_title', 'string', [
				'dir'         => 'start',
				'description' => 'The existing Post Title',
			] ),
			triggerhappy_field( 'post', 'wp_post', [ 'dir' => 'start', 'description' => 'The Post object' ] ),
			triggerhappy_field( 'title', 'string', [ 'dir' => 'in', 'description' => 'The Post Title' ] ),
		],
	];
	$nodes['th_core_the_content'] = [
		'description' => 'Modify the post content before it is displayed',
		'name'        => 'Set Post Content',
		'plugin'      => '',
		'actionType'  => 'render',
		'nodeType'    => 'action',
		'cat'         => 'Front-end',
		'callback'    => 'triggerhappy_filter_hook',
		'hook'        => 'the_content',
		'globals'     => [ 'post' => 'post' ],
		'filters'     => [
			[
				TH::Filter( TH::Expression( "_N.wp.in_the_loop" ), 'equals', true ),
			],
		],
		'fields'      => [
			triggerhappy_field( 'current_content', 'string', [
				'dir'         => 'start',
				'description' => 'The existing Post Content',
			] ),
			triggerhappy_field( 'post', 'wp_post', [ 'dir' => 'start', 'description' => 'The Post object' ] ),
			triggerhappy_field( 'content', 'html', [ 'dir' => 'in', 'description' => 'The Post Content' ] ),
		],

		'expressions' => [
			'content' => "{{.current_content}}",
		],
	];
	$nodes['th_core_archive'] = [
		'description' => 'When a post archive is being viewed on the front-end',
		'name'        => 'When a Post Archive is viewed',
		'plugin'      => '',
		'nodeType'    => 'trigger',
		'hook'        => 'template_redirect',
		'callback'    => 'triggerhappy_action_hook',
		'cat'         => 'Front-end',
		'triggerType' => 'render',
		'fields'      => [],
		'nodeFilters' => [
			[
				TH::Filter( TH::Expression( "_N.wpPageFunctions.is_archive" ), 'equals', true ),
			],
		],
	];
	$nodes['th_core_single_post'] = [
		'description' => 'When a single post is being viewed on the front-end',
		'name'        => 'When a Single Post is viewed',
		'plugin'      => '',
		'triggerType' => 'render',
		'nodeType'    => 'trigger',
		'hook'        => 'template_redirect',
		'callback'    => 'triggerhappy_action_hook',
		'cat'         => 'Front-end',
		'globals'     => [ 'post' => 'post' ],
		'fields'      => [
			triggerhappy_field( 'post', 'wp_post', [ 'dir' => 'start' ] ),
		],
		'nodeFilters' => [
			[
				TH::Filter( TH::Expression( "_N.wpPageFunctions.is_single" ), 'equals', true ),
			],
		],
	];
	$nodes['th_core_any_url'] = [
		'description' => 'When any page, post or archive is being viewed on the front-end',
		'name'        => 'When any front-end URL is viewed',
		'plugin'      => '',
		'triggerType' => 'render',
		'nodeType'    => 'trigger',
		'hook'        => 'template_redirect',
		'callback'    => 'triggerhappy_action_hook',
		'cat'         => 'Front-end',
		'fields'      => [],
	];
	$nodes['th_core_cat_archive'] = [
		'description' => 'When a category is being viewed on the front-end',
		'name'        => 'When a Post Category Archive is viewed',
		'plugin'      => '',
		'nodeType'    => 'trigger',
		'hook'        => 'template_redirect',
		'callback'    => 'triggerhappy_action_hook',
		'cat'         => 'Front-end',
		'triggerType' => 'render',
		'fields'      => [],
		'nodeFilters' => [
			[
				TH::Filter( TH::Expression( "_N.wpPageFunctions.is_category" ), 'equals', true ),
			],
		],
	];
}

function deprecatedNodes() {
	$nodes['th_core_insert_html_after_post'] = [
		'name'        => 'Insert content into post',
		'plugin'      => '',
		'cat'         => 'Posts',
		'nodeType'    => 'action',
		'actionType'  => 'render',
		'callback'    => 'triggerhappy_render_html_after_post_content',
		'description' => 'Insert HTML into the body of a post',
		'fields'      => [
			triggerhappy_field( 'position', 'string', [
				'label'       => 'Position',
				'description' => 'Where to add the content',
				'dir'         => 'in',
				'choices'     => triggerhappy_assoc_to_choices( [
					'before_content' => 'Before the post content',
					'after_content'  => 'After the post content',
				] ),
			] ),
			triggerhappy_field( 'html', 'html', [
				'label'       => 'HTML',
				'description' => 'The HTML to be inserted',
				'dir'         => 'in',
			] ),
		],
	];

	$nodes['th_core_send_email'] = [
		'plugin'      => 'wordpress',
		'cat'         => 'WordPress',
		'name'        => 'Send an email',
		'description' => 'Send a custom email',
		'callback'    => 'triggerhappy_send_email',
		'nodeType'    => 'action',
		'fields'      => [
			triggerhappy_field( 'send_to', 'string', [
				'label'       => 'Send To',
				'description' => 'Enter the recipient email address',
			] ),
			triggerhappy_field( 'subject', 'string', [
				'label'       => 'Subject',
				'description' => 'The email subject',
			] ),
			triggerhappy_field( 'body', 'html', [ 'label' => 'Body', 'description' => 'The body of the email' ] ),

		],
	];

	$nodes['th_core_insert_html_sidebar'] = [
		'name'        => 'Insert content into sidebar',
		'plugin'      => '',
		'cat'         => 'Sidebar',
		'nodeType'    => 'action',
		'actionType'  => 'render',
		'callback'    => 'triggerhappy_render_html_on_position_action',
		'description' => 'Insert HTML into before or after the sidebar',
		'fields'      => [
			triggerhappy_field( 'position', 'string', [
				'label'       => 'Position',
				'description' => 'Where to add the content',
				'dir'         => 'in',
				'choices'     => triggerhappy_assoc_to_choices( [
					'dynamic_sidebar_before' => 'Before the sidebar has rendered',
					'dynamic_sidebar_after'  => 'After the sidebar has rendered',
				] ),
			] ),
			triggerhappy_field( 'html', 'html', [
				'label'       => 'HTML',
				'description' => 'The HTML to be inserted',
				'dir'         => 'in',
			] ),
		],
	];

	$nodes['th_core_create_post'] = [
		'name'        => 'Create a new post',
		'plugin'      => 'WordPress',
		'nodeType'    => 'action',
		'description' => 'Creates (or updates) a page or post',
		'cat'         => 'Posts',
		'callback'    => 'triggerhappy_create_post',
		'fields'      => [
			triggerhappy_field( 'post_id', 'string', [
				'label'       => 'Post ID',
				'description' => 'Specify the post ID to update an existing post. Leave blank to create a new post',
				'dir'         => 'in',
			] ),
			triggerhappy_field( 'post_type', 'wp_post_type', [
				'label'       => 'Post Type',
				'description' => 'Specify the type of post to create',
				'dir'         => 'in',
			] ),
			triggerhappy_field( 'post_title', 'string', [
				'label' => 'Post Title',
				'dir'   => 'in',
			] ),
			triggerhappy_field( 'post_content', 'string', [
				'label' => 'Content',
				'dir'   => 'in',
			] )
			,
			triggerhappy_field( 'post_status', 'wp_post_status', [
				'label' => 'Post Status',
				'dir'   => 'in',
			] ),
		],
	];

	$nodes['th_core_single_post_query'] = [
		'description' => 'When single post data is being queried',
		'name'        => 'When data for a Single Post is being loaded',
		'plugin'      => '',
		'triggerType' => 'query',
		'nodeType'    => 'trigger',
		'hook'        => 'template_redirect',
		'callback'    => 'triggerhappy_action_hook',
		'cat'         => 'Queries',
		'globals'     => [ 'post' => 'post', 'query' => 'wp_query' ],
		'fields'      => [

			triggerhappy_field( 'query', 'wp_query', [ 'dir' => 'start' ] ),
		],
		'nodeFilters' => [
			[
				TH::Filter( TH::Expression( "_N1.query.is_single" ), 'equals', true ),
			],
		],
		'filters'     => [
			[
				TH::Filter( TH::Expression( "_self.query.is_main_query" ), 'equals', true ),
			],
		],
	];

	$nodes['th_core_userupdated'] = [
		'name'     => 'When a User profile is updated',
		'plugin'   => '',
		'cat'      => 'Users',
		'nodeType' => 'trigger',
		'callback' => 'triggerhappy_action_hook',
		'hook'     => 'profile_update',

		'description' => 'When a user profile is saved',
		'fields'      => [
			triggerhappy_field( 'id', 'number', [ 'description' => 'The user ID', 'dir' => 'start' ] ),
			triggerhappy_field( 'user', 'wp_user', [ 'description' => 'The user data', 'dir' => 'start' ] ),
		],

	];

	// Replaced by th_core_wp_user_login
	$nodes['th_core_wp_login'] = [
		'name'        => 'User Logged In',
		'plugin'      => '',
		'nodeType'    => 'trigger',
		'description' => 'When a user has successfully logged in',
		'cat'         => 'Users',
		'callback'    => 'triggerhappy_action_hook',
		'hook'        => 'wp_login',
		'fields'      => [
			triggerhappy_field(
				'user_login', 'string', [
					'dir' => 'start',
				]
			),
			triggerhappy_field(
				'user', 'wp_user', [
					'dir' => 'start',
				]
			),
		],
	];

	// Replaced by th_core_wp_user_logout
	$nodes['th_core_wp_logout'] = [
		'name'        => 'User Logged Out',
		'plugin'      => 'WordPress',
		'nodeType'    => 'trigger',
		'description' => 'When a user has logged out',
		'cat'         => 'User Triggers',
		'callback'    => 'triggerhappy_action_hook',
		'hook'        => 'wp_logout',
		'fields'      => [],
	];

	// Not implemented because of childGraphs functionality which was broken
	$nodes['th_core_triggerhappy_set_navigation_menu'] = [
		'name'        => 'Set the Navigation Menu',
		'description' => 'Set the Navigation Menu',
		'nodeType'    => 'action',
		'simple'      => true,
		'cat'         => 'WordPress',
		"fields"      => [
			triggerhappy_field( 'location', 'wp_nav_location', [ 'description' => 'The Nav Menu Location to update' ] ),
			triggerhappy_field( 'menu', 'wp_nav_menu', [ 'description' => 'The Nav Menu to use' ] ),
		],

		'childGraphs' => json_encode(
			TH::Graph(
				[
					'type'    => 'wp_nav_menu_args',
					'filters' => [
						[
							TH::Filter( TH::Expression( "_N0.location" ), 'equals', TH::Expression( "_N1.settings.theme_location" ) ),
						],
					],
				],
				[
					'type'        => 'core_set_value',
					'expressions' => [
						'set'     => TH::Expression( "_N1.settings.menu" ),
						'toValue' => TH::Expression( "_N0.menu" ),
					],
				]
			)
		),
	];
}