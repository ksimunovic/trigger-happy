<?php

namespace HotSource\TriggerHappy\Nodes\Actions;

use HotSource\TriggerHappy\Nodes\CoreActionNode;

class Logout extends CoreActionNode {

	/**
	 * Logout constructor.
	 */
	public function __construct() {
		$this->name = 'Logout the current user';
		$this->description = 'Forces the user to log ou';
		$this->cat = 'WordPress';
		$this->nodeType = $this->getNodeType();
		$this->callback = 'triggerhappy_wp_logout';
	}

	/**
	 * @param $node
	 * @param $context
	 * @param null $data
	 *
	 * @return void|null
	 */
	public function runCallback( $node, $context, $data = null ) {
		if ( is_user_logged_in() ) {
			wp_logout();
		}
		$node->next( $context, [] );
	}
}