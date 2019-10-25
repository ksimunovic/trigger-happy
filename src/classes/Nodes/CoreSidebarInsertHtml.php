<?php

namespace HotSource\TriggerHappy\Nodes;

use HotSource\TriggerHappy\CoreActionNode;
use HotSource\TriggerHappy\NodeField;

class CoreSidebarInsertHtml extends CoreActionNode {

	public function __construct() {
		$this->name = 'Insert content into sidebar';
		$this->description = 'Insert HTML into before or after the sidebar';
		$this->plugin = '';
		$this->cat = 'Sidebar';
		$this->actionType = 'render';
		$this->nodeType = $this->getNodeType();
		$this->fields = $this->generateFields();
		$this->filters = [];
	}

	/**
	 * @return NodeField[]
	 */
	public function generateFields() {
		return [
			new NodeField( 'position', 'string', [
				'label'       => 'Position',
				'description' => 'Where to add the content',
				'dir'         => 'in',
				'choices'     => triggerhappy_assoc_to_choices( [
					'dynamic_sidebar_before' => 'Before the sidebar has rendered',
					'dynamic_sidebar_after'  => 'After the sidebar has rendered',
				] ),
			] ),
			new NodeField( 'html', 'html', [
				'label'       => 'HTML',
				'description' => 'The HTML to be inserted',
				'dir'         => 'in',
			] ),
		];
	}

	/**
	 * @param \TriggerHappyNode $node
	 * @param \TriggerHappyContext $context
	 * @param null $data
	 *
	 * @return void|null
	 */
	public function runCallback( $node, $context, $data = null ) {
		if ( ! empty( $data['position'] ) ) {
			add_action( $data['position'], function () use ( $data ) {
				echo $data['html'];
			} );
		}
		$node->next( $context, [] );
	}
}
