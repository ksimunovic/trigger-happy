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
		$this->hook        = 'woocommerce_order_status_changed';
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
		add_action( $this->hook, function ( $order_id, $status_from, $status_to ) use ( $node, $data, $context ) {
			$wc_status_from = 'wc-' . $status_from;
			$wc_status_to   = 'wc-' . $status_to;
			if (
				( $data['from'] === null && $data['to'] === null ) ||
				( $data['from'] === $wc_status_from && $data['to'] === $wc_status_to ) ||
				( $data['from'] === null && $data['to'] === $wc_status_to ) ||
				( $data['from'] === $wc_status_from && $data['to'] === null )
			) {
				return $node->next( $context );
			}
		}, 10, 3 );
	}
}