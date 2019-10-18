<?php

namespace HotSource\TriggerHappy;

require_once( dirname( __FILE__ ) . '/../classes/CoreNode.php' );

class CoreActionNode extends CoreNode {

	public function getNodeType(): string {
		return "action";
	}

	/**
	 * @param $node
	 * @param $context
	 * @param null $data
	 *
	 * @return null
	 */
	public function runCallback( $node, $context, $data = null ) {
		return;
	}

	/**
	 *
	 * @return NodeField[]
	 */
	public function generateFields() {
		return [];
	}

	/**
	 *
	 * @return array
	 */
	public function toArray() {
		return [];
	}
}
