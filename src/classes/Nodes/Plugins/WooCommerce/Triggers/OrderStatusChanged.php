<?php


namespace HotSource\TriggerHappy\Nodes\Plugins\WooCommerce\Triggers;


use HotSource\TriggerHappy\NodeField;
use HotSource\TriggerHappy\Nodes\CoreTriggerNode;

class OrderStatusChanged extends CoreTriggerNode {
	public function __construct() {
		$this->name        = 'Order Status Change';
		$this->description = 'Runs on specific order status change';
		$this->cat         = 'WooCommerce';
		$this->nodeType    = $this->getNodeType();
		$this->fields      = $this->generateFields();
		$this->plugin      = 'woocommerce';
		$this->callback    = 'triggerhappy_action_hook';
	}

	public function generateFields() {
		return [
			new NodeField( 'from', 'string',
				[
					'choices' => triggerhappy_assoc_to_choices( wc_get_order_statuses() ),
				]
			),
			new NodeField( 'to', 'string',
				[
					'choices' => triggerhappy_assoc_to_choices( wc_get_order_statuses() ),
				]
			),
		];
	}

	public function runCallback( $node, $context, $data = null ) {
		$data['hook'] = 'woocommerce_order_status_changed';

		add_action( $data['hook'], function () use ( $node, $data, $context ) {
			echo "";

			if (
				($data['from'] === $_POST['post_status'] && $data['to'] === $_POST['order_status']) ||
				($data['from'] === null && $data['to'] === $_POST['order_status']) ||
				($data['from'] === $_POST['post_status'] && $data['to'] === null)
			) {
				return $node->next( $context );
			}
		}, 10, 3 );
	}
}