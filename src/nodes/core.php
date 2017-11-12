<?php
include( dirname(__FILE__) . "/functions/core_functions.php" );

function triggerhappy_load_core_nodes( $nodes ) {
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

	$nodes['th_core_wp_login'] = array(
		'name' => 'User Logged In',
		'plugin' => '',
		'nodeType' => 'trigger',
		'description' => 'When a user has successfully logged in',
		'cat' => 'Users',
		'callback' => 'triggerhappy_action_hook',
		'hook' => 'wp_login',
		'fields' => array(
			triggerhappy_field(
				'user_login', 'string', array(
					'dir' => 'start',
				)
			),
			triggerhappy_field(
				'user', 'wp_user', array(
					'dir' => 'start',
				)
			)
		)
	);


	$nodes['th_core_wp_logout'] = array(
		'name' => 'User Logged Out',
		'plugin' => 'WordPress',
		'nodeType' => 'trigger',
		'description' => 'When a user has logged out',
		'cat' => 'User Triggers',
		'callback' => 'triggerhappy_action_hook',
		'hook' => 'wp_logout',
		'fields' => array(

		)
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
	$nodes['th_core_triggerhappy_set_navigation_menu'] = array(
		'name' => 'Set the Navigation Menu',
		'description' => 'Set the Navigation Menu',
		'nodeType' => 'action',
		'simple'=>true,
		'cat' => 'WordPress',
		"fields" => array(
			triggerhappy_field( 'location', 'wp_nav_location' , array( 'description' => 'The Nav Menu Location to update')),
			triggerhappy_field( 'menu', 'wp_nav_menu' , array( 'description' => 'The Nav Menu to use')),
		),

		'childGraphs' => json_encode(
			TH::Graph(
				array(
					'type' => 'wp_nav_menu_args',
					'filters' => array(
							array(
								TH::Filter(TH::Expression("_N0.location"), 'equals', TH::Expression("_N1.settings.theme_location"))
							)
						),
				),
				array(
					'type' => 'core_set_value',
					'expressions' => array(
						'set' => TH::Expression("_N1.settings.menu"),
						'toValue' => TH::Expression("_N0.menu")
					),
				)
			)
		)
	);
	$nodes['th_core_timer'] = array(
		'cat' => 'WordPress',
		'name' => 'On Timer',
		'description' => 'Run every x hours/days etc',
		'callback' => 'triggerhappy_timer_trigger',
		'nodeType' => 'trigger',
		'plugin' => 'wordpress',
		'fields' => array( triggerhappy_field( 'hours', 'number' ) )
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
	$nodes['th_core_send_email'] = array(
		'plugin' => 'wordpress',
		'cat' => 'WordPress',
		'name' => 'Send an email',
		'description' => 'Send a custom email',
		'callback' => 'triggerhappy_send_email',
		'nodeType' => 'action',
		'fields' => array(
			triggerhappy_field( 'send_to', 'string', array( 'label' => 'Send To', 'description' => 'Enter the recipient email address') ),
			triggerhappy_field( 'subject', 'string', array( 'label' => 'Subject', 'description' => 'The email subject' ) ),
			triggerhappy_field( 'body', 'html', array( 'label' => 'Body', 'description' => 'The body of the email' ) ),

		),
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

	$nodes['th_core_set_title'] = array(
		'description' => 'Set the post title',
		'name' => 'Set Post Title',
		'plugin' => '',
		'actionType'=>'render',
		'nodeType' => 'action',
		'cat' => 'Front-end',
		'callback' => 'triggerhappy_filter_hook',
		'hook' => 'the_title',
		'filters' => array(
			array(
				TH::Filter(TH::Expression("_N.wp.in_the_loop"),'equals',true)
			)
		),
		'expressions' => array(
			'title' => "{{.current_title}}"
		),
		'fields' => array(
			triggerhappy_field( 'current_title', 'string', array('dir'=>'start','description'=>'The existing Post Title') ),
			triggerhappy_field( 'post', 'wp_post', array('dir'=>'start','description'=>'The Post object') ),
			triggerhappy_field( 'title', 'string', array('dir'=>'in','description'=>'The Post Title') ),
		)
	);
	$nodes['th_core_the_content'] = array(
		'description' => 'Modify the post content before it is displayed',
		'name' => 'Set Post Content',
		'plugin' => '',
		'actionType'=>'render',
		'nodeType' => 'action',
		'cat' => 'Front-end',
		'callback' => 'triggerhappy_filter_hook',
		'hook' => 'the_content',
		'globals'=> array('post'=>'post'),
		'filters' => array(
			array(
				TH::Filter(TH::Expression("_N.wp.in_the_loop"),'equals',true)
			)
		),
		'fields' => array(
			triggerhappy_field( 'current_content', 'string', array('dir'=>'start','description'=>'The existing Post Content') ),
			triggerhappy_field( 'post', 'wp_post', array('dir'=>'start','description'=>'The Post object') ),
			triggerhappy_field( 'content', 'html', array('dir'=>'in','description'=>'The Post Content') ),
		),

		'expressions' => array(
			'content' => "{{.current_content}}"
		),
	);
	$nodes['th_core_set_query_param'] = array(
		'description' => 'Set a query parameter',
		'name' => 'Set Query Parameter',
		'plugin' => '',
		'nodeType' => 'action',
		'cat' => 'WordPress',
		'callback' => 'triggerhappy_query_param',
		'helpText'=> 'Sets a query parameter. Note: multiple queries are run on every page load, even the Admin dashboard. If you want to modify the main query, you\'ll need to check that is_main_query is set via the filters panel',
		'fields' => array(
			triggerhappy_field( 'query', 'wp_query' , array( 'description' => 'The Query to be updated')),
			triggerhappy_field( 'query_param', 'string', array( 'description' => 'Query Parameter Name') ),
			triggerhappy_field( 'value', 'string',  array( 'description' => 'The value to set') ),
		),
		'advanced' => true
	);
	$nodes['th_core_add_nav_menu_item'] = array(
		'description' => 'Adds a link to a navigation menu',
		'name' => 'Add Nav Menu Item',
		'plugin' => '',
		'nodeType' => 'action',
		'cat' => 'WordPress',
		'callback' => 'triggerhappy_add_nav_menu_item',

		'fields' => array(
			triggerhappy_field( 'nav_menu', 'wp_nav_menu' , array( 'description' => 'The Nav Menu to be modified ')),
			triggerhappy_field( 'text', 'string', array( 'description' => 'The item text') ),
			triggerhappy_field( 'url', 'string',  array( 'description' => 'The item URL') ),
		)
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
	$nodes['th_core_single_post'] = array(
		'description' => 'When a single post is being viewed on the front-end',
		'name' => 'When a Single Post is viewed',
		'plugin' => '',
		'triggerType'=>'render',
		'nodeType' => 'trigger',
		'hook'=>'template_redirect',
		'callback'=>'triggerhappy_action_hook',
		'cat' => 'Front-end',
		'globals'=>array('post'=>'post'),
		'fields' => array(

			triggerhappy_field( 'post', 'wp_post', array('dir'=>'start') ),
		),
		'nodeFilters'=> array(
			array(
				TH::Filter(TH::Expression("_N.wpPageFunctions.is_single"),'equals',true)
			)
		)
	);

	$nodes['th_core_archive'] = array(
		'description' => 'When a post archive is being viewed on the front-end',
		'name' => 'When a Post Archive is viewed',
		'plugin' => '',
		'nodeType' => 'trigger',
		'hook'=>'template_redirect',
		'callback'=>'triggerhappy_action_hook',
		'cat' => 'Front-end',
		'triggerType'=>'render',
		'fields' => array(

		),
		'nodeFilters'=> array(
			array(
				TH::Filter(TH::Expression("_N.wpPageFunctions.is_archive"),'equals',true)
			)
		)
	);

	$nodes['th_core_cat_archive'] = array(
		'description' => 'When a category is being viewed on the front-end',
		'name' => 'When a Post Category Archive is viewed',
		'plugin' => '',
		'nodeType' => 'trigger',
		'hook'=>'template_redirect',
		'callback'=>'triggerhappy_action_hook',
		'cat' => 'Front-end',
		'triggerType'=>'render',
		'fields' => array(

		),
		'nodeFilters'=> array(
			array(
				TH::Filter(TH::Expression("_N.wpPageFunctions.is_category"),'equals',true)
			)
		)
	);
	$nodes['th_core_tax_archive'] = array(
		'description' => 'When a Taxonomy Archive is being viewed on the front-end',
		'name' => 'When a Taxonomy Archive is viewed',
		'plugin' => '',
		'nodeType' => 'trigger',
		'hook'=>'template_redirect',
		'callback'=>'triggerhappy_action_hook',
		'cat' => 'Front-end',
		'triggerType'=>'render',
		'fields' => array(

		),
		'nodeFilters'=> array(
			array(
				TH::Filter(TH::Expression("_N.wpPageFunctions.is_tax"),'equals',true)
			)
		)
	);

	$nodes['th_core_any_url'] = array(
		'description' => 'When any page, post or archive is being viewed on the front-end',
		'name' => 'When any front-end URL is viewed',
		'plugin' => '',
		'triggerType'=>'render',
		'nodeType' => 'trigger',
		'hook'=>'template_redirect',
		'callback'=>'triggerhappy_action_hook',
		'cat' => 'Front-end',
		'fields' => array(
		),
	);
	$nodes['th_core_commentcreated'] = array(
		'name' => 'When a Comment is created',
		'plugin' => '',
		'cat' => 'Comments',
		'nodeType' => 'trigger',
		'callback' => 'triggerhappy_filter_hook',
		'hook' => 'wp_insert_comment',

		'description' => 'When a comment is created and saved',
		'fields' => array(
			triggerhappy_field( 'commentId', 'number', array( 'description'=>'The comment ID', 'dir' => 'start' ) ),
			triggerhappy_field( 'comment', 'wp_comment', array( 'description'=>'The added comment', 'dir' => 'start' ) )
		)

	);
	$nodes['th_core_commentapproved'] = array(
		'name' => 'When a Comment is approved',
		'plugin' => '',
		'cat' => 'Comments',
		'nodeType' => 'trigger',
		'callback' => 'triggerhappy_filter_hook',
		'hook' => 'wp_insert_comment',

		'description' => 'When a comment is created and saved',
		'fields' => array(
			triggerhappy_field( 'commentId', 'number', array( 'description'=>'The comment ID', 'dir' => 'start' ) ),
			triggerhappy_field( 'comment', 'wp_comment', array( 'description'=>'The added comment', 'dir' => 'start' ) )
		)

	);
	$nodes['th_core_postsaved'] = array(
		'name' => 'When a Post is saved',
		'plugin' => '',
		'cat' => 'Posts',
		'nodeType' => 'trigger',
		'callback' => 'triggerhappy_action_hook',
		'hook' => 'save_post',

		'description' => 'When a post is created or updated',
		'fields' => array(
			triggerhappy_field( 'ID', 'number', array( 'description'=>'The comment ID', 'dir' => 'start' ) ),
			triggerhappy_field( 'post', 'wp_post', array( 'description'=>'The added comment', 'dir' => 'start' ) )
		)

	);
	$nodes['th_core_postpublished'] = array(
		'name' => 'When a Post is published',
		'plugin' => '',
		'cat' => 'Posts',
		'nodeType' => 'trigger',
		'callback' => 'triggerhappy_action_hook',
		'hook' => 'publish_post',

		'description' => 'When a post is saved as Published',
		'fields' => array(
			triggerhappy_field( 'id', 'number', array( 'description'=>'The post ID', 'dir' => 'start' ) ),
			triggerhappy_field( 'post', 'wp_post', array( 'description'=>'The saved post', 'dir' => 'start' ) )
		)

	);

	$nodes['th_core_userupdated'] = array(
		'name' => 'When a User profile is updated',
		'plugin' => '',
		'cat' => 'Users',
		'nodeType' => 'trigger',
		'callback' => 'triggerhappy_action_hook',
		'hook' => 'profile_update',

		'description' => 'When a user profile is saved',
		'fields' => array(
			triggerhappy_field( 'id', 'number', array( 'description'=>'The user ID', 'dir' => 'start' ) ),
			triggerhappy_field( 'user', 'wp_user', array( 'description'=>'The user data', 'dir' => 'start' ) )
		)

	);

	$nodes['th_core_user_register'] = array(
		'name' => 'When a User profile is created',
		'plugin' => '',
		'cat' => 'Users',
		'nodeType' => 'trigger',
		'callback' => 'triggerhappy_action_hook',
		'hook' => 'user_register',

		'description' => 'When a User Profile is created/registered',
		'fields' => array(
			triggerhappy_field( 'id', 'number', array( 'description'=>'The user ID', 'dir' => 'start' ) ),
			triggerhappy_field( 'user', 'wp_user', array( 'description'=>'The user data', 'dir' => 'start' ) )
		)

	);
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

	$nodes['th_core_wp_redirect'] = array(
		'name' => 'Redirect to URL',
		'plugin' => '',
		'cat' => 'WordPress',
		'nodeType'=>'action',
		'callback' => 'triggerhappy_wp_redirect',
		'fields' => array(
			triggerhappy_field( 'url', 'array' )
		)
	);


	$nodes['th_core_wp_logout'] = array(
		'name' => 'Logout the current user',
		'description' => 'Forces the user to log out',
		'plugin' => '',
		'cat' => 'WordPress',
		'nodeType'=>'action',
		'callback' => 'triggerhappy_wp_logout',
		'fields' => array(
		)
	);

	$nodes['th_core_wp_login'] = array(
		'name' => 'Log in as user',
		'description' => 'Logs in as the specified user',
		'plugin' => '',
		'cat' => 'WordPress',
		'nodeType'=>'action',
		'callback' => 'triggerhappy_wp_login',
		'fields' => array(
			triggerhappy_field( 'username', 'string', array( 'label'=>'User Name', 'description' => 'The user name of the user to log in as' ) ),
			triggerhappy_field( 'password', 'string', array( 'label'=>'Password', 'description' => 'The password to use when logging in ' ) )
		)
	);


	$nodes['th_core_filter_logged_in'] = array(
		'name' => 'If the user is logged in',
		'plugin' => '',
		'cat' => 'Users',
		'nodeType'=>'condition',
		'callback' => 'triggerhappy_condition',
		'fields' => array(
		),
		'nodeFilters'=> array(
			array(

				TH::Filter(TH::Expression("_N.wp.is_user_logged_in"),'equals',true)
			)
		)
	);

	$nodes['th_core_filter_not_logged_in'] = array(
		'name' => 'If the user is not logged in',
		'plugin' => '',
		'cat' => 'Users',
		'nodeType'=>'condition',
		'callback' => 'triggerhappy_condition',
		'fields' => array(
		),
		'nodeFilters'=> array(
			array(

				TH::Filter(TH::Expression("_N.wp.is_user_logged_in"),'equals',false)
			)
		)

	);

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
		function() {

			$user =  wp_get_current_user();
			$data = array(
				'id'=>$user->ID,
				'username' => $user->user_login,
				'fullName' => $user->first_name . ' ' . $user->last_name
			);
			return $data;
		}
	);

	triggerhappy_register_global_field(
		'wp',
		'wp_query_functions',
		'Built-in WordPress functions',
		function() {

			return array(
				'__triggerhappy_call_functions' => 'true'
			);
		}
	);

	triggerhappy_register_global_field(
		'wpPageFunctions',
		'wp_page_functions',
		'Built-in WordPress page/query conditions',
		function() {

			return array(
				'__triggerhappy_call_functions' => 'true'
			);
		}
	);



	triggerhappy_register_value_type(
		'wp_post', 'number', function ( $search ) {
			$req = new WP_REST_Request( 'GET', '/wp/v2/posts' );
			$req->set_param( 'search', $search );
			$response = rest_do_request( $req );
			$data = ($response->get_data());
			return array_map(
				function ( $d ) {
					return array(
						'id' => $d['id'],
						'text' => $d['name'],
					);
				}, $data
			);
		}, true
	);



	triggerhappy_register_value_type(
		'wp_comment', 'number', function ( $search ) {
			$req = new WP_REST_Request( 'GET', '/wp/v2/comments' );
			$req->set_param( 'search', $search );
			$response = rest_do_request( $req );
			$data = ($response->get_data());
			return array_map(
				function ( $d ) {
					return array(
						'id' => $d['id'],
						'text' => $d['name'],
					);
				}, $data
			);
		}, true
	);


	triggerhappy_register_value_type(
		'triggerhappy_hook_type', 'string', function (  ) {
			return array(
				array('id'=>'filter','text'=>'Filter'),
				array('id'=>'action','text'=>'Action')
			);
		}, false
	);

	triggerhappy_register_value_type(
		'wp_nav_location', 'string', function (  ) {
			$results = array();
			foreach (get_registered_nav_menus() as $id=>$text) {
				array_push($results,array('id'=>$id,'text'=>$text));
			}
			return $results;

		}, false
	);
	triggerhappy_register_value_type(
		'wp_nav_menu', 'number', function (  ) {
			$results = array();
			$menus = get_terms( 'nav_menu' );
			$menus = array_combine( wp_list_pluck( $menus, 'term_id' ), wp_list_pluck( $menus, 'name' ) );
			foreach ($menus as $id=>$text) {
				array_push($results,array('id'=>$id,'text'=>$text));
			}
			return $results;

		}, false
	);

	triggerhappy_register_value_type(
		'wp_query', 'array', function() { }, false
	);

	triggerhappy_register_value_type(
		'wp_nav_menu_settings', 'array', function() { }, false
	);

	triggerhappy_register_value_type(
		'wp_user', 'number', function ( $search ) {
			$req = new WP_REST_Request( 'GET', '/wp/v2/users' );
			$req->set_param( 'search', $search );
			$response = rest_do_request( $req );
			$data = ($response->get_data());
			return array_map(
				function ( $d ) {
					return array(
						'id' => $d['id'],
						'text' => $d['name'],
					);
				}, $data
			);
		}, true
	);
	triggerhappy_register_json_schema(
		'wp_user',
		array(
			'$schema' => 'http://json-schema.org/draft-04/schema#',
			'title' => 'user',
			'type' => 'object',
			'properties' =>
			  array(
				  'id' => array(
					  'description' => 'Unique identifier for the user.',
					  'type' => 'integer',
					  'readonly' => true,
				  ),
				  'username' => array(
					 'description' => 'User Name',
					 'type' => 'string'
				 ),
				 'fullName' => array(
					 'description' => 'Full Name',
					 'type' => 'string'
				 )
			 )
		 )
	 );
	 triggerhappy_register_json_schema(
		 'wp_query_functions',
		 array(
			 '$schema' => 'http://json-schema.org/draft-04/schema#',
			 'title' => 'WP Query',
			 'type' => 'object',
			 'properties' =>
			   array(
				   'is_main_query' => array(
					   'description' => 'Is currently executing the main query on the page',
					   'type' => 'boolean',
					   'readonly' => true,
				   ),
				   'in_the_loop' => array(
					   'description' => 'Is currently in the loop',
					   'type' => 'boolean',
					   'readonly' => true,
				   ),
				   'is_user_logged_in' => array(
					  'description' => 'Is the user currently logged in',
					  'type' => 'boolean',
					  'readonly' => true,
				  ),
			   )

		 )
	 );
	 triggerhappy_register_json_schema(
		'wp_query',
		array(
			'$schema' => 'http://json-schema.org/draft-04/schema#',
			'title' => 'WP Query',
			'type' => 'object',
			'properties' =>
			array(
				'is_main_query' => array(
					'description' => 'Is currently executing the main query on the page',
					'type' => 'boolean',
					'readonly' => true,
				),
				'in_the_loop' => array(
					'description' => 'Is currently in the loop',
					'type' => 'boolean',
					'readonly' => true,
				),
			)
		)
	);

	 triggerhappy_register_json_schema(
		'wp_nav_menu_settings',
		array(
			'$schema' => 'http://json-schema.org/draft-04/schema#',
			'title' => 'WP Nav Menu Settings',
			'type' => 'object',
			'properties' =>
			array(
				'menu' => array(
					'description' => 'Desired Menu (ID, name)',
					'type' => 'wp_nav_menu',
					'readonly' => true,
				),
				'theme_location' => array(
					'description' => 'The theme location',
					'type' => 'wp_nav_location',
					'readonly' => true,
				),
			)
		)
	 );
	 triggerhappy_register_json_schema(
		'wp_page_functions',
		array(
			'$schema' => 'http://json-schema.org/draft-04/schema#',
			'title' => 'WP Functions',
			'type' => 'object',
			'properties' =>
			 	array(
				'is_single' => array(
					'description' => 'Is a single post being displayed (any post type, not included Pages)',
					'type' => 'boolean',
					'readonly' => true,
				),
				'is_post_type_archive' => array(
					'description' => 'Is a post archive being displayed',
					'type' => 'boolean',
					'readonly' => true,
				),

				'is_singular' => array(
					'description' => 'Is a single post being displayed (including Pages)',
					'type' => 'boolean',
					'readonly' => true,
				),
				'is_single_page' => array(
					'description' => 'Is a single Page being displayed ',
					'type' => 'boolean',
					'readonly' => true,
				),
				'is_home' => array(
					'description' => 'Is the Home Page (When home is set to blog archive)',
					'type' => 'boolean',
					'readonly' => true,
				),
				'is_front_page' => array(
					'description' => 'Is the Front Page (When home is set to a static page)',
					'type' => 'boolean',
					'readonly' => true,
				),
				'is_admin' => array(
					'description' => 'Is a page in the admin dashboard',
					'type' => 'boolean',
					'readonly' => true,
				),

				'is_category' => array(
					'description' => 'Is a category archive page being displayed',
					'type' => 'boolean',
					'readonly' => true,
				),

				'is_category' => array(
					'description' => 'Is a tag archive page being displayed',
					'type' => 'boolean',
					'readonly' => true,
				),
				'is_tax' => array(
					'description' => 'Is a taxonomy archive page being displayed',
					'type' => 'boolean',
					'readonly' => true,
				),

				'is_404' => array(
					'description' => 'When a 404 page is displayed',
					'type' => 'boolean',
					'readonly' => true,
				),
			)
		)
	);
	triggerhappy_register_json_schema(
		'wp_post',
		array(
			'$schema' => 'http://json-schema.org/draft-04/schema#',
			'title' => 'post',
			'type' => 'object',
			'properties' =>
			  array(
				  'id' => array(
					  'description' => 'Unique identifier for the resource.',
					  'type' => 'integer',
					  'readonly' => true,
				  ),
				  'post_title' => array(
					  'description' => 'Post Title.',
					  'type' => 'string',
					  'context' =>
					  array(
						  0 => 'view',
						  1 => 'edit',
					  ),
				  ),
				  'post_name' =>
				  array(
					  'description' => 'Post slug.',
					  'type' => 'string',
					  'context' =>
					  array(
						  0 => 'view',
						  1 => 'edit',
					  ),
				  ),
				  'permalink' =>
				  array(
					  'description' => 'Post URL.',
					  'type' => 'string',
					  'format' => 'uri',
					  'context' =>
					  array(
						  0 => 'view',
						  1 => 'edit',
					  ),
					  'readonly' => true,
				  ),
				  'date_created' =>
				  array(
					  'description' => 'The date the post was created, in the sites timezone.',
					  'type' => 'date-time',
					  'context' =>
					  array(
						  0 => 'view',
						  1 => 'edit',
					  ),
					  'readonly' => true,
				  ),
				  'meta_data' =>
				  array(
					  'description' => 'Meta data.',
					  'type' => 'array',
					  'context' =>
					  array(
						  0 => 'view',
						  1 => 'edit',
					  ),
					  'items' =>
					  array(
						  'type' => 'object',
						  'properties' =>
						  array(
							  'id' =>
							  array(
								  'description' => 'Meta ID.',
								  'type' => 'integer',
								  'context' =>
								  array(
									  0 => 'view',
									  1 => 'edit',
								  ),
								  'readonly' => true,
							  ),
							  'key' =>
							  array(
								  'description' => 'Meta key.',
								  'type' => 'string',
								  'context' =>
								  array(
									  0 => 'view',
									  1 => 'edit',
								  ),
							  ),
							  'value' =>
							  array(
								  'description' => 'Meta value.',
								  'type' => 'string',
								  'context' =>
								  array(
									  0 => 'view',
									  1 => 'edit',
								  ),
							  ),
						  ),
					  ),
				  ),
			  ),
		)
	);

triggerhappy_register_json_schema(
   'wp_comment',
   array(
	   '$schema' => 'http://json-schema.org/draft-04/schema#',
	   'title' => 'comment',
	   'type' => 'object',
	   'properties' =>
		 array(
			 'id' => array(
				 'description' => 'Unique identifier for the resource.',
				 'type' => 'integer',
				 'readonly' => true,
			 ),
			 'post_id' => array(
				 'description' => 'Post ID',
				 'type' => 'string'
			 ),
			 'post' => array(
				 'description' => 'The associated post',
				 'type' => 'wp_post'
			 ),
			 'comment_author' => array(
				 'description' => 'The comment authors name',
				 'type' => 'string',
			 ),
			 'comment_author_email' => array(
				'description' => 'The comment authors email',
				'type' => 'string',
			),

			'comment_author_url' => array(
			   'description' => 'The comment authors webpage',
			   'type' => 'string',
		   ),

		   'comment_author_ip' => array(
			  'description' => 'The comment authors IP',
			  'type' => 'string',
		  ),
		  'comment_date' => array(
			 'description' => 'The date the comment was created (in server\'s timezone)',
			 'type' => 'string',
		 ),
		 'comment_date' => array(
			'description' => 'The date the comment was created (in server\'s timezone)',
			'type' => 'string',
		),
		'comment_content' => array(
		   'description' => 'The content of the comment',
		   'type' => 'string',
	   	),
		'comment_type' => array(
		   'description' => 'The type of the comment (pingback, trackback or empty for normal comments)',
		   'type' => 'string',
	   	),

		'user_id' => array(
		   'description' => 'The ID of the author if registered (0 otherwise)',
		   'type' => 'number',
	   	),

		'user' => array(
		   'description' => 'The author (if registered)',
		   'type' => 'wp_user',
	   	),
	)
   )
);
}

add_filter( 'triggerhappy_nodes', 'triggerhappy_load_core_nodes' );

add_action( 'triggerhappy_schema', 'triggerhappy_load_core_schema' );
add_filter(	'triggerhappy_resolve_field_wp_post__meta_data', 'triggerhappy_resolve_field_wp_post__meta_data', 10, 3);


add_filter(	'triggerhappy_expression_call_getItem', function ( $result, $obj, $methodName, $args ) {
	if ( is_array( $obj ) && isset( $obj[ $args[0] ] ) ) {
		return $obj[ $args[0] ];
	}
	return null;
}, 4, 4 );

function triggerhappy_resolve_field_wp_post__meta_data( $result, $obj, $fieldName ) {
	if ( $obj == null ) {
		return null;
	}

	$meta = get_post_meta( $obj->ID );
	$formattedMeta = array();
	foreach ( $meta as $i => $data ) {
		$formattedMeta[ $i ] = $data[0];
	}
	return array(
		'type' => 'object',
		'value' => $formattedMeta,
	);
}
