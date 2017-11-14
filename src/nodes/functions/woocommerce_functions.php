<?php

function triggerhappy_get_wc_cart( $node, $context ) {

	global $woocommerce;

	$node->next(
		$context, array(
			'cart' => $woocommerce->cart,
		)
	);
}
function triggerhappy_woocommerce_output_html_single_product( $node, $context ) {
	$hook = isset( $node->def['hook'] ) ? $node->def['hook'] : $node->def['id'];
	$priority = 10;
	if (strpos($hook,'$') === 0) {
		$hookField = substr($hook,1);
		$inputData = $node->getInputData($context);
		$hook = $inputData[$hookField];
		$hook_parts = explode( ':', $hook );
		$hook = $hook_parts[0];
		if (count($hook_parts) > 1) {
			$priority = $hook_parts[1];
		}
	}
	
	add_action(	$hook, function () use ( $hook, $node, $context ) {
		$data = $node->getInputData( $context );
		echo $data['html'];
	},$priority);
}
function triggerhappy_wc_add_fee( $node, $context ) {
	$data = $node->getInputData( $context );

	global $woocommerce;

	$woocommerce->cart->add_fee( $data['description'], $data['fee'], true, '' );
}

function triggerhappy_wc_add_coupon( $node, $context ) {
	$data = $node->getInputData( $context );

	$coupon = array(
		'post_title' => $data['code'],
		'post_content' => '',
		'post_status' => 'publish',
		'post_author' => 1,
		'post_type'	=> 'shop_coupon'
	);
	$new_coupon_id = wp_insert_post( $coupon );
	update_post_meta( $new_coupon_id, 'discount_type', $data[ 'discount_type' ] );
	update_post_meta( $new_coupon_id, 'coupon_amount', $data[ 'amount' ] );
	update_post_meta( $new_coupon_id, 'individual_use', 'no' );
	update_post_meta( $new_coupon_id, 'product_ids', '' );
	update_post_meta( $new_coupon_id, 'exclude_product_ids', '' );
	update_post_meta( $new_coupon_id, 'usage_limit', '' );
	update_post_meta( $new_coupon_id, 'expiry_date', '' );
	update_post_meta( $new_coupon_id, 'apply_before_tax', 'yes' );
	update_post_meta( $new_coupon_id, 'free_shipping', 'no' );
	$node->next( $context, array( 'coupon_id' => $new_coupon_id ) );

}
