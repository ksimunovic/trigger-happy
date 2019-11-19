<?php


namespace HotSource\TriggerHappy\Nodes\Triggers;


use HotSource\TriggerHappy\NodeField;
use HotSource\TriggerHappy\Nodes\CoreNode;
use HotSource\TriggerHappy\Nodes\CoreTriggerNode;

class PostPublished extends CoreTriggerNode {

	/**
	 * PostPublished constructor.
	 */
	public function __construct() {
		$this->name = 'When a Post is published';
		$this->description = 'When a post is saved as Published';
		$this->cat = 'Posts';
		$this->nodeType = $this->getNodeType();
		$this->fields = $this->generateFields();
		$this->hook = 'publish_post';
		$this->callback = 'triggerhappy_action_hook';
	}

	/**
	 * @return NodeField[]
	 */
	public function generateFields() {
		return [
			new NodeField( 'id', 'number',
				[ 'description' => 'The post ID', 'dir' => 'start' ]
			),
			new NodeField( 'post', 'wp_post',
				[ 'description' => 'The saved post', 'dir' => 'start' ]
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
