<?php

namespace HotSource\TriggerHappy\Nodes\Plugins\WooCommerce\Actions;

use HotSource\TriggerHappy\NodeField;
use HotSource\TriggerHappy\Nodes\CoreActionNode;
use HotSource\TriggerHappy\Nodes\CoreNode;

class AddFee extends CoreActionNode {

	/**
	 * AddFee constructor.
	 */
	public function __construct() {
		$this->name = 'Add Fee';
		$this->description = 'Add a new fee to the order';
		$this->plugin = 'WooCommerce';
		$this->cat = 'WooCommerce';
		$this->callback = 'triggerhappy_wc_add_fee';
		$this->nodeType = $this->getNodeType();
		$this->fields = $this->generateFields();
	}

	/**
	 * @return NodeField[]
	 */
	public function generateFields() {
		return [
			new NodeField(
				'fee', 'number', [
					'label'       => 'Fee',
					'description' => 'Enter the fee amount',
				]
			),
			new NodeField(
				'description', 'string', [
					'label'       => 'Description',
					'description' => 'The description of the fee - will be shown on the order',
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
		WC()->cart->add_fee( $data['description'], $data['fee'], true, '' );
	}
}
