<?php

namespace HotSource\TriggerHappy\Nodes;

use HotSource\TriggerHappy\NodeField;

class CoreActionNode extends CoreNode {

	/**
	 * @var string
	 */
	protected $actionType = '';

	public function getNodeType(): string {
		return 'action';
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
			'helpText'    => $this->helpText,
			'cat'         => $this->cat,
			'actionType'  => $this->actionType,
			'nodeType'    => $this->nodeType,
			'fields'      => $fieldsArray,
		];
	}
}
