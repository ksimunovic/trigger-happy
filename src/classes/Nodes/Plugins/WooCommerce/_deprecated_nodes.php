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
	
}
