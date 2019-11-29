<?php

namespace HotSource\TriggerHappy\Nodes\Plugins\WooCommerce\Triggers;

use HotSource\TriggerHappy\NodeField;
use HotSource\TriggerHappy\Nodes\CoreNode;
use HotSource\TriggerHappy\Nodes\CoreTriggerNode;

class CalculateFees extends CoreTriggerNode {

	/**
	 * CalculateFees constructor.
	 */
	public function __construct() {
		$this->name = 'Calculate Fees';
		$this->description = 'When calculating fees at checkout';
		$this->cat = 'WooCommerce';
		$this->nodeType = $this->getNodeType();
		$this->plugin = 'WooCommerce';
		$this->callback = 'triggerhappy_filter_action';
		$this->hook = 'woocommerce_cart_calculate_fees';
	}

	/**
	 * @return NodeField[]
	 */
	public function generateFields() {
		return [
			new NodeField( 'cart', 'wc_cart', [
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
