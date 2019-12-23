<?php

namespace HotSource\TriggerHappy\Nodes\Plugins\WooCommerce\Actions;

use HotSource\TriggerHappy\NodeField;
use HotSource\TriggerHappy\Nodes\CoreActionNode;
use HotSource\TriggerHappy\Nodes\CoreNode;

class AddCoupon extends CoreActionNode {

	/**
	 * AddCoupon constructor.
	 */
	public function __construct() {
		$this->name = 'Create a new coupon';
		$this->description = 'Create coupon';
		$this->plugin = 'WooCommerce';
		$this->cat = 'WooCommerce';
		$this->callback = 'triggerhappy_wc_add_coupon';
		$this->nodeType = $this->getNodeType();
		$this->fields = $this->generateFields();
	}

	/**
	 * @return NodeField[]
	 */
	public function generateFields() {
		return [
			new NodeField( 'code', 'string' ),
			new NodeField( 'discount_type', 'string', [
				'choices' => triggerhappy_assoc_to_choices( wc_get_coupon_types() ),
			] ),
			new NodeField( 'amount', 'number' ),
		];
	}

	/**
	 * @param CoreNode $node
	 * @param \TriggerHappyContext $context
	 * @param null $data
	 *
	 * @return void|null
	 */
	public function runCallback( $node, $context, $data = null ) {

		$coupon = [
			'post_title'   => $data['code'],
			'post_content' => '',
			'post_status'  => 'publish',
			'post_author'  => 1,
			'post_type'    => 'shop_coupon',
		];

		$data['coupon_id'] = wp_insert_post( $coupon );
		update_post_meta( $data['coupon_id'], 'discount_type', $data['discount_type'] );
		update_post_meta( $data['coupon_id'], 'coupon_amount', $data['amount'] );
		update_post_meta( $data['coupon_id'], 'individual_use', 'no' );
		update_post_meta( $data['coupon_id'], 'product_ids', '' );
		update_post_meta( $data['coupon_id'], 'exclude_product_ids', '' );
		update_post_meta( $data['coupon_id'], 'usage_limit', '' );
		update_post_meta( $data['coupon_id'], 'expiry_date', '' );
		update_post_meta( $data['coupon_id'], 'apply_before_tax', 'yes' );
		update_post_meta( $data['coupon_id'], 'free_shipping', 'no' );

		$node->next( $context, $data );
	}
}
