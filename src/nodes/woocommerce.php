<?php
include( dirname(__FILE__) . "/functions/woocommerce_functions.php" );

function triggerhappy_load_woocommerce_nodes( $nodes ) {


	$nodes['th_woocommerce_get_price_html'] = array(
		'description' => 'Modify the product price before it is displayed',
		'name' => 'Product Price',
		'plugin' => 'woocommerce',
		'nodeType' => 'trigger',
		'cat' => 'woocommerce',
		'fields' => array(

			triggerhappy_field( 'price', 'html', array('dir'=>'start') ),
			triggerhappy_field( 'product', 'wc_product', array('dir'=>'start') ),

		),
		'callback' => 'triggerhappy_filter_hook',
	);

	$nodes['th_woocommerce_single_product'] = array(
		'description' => 'When a single product is being viewed on the front-end',
		'name' => 'When a Single Product is viewed',
		'plugin' => '',
		'triggerType'=>'product_render',
		'nodeType' => 'trigger',
		'hook'=>'template_redirect',
		'callback'=>'triggerhappy_action_hook',
		'cat' => 'Front-end - WooCommerce',
		'globals'=>array('post'=>'post'),
		'fields' => array(
			triggerhappy_field( 'post', 'wp_post', array('dir'=>'start') ),
		),
		'nodeFilters'=> array(
			array(
				TH::Filter(TH::Expression("_N.wpPageFunctions.is_single"),'equals',true),
				TH::Filter(TH::Expression("_N.wpPageFunctions.get_post_type"),'equals','product'),
			)
		)
	);
	$nodes['th_woocommerce_insert_html_single_product'] = array(
		'description' => 'Add content to a single product page',
		'name' => 'Add HTML to Single Product template',
		'plugin' => 'woocommerce',
		'nodeType' => 'action',
		'actionType'=>'product_render',
		'hook'=>'$section',
		'cat' => 'woocommerce',
		'globals'=>array('product'=>'product'),
		'fields' => array(

			triggerhappy_field( 'product', 'wc_product', array('dir'=>'start') ),
			triggerhappy_field( 'section', 'string', array(
				'label'=>'Section',
				'description' => 'Select the section of the page you attach this flow to',
				'dir'=>'in',
				'choices'=>array(
					array('id'=>'woocommerce_before_main_content','text'=>'Before the main content section'),
					array('id'=>'woocommerce_after_main_content','text'=>'After the main content section'),
					array('id'=>'woocommerce_before_single_product','text'=>'Top of the page (before product content)'),
					array('id'=>'woocommerce_after_single_product','text'=>'Bottom of the page (after product content)'),

					array('id'=>'woocommerce_before_single_product_summary','text'=>'Before the summary section'),
					array('id'=>'woocommerce_single_product_summary:4','text'=>'Before the product title'),

					array('id'=>'woocommerce_single_product_summary:6','text'=>'Before the product rating/price'),
					array('id'=>'woocommerce_single_product_summary:15','text'=>'Before the product excerpt'),
					array('id'=>'woocommerce_single_product_summary:25','text'=>'Before the add to cart buttons'),
					array('id'=>'woocommerce_single_product_summary:35','text'=>'After the add to cart buttons'),
					array('id'=>'woocommerce_single_product_summary:45','text'=>'Before the sharing buttons'),
					array('id'=>'woocommerce_single_product_summary:55','text'=>'After the sharing buttons'),
					array('id'=>'woocommerce_after_single_product_summary:5','text'=>'After the summary section - before tabs'),
					array('id'=>'woocommerce_after_single_product_summary:25','text'=>'After the summary section - after related products'),
				)
			) ),
			triggerhappy_field( 'html', 'html', array( 'label' => 'HTML', 'description'=>'The HTML to be inserted', 'dir' => 'in' ) )
		),
		'callback' => 'triggerhappy_woocommerce_output_html_single_product',
	);

	$nodes['th_woocommerce_checkout_fields'] = array(
		'name' => 'Checkout Fields',
		'plugin' => 'woocommerce',
		'nodeType' => 'trigger',
		'hook' => 'woocommerce_checkout_fields',
		'cat' => 'woocommerce',
		'callback' => 'triggerhappy_filter_hook',
		'fields' => array(
				triggerhappy_field(
					'fields', 'array', array(
						'argIndex' => 0,
						'dir'=>'out'
					)

			),
		),
	);
	$nodes['th_woocommerce_before_checkout_form'] = array(
		'name' => 'Before Checkout Form',
		'plugin' => 'woocommerce',
		'nodeType' => 'trigger',
		'description'=>'Before the checkout form is displayed',
		'hook' => 'woocommerce_before_checkout_form',
		'cat' => 'woocommerce',
		'callback' => 'triggerhappy_action_hook',
		'fields' => array(
		),
	);
	$nodes['th_wc_add_coupon'] = array(
		'description' => 'Create a new coupon',
		'name' => 'Create coupon',
		'plugin' => 'woocommerce',
		'nodeType' => 'action',
		'callback' => 'triggerhappy_wc_add_coupon',
		'cat' => 'woocommerce',
		'fields' => array(

			triggerhappy_field( 'code', 'string' ),
			triggerhappy_field( 'code', 'string' ),
			triggerhappy_field( 'discount_type', 'string', array(
				'choices'=> triggerhappy_assoc_to_choices(wc_get_coupon_types())
			) ),
			triggerhappy_field( 'amount', 'number' ),
		),
	);

	$nodes['th_wc_calculate_fees'] = array(
		'name' => 'Calculate Fees',
		'plugin' => 'woocommerce',
		'nodeType' => 'trigger',
		'description' => 'When calculating fees at checkout',
		'hook' => 'woocommerce_cart_calculate_fees',
		'cat' => 'woocommerce',
		'callback' => 'triggerhappy_action_hook',

		'fields' => array(
			triggerhappy_field( 'cart', 'wc_cart', array('dir'=>'start') ),

		),
	);
	$nodes['th_wc_add_notice'] = array(
		'name' => 'Add WooCommerce Notice',
		'plugin'=> 'woocommerce',
		'nodeType'=>'action',
		'description'=>'Add a WooCommerce notice message',
		'cat'=>'woocommerce',
		'callback' => 'triggerhappy_function_call',
		'function' => 'wc_add_notice',
		'function_args'=> array('notice','notice_type'),
		'fields' => array(

			triggerhappy_field('notice_type','wc_notice_type', array('label'=>'Notice Type', 'description'=>'The type of notice')),
			triggerhappy_field('notice','html', array('label'=>'Content', 'description'=>'The message to be displayed'))
		)
	);

	$nodes['th_woocommerce_quantity_input_args'] = array(
		'name' => 'Quantity Input Args',
		'plugin' => 'woocommerce',
		'nodeType' => 'trigger',
		'description' => 'Adjust quantity input values',
		'hook' => 'woocommerce_quantity_input_args',
		'cat' => 'woocommerce',
		'callback' => 'triggerhappy_filter_hook',
		'resultLabel' => 'Input Args Result',
		'resultDesc' => 'Updated quantity input args',
		'fields' => array(

			triggerhappy_field(
				'args', 'wc_quantity_input_args', array(
					'dir' => 'start',
				)
			),
			triggerhappy_field(
				'product', 'wc_product', array(
					'dir' => 'start',
				)
			),

		),
	);
	$nodes['th_woocommerce_wc_order_created'] = array(
		'name' => 'Order Created',
		'description' => 'When an order is created',
		'cat' => 'woocommerce',
		'plugin' => 'woocommerce',
		'nodeType' => 'trigger',
		'hook' => 'woocommerce_thankyou',
		'callback' => 'triggerhappy_action_hook',
		'fields' => array(
			triggerhappy_field(
				'order', 'wc_order', array(
					'dir' => 'start',
				)
			),
		),
	);
	$nodes['th_woocommerce_wc_add_fee'] = array(
		'name' => 'Add Fee',
		'plugin' => 'woocommerce',
		'cat' => 'woocommerce',
		'nodeType' => 'action',
		'description' => 'Add a new fee to the order',
		'callback' => 'triggerhappy_wc_add_fee',
		'fields' => array(
			triggerhappy_field(
				'fee', 'number', array(
					'label' => 'Fee',
					'description' => 'Enter the fee amount',
				)
			),
			triggerhappy_field(
				'description', 'string', array(
					'label' => 'Description',
					'description' => 'The description of the fee - will be shown on the order',
				)
			),
		),
	);
	return $nodes;
}

