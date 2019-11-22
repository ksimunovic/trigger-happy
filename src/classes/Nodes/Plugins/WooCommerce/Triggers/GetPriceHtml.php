<?php

namespace HotSource\TriggerHappy\Nodes\Plugins\WooCommerce\Triggers;

use HotSource\TriggerHappy\NodeField;
use HotSource\TriggerHappy\Nodes\CoreNode;
use HotSource\TriggerHappy\Nodes\CoreTriggerNode;

class GetPriceHtml extends CoreTriggerNode {

	/**
	 * GetPriceHtml constructor.
	 */
	public function __construct() {
		$this->name = 'Product Price';
		$this->description = 'Modify the product price before it is displayed';
		$this->cat = 'WooCommerce';
		$this->nodeType = $this->getNodeType();
		$this->fields = $this->generateFields();
		$this->plugin = 'WooCommerce';
		$this->callback = 'triggerhappy_filter_hook';
		$this->hook = 'woocommerce_get_price_html';
	}

	/**
	 * @return NodeField[]
	 */
	public function generateFields() {
		return [
			new NodeField( 'price', 'html', [
				'dir' => 'start'
			] ),
			new NodeField( 'product', 'wc_product', [
				'dir' => 'start'
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
