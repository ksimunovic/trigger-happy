<?php

namespace HotSource\TriggerHappy\Nodes\Plugins\NinjaForms\Actions;

use HotSource\TriggerHappy\NodeField;
use HotSource\TriggerHappy\Nodes\CoreActionNode;
use HotSource\TriggerHappy\Nodes\CoreNode;

class DisplayForm extends CoreActionNode {

	/**
	 * DisplayForm constructor.
	 */
	public function __construct() {
		$this->name = 'Display Ninja Form';
		$this->description = 'Display a Ninja Form';
		$this->plugin = 'NinjaForms';
		$this->cat = 'Forms';
		$this->callback = 'triggerhappy_nf_display_form';
		$this->nodeType = $this->getNodeType();
		$this->fields = $this->generateFields();
	}

	/**
	 * @return NodeField[]
	 */
	public function generateFields() {
		return [
			new NodeField( 'form', 'nf_form_id',
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
		echo do_shortcode( "[ninja_form id=" . $data['form'] . "]" );
	}
}
