<?php


namespace HotSource\TriggerHappy\Nodes\Plugins\WooCommerce\Triggers;


use HotSource\TriggerHappy\NodeField;
use HotSource\TriggerHappy\Nodes\CoreNode;
use HotSource\TriggerHappy\Nodes\CoreTriggerNode;

class OrderPaid extends CoreTriggerNode {
	/**
	 * OrderPaid constructor.
	 */
	public function __construct() {
		$this->name        = 'Order Paid';
		$this->description = 'When an order is paid';
		$this->cat         = 'WooCommerce';
		$this->nodeType    = $this->getNodeType();
		$this->fields      = $this->generateFields();
		$this->plugin      = 'WooCommerce';
		$this->callback    = 'triggerhappy_action_hook';
		$this->hook        = 'woocommerce_order_status_processing';
	}

	/**
	 * @return NodeField[]
	 */
	public function generateFields() {
		return [
			new NodeField( 'id', 'number', [
				'dir' => 'start',
			] ),
			new NodeField( 'order', 'wc_order', [
				'dir' => 'start',
			] ),
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
		$this->actionHook( $node, $context );
	}
}