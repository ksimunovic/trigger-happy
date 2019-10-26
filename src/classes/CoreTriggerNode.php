<?php

namespace HotSource\TriggerHappy;

use TH;

class CoreTriggerNode extends CoreNode {

	/**
	 * @var string
	 */
	protected $hook = '';

	/**
	 * @var array
	 */
	protected $globals = [];

	/**
	 * @var TH::Filter[]
	 */
	protected $nodeFilters = [];

	/**
	 * @var string
	 */
	protected $triggerType = '';

	/**
	 * @var string
	 */
	protected $callback = '';

	/**
	 * @return string
	 */
	public function getNodeType(): string {
		return 'trigger';
	}


	/**
	 * @param $node
	 * @param $context
	 * @param null $data
	 *
	 * @return null
	 */
	public function runCallback( $node, $context, $data = null ) {
		return;
	}

	/**
	 *
	 * @return NodeField[]
	 */
	public function generateFields() {
		return [];
	}

	/**
	 * Used in Frontend: FlowHooksController::get_available_nodes() and
	 * FlowPluginsController::get_available_plugins()
	 * @return array
	 */
	public function toArray() {
		$fieldsArray = [];
		foreach ( $this->fields as $field ) {
			$fieldsArray[] = $field->createFieldDefinition();
		}

		return [
			'name'        => $this->name,
			'plugin'      => $this->plugin,
			'description' => $this->description,
			'cat'         => $this->cat,
			'hook'        => $this->hook,
			'globals'     => $this->globals,
			'triggerType' => $this->triggerType,
			'nodeType'    => $this->nodeType,
			'fields'      => $fieldsArray,
			'filters'     => $this->filters,
			'nodeFilters' => $this->nodeFilters,
			'callback'    => $this->callback,
		];
	}

}