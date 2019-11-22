<?php

namespace HotSource\TriggerHappy\Nodes\Conditions;

use HotSource\TriggerHappy\Nodes\CoreConditionNode;
use TH;

class NotLoggedIn extends CoreConditionNode {

	/**
	 * NotLoggedIn constructor.
	 */
	public function __construct() {
		$this->name = 'If the user is NOT logged in';
		$this->cat = 'Users';
		$this->nodeType = $this->getNodeType();
		$this->callback = 'triggerhappy_condition';
		$this->nodeFilters = [
			[
				TH::Filter( TH::Expression( "_N.wp.is_user_logged_in" ), 'equals', false ),
			],
		];
	}

	/**
	 * @param $node
	 * @param $context
	 * @param null $data
	 *
	 * @return void|null
	 */
	public function runCallback( $node, $context, $data = null ) {
		$node->next( $context, [] );
	}
}
