<?php


namespace HotSource\TriggerHappy\Nodes\Triggers;


use HotSource\TriggerHappy\NodeField;
use HotSource\TriggerHappy\Nodes\CoreNode;
use HotSource\TriggerHappy\Nodes\CoreTriggerNode;
use TH;

class TaxonomyQuery extends CoreTriggerNode {

	/**
	 * TaxonomyQuery constructor.
	 */
	public function __construct() {
		$this->name = 'When Taxonomy Archive data is loaded';
		$this->description = 'When loading data for a Taxonomy Archive';
		$this->cat = 'Queries';
		$this->triggerType = 'query';
		$this->nodeType = $this->getNodeType();
		$this->fields = $this->generateFields();
		$this->hook = 'pre_get_posts';
		$this->callback = 'triggerhappy_action_hook';
		$this->globals = [ 'query' => 'wp_query' ];

		$this->nodeFilters = [
			[
				TH::Filter( TH::Expression( "_N1.query.is_tax" ), 'equals', true ),
			],
		];
		$this->filters = [
			[
				TH::Filter( TH::Expression( "_self.query.is_main_query" ), 'equals', true ),
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