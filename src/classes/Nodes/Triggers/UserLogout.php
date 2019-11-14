<?php

namespace HotSource\TriggerHappy\Nodes\Triggers;

use HotSource\TriggerHappy\CoreNode;
use HotSource\TriggerHappy\Nodes\CoreTriggerNode;

class UserLogout extends CoreTriggerNode {

	/**
	 * UserLogout constructor.
	 */
	public function __construct() {
		$this->name = 'User Logged Out';
		$this->description = 'When a user has logged out';
		$this->cat = 'Users';
		$this->plugin = 'WordPress';
		$this->triggerType = 'query';
		$this->nodeType = $this->getNodeType();
		$this->hook = 'wp_logout';
		$this->callback = 'triggerhappy_action_hook';
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
