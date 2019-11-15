<?php

namespace HotSource\TriggerHappy\Nodes\Triggers;

use HotSource\TriggerHappy\Nodes\CoreNode;
use HotSource\TriggerHappy\Nodes\CoreTriggerNode;
use HotSource\TriggerHappy\NodeField;

class UserLogin extends CoreTriggerNode {

	/**
	 * UserLogin constructor.
	 */
	public function __construct() {
		$this->name = 'User Logged In';
		$this->description = 'When a user has successfully logged in';
		$this->cat = 'Users';
		$this->triggerType = 'query';
		$this->nodeType = $this->getNodeType();
		$this->fields = $this->generateFields();
		$this->hook = 'wp_login';
		$this->callback = 'triggerhappy_action_hook';
	}

	/**
	 * @return NodeField[]
	 */
	public function generateFields() {
		return [
			new NodeField(
				'user_login', 'string', [
					'dir' => 'start',
				]
			),
			new NodeField(
				'user', 'wp_user', [
					'dir' => 'start',
				]
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
