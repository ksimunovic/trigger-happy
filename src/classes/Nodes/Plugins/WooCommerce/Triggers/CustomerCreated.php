<?php


namespace HotSource\TriggerHappy\Nodes\Plugins\WooCommerce\Triggers;

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
		$this->hook = 'user_register';
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
		add_action($this->hook, function ($user_id) use ($node, $data, $context) {
			$user = get_user_by('id', $user_id);

			if ( in_array( 'customer', (array) $user->roles ) ) {
				return $node->next($context);
			}
		},10, 1);
	}
}