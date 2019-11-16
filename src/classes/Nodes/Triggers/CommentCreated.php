<?php

namespace HotSource\TriggerHappy\Nodes\Triggers;

use HotSource\TriggerHappy\NodeField;
use HotSource\TriggerHappy\Nodes\CoreNode;
use HotSource\TriggerHappy\Nodes\CoreTriggerNode;

class CommentCreated extends CoreTriggerNode {

	/**
	 * CommentCreated constructor.
	 */
	public function __construct() {
		$this->name = 'When a Comment is created';
		$this->description = 'When a comment is created and saved';
		$this->cat = 'Comments';
		$this->nodeType = $this->getNodeType();
		$this->fields = $this->generateFields();
		$this->hook = 'wp_insert_comment';
		$this->callback = 'triggerhappy_action_hook';
		$this->globals = [ 'query' => 'wp_query' ];
	}

	/**
	 * @return NodeField[]
	 */
	public function generateFields() {
		return [
			new NodeField( 'commentId',
				'number',
				[ 'description' => 'The comment ID', 'dir' => 'start' ]
			),
			new NodeField( 'comment', 'wp_comment', [
				'description' => 'The added comment',
				'dir'         => 'start',
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
		$this->actionHook( $node, $context );
	}
}
