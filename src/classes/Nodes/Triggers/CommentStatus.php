<?php

namespace HotSource\TriggerHappy\Nodes\Triggers;

use HotSource\TriggerHappy\NodeField;
use HotSource\TriggerHappy\Nodes\CoreNode;
use HotSource\TriggerHappy\Nodes\CoreTriggerNode;

class CommentStatus extends CoreTriggerNode {

	/**
	 * CommentStatus constructor.
	 */
	public function __construct() {
		$this->name = 'When a Comment status is changed';
		$this->description = 'When a Comment status is changed, e.g. approve, hold';
		$this->cat = 'Comments';
		$this->nodeType = $this->getNodeType();
		$this->fields = $this->generateFields();
		$this->hook = 'wp_set_comment_status';
		$this->callback = 'triggerhappy_action_hook';
	}

	/**
	 * @return NodeField[]
	 */
	public function generateFields() {
		return [
			new NodeField( 'commentId', 'number', [
				'description' => 'The comment ID',
				'dir'         => 'start',
			] ),
			new NodeField( 'commentStatus', 'wp_comment', [
				'description' => 'The status of the comment',
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
