<?php

namespace HotSource\TriggerHappy\Nodes\Plugins\WooCommerce\Triggers;

use HotSource\TriggerHappy\NodeField;
use HotSource\TriggerHappy\Nodes\CoreNode;
use HotSource\TriggerHappy\Nodes\CoreTriggerNode;
use TH;

class ProductCreated extends CoreTriggerNode {
	public function __construct() {
		$this->name = 'When a Product is created';
		$this->description = 'When a Product is created';
		$this->cat = 'Products';
		$this->nodeType = $this->getNodeType();
		$this->fields = $this->generateFields();
		$this->hook = 'save_post_product';
		$this->callback = 'triggerhappy_action_hook';
		$this->nodeFilters = [
			[
				TH::Filter( TH::Expression( "_N1.post.post_date" ), 'equals', TH::Expression( "_N1.post.post_modified" ) ),
				TH::Filter( TH::Expression( "_N1.post.post_status" ), 'equals', 'publish' )
			],
		];
	}

	/**
	 * @return NodeField[]
	 */
	public function generateFields() {
		return [
			new NodeField( 'ID', 'number',
				[ 'description' => 'The product ID', 'dir' => 'start' ]
			),
			new NodeField( 'post', 'wp_post',
				[ 'description' => 'The added product', 'dir' => 'start' ]
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
		$this->actionHook( $node, $context );
	}
}
