<?php

namespace HotSource\TriggerHappy\Nodes\Triggers;

use HotSource\TriggerHappy\NodeField;
use HotSource\TriggerHappy\Nodes\CoreNode;
use HotSource\TriggerHappy\Nodes\CoreTriggerNode;

class UserRegister extends CoreTriggerNode {

	/**
	 * UserRegister constructor.
	 */
	public function __construct() {
		$this->name = 'When a User profile is created';
		$this->description = 'When a User Profile is created/registered';
		$this->cat = 'Users';
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
			new NodeField( 'id', 'number',
				[ 'description' => 'The user ID', 'dir' => 'start' ]
			),
			new NodeField( 'user', 'wp_user',
				[ 'description' => 'The user data', 'dir' => 'start' ]
			),
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
