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

	/**
	 * @param \TriggerHappyNode $node
	 * @param \TriggerHappyContext $context
	 *
	 * @return void|null
	 */
	protected function actionHook( $node, $context ) {
		if(!empty($node->def->hook)){ // TEMP
			$hook = isset( $node->def->hook ) ? $node->def->hook : $node->id;
		} else {
			$hook = isset( $node->hook ) ? $node->hook : $node->id;
		}

		$priority = 10;
		if ( strpos( $hook, '$' ) === 0 ) {
			$hookField = substr( $hook, 1 );
			$inputData = $node->getInputData( $context );
			$hook = $inputData[ $hookField ];
			$hook_parts = explode( ':', $hook );
			$hook = $hook_parts[0];
			if ( count( $hook_parts ) > 1 ) {
				$priority = $hook_parts[1];
			}
		}
		add_action(
			$hook, function () use ( $hook, $node, $context ) {

			$args = [];
			$passed_args = func_get_args();

			// Remove empty string value func_get_args returns
			$passed_args = ! empty( $passed_args[0] ) ? $passed_args : [];

			if(!empty($node->def)){
				$nodeDef = $node->def;
			} else {
				$nodeDef = $node;
			}

			if ( isset( $nodeDef->globals ) ) {
				foreach ( $nodeDef->globals as $id => $key ) {
					$args[ $id ] = $GLOBALS[ $key ];
				}
			}

			$i = 0;
			foreach ( $nodeDef->fields as $i => $field ) {
				/** @var NodeField $field */
				$field = $field->createFieldDefinition();
				if ( $field['dir'] !== 'start' || isset( $args[ $field['name'] ] ) ) {
					continue;
				}

				if ( isset( $passed_args[ $i ] ) ) {
					$key = $field['name'];
					$val = $passed_args[ $i ];
					if ( is_numeric( $val ) && $field['type'] !== 'number' ) {
						if ( has_filter( 'triggerhappy_resolve_' . $field['type'] . '_from_number' ) ) {
							$val = apply_filters( 'triggerhappy_resolve_' . $field['type'] . '_from_number', $val );
						}
					}
					$args[ $key ] = $val;
				}
				$i ++;
			}
			// XXXX:
			$node->setData( $context, $args );

			if ( ! $node->canExecute( $context ) ) {
				return;
			}

			return $node->next( $context, $args );
		}, $priority, 9999
		);
	}
}