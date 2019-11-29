<?php

function triggerhappy_load_woocommerce_nodes( $nodes ) {

	$nodes['th_woocommerce_get_price_html'] = new \HotSource\TriggerHappy\Nodes\Plugins\WooCommerce\Triggers\GetPriceHtml();
	$nodes['th_woocommerce_single_product'] = new \HotSource\TriggerHappy\Nodes\Plugins\WooCommerce\Triggers\SingleProduct();
	$nodes['th_woocommerce_calculate_fees'] = new \HotSource\TriggerHappy\Nodes\Plugins\WooCommerce\Triggers\CalculateFees();
	$nodes['th_woocommerce_wc_order_created'] = new \HotSource\TriggerHappy\Nodes\Plugins\WooCommerce\Triggers\OrderCreated();
	$nodes['th_woocommerce_quantity_input_args'] = new \HotSource\TriggerHappy\Nodes\Plugins\WooCommerce\Triggers\QuantityInputArgs();
	$nodes['th_woocommerce_before_checkout_form'] = new \HotSource\TriggerHappy\Nodes\Plugins\WooCommerce\Triggers\BeforeCheckoutForm();
	$nodes['th_woocommerce_checkout_fields'] = new \HotSource\TriggerHappy\Nodes\Plugins\WooCommerce\Triggers\CheckoutFieldsRendering();
	$nodes['th_woocommerce_wc_add_fee'] = new \HotSource\TriggerHappy\Nodes\Plugins\WooCommerce\Actions\AddFee();
	$nodes['th_woocommerce_add_notice'] = new \HotSource\TriggerHappy\Nodes\Plugins\WooCommerce\Actions\AddNotice();
	$nodes['th_woocommerce_add_coupon'] = new \HotSource\TriggerHappy\Nodes\Plugins\WooCommerce\Actions\AddCoupon();
	$nodes['th_woocommerce_create_product'] = new \HotSource\TriggerHappy\Nodes\Plugins\WooCommerce\Actions\CreateProduct();
	$nodes['th_woocommerce_insert_html_single_product'] = new \HotSource\TriggerHappy\Nodes\Plugins\WooCommerce\Actions\InsertHtmlSingleProduct();

	return $nodes;
}

