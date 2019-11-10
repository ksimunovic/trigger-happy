<?php

namespace HotSource\TriggerHappy\Nodes;

use HotSource\TriggerHappy\CoreNode;
use HotSource\TriggerHappy\CoreTriggerNode;
use HotSource\TriggerHappy\NodeField;

class UserUpdated extends CoreTriggerNode {

	/**
	 * UserUpdated constructor.
	 */
	public function __construct() {

		$this->name = 'When a User profile is updated';
		$this->description = 'When a user profile is saved';
		$this->cat = 'Users';
		$this->nodeType = $this->getNodeType();
		$this->fields = $this->generateFields();
		$this->hook = 'profile_update';
		$this->callback = 'triggerhappy_action_hook';
	}

	/**
	 * @return NodeField[]
	 */
	public function generateFields() {
		return [
			new NodeField( 'id', 'number', [ 'description' => 'The user ID', 'dir' => 'start' ] ),
			new NodeField( 'user', 'wp_user', [ 'description' => 'The user data', 'dir' => 'start' ] ),
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
