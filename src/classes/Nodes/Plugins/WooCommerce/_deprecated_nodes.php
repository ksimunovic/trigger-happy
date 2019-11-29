<?php

function deprecatedNodes() {

	$nodes['th_woocommerce_get_price_html'] = [
		'description' => 'Modify the product price before it is displayed',
		'name'        => 'Product Price',
		'plugin'      => 'woocommerce',
		'nodeType'    => 'trigger',
		'cat'         => 'woocommerce',
		'fields'      => [

			triggerhappy_field( 'price', 'html', [ 'dir' => 'start' ] ),
			triggerhappy_field( 'product', 'wc_product', [ 'dir' => 'start' ] ),

		],
		'callback'    => 'triggerhappy_filter_hook',
	];

	$nodes['th_woocommerce_create_product'] = [
		'name'        => 'Create a new product',
		'plugin'      => 'woocommerce',
		'nodeType'    => 'action',
		'description' => 'Creates (or updates) a product',
		'cat'         => 'Products',
		'callback'    => 'triggerhappy_woocommerce_create_product',
		'fields'      => [
			triggerhappy_field( 'id', 'string', [
				'label'       => 'Product ID',
				'description' => 'Specify the product ID to update an existing product. Leave blank to create a new product',
				'dir'         => 'in',
			] ),
			triggerhappy_field( 'name', 'string', [
				'label' => 'Product Name',
				'dir'   => 'in',
			] ),
			triggerhappy_field( 'description', 'html', [
				'label' => 'Description',
				'dir'   => 'in',
			] ),
			triggerhappy_field( 'price', 'number', [
				'label' => 'Price',
				'dir'   => 'in',
			] ),
			triggerhappy_field( 'stock_quantity', 'number', [
				'label' => 'Stock Quantity',
				'dir'   => 'in',
			] ),
		],
	];


	$nodes['th_woocommerce_single_product'] = [
		'description' => 'When a single product is being viewed on the front-end',
		'name'        => 'When a Single Product is viewed',
		'plugin'      => '',
		'triggerType' => 'product_render',
		'nodeType'    => 'trigger',
		'hook'        => 'template_redirect',
		'callback'    => 'triggerhappy_action_hook',
		'cat'         => 'Front-end - WooCommerce',
		'globals'     => [ 'post' => 'post' ],
		'fields'      => [
			triggerhappy_field( 'post', 'wp_post', [ 'dir' => 'start' ] ),
		],
		'nodeFilters' => [
			[
				TH::Filter( TH::Expression( "_N.wpPageFunctions.is_single" ), 'equals', true ),
				TH::Filter( TH::Expression( "_N.wpPageFunctions.get_post_type" ), 'equals', 'product' ),
			],
		],
	];

	$nodes['th_woocommerce_insert_html_single_product'] = [
		'description' => 'Add content to a single product page',
		'name'        => 'Add HTML to Single Product template',
		'plugin'      => 'woocommerce',
		'nodeType'    => 'action',
		'actionType'  => 'product_render',
		'hook'        => '$section',
		'cat'         => 'woocommerce',
		'globals'     => [ 'product' => 'product' ],
		'fields'      => [

			triggerhappy_field( 'product', 'wc_product', [ 'dir' => 'start' ] ),
			triggerhappy_field( 'section', 'string', [
				'label'       => 'Section',
				'description' => 'Select the section of the page you attach this flow to',
				'dir'         => 'in',
				'choices'     => [
					[ 'id' => 'woocommerce_before_main_content', 'text' => 'Before the main content section' ],
					[ 'id' => 'woocommerce_after_main_content', 'text' => 'After the main content section' ],
					[
						'id'   => 'woocommerce_before_single_product',
						'text' => 'Top of the page (before product content)',
					],
					[
						'id'   => 'woocommerce_after_single_product',
						'text' => 'Bottom of the page (after product content)',
					],

					[
						'id'   => 'woocommerce_before_single_product_summary',
						'text' => 'Before the summary section',
					],
					[ 'id' => 'woocommerce_single_product_summary:4', 'text' => 'Before the product title' ],

					[
						'id'   => 'woocommerce_single_product_summary:6',
						'text' => 'Before the product rating/price',
					],
					[ 'id' => 'woocommerce_single_product_summary:15', 'text' => 'Before the product excerpt' ],
					[
						'id'   => 'woocommerce_single_product_summary:25',
						'text' => 'Before the add to cart buttons',
					],
					[ 'id' => 'woocommerce_single_product_summary:35', 'text' => 'After the add to cart buttons' ],
					[ 'id' => 'woocommerce_single_product_summary:45', 'text' => 'Before the sharing buttons' ],
					[ 'id' => 'woocommerce_single_product_summary:55', 'text' => 'After the sharing buttons' ],
					[
						'id'   => 'woocommerce_after_single_product_summary:5',
						'text' => 'After the summary section - before tabs',
					],
					[
						'id'   => 'woocommerce_after_single_product_summary:25',
						'text' => 'After the summary section - after related products',
					],
				],
			] ),
			triggerhappy_field( 'html', 'html', [
				'label'       => 'HTML',
				'description' => 'The HTML to be inserted',
				'dir'         => 'in',
			] ),
		],
		'callback'    => 'triggerhappy_woocommerce_output_html_single_product',
	];

	$nodes['th_woocommerce_checkout_fields'] = [
		'name'        => 'When Rendering Checkout Fields',
		'description' => 'When displaying the checkout fields',
		'plugin'      => 'woocommerce',
		'nodeType'    => 'trigger',
		'hook'        => 'woocommerce_checkout_fields',
		'cat'         => 'woocommerce',
		'callback'    => 'triggerhappy_filter_hook',
		'fields'      => [
			triggerhappy_field(
				'fields', 'array', [
					'argIndex' => 0,
					'dir'      => 'out',
				]

			),
		],
	];
}
