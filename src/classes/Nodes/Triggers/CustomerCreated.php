<?php


namespace HotSource\TriggerHappy\Nodes\Triggers;


use HotSource\TriggerHappy\NodeField;
use HotSource\TriggerHappy\Nodes\CoreNode;
use HotSource\TriggerHappy\Nodes\CoreTriggerNode;

class CustomerCreated extends CoreTriggerNode {
	public function __construct() {
		$this->name = 'When a Customer is created';
		$this->description = 'When a new Customer is created';
		$this->cat = 'Customers';
		$this->nodeType = $this->getNodeType();
		$this->fields = $this->generateFields();
		$this->hook = 'woocommerce_new_customer_data';
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
				[ 'description' => 'The created customer data', 'dir' => 'start' ]
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