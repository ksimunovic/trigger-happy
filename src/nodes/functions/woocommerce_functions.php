<?php

function triggerhappy_get_wc_cart( $node, $context ) {

	global $woocommerce;

	$node->next(
		$context, [
			'cart' => $woocommerce->cart,
		]
	);
}

function triggerhappy_woocommerce_create_product( $node, $context ) {
	/* triggerhappy_field( 'post_id', 'string', array(
		'label' => 'Post ID',
		'description' => 'Specify the post ID to update an existing post. Leave blank to create a new post',
		'dir' => 'in',
	) ),
	triggerhappy_field(	'post_type', 'wp_post_type', array(
		'label' => 'Post Type',
		'description' => 'Specify the type of post to create',
		'dir' => 'in',
	) ),
	triggerhappy_field('post_title', 'string', array(
		'label' => 'Post Title',
		'dir' => 'in',
	) ),
	triggerhappy_field('post_content', 'string', array(
		'label' => 'Content',
		'dir' => 'in',
	) )
	,
	triggerhappy_field('post_status', 'wp_post_status', array(
		'label' => 'Post Status',
		'dir' => 'in',
	) )
) */
	$data = $node->getInputData( $context );
	$id = isset( $data['id'] ) ? $data['id'] : "";
	$data_to_save = [];
	foreach ( $data as $key => $value ) {
		if ( isset( $value ) && ! empty( $value ) ) {
			$data_to_save[ $key ] = $value;
		}
	}
	$request_type = 'PUT';
	if ( empty( $id ) ) {
		$request_type = 'POST';
	}
	$req = new WP_REST_Request( $request_type, '/wc/v2/products/' . $id );
	$req->set_header( 'Content-Type', 'application/json' );

	$req->set_body( json_encode( $data_to_save ) );
	add_filter( 'woocommerce_rest_check_permissions', '__return_true' );
	$response = rest_do_request( $req );
	remove_filter( 'woocommerce_rest_check_permissions', '__return_true' );
	$data = ( $response->get_data() );
	$node->next( $context, $data );

}

function triggerhappy_woocommerce_output_html_single_product( $node, $context ) {
	$hook = isset( $node->def['hook'] ) ? $node->def['hook'] : $node->def['id'];
	$priority = 10;
	if ( strpos( $hook, '$' ) === 0 ) {
		$hookField = substr( $hook, 1 );
		$inputData = $node->getInputData( $context );
		$hook = $inputData[ $hookField ];
		$hook_parts = explode( ':', $hook );
		$hook = $hook_parts[0];
		if ( count( $hook_parts ) > 1 ) {
			$priority = $hook_parts[1];
		}
	}

	add_action( $hook, function () use ( $hook, $node, $context ) {
		$data = $node->getInputData( $context );
		echo $data['html'];
	}, $priority );
}

function triggerhappy_wc_add_fee( $node, $context ) {
	$data = $node->getInputData( $context );

	global $woocommerce;

	$woocommerce->cart->add_fee( $data['description'], $data['fee'], true, '' );
}

function triggerhappy_wc_add_coupon( $node, $context ) {
	$data = $node->getInputData( $context );

	$coupon = [
		'post_title'   => $data['code'],
		'post_content' => '',
		'post_status'  => 'publish',
		'post_author'  => 1,
		'post_type'    => 'shop_coupon',
	];
	$new_coupon_id = wp_insert_post( $coupon );
	update_post_meta( $new_coupon_id, 'discount_type', $data['discount_type'] );
	update_post_meta( $new_coupon_id, 'coupon_amount', $data['amount'] );
	update_post_meta( $new_coupon_id, 'individual_use', 'no' );
	update_post_meta( $new_coupon_id, 'product_ids', '' );
	update_post_meta( $new_coupon_id, 'exclude_product_ids', '' );
	update_post_meta( $new_coupon_id, 'usage_limit', '' );
	update_post_meta( $new_coupon_id, 'expiry_date', '' );
	update_post_meta( $new_coupon_id, 'apply_before_tax', 'yes' );
	update_post_meta( $new_coupon_id, 'free_shipping', 'no' );
	$node->next( $context, [ 'coupon_id' => $new_coupon_id ] );

}
