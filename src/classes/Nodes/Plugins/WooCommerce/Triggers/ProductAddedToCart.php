<?php


namespace HotSource\TriggerHappy\Nodes\Plugins\WooCommerce\Triggers;


use HotSource\TriggerHappy\NodeField;
use HotSource\TriggerHappy\Nodes\CoreNode;
use HotSource\TriggerHappy\Nodes\CoreTriggerNode;

class ProductAddedToCart extends CoreTriggerNode {
	/**
	 * ProductAddedToCart constructor.
	 */
	public function __construct() {
		$this->name = 'Product Add to Cart';
		$this->description = 'When a product is add to cart';
		$this->cat = 'WooCommerce';
		$this->nodeType = $this->getNodeType();
		$this->fields = $this->generateFields();
		$this->plugin = 'WooCommerce';
		$this->callback = 'triggerhappy_action_hook';
		$this->hook = 'woocommerce_add_to_cart';
	}

	/**
	 * @return NodeField[]
	 */
	public function generateFields() {
		return [
			new NodeField( 'id', 'number', [
				'description' => 'The cart item key', 'dir' => 'start',
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