function triggerhappy_load_woocommerce_schema() {

	triggerhappy_register_value_type(
		'wc_product', 'number', function ( $search ) {
		$req = new WP_REST_Request( 'GET', '/wc/v2/products' );
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
		'wc_notice_type', 'string', function () {
		return [
			[ 'id' => 'error', 'text' => 'Error' ],
			[ 'id' => 'success', 'text' => 'Success' ],
			[ 'id' => 'notice', 'text' => 'Notice' ],
		];
	}, false
	);

	triggerhappy_register_json_schema( 'wc_product', [
		'$schema'    => 'http://json-schema.org/draft-04/schema#',
		'title'      => 'product',
		'type'       => 'object',
		'properties' => [
			'id'            => [
				'description' => 'Unique identifier for the resource.',
				'type'        => 'integer',
				'readonly'    => true,
			],
			'name'          => [
				'description' => 'Product name.',
				'type'        => 'string',
			],
			'slug'          => [
				'description' => 'Product slug.',
				'type'        => 'string',
			],
			'permalink'     => [
				'description' => 'Product URL.',
				'type'        => 'string',
				'format'      => 'uri',
				'readonly'    => true,
			],
			'date_created'  => [
				'description' => 'The date the product was created, in the sites timezone.',
				'type'        => 'date-time',
				'readonly'    => true,
			],
			'date_modified' => [
				'description' => 'The date the product was last modified, in the sites timezone.',
				'type'        => 'date-time',
				'readonly'    => true,
			],

			'type'                => [
				'description' => 'Product type.',
				'type'        => 'string',
				'default'     => 'simple',
				'enum'        => [
					0 => 'simple',
					1 => 'grouped',
					2 => 'external',
					3 => 'variable',
				],
			],
			'status'              => [
				'description' => 'Product status (post status).',
				'type'        => 'string',
				'default'     => 'publish',
				'enum'        => [
					0 => 'draft',
					1 => 'pending',
					2 => 'private',
					3 => 'publish',
				],
			],
			'featured'            => [
				'description' => 'Featured product.',
				'type'        => 'boolean',
				'default'     => false,
			],
			'catalog_visibility'  => [
				'description' => 'Catalog visibility.',
				'type'        => 'string',
				'default'     => 'visible',
				'enum'        => [
					0 => 'visible',
					1 => 'catalog',
					2 => 'search',
					3 => 'hidden',
				],
			],
			'description'         => [
				'description' => 'Product description.',
				'type'        => 'string',
			],
			'short_description'   => [
				'description' => 'Product short description.',
				'type'        => 'string',
			],
			'sku'                 => [
				'description' => 'Unique identifier.',
				'type'        => 'string',
			],
			'price'               => [
				'description' => 'Current product price.',
				'type'        => 'string',
				'readonly'    => true,
			],
			'regular_price'       => [
				'description' => 'Product regular price.',
				'type'        => 'string',
			],
			'sale_price'          => [
				'description' => 'Product sale price.',
				'type'        => 'string',
			],
			'date_on_sale_from'   => [
				'description' => 'Start date of sale price, in the sites timezone.',
				'type'        => 'date-time',
			],
			'date_on_sale_to'     => [
				'description' => 'End date of sale price, in the sites timezone.',
				'type'        => 'date-time',
			],
			'date_on_sale_to_gmt' => [
				'description' => 'End date of sale price, in the sites timezone.',
				'type'        => 'date-time',
			],
			'price_html'          => [
				'description' => 'Price formatted in HTML.',
				'type'        => 'string',
				'readonly'    => true,
			],
			'on_sale'             => [
				'description' => 'Shows if the product is on sale.',
				'type'        => 'boolean',
				'readonly'    => true,
			],
			'purchasable'         => [
				'description' => 'Shows if the product can be bought.',
				'type'        => 'boolean',
				'readonly'    => true,
			],
			'total_sales'         => [
				'description' => 'Amount of sales.',
				'type'        => 'integer',
				'readonly'    => true,
			],
			'virtual'             => [
				'description' => 'If the product is virtual.',
				'type'        => 'boolean',
				'default'     => false,
			],
			'downloadable'        => [
				'description' => 'If the product is downloadable.',
				'type'        => 'boolean',
				'default'     => false,
			],
			'downloads'           => [
				'description' => 'List of downloadable files.',
				'type'        => 'array',
				'items'       => [
					'type'       => 'object',
					'properties' => [
						'id'   => [
							'description' => 'File MD5 hash.',
							'type'        => 'string',
							'readonly'    => true,
						],
						'name' => [
							'description' => 'File name.',
							'type'        => 'string',
						],
						'file' => [
							'description' => 'File URL.',
							'type'        => 'string',
						],
					],
				],
			],
			'download_limit'      => [
				'description' => 'Number of times downloadable files can be downloaded after purchase.',
				'type'        => 'integer',
				'default'     => - 1,
			],
			'download_expiry'     => [
				'description' => 'Number of days until access to downloadable files expires.',
				'type'        => 'integer',
				'default'     => - 1,
			],
			'external_url'        => [
				'description' => 'Product external URL. Only for external products.',
				'type'        => 'string',
				'format'      => 'uri',
			],
			'button_text'         => [
				'description' => 'Product external button text. Only for external products.',
				'type'        => 'string',
			],
			'tax_status'          => [
				'description' => 'Tax status.',
				'type'        => 'string',
				'default'     => 'taxable',
				'enum'        => [
					0 => 'taxable',
					1 => 'shipping',
					2 => 'none',
				],
			],
			'tax_class'           => [
				'description' => 'Tax class.',
				'type'        => 'string',
			],
			'manage_stock'        => [
				'description' => 'Stock management at product level.',
				'type'        => 'boolean',
				'default'     => false,
			],
			'stock_quantity'      => [
				'description' => 'Stock quantity.',
				'type'        => 'integer',
			],
			'in_stock'            => [
				'description' => 'Controls whether or not the product is listed as "in stock" or "out of stock" on the frontend.',
				'type'        => 'boolean',
				'default'     => true,
			],
			'backorders'          => [
				'description' => 'If managing stock, this controls if backorders are allowed.',
				'type'        => 'string',
				'default'     => 'no',
				'enum'        => [
					0 => 'no',
					1 => 'notify',
					2 => 'yes',
				],
			],
			'backorders_allowed'  => [
				'description' => 'Shows if backorders are allowed.',
				'type'        => 'boolean',
				'readonly'    => true,
			],
			'backordered'         => [
				'description' => 'Shows if the product is on backordered.',
				'type'        => 'boolean',
				'readonly'    => true,
			],
			'sold_individually'   => [
				'description' => 'Allow one item to be bought in a single order.',
				'type'        => 'boolean',
				'default'     => false,
			],
			'weight'              => [
				'description' => 'Product weight (kg).',
				'type'        => 'string',
			],
			'dimensions'          => [
				'description' => 'Product dimensions.',
				'type'        => 'object',
				'properties'  => [
					'length' => [
						'description' => 'Product length (mm).',
						'type'        => 'string',
					],
					'width'  => [
						'description' => 'Product width (mm).',
						'type'        => 'string',
					],
					'height' => [
						'description' => 'Product height (mm).',
						'type'        => 'string',
					],
				],
			],
			'shipping_required'   => [
				'description' => 'Shows if the product need to be shipped.',
				'type'        => 'boolean',
				'readonly'    => true,
			],
			'shipping_taxable'    => [
				'description' => 'Shows whether or not the product shipping is taxable.',
				'type'        => 'boolean',
				'readonly'    => true,
			],
			'shipping_class'      => [
				'description' => 'Shipping class slug.',
				'type'        => 'string',
			],
			'shipping_class_id'   => [
				'description' => 'Shipping class ID.',
				'type'        => 'string',
				'readonly'    => true,
			],
			'reviews_allowed'     => [
				'description' => 'Allow reviews.',
				'type'        => 'boolean',
			],
			'average_rating'      => [
				'description' => 'Reviews average rating.',
				'type'        => 'string',
				'readonly'    => true,
			],
			'rating_count'        => [
				'description' => 'Amount of reviews that the product have.',
				'type'        => 'integer',
				'readonly'    => true,
			],
			'related_ids'         => [
				'description' => 'List of related products IDs.',
				'type'        => 'array',
				'items'       => [
					'type' => 'integer',
				],
				'readonly'    => true,
			],
			'upsell_ids'          => [
				'description' => 'List of up-sell products IDs.',
				'type'        => 'array',
				'items'       => [
					'type' => 'integer',
				],
			],
			'cross_sell_ids'      => [
				'description' => 'List of cross-sell products IDs.',
				'type'        => 'array',
				'items'       => [
					'type' => 'integer',
				],
			],
			'parent_id'           => [
				'description' => 'Product parent ID.',
				'type'        => 'integer',
			],
			'purchase_note'       => [
				'description' => 'Optional note to send the customer after purchase.',
				'type'        => 'string',
			],
			'categories'          => [
				'description' => 'List of categories.',
				'type'        => 'array',
				'items'       => [
					'type'       => 'object',
					'properties' => [
						'id'   => [
							'description' => 'Category ID.',
							'type'        => 'integer',
						],
						'name' => [
							'description' => 'Category name.',
							'type'        => 'string',
							'readonly'    => true,
						],
						'slug' => [
							'description' => 'Category slug.',
							'type'        => 'string',
							'readonly'    => true,
						],
					],
				],
			],
			'tags'                => [
				'description' => 'List of tags.',
				'type'        => 'array',
				'items'       => [
					'type'       => 'object',
					'properties' => [
						'id'   => [
							'description' => 'Tag ID.',
							'type'        => 'integer',
						],
						'name' => [
							'description' => 'Tag name.',
							'type'        => 'string',
							'readonly'    => true,
						],
						'slug' => [
							'description' => 'Tag slug.',
							'type'        => 'string',
							'readonly'    => true,
						],
					],
				],
			],
			'images'              => [
				'description' => 'List of images.',
				'type'        => 'object',
				'items'       => [
					'type'       => 'object',
					'properties' => [
						'id'            => [
							'description' => 'Image ID.',
							'type'        => 'integer',
						],
						'date_created'  => [
							'description' => 'The date the image was created, in the sites timezone.',
							'type'        => 'date-time',
							'readonly'    => true,
						],
						'date_modified' => [
							'description' => 'The date the image was last modified, in the sites timezone.',
							'type'        => 'date-time',
							'context'     => [
								0 => 'view',
								1 => 'edit',
							],
							'readonly'    => true,
						],
						'src'           => [
							'description' => 'Image URL.',
							'type'        => 'string',
							'format'      => 'uri',
						],
						'name'          => [
							'description' => 'Image name.',
							'type'        => 'string',
						],
						'alt'           => [
							'description' => 'Image alternative text.',
							'type'        => 'string',
						],
						'position'      => [
							'description' => 'Image position. 0 means that the image is featured.',
							'type'        => 'integer',
						],
					],
				],
			],
			'attributes'          => [
				'description' => 'List of attributes.',
				'type'        => 'array',
				'items'       => [
					'type'       => 'object',
					'properties' => [
						'id'        => [
							'description' => 'Attribute ID.',
							'type'        => 'integer',
						],
						'name'      => [
							'description' => 'Attribute name.',
							'type'        => 'string',
						],
						'position'  => [
							'description' => 'Attribute position.',
							'type'        => 'integer',
						],
						'visible'   => [
							'description' => 'Define if the attribute is visible on the "Additional information" tab in the product page.',
							'type'        => 'boolean',
							'default'     => false,
						],
						'variation' => [
							'description' => 'Define if the attribute can be used as variation.',
							'type'        => 'boolean',
							'default'     => false,
						],
						'options'   => [
							'description' => 'List of available term names of the attribute.',
							'type'        => 'array',
						],
					],
				],
			],
			'default_attributes'  => [
				'description' => 'Defaults variation attributes.',
				'type'        => 'array',
				'items'       => [
					'type'       => 'object',
					'properties' => [
						'id'     => [
							'description' => 'Attribute ID.',
							'type'        => 'integer',
						],
						'name'   => [
							'description' => 'Attribute name.',
							'type'        => 'string',
						],
						'option' => [
							'description' => 'Selected attribute term name.',
							'type'        => 'string',
						],
					],
				],
			],
			'variations'          => [
				'description' => 'List of variations IDs.',
				'type'        => 'array',
				'items'       => [
					'type' => 'integer',
				],
				'readonly'    => true,
			],
			'grouped_products'    => [
				'description' => 'List of grouped products ID.',
				'type'        => 'array',
				'items'       => [
					'type' => 'integer',
				],
			],
			'menu_order'          => [
				'description' => 'Menu order, used to custom sort products.',
				'type'        => 'integer',
			],
			'meta_data'           => [
				'description' => 'Meta data.',
				'type'        => 'array',
				'items'       => [
					'type'       => 'object',
					'properties' => [
						'id'  => [
							'description' => 'Meta ID.',
							'type'        => 'integer',
							'readonly'    => true,
						],
						'key' => [
							'description' => 'Meta Key',
							'value'       => [
								'description' => 'Meta value',
								'type'        => 'string',
							],
						],
					],
				],
			],
		],
	] );

	triggerhappy_register_value_type(
		'wc_order', 'number', function ( $search ) {
		$req = new WP_REST_Request( 'GET', '/wc/v2/orders' );
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

	triggerhappy_register_api_schema( 'wc_order', '/wc/v2/orders' );
	triggerhappy_register_json_schema(
		'wc_cart', [
			'title'      => 'shop_cart',
			'type'       => 'object',
			'properties' => [
				'cart_contents_total' => [
					'description' => 'Gets the order total (after calculation).',
					'type'        => 'number',
				],
				'cart'                => [
					'description' => 'Gets the contents of the cart.',
					'type'        => 'array',
					'items'       => [
						'type' => 'wc_cart_line',
					],
				],
			],
		]
	);
	triggerhappy_register_value_type( 'wc_cart', 'object' );

	triggerhappy_register_json_schema(
		'wc_quantity_input_args', [
			'title'      => 'wc_quantity_input_args',
			'type'       => 'object',
			'properties' => [
				'min_value' => [
					'description' => 'The minimum value',
					'type'        => 'number',
				],
				'max_value' => [
					'description' => 'The maximum value',
					'type'        => 'number',
				],
				'step'      => [
					'description' => 'The number to step by (eg: 5 would increase quantity in sets of 5)',
					'type'        => 'number',
				],
			],
		]
	);
	triggerhappy_register_value_type( 'wc_quantity_input_args', 'object' );

	triggerhappy_register_json_schema(
		'wc_cart_line', [
			'title'      => 'shop_cart_line',
			'type'       => 'object',
			'properties' => [
				'product_id'   => [
					'description' => 'Gets the Product ID.',
					'type'        => 'number',
				],
				'variation_id' => [
					'description' => 'Gets the Variation ID.',
					'type'        => 'number',
				],
				'quantity'     => [
					'description' => 'Gets the Quantity.',
					'type'        => 'number',
				],
				'line_total'   => [
					'description' => 'Gets the Line Total.',
					'type'        => 'number',
				],
				'data'         => [
					'description' => 'The Product Data',
					'type'        => 'wc_product',
				],

			],
		]
	);

	triggerhappy_register_value_type( 'wc_cart_line', 'object' );

}

add_filter( 'triggerhappy_resolve_wc_order_from_number', function ( $id ) {
	return new WC_Order( $id );
} );
add_filter( 'triggerhappy_to_json__WC_Order', function ( $order ) {
	$req = new WP_REST_Request( 'GET', '/wc/v2/orders/' . $order->ID );

	$response = rest_do_request( $req );
	$data = ( $response->get_data() );

	return $data;
} );
add_filter( 'triggerhappy_resolve_field_wc_product__meta_data', function ( $result, $obj, $fieldName ) {
	$meta = $obj->get_meta_data();
	$formattedMeta = [];
	foreach ( $meta as $i => $data ) {
		$formattedMeta[ $data->key ] = $data->value;
	}

	return [
		'type'  => 'object',
		'value' => $formattedMeta,
	];
}, 10, 3
);

add_action( 'triggerhappy_schema', 'triggerhappy_load_woocommerce_schema' );
add_filter( 'triggerhappy_nodes', 'triggerhappy_load_woocommerce_nodes' );
