<?php

namespace HotSource\TriggerHappy\Nodes;

use HotSource\TriggerHappy\CoreActionNode;
use HotSource\TriggerHappy\NodeField;

class CorePostInsertHtml extends CoreActionNode {

	public function __construct() {
		$this->name = 'Insert content into post';
		$this->description = 'Insert HTML into the body of a post';
		$this->plugin = '';
		$this->cat = 'Posts';
		$this->actionType = 'render';
		$this->nodeType = $this->getNodeType();
		$this->fields = $this->generateFields();
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
					'before_content' => 'Before the post content',
					'after_content'  => 'After the post content',
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
		$position = $data['position'];
		$hook = 'the_content';
		if ( $position == 'before_title' || $position == 'after_title' ) {
			$hook = 'the_title';
		}
		add_filter( $hook, function ( $existing ) use ( $position, $data ) {
			if ( $position == 'before_content' ) {
				return $data['html'] . $existing;
			}

			return $existing . $data['html'];
		} );
		$node->next( $context, [] );
	}
}
