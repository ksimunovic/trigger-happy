<?php


namespace HotSource\TriggerHappy\Nodes\Triggers;


use HotSource\TriggerHappy\NodeField;
use HotSource\TriggerHappy\Nodes\CoreNode;
use HotSource\TriggerHappy\Nodes\CoreTriggerNode;
use TH;

class ProductUpdated extends CoreTriggerNode {
	public function __construct() {
		$this->name = 'When a Product is updated';
		$this->description = 'When a Product is updated';
		$this->cat = 'Products';
		$this->nodeType = $this->getNodeType();
		$this->fields = $this->generateFields();
		$this->hook = 'save_post_product';
		$this->callback = 'triggerhappy_action_hook';
		$this->nodeFilters = [
			[
				TH::Filter( TH::Expression( "_N1.post.post_date" ), 'notequals', TH::Expression( "_N1.post.post_modified" ) )
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
				[ 'description' => 'The updated product', 'dir' => 'start' ]
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
		$this->filterHook( $node, $context );
	}
}