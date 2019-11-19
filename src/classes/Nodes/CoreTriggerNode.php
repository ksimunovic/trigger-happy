<?php

namespace HotSource\TriggerHappy\Nodes;

use HotSource\TriggerHappy\NodeField;
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
	 *
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
	 * @param CoreNode $node
	 * @param \TriggerHappyContext $context
	 *
	 * @return void|null
	 */
	protected function actionHook( $node, $context ) {
		$hook = isset( $node->hook ) ? $node->hook : $node->id;

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

			$passed_args = func_get_args();
			$args = $this->mergeArguments( $passed_args );

			$node->setData( $context, $args );

			if ( ! $node->canExecute( $context ) ) {
				return;
			}

			return $node->next( $context, $args );
		}, $priority, 9999
		);
	}

	/**
	 * @param $node CoreNode
	 * @param $context \TriggerHappyContext
	 */
	protected function filterHook( $node, $context ) {
		$filter = isset( $node->hook ) ? $node->hook : $node->id;
		$priority = 10;
		if ( isset( $node->priority ) ) {
			$priority = $node->priority;
		}

		add_filter(
			$filter, function () use ( $filter, &$node, $context ) {

			$passed_args = func_get_args();
			$args = $this->mergeArguments( $passed_args );

			$node->setData( $context, $args );
			if ( ! $node->canExecute( $context ) ) {
				return reset( $passed_args );
			}

			$inputData = $node->getInputData( $context );

			$returnData = $node->next( $context, $args );

			if ( $node->hasReturnData( $context ) ) {
				return $node->getReturnData( $context );
			}

			$inFields = array_filter( $node->fields, function ( $arr_value ) {
				return $arr_value->options['dir'] == 'start';
			} );

			$first = reset( $inFields );
			if ( isset( $inputData[ $first->name ] ) ) {
				return $inputData[ $first->name ];
			}

			return $passed_args[0];
		}, $priority, 9999
		);
	}

	protected function mergeArguments( $passedArgs ) {
		$args = [];

		// Remove empty string value func_get_args returns
		$passedArgs = ! empty( $passedArgs[0] ) ? $passedArgs : [];

		if(!empty($passedArgs)){
			$index = - 1;
			foreach ( $this->fields as $fieldName => $field ) {
				$index ++;

				/** @var NodeField $field */
				$fieldDef = $field->createFieldDefinition();

				if ( isset( $passedArgs[ $index ] ) ) {
					$value = $passedArgs[ $index ];
				}

				if ( is_numeric( $value ) && $fieldDef['type'] !== 'number' ) {
					if ( has_filter( 'triggerhappy_resolve_' . $fieldDef['type'] . '_from_number' ) ) {
						$value = apply_filters( 'triggerhappy_resolve_' . $fieldDef['type'] . '_from_number', $value );
					}
				}

				$args[ $fieldName ] = $value;
			}
		}

		if ( isset( $this->globals ) ) {
			foreach ( $this->globals as $id => $key ) {
				$args[ $id ] = $GLOBALS[ $key ];
			}
		}

		return $args;
	}
}
