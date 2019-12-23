<?php


namespace HotSource\TriggerHappy\Nodes\Plugins\WooCommerce\Triggers;

use HotSource\TriggerHappy\NodeField;
use HotSource\TriggerHappy\Nodes\CoreNode;
use HotSource\TriggerHappy\Nodes\CoreTriggerNode;

class ProductStockUpdated extends CoreTriggerNode {
	public function __construct() {
		$this->name        = 'When a Product stock is updated';
		$this->description = 'When a Product stock level is changed';
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
			new NodeField( 'stock_level_limit', 'number', [
				'label'       => 'Stock level limit',
				'description' => 'When product\'s stock level falls bellow stock level limit action will be triggered. '
			] )
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
		$data['hook'] = 'woocommerce_updated_product_stock';

		add_action( $data['hook'], function ( $product_id ) use ( $node, $data, $context ) {
			$product        = wc_get_product( $product_id );
			$stock_quantity = $product->get_stock_quantity();

			if ( $stock_quantity < $data['stock_level_limit'] ) {
				return $node->next( $context );
			}
		}, 10, 1 );
	}
}