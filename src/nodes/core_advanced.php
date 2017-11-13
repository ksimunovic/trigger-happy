<?php
function triggerhappy_load_core_nodes_advanced( $nodes ) {
	$nodes['th_core_arrayremove'] = array(
		'name' => 'Remove from Array',
		'plugin' => '',
		'cat' => 'php',

		'callback' => 'triggerhappy_arr_remove',
		'collapse' => true,
		'fields' => array(
			triggerhappy_field( 'data', 'array' ),
			triggerhappy_field( 'key', 'string' ),
			triggerhappy_field(
				'result', 'string', array(
					'dir' => 'out',
				)
			),
		),
		'advanced' => true
	);
	$nodes['th_core_set_array_param'] = array(
		'description' => 'Sets an array value',
		'name' => 'Set Array Value',
		'plugin' => '',
		'nodeType' => 'action',
		'cat' => 'WordPress',
		'callback' => 'triggerhappy_set_arr_value',

		'fields' => array(
			triggerhappy_field( 'array', 'array' ),
			triggerhappy_field( 'key', 'string' ),
			triggerhappy_field( 'value', 'string' ),
		),
		'advanced' => true
	);



	$nodes['th_core_set_meta_value'] = array(
		'description' => 'Sets a meta value',
		'name' => 'Set Meta Value',
		'plugin' => '',
		'nodeType' => 'action',
		'cat' => 'WordPress',
		'callback' => 'triggerhappy_set_meta_value',

		'fields' => array(
			triggerhappy_field( 'post', 'wp_post' ),
			triggerhappy_field( 'key', 'string' ),
			triggerhappy_field( 'value', 'number' ),
		),
		'advanced' => true
	);
	$nodes['th_core_set_meta_value_str'] = array(
		'description' => 'Sets a meta value',
		'name' => 'Set Meta Value',
		'plugin' => '',
		'nodeType' => 'action',
		'cat' => 'WordPress',
		'callback' => 'triggerhappy_set_meta_value',

		'fields' => array(
			triggerhappy_field( 'post', 'wp_post' ),
			triggerhappy_field( 'key', 'string' ),
			triggerhappy_field( 'value', 'string' ),
		),
		'advanced' => true
	);
	$nodes['th_core_set_value'] = array(
		'description' => 'Modify a value from another action',
		'name' => 'Set Value',
		'plugin' => '',
		'callback' => 'triggerhappy_core_set_value',
		'nodeType' => 'action',
		'cat' => 'WordPress',
		'fields' => array(
			triggerhappy_field( 'set', '@any' ),
			triggerhappy_field( 'toValue', '$set' ),
			triggerhappy_field(
				'updated', 'object', array(
					'dir' => 'out',
				)
			)
		),
		'advanced' => true
	);
	$nodes['th_core_init'] = array(
		'cat' => 'WordPress',
		'name' => 'On Init',
		'description' => 'Whenever a WordPress page is loaded',
		'callback' => 'triggerhappy_action_hook',
		'hook' => 'init',
		'nodeType' => 'trigger',
		'plugin' => 'wordpress',
		'fields' => array(),
		'advanced' => true
	);
	$nodes['th_core_create_comment'] = array(
		'plugin' => 'wordpress',
		'cat' => 'WordPress',
		'name' => 'Create a comment',
		'description' => 'Add a comment to a post',
		'callback' => 'triggerhappy_create_comment',
		'nodeType' => 'action',
		'fields' => array(
			triggerhappy_field( 'post_id', 'int' ),
			triggerhappy_field( 'author', 'string' ),
			triggerhappy_field( 'author_email', 'string' ),
			triggerhappy_field( 'author_url', 'string' ),
			triggerhappy_field( 'comment_text', 'string' ),

		),
		'advanced' => true
	);
	$nodes['th_core_render_html'] = array(
		'description' => 'Renders custom HTML',
		'name' => 'Display HTML',
		'plugin' => '',
		'actionType'=>'render',
		'nodeType' => 'action',
		'cat' => 'Front-end',
		'callback' => 'triggerhappy_core_render_html',
		'fields' => array(
			triggerhappy_field( 'html', 'html' ),
		),
		'actionType'=>'output',
		'advanced'=>true
	);

	$nodes['th_core_create_post_type'] = array(
		'plugin' => 'wordpress',
		'cat' => 'WordPress',
		'name' => 'Create a new Post Type',
		'description' => 'Adds a custom Post Type',
		'callback' => 'triggerhappy_create_post_type',
		'nodeType' => 'action',
		'fields' => array(
			triggerhappy_field( 'post_type', 'string', array( 'required'=>true, 'label' => 'Post Type Name', 'description' => 'Must contain lowercase alphanumeric characters only (no spaces)') ),
			triggerhappy_field( 'label', 'string', array( 'label' => 'Label', 'description' => 'The label of the post type to be shown in the WordPress dashboard (eg: Movie)' ) ),
			triggerhappy_field( 'label_plural', 'string', array( 'label' => 'Label (Plural)', 'description' => 'The plural label of the post type to be shown in the WordPress dashboard (eg: Movies)' ) ),
			triggerhappy_field( 'public', 'boolean', array( 'label' => 'Is Public?', 'description' => 'Whether or not this post type should be publicly visible (ie: browseable on the front-end)' ) ),
			triggerhappy_field( 'has_archive', 'boolean', array( 'label' => 'Has Archive?', 'description' => 'Whether or not this post type should have archive pages' ) ),

			triggerhappy_field( 'add_new', 'string', array( 'advanced'=>'Labels', 'label' => 'Add New Text', 'description' => 'The Add New button text (default: "Add New")' ) ),
			triggerhappy_field( 'add_new_item', 'string', array( 'advanced'=>'Labels', 'label' => 'Add New Item Text', 'description' => 'The Add New button text (default: "Add New Post")' ) ),
			triggerhappy_field( 'edit_item', 'string', array( 'advanced'=>'Labels', 'label' => 'Edit Item Text', 'description' => 'The Edit item text (default: "Edit Post")' ) ),
			triggerhappy_field( 'new_item', 'string', array( 'advanced'=>'Labels', 'label' => 'New Item Text', 'description' => 'The New item text (default: "New Post")' ) ),
			triggerhappy_field( 'view_item', 'string', array( 'advanced'=>'Labels', 'label' => 'View Item Text', 'description' => 'The View item text (default: "View Post")' ) ),
			triggerhappy_field( 'view_items', 'string', array( 'advanced'=>'Labels', 'label' => 'View Items (Archive)', 'description' => 'The View Archive text (default: "View Posts")' ) ),
			triggerhappy_field( 'search_items', 'string', array( 'advanced'=>'Labels', 'label' => 'Search Items Text', 'description' => 'The Search Item text (default: "Search Posts")' ) ),
			triggerhappy_field( 'not_found', 'string', array( 'advanced'=>'Labels', 'label' => 'Not Found Text', 'description' => 'The  text displayed when no posts are found (default: "No posts found")' ) ),
			triggerhappy_field( 'not_found_in_trash', 'string', array( 'advanced'=>'Labels', 'label' => 'Not Found Trash Text', 'description' => 'The text displayed when no posts are found in trash (default: "No posts found in trash")' ) ),
			triggerhappy_field( 'all_items', 'string', array( 'advanced'=>'Labels', 'label' => 'All Items Label', 'description' => 'The label for the All Items submenu (default: "All Posts")' ) ),
			triggerhappy_field( 'archives', 'string', array( 'advanced'=>'Labels', 'label' => 'Archives Label', 'description' => 'The label for the Archive in menus  (default: "Post Archives")' ) ),
			triggerhappy_field( 'attributes', 'string', array( 'advanced'=>'Labels', 'label' => 'Attributes Label', 'description' => 'The label for the Attributes meta box (default: "Post Attributes")' ) ),


			triggerhappy_field( 'exclude_from_search', 'boolean', array( 'advanced'=>'Advanced Settings', 'label' => 'Exclude from Search', 'description' => 'Whether or not the posts should be excluded from front-end search results' ) ),
			triggerhappy_field( 'description', 'string', array( 'advanced'=>'Advanced Settings', 'label' => 'Description', 'description' => 'A short descriptive summary of what the post type is.' ) ),
			triggerhappy_field( 'menu_position', 'string', array( 'advanced'=>'Advanced Settings', 'label' => 'Menu Position', 'description' => 'The position in the menu order the post type should appear. (number from 0 - 100, eg: 5 = Below Posts, 10 = Below Media)' ) ),
			triggerhappy_field( 'is_hierarchical', 'string', array( 'advanced'=>'Advanced Settings', 'label' => 'Is Hierarchical', 'description' => 'Is Hierarchical (eg: Allows a parent post to be specified)' ) ),



		),
		'advanced' => true
	);
	$nodes['th_core_wp_manage_posts_columns'] = array(
		'name' => 'Manage Posts Columns',
		'plugin' => 'WordPress',
		'nodeType' => 'trigger',
		'description' => 'Add/Remove post table columns',
		'hook' => 'manage_posts_columns',
		'cat' => 'Admin',
		'callback' => 'triggerhappy_filter_hook',
		'fields' => array(
			triggerhappy_field(
				'columns', 'array', array(
					'dir' => 'start',
				)
			),
		),
		'advanced' => true
	);
	$nodes['th_core_wp_head'] = array(
		'name' => 'Page Header rendered',
		'plugin' => 'WordPress',
		'nodeType' => 'trigger',
		'description' => 'When the Page Header is rendered (wp_head)',
		'cat' => 'Front-end',
		'callback' => 'triggerhappy_action_hook',
		'hook' => 'wp_head',
		'globals'=> array('post'=>'post'),
		'fields' => array(
			triggerhappy_field(
				'post', 'wp_post', array(
					'dir' => 'start',
				)
			),
		),
		'advanced' => true
	);
	$nodes['th_core_wp_nav_menu_items'] = array(
		'name' => 'Nav Menu Items',
		'plugin' => 'WordPress',
		'nodeType' => 'trigger',
		'description' => 'Modify the nav menu items',
		'cat' => 'Front-end',
		'callback' => 'triggerhappy_filter_hook',
		'hook' => 'wp_nav_menu_items',
		'globals'=> array('post'=>'post'),
		'fields' => array(
			triggerhappy_field(
				'menu_items', 'array', array(
					'dir' => 'start',
				)
			),
			triggerhappy_field(
				'settings', 'wp_nav_menu_settings', array(
					'dir' => 'start',
				)
			),
		),
		'advanced' => true
	);
	$nodes['th_core_wp_nav_menu_args'] = array(
		'name' => 'Modify Nav Menu Settings',
		'plugin' => 'WordPress',
		'nodeType' => 'trigger',
		'description' => 'Modify the nav menu arguments',
		'cat' => 'Front-end',
		'callback' => 'triggerhappy_filter_hook',
		'hook' => 'wp_nav_menu_args',
		'globals'=> array('post'=>'post'),
		'fields' => array(
			triggerhappy_field(
				'settings', 'wp_nav_menu_settings', array(
					'dir' => 'start',
				)
			),
		),
		'advanced' => true
	);

	$nodes['th_core_wp_footer'] = array(
		'name' => 'Page Footer',
		'plugin' => 'WordPress',
		'nodeType' => 'trigger',
		'description' => 'Add scripts or HTML at the bottom of a page on the front-end',
		'cat' => 'Front-end',
		'callback' => 'triggerhappy_action_hook',
		'hook' => 'wp_footer',
		'globals'=> array('post'=>'post'),
		'fields' => array(
			triggerhappy_field(
				'post', 'wp_post', array(
					'dir' => 'start',
				)
			),
		),
		'advanced' => true
	);

	$nodes['th_core_pre_get_posts'] = array(
		'name' => 'Before fetching posts',
		'plugin' => 'WordPress',
		'nodeType' => 'trigger',
		'description' => 'Modify the query before fetching posts',
		'cat' => 'Query',

		'callback' => 'triggerhappy_action_hook',
		'hook' => 'pre_get_posts',
		'fields' => array(
			triggerhappy_field(
				'query', 'wp_query', array(
					'dir' => 'start',
				)
			)
		),
		'advanced' => true
	);
	$nodes['th_core_pre_get_comments'] = array(
		'name' => 'Before fetching comments',
		'plugin' => 'WordPress',
		'nodeType' => 'trigger',
		'description' => 'Modify the query before fetching comments',
		'cat' => 'Query',

		'callback' => 'triggerhappy_action_hook',
		'hook' => 'pre_get_posts',
		'fields' => array(
			triggerhappy_field(
				'query', 'array', array(
					'dir' => 'start',
				)
			)
		),
		'advanced' => true
	);
	$nodes['th_core_dynamic_sidebar_before'] = array(
		'name' => 'Before the sidebar is displayed',
		'plugin' => 'WordPress',
		'nodeType' => 'trigger',
		'triggerType' => 'output',
		'description' => 'Before the sidebar has started rendering',
		'cat' => 'Front-end',
		'callback' => 'triggerhappy_action_hook',
		'hook' => 'dynamic_sidebar_before',
		'globals'=> array('post'=>'post'),
		'fields' => array(
			triggerhappy_field(
				'post', 'wp_post', array(
					'dir' => 'start',
				)
			),
		),
		'advanced'=>true
	);
	$nodes['th_core_dynamic_sidebar_after'] = array(
		'name' => 'After the sidebar is displayed',
		'plugin' => 'WordPress',
		'nodeType' => 'trigger',
		'triggerType' => 'output',
		'description' => 'After the sidebar has finished rendering',
		'cat' => 'Front-end',
		'callback' => 'triggerhappy_action_hook',
		'hook' => 'dynamic_sidebar_after',
		'globals'=> array('post'=>'post'),
		'fields' => array(
			triggerhappy_field(
				'post', 'wp_post', array(
					'dir' => 'start',
				)
			),
		),
		'advanced'=>true
	);
	$nodes['th_core_wp_custom_hook'] = array(
		'name' => 'Custom Hook',
		'plugin' => 'WordPress',
		'nodeType' => 'trigger',
		'description' => 'When a custom hook is fired',
		'cat' => 'WordPress',
		'callback' => 'triggerhappy_custom_hook',
		'fields' => array(
			triggerhappy_field(
				'hookType', 'triggerhappy_hook_type', array(
					'dir' => 'in',
					'label'=>'Hook Type',
					'description'=> 'The type of hook (filter or action)'
				)
			),
			triggerhappy_field(
				'hookName', 'string', array(
					'dir' => 'in',
					'label'=>'Hook Name',
					'description'=> 'The name of the custom hook'
				)
			),
			triggerhappy_field(
				'priority', 'number', array(
					'dir' => 'in',
					'label'=>'Priority',
					'default'=>'10',
					'description'=> 'The priority of hook (default 10)'
				)
			),
			triggerhappy_field(
				'args', 'string', array(
					'dir' => 'start',
					'label'=>'Arguments',
					'description'=> 'The hook arguments'
				)
			),
		),
		'advanced' => true
	);
	$nodes['th_core_wp_post_column_content'] = array(
		'name' => 'Post Column Content',
		'plugin' => 'WordPress',
		'nodeType' => 'trigger',
		'description' => 'Display content for column',
		'hook' => 'manage_posts_custom_column',
		'cat' => 'Admin',
		'callback' => 'triggerhappy_custom_column',
		'fields' => array(
			triggerhappy_field( 'columnName', 'string', array(
				'dir' => 'start',
			) ),
			triggerhappy_field( 'post', 'wp_post', array(
				'dir' => 'start',
			) ),
		),
		'advanced' => true
	);
	$nodes['th_core_wp_custom_url'] = array(
		'name' => 'Custom URL',
		'plugin' => 'core',
		'nodeType' => 'trigger',
		'description' => 'Run this flow when a custom URL is visited',
		'cat' => 'Front-end',
		'callback' => 'triggerhappy_wordpress_custom_url',
		'fields' => array(
			triggerhappy_field( 'url', 'string', array(
				'dir' => 'start',
			) ),
		),
		'advanced' => true
	);
	$nodes['th_core_wp_custom_shortcode'] = array(
		'name' => 'Custom Shortcode',
		'plugin' => 'core',
		'nodeType' => 'trigger',
		'description' => 'Add a custom shortcode',
		'cat' => 'WordPress',
		'callback' => 'triggerhappy_wordpress_custom_shortcode',
		'fields' => array(
			triggerhappy_field( 'shortcode', 'string', array(
				'dir' => 'in',
			) ),
			triggerhappy_field( 'allowedAttributes', 'string', array(
				'dir' => 'in',
				'description' => 'A comma delimited list of allowed attributes (eg: "name,description,title,url")'
			) ),
			triggerhappy_field( 'shortcodeData', 'array', array(
				'dir' => 'out',
				'description' => 'The supplied shortcode data'
			) ),
		),
		'advanced' => true
	);
	return $nodes;
}
