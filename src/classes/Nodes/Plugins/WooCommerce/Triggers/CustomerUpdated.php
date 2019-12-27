<?php


namespace HotSource\TriggerHappy\Nodes\Plugins\WooCommerce\Triggers;


use HotSource\TriggerHappy\NodeField;
use HotSource\TriggerHappy\Nodes\CoreNode;
use HotSource\TriggerHappy\Nodes\CoreTriggerNode;

class CustomerUpdated extends CoreTriggerNode {
	public function __construct() {
		$this->name = 'When a Customer is updated';
		$this->description = 'When a new Customer is updated';
		$this->cat = 'Customers';
		$this->nodeType = $this->getNodeType();
		$this->fields = $this->generateFields();
		$this->hook = 'woocommerce_update_customer_args';
		$this->callback = 'triggerhappy_action_hook';
	}

	/**
	 * @return NodeField[]
	 */
	public function generateFields() {
		return [
			new NodeField( 'ID', 'number',
				[ 'description' => 'The customer ID', 'dir' => 'start' ]
			),
			new NodeField( 'post', 'wp_post',
				[ 'description' => 'The updated customer data', 'dir' => 'start' ]
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