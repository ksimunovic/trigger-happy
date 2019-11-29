<?php

namespace HotSource\TriggerHappy\Nodes\Plugins\WooCommerce\Triggers;

use HotSource\TriggerHappy\NodeField;
use HotSource\TriggerHappy\Nodes\CoreNode;
use HotSource\TriggerHappy\Nodes\CoreTriggerNode;

class CheckoutFieldsRendering extends CoreTriggerNode {

	/**
	 * CheckoutFieldsRendering constructor.
	 */
	public function __construct() {
		$this->name = 'When Rendering Checkout Fields';
		$this->description = 'When displaying the checkout fields';
		$this->cat = 'WooCommerce';
		$this->nodeType = $this->getNodeType();
		$this->fields = $this->generateFields();
		$this->plugin = 'WooCommerce';
		$this->callback = 'triggerhappy_filter_hook';
		$this->hook = 'woocommerce_checkout_fields';
	}

	/**
	 * @return NodeField[]
	 */
	public function generateFields() {
		return [
			new NodeField( 'fields', 'array', [
				'argIndex' => 0,
				'dir'      => 'out',
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
		$this->filterHook( $node, $context );
	}
}
