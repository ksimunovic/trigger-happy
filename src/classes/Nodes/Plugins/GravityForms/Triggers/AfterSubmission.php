<?php

namespace HotSource\TriggerHappy\Nodes\Plugins\GravityForms\Triggers;

use HotSource\TriggerHappy\NodeField;
use HotSource\TriggerHappy\Nodes\CoreNode;
use HotSource\TriggerHappy\Nodes\CoreTriggerNode;

class AfterSubmission extends CoreTriggerNode {

	/**
	 * AfterSubmission constructor
	 */
	public function __construct() {
		$this->name = 'Gravity Form Submitted';
		$this->description = 'When a Gravity Form is submitted';
		$this->cat = 'Forms';
		$this->nodeType = $this->getNodeType();
		$this->fields = $this->generateFields();
		$this->plugin = 'GravityForms';
		$this->hook = 'gform_after_submission';
		$this->callback = 'triggerhappy_action_hook';
	}

	/**
	 * @return NodeField[]
	 */
	public function generateFields() {
		return [
			new NodeField( 'form_data', 'array',
				[ 'dir' => 'start', ]
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
