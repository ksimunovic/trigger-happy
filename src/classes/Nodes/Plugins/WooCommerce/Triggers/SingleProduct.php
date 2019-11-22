<?php


namespace HotSource\TriggerHappy\Nodes\Plugins\WooCommerce\Triggers;


use HotSource\TriggerHappy\NodeField;
use HotSource\TriggerHappy\Nodes\CoreNode;
use HotSource\TriggerHappy\Nodes\CoreTriggerNode;
use TH;

class SingleProduct extends CoreTriggerNode {

	/**
	 * SingleProduct constructor.
	 */
	public function __construct() {
		$this->name = 'When a Single Product is viewed';
		$this->description = 'When a single product is being viewed on the front-end';
		$this->cat = 'WooCommerce';
		$this->nodeType = $this->getNodeType();
		$this->fields = $this->generateFields();
		$this->plugin = 'WooCommerce';
		$this->triggerType = 'product_render';
		$this->callback = 'triggerhappy_action_hook';
		$this->hook = 'template_redirect';
		$this->globals = [ 'post' => 'post' ];
		$this->nodeFilters = [
			[
				TH::Filter( TH::Expression( "_N.wpPageFunctions.is_single" ), 'equals', true ),
				TH::Filter( TH::Expression( "_N.wpPageFunctions.get_post_type" ), 'equals', 'product' ),
			],
		];
	}

	/**
	 * @return NodeField[]
	 */
	public function generateFields() {
		return [
			new NodeField( 'post', 'wp_post', [ 'dir' => 'start' ] ),
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
