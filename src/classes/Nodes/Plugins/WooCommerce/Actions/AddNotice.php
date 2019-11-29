<?php

namespace HotSource\TriggerHappy\Nodes\Plugins\WooCommerce\Actions;

use HotSource\TriggerHappy\NodeField;
use HotSource\TriggerHappy\Nodes\CoreActionNode;
use HotSource\TriggerHappy\Nodes\CoreNode;

class AddNotice extends CoreActionNode {

	/**
	 * AddNotice constructor.
	 */
	public function __construct() {
		$this->name = 'Add WooCommerce Notice';
		$this->description = 'Add a WooCommerce notice message';
		$this->plugin = 'WooCommerce';
		$this->cat = 'WooCommerce';
		$this->callback = 'triggerhappy_function_call';
		$this->nodeType = $this->getNodeType();
		$this->fields = $this->generateFields();
	}

	/**
	 * @return NodeField[]
	 */
	public function generateFields() {
		return [
			new NodeField( 'notice_type', 'wc_notice_type', [
				'label'       => 'Notice Type',
				'description' => 'The type of notice',
			] ),
			new NodeField( 'notice', 'html', [
				'label'       => 'Content',
				'description' => 'The message to be displayed',
			] ),
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
		wc_add_notice( $data['notice'], $data['notice_type'] );
	}
}
