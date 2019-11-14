<?php

namespace HotSource\TriggerHappy\Nodes\Triggers;

use HotSource\TriggerHappy\Nodes\CoreNode;
use HotSource\TriggerHappy\Nodes\CoreTriggerNode;
use HotSource\TriggerHappy\NodeField;
use TH;

class CoreSinglePostViewed extends CoreTriggerNode {

	/**
	 * CoreSinglePostViewed constructor.
	 */
	public function __construct() {
		$this->name = 'When single post data is being queried';
		$this->description = 'When data for a Single Post is being loaded';
		$this->cat = 'Queries';
		$this->triggerType = 'query';
		$this->nodeType = $this->getNodeType();
		$this->fields = $this->generateFields();
		$this->hook = 'template_redirect';
		$this->callback = 'triggerhappy_action_hook';
		$this->globals = [ 'post' => 'post', 'query' => 'wp_query' ];

		$this->nodeFilters = [
			[
				TH::Filter( TH::Expression( "_N.wpPageFunctions.is_single" ), 'equals', true ),
			],
		];
	}

	/**
	 * @return NodeField[]
	 */
	public function generateFields() {
		return [
			new NodeField( 'query', 'wp_query', [ 'dir' => 'start' ] ),
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
