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

}