function triggerhappy_load_woocommerce_schema() {

	triggerhappy_register_value_type(
		'wc_product', 'number', function( $search ) {
			$req = new WP_REST_Request( 'GET', '/wc/v2/products' );
			$req->set_param( 'search', $search );
			$response = rest_do_request( $req );
			$data     = ($response->get_data());
			return array_map(
				function( $d ) {
					return array(
						'id' => $d['id'],
						'text' => $d['name'],
					);
				}, $data
			);
		}, true
	);
	triggerhappy_register_value_type(
		'wc_notice_type', 'string', function( ) {
			return array(
				array('id'=>'error','text'=>'Error'),
				array('id'=>'success','text'=>'Success'),
				array('id'=>'notice','text'=>'Notice')
			);
		}, false
	);

	triggerhappy_register_json_schema('wc_product', array(
		'$schema' => 'http://json-schema.org/draft-04/schema#',
		'title' => 'product',
		'type' => 'object',
		'properties' => array(
			'id' => array(
				'description' => 'Unique identifier for the resource.',
				'type' => 'integer',
				'readonly' => true,
			),
			'name' => array(
				'description' => 'Product name.',
				'type' => 'string',
			),
			'slug' => array(
				'description' => 'Product slug.',
				'type' => 'string',
			),
			'permalink' => array(
				'description' => 'Product URL.',
				'type' => 'string',
				'format' => 'uri',
				'readonly' => true,
			),
			'date_created' => array(
				'description' => 'The date the product was created, in the sites timezone.',
				'type' => 'date-time',
				'readonly' => true,
			),
			'date_modified' => array(
				'description' => 'The date the product was last modified, in the sites timezone.',
				'type' => 'date-time',
				'readonly' => true,
			),

			'type' => array(
				'description' => 'Product type.',
				'type' => 'string',
				'default' => 'simple',
				'enum' => array(
					0 => 'simple',
					1 => 'grouped',
					2 => 'external',
					3 => 'variable',
				),
			),
			'status' => array(
				'description' => 'Product status (post status).',
				'type' => 'string',
				'default' => 'publish',
				'enum' => array(
					0 => 'draft',
					1 => 'pending',
					2 => 'private',
					3 => 'publish',
				),
			),
			'featured' => array(
				'description' => 'Featured product.',
				'type' => 'boolean',
				'default' => false,
			),
			'catalog_visibility' => array(
				'description' => 'Catalog visibility.',
				'type' => 'string',
				'default' => 'visible',
				'enum' => array(
					0 => 'visible',
					1 => 'catalog',
					2 => 'search',
					3 => 'hidden',
				),
			),
			'description' => array(
				'description' => 'Product description.',
				'type' => 'string',
			),
			'short_description' => array(
				'description' => 'Product short description.',
				'type' => 'string',
			),
			'sku' => array(
				'description' => 'Unique identifier.',
				'type' => 'string',
			),
			'price' => array(
				'description' => 'Current product price.',
				'type' => 'string',
				'readonly' => true,
			),
			'regular_price' => array(
				'description' => 'Product regular price.',
				'type' => 'string',
			),
			'sale_price' => array(
				'description' => 'Product sale price.',
				'type' => 'string',
			),
			'date_on_sale_from' => array(
				'description' => 'Start date of sale price, in the sites timezone.',
				'type' => 'date-time',
			),
			'date_on_sale_to' => array(
				'description' => 'End date of sale price, in the sites timezone.',
				'type' => 'date-time',
			),
			'date_on_sale_to_gmt' => array(
				'description' => 'End date of sale price, in the sites timezone.',
				'type' => 'date-time',
			),
			'price_html' => array(
				'description' => 'Price formatted in HTML.',
				'type' => 'string',
				'readonly' => true,
			),
			'on_sale' => array(
				'description' => 'Shows if the product is on sale.',
				'type' => 'boolean',
				'readonly' => true,
			),
			'purchasable' => array(
				'description' => 'Shows if the product can be bought.',
				'type' => 'boolean',
				'readonly' => true,
			),
			'total_sales' => array(
				'description' => 'Amount of sales.',
				'type' => 'integer',
				'readonly' => true,
			),
			'virtual' => array(
				'description' => 'If the product is virtual.',
				'type' => 'boolean',
				'default' => false,
			),
			'downloadable' => array(
				'description' => 'If the product is downloadable.',
				'type' => 'boolean',
				'default' => false,
			),
			'downloads' => array(
				'description' => 'List of downloadable files.',
				'type' => 'array',
				'items' => array(
					'type' => 'object',
					'properties' => array(
						'id' => array(
							'description' => 'File MD5 hash.',
							'type' => 'string',
							'readonly' => true,
						),
						'name' => array(
							'description' => 'File name.',
							'type' => 'string',
						),
						'file' => array(
							'description' => 'File URL.',
							'type' => 'string',
						),
					),
				),
			),
			'download_limit' => array(
				'description' => 'Number of times downloadable files can be downloaded after purchase.',
				'type' => 'integer',
				'default' => -1,
			),
			'download_expiry' => array(
				'description' => 'Number of days until access to downloadable files expires.',
				'type' => 'integer',
				'default' => -1,
			),
			'external_url' => array(
				'description' => 'Product external URL. Only for external products.',
				'type' => 'string',
				'format' => 'uri',
			),
			'button_text' => array(
				'description' => 'Product external button text. Only for external products.',
				'type' => 'string',
			),
			'tax_status' => array(
				'description' => 'Tax status.',
				'type' => 'string',
				'default' => 'taxable',
				'enum' => array(
					0 => 'taxable',
					1 => 'shipping',
					2 => 'none',
				),
			),
			'tax_class' => array(
				'description' => 'Tax class.',
				'type' => 'string',
			),
			'manage_stock' => array(
				'description' => 'Stock management at product level.',
				'type' => 'boolean',
				'default' => false,
			),
			'stock_quantity' => array(
				'description' => 'Stock quantity.',
				'type' => 'integer',
			),
			'in_stock' => array(
				'description' => 'Controls whether or not the product is listed as "in stock" or "out of stock" on the frontend.',
				'type' => 'boolean',
				'default' => true,
			),
			'backorders' => array(
				'description' => 'If managing stock, this controls if backorders are allowed.',
				'type' => 'string',
				'default' => 'no',
				'enum' => array(
					0 => 'no',
					1 => 'notify',
					2 => 'yes',
				),
			),
			'backorders_allowed' => array(
				'description' => 'Shows if backorders are allowed.',
				'type' => 'boolean',
				'readonly' => true,
			),
			'backordered' => array(
				'description' => 'Shows if the product is on backordered.',
				'type' => 'boolean',
				'readonly' => true,
			),
			'sold_individually' => array(
				'description' => 'Allow one item to be bought in a single order.',
				'type' => 'boolean',
				'default' => false,
			),
			'weight' => array(
				'description' => 'Product weight (kg).',
				'type' => 'string',
			),
			'dimensions' => array(
				'description' => 'Product dimensions.',
				'type' => 'object',
				'properties' => array(
					'length' => array(
						'description' => 'Product length (mm).',
						'type' => 'string',
					),
					'width' => array(
						'description' => 'Product width (mm).',
						'type' => 'string',
					),
					'height' => array(
						'description' => 'Product height (mm).',
						'type' => 'string',
					),
				),
			),
			'shipping_required' => array(
				'description' => 'Shows if the product need to be shipped.',
				'type' => 'boolean',
				'readonly' => true,
			),
			'shipping_taxable' => array(
				'description' => 'Shows whether or not the product shipping is taxable.',
				'type' => 'boolean',
				'readonly' => true,
			),
			'shipping_class' => array(
				'description' => 'Shipping class slug.',
				'type' => 'string',
			),
			'shipping_class_id' => array(
				'description' => 'Shipping class ID.',
				'type' => 'string',
				'readonly' => true,
			),
			'reviews_allowed' => array(
				'description' => 'Allow reviews.',
				'type' => 'boolean',
			),
			'average_rating' => array(
				'description' => 'Reviews average rating.',
				'type' => 'string',
				'readonly' => true,
			),
			'rating_count' => array(
				'description' => 'Amount of reviews that the product have.',
				'type' => 'integer',
				'readonly' => true,
			),
			'related_ids' => array(
				'description' => 'List of related products IDs.',
				'type' => 'array',
				'items' => array(
					'type' => 'integer',
				),
				'readonly' => true,
			),
			'upsell_ids' => array(
				'description' => 'List of up-sell products IDs.',
				'type' => 'array',
				'items' => array(
					'type' => 'integer',
				),
			),
			'cross_sell_ids' => array(
				'description' => 'List of cross-sell products IDs.',
				'type' => 'array',
				'items' => array(
					'type' => 'integer',
				),
			),
			'parent_id' => array(
				'description' => 'Product parent ID.',
				'type' => 'integer',
			),
			'purchase_note' => array(
				'description' => 'Optional note to send the customer after purchase.',
				'type' => 'string',
			),
			'categories' => array(
				'description' => 'List of categories.',
				'type' => 'array',
				'items' => array(
					'type' => 'object',
					'properties' => array(
						'id' => array(
							'description' => 'Category ID.',
							'type' => 'integer',
						),
						'name' => array(
							'description' => 'Category name.',
							'type' => 'string',
							'readonly' => true,
						),
						'slug' => array(
							'description' => 'Category slug.',
							'type' => 'string',
							'readonly' => true,
						),
					),
				),
			),
			'tags' => array(
				'description' => 'List of tags.',
				'type' => 'array',
				'items' => array(
					'type' => 'object',
					'properties' => array(
						'id' => array(
							'description' => 'Tag ID.',
							'type' => 'integer',
						),
						'name' => array(
							'description' => 'Tag name.',
							'type' => 'string',
							'readonly' => true,
						),
						'slug' => array(
							'description' => 'Tag slug.',
							'type' => 'string',
							'readonly' => true,
						),
					),
				),
			),
			'images' => array(
				'description' => 'List of images.',
				'type' => 'object',
				'items' => array(
					'type' => 'object',
					'properties' => array(
						'id' => array(
							'description' => 'Image ID.',
							'type' => 'integer',
						),
						'date_created' => array(
							'description' => 'The date the image was created, in the sites timezone.',
							'type' => 'date-time',
							'readonly' => true,
						),
						'date_modified' => array(
							'description' => 'The date the image was last modified, in the sites timezone.',
							'type' => 'date-time',
							'context' => array(
								0 => 'view',
								1 => 'edit',
							),
							'readonly' => true,
						),
						'src' => array(
							'description' => 'Image URL.',
							'type' => 'string',
							'format' => 'uri',
						),
						'name' => array(
							'description' => 'Image name.',
							'type' => 'string',
						),
						'alt' => array(
							'description' => 'Image alternative text.',
							'type' => 'string',
						),
						'position' => array(
							'description' => 'Image position. 0 means that the image is featured.',
							'type' => 'integer',
						),
					),
				),
			),
			'attributes' => array(
				'description' => 'List of attributes.',
				'type' => 'array',
				'items' => array(
					'type' => 'object',
					'properties' => array(
						'id' => array(
							'description' => 'Attribute ID.',
							'type' => 'integer',
						),
						'name' => array(
							'description' => 'Attribute name.',
							'type' => 'string',
						),
						'position' => array(
							'description' => 'Attribute position.',
							'type' => 'integer',
						),
						'visible' => array(
							'description' => 'Define if the attribute is visible on the "Additional information" tab in the product page.',
							'type' => 'boolean',
							'default' => false,
						),
						'variation' => array(
							'description' => 'Define if the attribute can be used as variation.',
							'type' => 'boolean',
							'default' => false,
						),
						'options' => array(
							'description' => 'List of available term names of the attribute.',
							'type' => 'array',
						),
					),
				),
			),
			'default_attributes' => array(
				'description' => 'Defaults variation attributes.',
				'type' => 'array',
				'items' => array(
					'type' => 'object',
					'properties' => array(
						'id' => array(
							'description' => 'Attribute ID.',
							'type' => 'integer',
						),
						'name' => array(
							'description' => 'Attribute name.',
							'type' => 'string',
						),
						'option' => array(
							'description' => 'Selected attribute term name.',
							'type' => 'string',
						),
					),
				),
			),
			'variations' => array(
				'description' => 'List of variations IDs.',
				'type' => 'array',
				'items' => array(
					'type' => 'integer',
				),
				'readonly' => true,
			),
			'grouped_products' => array(
				'description' => 'List of grouped products ID.',
				'type' => 'array',
				'items' => array(
					'type' => 'integer',
				),
			),
			'menu_order' => array(
				'description' => 'Menu order, used to custom sort products.',
				'type' => 'integer',
			),
			'meta_data' => array(
				'description' => 'Meta data.',
				'type' => 'array',
				'items' => array(
					'type' => 'object',
					'properties' => array(
						'id' => array(
							'description' => 'Meta ID.',
							'type' => 'integer',
							'readonly' => true,
						),
						'key' => array(
							'description' => 'Meta Key',
							'value' => array(
								'description' => 'Meta value',
								'type' => 'string',
							),
						),
					),
				),
			),
		),
	));

	triggerhappy_register_value_type(
		'wc_order', 'number', function( $search ) {
			$req = new WP_REST_Request( 'GET', '/wc/v2/orders' );
			$req->set_param( 'search', $search );
			$response = rest_do_request( $req );
			$data     = ($response->get_data());
			return array_map(
				function( $d ) {
					return array(
						'id' => $d['id'],
						'text' => $d['name'],
					);
				}, $data
			);
		}, true
	);

	triggerhappy_register_api_schema( 'wc_order', '/wc/v2/orders' );
	triggerhappy_register_json_schema(
		'wc_cart', array(
			'title' => 'shop_cart',
			'type' => 'object',
			'properties' => array(
				'cart_contents_total' => array(
					'description' => 'Gets the order total (after calculation).',
					'type' => 'number',
				),
				'cart' => array(
					'description' => 'Gets the contents of the cart.',
					'type' => 'array',
					'items' => array(
						'type' => 'wc_cart_line',
					),
				),
			),
		)
	);
	triggerhappy_register_value_type( 'wc_cart', 'object' );

	triggerhappy_register_json_schema(
		'wc_quantity_input_args', array(
			'title' => 'wc_quantity_input_args',
			'type' => 'object',
			'properties' => array(
				'min_value' => array(
					'description' => 'The minimum value',
					'type' => 'number',
				),
				'max_value' => array(
					'description' => 'The maximum value',
					'type' => 'number',
				),
				'step' => array(
					'description' => 'The number to step by (eg: 5 would increase quantity in sets of 5)',
					'type' => 'number',
				),
			),
		)
	);
	triggerhappy_register_value_type( 'wc_quantity_input_args', 'object' );

	triggerhappy_register_json_schema(
		'wc_cart_line', array(
			'title' => 'shop_cart_line',
			'type' => 'object',
			'properties' => array(
				'product_id' => array(
					'description' => 'Gets the Product ID.',
					'type' => 'number',
				),
				'variation_id' => array(
					'description' => 'Gets the Variation ID.',
					'type' => 'number',
				),
				'quantity' => array(
					'description' => 'Gets the Quantity.',
					'type' => 'number',
				),
				'line_total' => array(
					'description' => 'Gets the Line Total.',
					'type' => 'number',
				),
				'data' => array(
					'description' => 'The Product Data',
					'type' => 'wc_product',
				),

			),
		)
	);

	triggerhappy_register_value_type( 'wc_cart_line', 'object' );

}

add_filter(	'triggerhappy_resolve_field_wc_product__meta_data', function( $result, $obj, $fieldName ) {
		$meta          = $obj->get_meta_data();
		$formattedMeta = array();
		foreach ( $meta as $i => $data ) {
			$formattedMeta[ $data->key ] = $data->value;
		}
		return array(
			'type' => 'object',
			'value' => $formattedMeta,
		);
	}, 10, 3
);

add_action( 'triggerhappy_schema', 'triggerhappy_load_woocommerce_schema' );
add_filter( 'triggerhappy_nodes', 'triggerhappy_load_woocommerce_nodes' );
