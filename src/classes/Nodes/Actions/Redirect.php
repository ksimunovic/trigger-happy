<?php

namespace HotSource\TriggerHappy\Nodes\Actions;

use HotSource\TriggerHappy\NodeField;
use HotSource\TriggerHappy\Nodes\CoreActionNode;

class Redirect extends CoreActionNode {

	/**
	 * Timer constructor.
	 */
	public function __construct() {
		$this->name = 'Redirect to URL';
		$this->cat = 'WordPress';
		$this->nodeType = $this->getNodeType();
		$this->fields = $this->generateFields();
		$this->plugin = 'wordpress';
		$this->callback = 'triggerhappy_action_hook';
	}

	/**
	 * @return NodeField[]
	 */
	public function generateFields() {
		return [
			new NodeField( 'url', 'string' ),
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
		if ( filter_var( $data['url'], FILTER_VALIDATE_URL ) !== false || filter_var( 'http://www.example.com' . $data['url'], FILTER_VALIDATE_URL ) !== false ) {
			wp_redirect( $data['url'] );
			exit;
		}
	}
}
