<?php

namespace HotSource\TriggerHappy\Nodes\Triggers;

use HotSource\TriggerHappy\NodeField;
use HotSource\TriggerHappy\Nodes\CoreNode;
use HotSource\TriggerHappy\Nodes\CoreTriggerNode;
use TH;

class PostSaved extends CoreTriggerNode {

	/**
	 * Timer PostSaved.
	 */
	public function __construct() {
		$this->name = 'When a Post is saved';
		$this->description = 'When a post is created or updated';
		$this->cat = 'Posts';
		$this->nodeType = $this->getNodeType();
		$this->fields = $this->generateFields();
		$this->hook = 'save_post';
		$this->callback = 'triggerhappy_action_hook';
		$this->nodeFilters = [
			[
				TH::Filter( TH::Expression( "_N1.post.post_content" ), 'notequals', '' ),
			],
			[
				TH::Filter( TH::Expression( "_N1.post.post_title" ), 'notequals', '' ),
				TH::Filter( TH::Expression( "_N1.post.post_title" ), 'notequals', 'Auto Draft' ),
			],
		];
	}

	/**
	 * @return NodeField[]
	 */
	public function generateFields() {
		return [
			new NodeField( 'ID', 'number',
				[ 'description' => 'The comment ID', 'dir' => 'start' ]
			),
			new NodeField( 'post', 'wp_post',
				[ 'description' => 'The added comment', 'dir' => 'start' ]
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
		$this->filterHook( $node, $context );
	}
}
