<?php

function triggerhappy_get_wc_cart( $node, $context ) {

	global $woocommerce;

	$node->next(
		$context, array(
			'cart' => $woocommerce->cart,
		)
	);
}
function triggerhappy_wc_add_fee( $node, $context ) {
	$data = $node->getInputData( $context );

	global $woocommerce;

	$woocommerce->cart->add_fee( $data['description'], $data['fee'], true, '' );
}
