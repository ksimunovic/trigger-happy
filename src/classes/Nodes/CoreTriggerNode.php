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

			$args = [];
			$passed_args = func_get_args();

			// Remove empty string value func_get_args returns
			$passed_args = ! empty( $passed_args[0] ) ? $passed_args : [];

			if ( ! empty( $node->def ) ) {
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
			$start_fields = array_filter( $node->fields, function ( $arr_value ) {
				return $arr_value->options['dir'] == 'start';
			} );
			$in_fields = array_filter( $node->fields, function ( $arr_value ) {
				return $arr_value->options['dir'] == 'in';
			} );
			$args = [];
			$start_fields = array_values( $start_fields );
			foreach ( $passed_args as $i => $val ) {
				if ( isset( $start_fields[ $i ] ) ) {
					$key = $start_fields[ $i ]->name;
					$args[ $key ] = $val;
				}
			}

			if ( isset( $node->globals ) ) {
				foreach ( $node->globals as $id => $key ) {
					$args[ $id ] = $GLOBALS[ $key ];
				}
			}

			$node->setData( $context, $args );
			if ( ! $node->canExecute( $context ) ) {
				return reset( $passed_args );
			}

			$inputData = $node->getInputData( $context );

			$returnData = $node->next( $context, $args );

			if ( $node->hasReturnData( $context ) ) {

				return $node->getReturnData( $context );
			}
			$first = reset( $in_fields );
			if ( isset( $inputData[ $first['name'] ] ) ) {
				return $inputData[ $first['name'] ];
			}

			return $passed_args[0];
		}, $priority, 9999
		);
	}
}
