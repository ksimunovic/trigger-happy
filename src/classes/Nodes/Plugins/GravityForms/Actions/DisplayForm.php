<?php

namespace HotSource\TriggerHappy\Nodes\Plugins\GravityForms\Actions;

use HotSource\TriggerHappy\NodeField;
use HotSource\TriggerHappy\Nodes\CoreActionNode;
use HotSource\TriggerHappy\Nodes\CoreNode;

class DisplayForm extends CoreActionNode {

	/**
	 * DisplayForm constructor.
	 */
	public function __construct() {
		$this->name = 'Display Gravity Form';
		$this->description = 'Display a Gravity Form';
		$this->plugin = 'GravityForms';
		$this->cat = 'Forms';
		$this->nodeType = $this->getNodeType();
		$this->fields = $this->generateFields();
	}

	/**
	 * @return NodeField[]
	 */
	public function generateFields() {
		return [
			new NodeField( 'form', 'gf_form_id',
				[ 'dir' => 'in', ]
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
		echo do_shortcode( "[gravityform id=" . $data['form'] . "]" );
	}
}
