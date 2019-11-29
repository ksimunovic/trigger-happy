<?php

namespace HotSource\TriggerHappy\Nodes\Plugins\WooCommerce\Triggers;

use HotSource\TriggerHappy\NodeField;
use HotSource\TriggerHappy\Nodes\CoreNode;
use HotSource\TriggerHappy\Nodes\CoreTriggerNode;

class QuantityInputArgs extends CoreTriggerNode {

	/**
	 * QuantityInputArgs constructor.
	 */
	public function __construct() {
		$this->name = 'Quantity Input Args';
		$this->description = 'Adjust quantity input values';
		$this->cat = 'WooCommerce';
		$this->nodeType = $this->getNodeType();
		$this->plugin = 'WooCommerce';
		$this->callback = 'triggerhappy_filter_hook';
		$this->hook = 'woocommerce_quantity_input_args';

		// NodeType = result?
		/*
		'resultLabel' => 'Input Args Result',
		'resultDesc'  => 'Updated quantity input args',
		*/
	}

	/**
	 * @return NodeField[]
	 */
	public function generateFields() {
		return [
			new NodeField(
				'args', 'wc_quantity_input_args', [
					'dir' => 'start',
				]
			),
			new NodeField(
				'product', 'wc_product', [
					'dir' => 'start',
				]
			),
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
		$this->filterHook( $node, $context );
	}
}
