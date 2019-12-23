<?php


namespace HotSource\TriggerHappy\Nodes\Plugins\WooCommerce\Triggers;


use HotSource\TriggerHappy\NodeField;
use HotSource\TriggerHappy\Nodes\CoreNode;
use HotSource\TriggerHappy\Nodes\CoreTriggerNode;

class ProductPriceChanged extends CoreTriggerNode {
	public function __construct() {
		$this->name        = 'When a Product price is changed';
		$this->description = 'When a Product price is changed';
		$this->cat         = 'Products';
		$this->nodeType    = $this->getNodeType();
		$this->fields      = $this->generateFields();
		$this->callback    = 'triggerhappy_action_hook';
	}

	/**
	 * @return NodeField[]
	 */
	public function generateFields() {
		return [
			new NodeField( 'ID', 'number',
				[ 'description' => 'The product ID', 'dir' => 'start' ]
			)
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
		$data['hook'] = 'woocommerce_product_object_updated_props';

		add_action( $data['hook'], function ( $product, $updated_props ) use ( $node, $data, $context ) {
			if ( ( in_array( 'regular_price', $updated_props, true ) ||
			       in_array( 'sale_price', $updated_props, true ) ) ) {
				return $node->next( $context );
			}
		}, 10, 2 );
	}
}