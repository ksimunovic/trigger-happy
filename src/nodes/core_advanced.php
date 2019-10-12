<?php
function triggerhappy_load_core_nodes_advanced( $nodes ) {
	$nodes['th_core_arrayremove'] = [
		'name'   => 'Remove from Array',
		'plugin' => '',
		'cat'    => 'php',

		'callback' => 'triggerhappy_arr_remove',
		'collapse' => true,
		'fields'   => [
			triggerhappy_field( 'data', 'array' ),
			triggerhappy_field( 'key', 'string' ),
			triggerhappy_field(
				'result', 'string', [
					'dir' => 'out',
				]
			),
		],
		'advanced' => true,
	];
	$nodes['th_core_set_array_param'] = [
		'description' => 'Sets an array value',
		'name'        => 'Set Array Value',
		'plugin'      => '',
		'nodeType'    => 'action',
		'cat'         => 'WordPress',
		'callback'    => 'triggerhappy_set_arr_value',

		'fields'   => [
			triggerhappy_field( 'array', 'array' ),
			triggerhappy_field( 'key', 'string' ),
			triggerhappy_field( 'value', 'string' ),
		],
		'advanced' => true,
	];


	$nodes['th_core_set_meta_value'] = [
		'description' => 'Sets a meta value',
		'name'        => 'Set Meta Value',
		'plugin'      => '',
		'nodeType'    => 'action',
		'cat'         => 'WordPress',
		'callback'    => 'triggerhappy_set_meta_value',

		'fields'   => [
			triggerhappy_field( 'post', 'wp_post' ),
			triggerhappy_field( 'key', 'string' ),
			triggerhappy_field( 'value', 'number' ),
		],
		'advanced' => true,
	];
	$nodes['th_core_set_meta_value_str'] = [
		'description' => 'Sets a meta value',
		'name'        => 'Set Meta Value',
		'plugin'      => '',
		'nodeType'    => 'action',
		'cat'         => 'WordPress',
		'callback'    => 'triggerhappy_set_meta_value',

		'fields'   => [
			triggerhappy_field( 'post', 'wp_post' ),
			triggerhappy_field( 'key', 'string' ),
			triggerhappy_field( 'value', 'string' ),
		],
		'advanced' => true,
	];
	$nodes['th_core_set_value'] = [
		'description' => 'Modify a value from another action',
		'name'        => 'Set Value',
		'plugin'      => '',
		'callback'    => 'triggerhappy_core_set_value',
		'nodeType'    => 'action',
		'cat'         => 'WordPress',
		'fields'      => [
			triggerhappy_field( 'set', '@any' ),
			triggerhappy_field( 'toValue', '$set' ),
			triggerhappy_field(
				'updated', 'object', [
					'dir' => 'out',
				]
			),
		],
		'advanced'    => true,
	];
	$nodes['th_core_init'] = [
		'cat'         => 'WordPress',
		'name'        => 'On Init',
		'description' => 'Whenever a WordPress page is loaded',
		'callback'    => 'triggerhappy_action_hook',
		'hook'        => 'init',
		'nodeType'    => 'trigger',
		'plugin'      => 'wordpress',
		'fields'      => [],
		'advanced'    => true,
	];
	$nodes['th_core_create_comment'] = [
		'plugin'      => 'wordpress',
		'cat'         => 'WordPress',
		'name'        => 'Create a comment',
		'description' => 'Add a comment to a post',
		'callback'    => 'triggerhappy_create_comment',
		'nodeType'    => 'action',
		'fields'      => [
			triggerhappy_field( 'post_id', 'int' ),
			triggerhappy_field( 'author', 'string' ),
			triggerhappy_field( 'author_email', 'string' ),
			triggerhappy_field( 'author_url', 'string' ),
			triggerhappy_field( 'comment_text', 'string' ),

		],
		'advanced'    => true,
	];
	$nodes['th_core_render_html'] = [
		'description' => 'Renders custom HTML',
		'name'        => 'Display HTML',
		'plugin'      => '',
		'actionType'  => 'render',
		'nodeType'    => 'action',
		'cat'         => 'Front-end',
		'callback'    => 'triggerhappy_core_render_html',
		'fields'      => [
			triggerhappy_field( 'html', 'html' ),
		],
		'actionType'  => 'output',
		'advanced'    => true,
	];

	$nodes['th_core_create_post_type'] = [
		'plugin'      => 'wordpress',
		'cat'         => 'WordPress',
		'name'        => 'Create a new Post Type',
		'description' => 'Adds a custom Post Type',
		'callback'    => 'triggerhappy_create_post_type',
		'nodeType'    => 'action',
		'fields'      => [
			triggerhappy_field( 'post_type', 'string', [
				'required'    => true,
				'label'       => 'Post Type Name',
				'description' => 'Must contain lowercase alphanumeric characters only (no spaces)',
			] ),
			triggerhappy_field( 'label', 'string', [
				'label'       => 'Label',
				'description' => 'The label of the post type to be shown in the WordPress dashboard (eg: Movie)',
			] ),
			triggerhappy_field( 'label_plural', 'string', [
				'label'       => 'Label (Plural)',
				'description' => 'The plural label of the post type to be shown in the WordPress dashboard (eg: Movies)',
			] ),
			triggerhappy_field( 'public', 'boolean', [
				'label'       => 'Is Public?',
				'description' => 'Whether or not this post type should be publicly visible (ie: browseable on the front-end)',
			] ),
			triggerhappy_field( 'has_archive', 'boolean', [
				'label'       => 'Has Archive?',
				'description' => 'Whether or not this post type should have archive pages',
			] ),

			triggerhappy_field( 'add_new', 'string', [
				'advanced'    => 'Labels',
				'label'       => 'Add New Text',
				'description' => 'The Add New button text (default: "Add New")',
			] ),
			triggerhappy_field( 'add_new_item', 'string', [
				'advanced'    => 'Labels',
				'label'       => 'Add New Item Text',
				'description' => 'The Add New button text (default: "Add New Post")',
			] ),
			triggerhappy_field( 'edit_item', 'string', [
				'advanced'    => 'Labels',
				'label'       => 'Edit Item Text',
				'description' => 'The Edit item text (default: "Edit Post")',
			] ),
			triggerhappy_field( 'new_item', 'string', [
				'advanced'    => 'Labels',
				'label'       => 'New Item Text',
				'description' => 'The New item text (default: "New Post")',
			] ),
			triggerhappy_field( 'view_item', 'string', [
				'advanced'    => 'Labels',
				'label'       => 'View Item Text',
				'description' => 'The View item text (default: "View Post")',
			] ),
			triggerhappy_field( 'view_items', 'string', [
				'advanced'    => 'Labels',
				'label'       => 'View Items (Archive)',
				'description' => 'The View Archive text (default: "View Posts")',
			] ),
			triggerhappy_field( 'search_items', 'string', [
				'advanced'    => 'Labels',
				'label'       => 'Search Items Text',
				'description' => 'The Search Item text (default: "Search Posts")',
			] ),
			triggerhappy_field( 'not_found', 'string', [
				'advanced'    => 'Labels',
				'label'       => 'Not Found Text',
				'description' => 'The  text displayed when no posts are found (default: "No posts found")',
			] ),
			triggerhappy_field( 'not_found_in_trash', 'string', [
				'advanced'    => 'Labels',
				'label'       => 'Not Found Trash Text',
				'description' => 'The text displayed when no posts are found in trash (default: "No posts found in trash")',
			] ),
			triggerhappy_field( 'all_items', 'string', [
				'advanced'    => 'Labels',
				'label'       => 'All Items Label',
				'description' => 'The label for the All Items submenu (default: "All Posts")',
			] ),
			triggerhappy_field( 'archives', 'string', [
				'advanced'    => 'Labels',
				'label'       => 'Archives Label',
				'description' => 'The label for the Archive in menus  (default: "Post Archives")',
			] ),
			triggerhappy_field( 'attributes', 'string', [
				'advanced'    => 'Labels',
				'label'       => 'Attributes Label',
				'description' => 'The label for the Attributes meta box (default: "Post Attributes")',
			] ),


			triggerhappy_field( 'exclude_from_search', 'boolean', [
				'advanced'    => 'Advanced Settings',
				'label'       => 'Exclude from Search',
				'description' => 'Whether or not the posts should be excluded from front-end search results',
			] ),
			triggerhappy_field( 'description', 'string', [
				'advanced'    => 'Advanced Settings',
				'label'       => 'Description',
				'description' => 'A short descriptive summary of what the post type is.',
			] ),
			triggerhappy_field( 'menu_position', 'string', [
				'advanced'    => 'Advanced Settings',
				'label'       => 'Menu Position',
				'description' => 'The position in the menu order the post type should appear. (number from 0 - 100, eg: 5 = Below Posts, 10 = Below Media)',
			] ),
			triggerhappy_field( 'is_hierarchical', 'string', [
				'advanced'    => 'Advanced Settings',
				'label'       => 'Is Hierarchical',
				'description' => 'Is Hierarchical (eg: Allows a parent post to be specified)',
			] ),


		],
		'advanced'    => true,
	];
	$nodes['th_core_wp_manage_posts_columns'] = [
		'name'        => 'Manage Posts Columns',
		'plugin'      => 'WordPress',
		'nodeType'    => 'trigger',
		'description' => 'Add/Remove post table columns',
		'hook'        => 'manage_posts_columns',
		'cat'         => 'Admin',
		'callback'    => 'triggerhappy_filter_hook',
		'fields'      => [
			triggerhappy_field(
				'columns', 'array', [
					'dir' => 'start',
				]
			),
		],
		'advanced'    => true,
	];
	$nodes['th_core_wp_head'] = [
		'name'        => 'Page Header rendered',
		'plugin'      => 'WordPress',
		'nodeType'    => 'trigger',
		'description' => 'When the Page Header is rendered (wp_head)',
		'cat'         => 'Front-end',
		'callback'    => 'triggerhappy_action_hook',
		'hook'        => 'wp_head',
		'globals'     => [ 'post' => 'post' ],
		'fields'      => [
			triggerhappy_field(
				'post', 'wp_post', [
					'dir' => 'start',
				]
			),
		],
		'advanced'    => true,
	];
	$nodes['th_core_wp_nav_menu_items'] = [
		'name'        => 'Nav Menu Items',
		'plugin'      => 'WordPress',
		'nodeType'    => 'trigger',
		'description' => 'Modify the nav menu items',
		'cat'         => 'Front-end',
		'callback'    => 'triggerhappy_filter_hook',
		'hook'        => 'wp_nav_menu_items',
		'globals'     => [ 'post' => 'post' ],
		'fields'      => [
			triggerhappy_field(
				'menu_items', 'array', [
					'dir' => 'start',
				]
			),
			triggerhappy_field(
				'settings', 'wp_nav_menu_settings', [
					'dir' => 'start',
				]
			),
		],
		'advanced'    => true,
	];
	$nodes['th_core_wp_nav_menu_args'] = [
		'name'        => 'Modify Nav Menu Settings',
		'plugin'      => 'WordPress',
		'nodeType'    => 'trigger',
		'description' => 'Modify the nav menu arguments',
		'cat'         => 'Front-end',
		'callback'    => 'triggerhappy_filter_hook',
		'hook'        => 'wp_nav_menu_args',
		'globals'     => [ 'post' => 'post' ],
		'fields'      => [
			triggerhappy_field(
				'settings', 'wp_nav_menu_settings', [
					'dir' => 'start',
				]
			),
		],
		'advanced'    => true,
	];

	$nodes['th_core_wp_footer'] = [
		'name'        => 'Page Footer',
		'plugin'      => 'WordPress',
		'nodeType'    => 'trigger',
		'description' => 'Add scripts or HTML at the bottom of a page on the front-end',
		'cat'         => 'Front-end',
		'callback'    => 'triggerhappy_action_hook',
		'hook'        => 'wp_footer',
		'globals'     => [ 'post' => 'post' ],
		'fields'      => [
			triggerhappy_field(
				'post', 'wp_post', [
					'dir' => 'start',
				]
			),
		],
		'advanced'    => true,
	];

	$nodes['th_core_pre_get_posts'] = [
		'name'        => 'Before fetching posts',
		'plugin'      => 'WordPress',
		'nodeType'    => 'trigger',
		'description' => 'Modify the query before fetching posts',
		'cat'         => 'Query',

		'callback' => 'triggerhappy_action_hook',
		'hook'     => 'pre_get_posts',
		'fields'   => [
			triggerhappy_field(
				'query', 'wp_query', [
					'dir' => 'start',
				]
			),
		],
		'advanced' => true,
	];
	$nodes['th_core_pre_get_comments'] = [
		'name'        => 'Before fetching comments',
		'plugin'      => 'WordPress',
		'nodeType'    => 'trigger',
		'description' => 'Modify the query before fetching comments',
		'cat'         => 'Query',

		'callback' => 'triggerhappy_action_hook',
		'hook'     => 'pre_get_posts',
		'fields'   => [
			triggerhappy_field(
				'query', 'array', [
					'dir' => 'start',
				]
			),
		],
		'advanced' => true,
	];
	$nodes['th_core_dynamic_sidebar_before'] = [
		'name'        => 'Before the sidebar is displayed',
		'plugin'      => 'WordPress',
		'nodeType'    => 'trigger',
		'triggerType' => 'output',
		'description' => 'Before the sidebar has started rendering',
		'cat'         => 'Front-end',
		'callback'    => 'triggerhappy_action_hook',
		'hook'        => 'dynamic_sidebar_before',
		'globals'     => [ 'post' => 'post' ],
		'fields'      => [
			triggerhappy_field(
				'post', 'wp_post', [
					'dir' => 'start',
				]
			),
		],
		'advanced'    => true,
	];
	$nodes['th_core_dynamic_sidebar_after'] = [
		'name'        => 'After the sidebar is displayed',
		'plugin'      => 'WordPress',
		'nodeType'    => 'trigger',
		'triggerType' => 'output',
		'description' => 'After the sidebar has finished rendering',
		'cat'         => 'Front-end',
		'callback'    => 'triggerhappy_action_hook',
		'hook'        => 'dynamic_sidebar_after',
		'globals'     => [ 'post' => 'post' ],
		'fields'      => [
			triggerhappy_field(
				'post', 'wp_post', [
					'dir' => 'start',
				]
			),
		],
		'advanced'    => true,
	];
	$nodes['th_core_wp_custom_hook'] = [
		'name'        => 'Custom Hook',
		'plugin'      => 'WordPress',
		'nodeType'    => 'trigger',
		'description' => 'When a custom hook is fired',
		'cat'         => 'WordPress',
		'callback'    => 'triggerhappy_custom_hook',
		'fields'      => [
			triggerhappy_field(
				'hookType', 'triggerhappy_hook_type', [
					'dir'         => 'in',
					'label'       => 'Hook Type',
					'description' => 'The type of hook (filter or action)',
				]
			),
			triggerhappy_field(
				'hookName', 'string', [
					'dir'         => 'in',
					'label'       => 'Hook Name',
					'description' => 'The name of the custom hook',
				]
			),
			triggerhappy_field(
				'priority', 'number', [
					'dir'         => 'in',
					'label'       => 'Priority',
					'default'     => '10',
					'description' => 'The priority of hook (default 10)',
				]
			),
			triggerhappy_field(
				'args', 'string', [
					'dir'         => 'start',
					'label'       => 'Arguments',
					'description' => 'The hook arguments',
				]
			),
		],
		'advanced'    => true,
	];
	$nodes['th_core_wp_post_column_content'] = [
		'name'        => 'Post Column Content',
		'plugin'      => 'WordPress',
		'nodeType'    => 'trigger',
		'description' => 'Display content for column',
		'hook'        => 'manage_posts_custom_column',
		'cat'         => 'Admin',
		'callback'    => 'triggerhappy_custom_column',
		'fields'      => [
			triggerhappy_field( 'columnName', 'string', [
				'dir' => 'start',
			] ),
			triggerhappy_field( 'post', 'wp_post', [
				'dir' => 'start',
			] ),
		],
		'advanced'    => true,
	];
	$nodes['th_core_wp_custom_url'] = [
		'name'        => 'Custom URL',
		'plugin'      => 'core',
		'nodeType'    => 'trigger',
		'description' => 'Run this flow when a custom URL is visited',
		'cat'         => 'Front-end',
		'callback'    => 'triggerhappy_wordpress_custom_url',
		'fields'      => [
			triggerhappy_field( 'url', 'string', [
				'dir' => 'start',
			] ),
		],
		'advanced'    => true,
	];
	$nodes['th_core_wp_custom_shortcode'] = [
		'name'        => 'Custom Shortcode',
		'plugin'      => 'core',
		'nodeType'    => 'trigger',
		'description' => 'Add a custom shortcode',
		'cat'         => 'WordPress',
		'callback'    => 'triggerhappy_wordpress_custom_shortcode',
		'fields'      => [
			triggerhappy_field( 'shortcode', 'string', [
				'dir' => 'in',
			] ),
			triggerhappy_field( 'allowedAttributes', 'string', [
				'dir'         => 'in',
				'description' => 'A comma delimited list of allowed attributes (eg: "name,description,title,url")',
			] ),
			triggerhappy_field( 'shortcodeData', 'array', [
				'dir'         => 'out',
				'description' => 'The supplied shortcode data',
			] ),
		],
		'advanced'    => true,
	];

	return $nodes;
}
