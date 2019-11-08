<?php

namespace HotSource\TriggerHappy;

use TH;
use TriggerHappyContext;
use TriggerHappyFlow;

abstract class CoreNode {

	/**
	 * @var int
	 */
	public $id;

	/**
	 * @var array
	 */
	public $next = [];

	/**
	 * @var string
	 */
	public $type;

	/**
	 * @var \TriggerHappyFlow
	 */
	public $graph;

	/**
	 * @var boolean
	 */
	protected $isExecuting;

	/**
	 * @var array
	 */
	protected $inputData;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $description;

	/**
	 * @var string
	 */
	protected $plugin;

	/**
	 * @var string
	 */
	protected $cat;

	/**
	 * @var string
	 */
	protected $nodeType;

	/**
	 * @var NodeField[]
	 */
	protected $fields;

	/**
	 * @var TH::Filter[]
	 */
	protected $filters;

	/**
	 * @return string
	 */
	abstract public function getNodeType(): string;

	/**
	 *
	 * @return array
	 */
	abstract public function toArray();

	public function addNodeToFields() {
		$this->fields = $this->getFieldsWithNode( $this );
	}

	/**
	 * @param CoreNode $node
	 *
	 * @return NodeField[]
	 */

	public function getFieldsWithNode( $node ): array {
		$fields = [];
		foreach ( $this->generateFields() as $field ) {
			$field->setNode( $node );
			$fields[ $field->getName() ] = $field;
		}

		return $fields;
	}

	/**
	 *
	 * @return NodeField[]
	 */
	abstract public function generateFields();

	public function findField( $prop ) {
		foreach ( $this->generateFields() as $key => $field ) {
			$fieldDef = $field->createFieldDefinition();
			if ( isset( $fieldDef['name'] ) && $prop == $fieldDef['name'] ) {
				return $fieldDef;
			}
		}
	}

	public function setFilters( $filters ) {
		$this->filters = $filters;
	}

	/****************************/

	public function getFieldDef( $fieldId ) {
		if ( isset( $this->def['fields'] ) ) {
			foreach ( $this->def['fields'] as $i => $fld ) {
				if ( $fld['name'] == $fieldId ) {
					return $fld;
				}
			}
		}

		return null;

	}

	public function getField( $fieldId ) {
		foreach ( $this->fields as $field ) {
			if ( $field->getName() === $fieldId ) {
				return $field;
			}
		}

		return false;
	}

	public function getReturnField( $fieldId ) {

		if ( isset( $this->returnFields[ $fieldId ] ) ) {
			return $this->returnFields[ $fieldId ];

		}

		return false;
	}

	public function hasReturnData( $context ) {
		return isset( $context->returnData );
	}

	public function getNext() {
		return $this->next;
	}

	public function setNext( $nextIds ) {
		$this->next = $nextIds;
	}

	public function getFieldsByType( $type ) {
		$fields = [];

		foreach ( $this->fields as $p => $field ) {
			if ( $field->type == $type ) {
				array_push( $fields, $field );
			}
		}

		return $fields;
	}

	public function addField( $fieldId, $type ) {
		return $this->fields[ $fieldId ] = new TriggerHappyField( $fieldId, $type, $this );
	}

	public function addReturnField( $fieldId, $type ) {
		return $this->returnFields[ $fieldId ] = new TriggerHappyField( $fieldId, $type, $this );
	}

	public function execute( $context ) {
		if ( $this->isExecuting ) {
			return;
		}

		// Adding inputData to flowNode so context can be removed from getInputData
		$this->inputData = $this->getInputData( new TriggerHappyContext() );

		if ( ! empty( $this->childGraphs ) ) {
			$child_graphs = json_decode( $this->childGraphs );
			foreach ( $child_graphs as $i => $graph ) {
				$childFlow = new TriggerHappyFlow( '', $graph, false );
				$newContext = new TriggerHappyContext();
				$context->setData( $this->id, $this->inputData );
				$newContext->parentContext = $context;
				$newContext->parentNodeId = $this->id;
				$childFlow->parentFlow = $this->graph;
				$childFlow->start( $newContext );
			}

			$this->next( $context );
		} else {
			$this->runCallback( $this, $context, $this->inputData );
		}

		$this->isExecuting = false;
	}

	public function getInputData( $context ) {
		$data = [];
		if ( ! empty( $this->fields ) ) {
			foreach ( $this->fields as $fieldId => $field ) {
				$data[ $field->getName() ] = $field->resolveExpression( $context );
			}
		}

		return $data;
	}

	public function next( $context, $data = null ) {
		if ( $data !== null ) {
			$context->setData( $this->id, $data );
		}

		$result = null;
		if ( ! $this->canExecute( $context ) ) {
			return false;
		}

		foreach ( $this->next as $i => $id ) {
			$result = $this->graph->getNode( $id )->execute( $context );
		}

		return $this->getReturnData( $context );
	}

	public function canExecute( $context ) {
		$success = true;
		if ( ! empty( $this->filters ) ) {
			$success = $success && $this->applyFilters( $context, $this->filters );
		}

		if ( ! empty( $this->nodeFilters ) ) {
			$success = $success && $this->applyFilters( $context, json_decode( json_encode( $this->nodeFilters ) ) );
		}

		return $success;
	}

	protected function applyFilters( $context, $filters ) {
		foreach ( $filters as $orGroup ) {
			$success = true;
			foreach ( $orGroup as $andFilter ) {

				$left = $andFilter->left;
				if ( is_object( $left ) && isset( $left->expr ) ) {
					$left = $left->expr;
				}
				$right = $andFilter->right->expr;
				if ( is_object( $right ) && isset( $right->expr ) ) {
					$right = $right->expr;
				}
				$op = $andFilter->op;

				$resolvedLeft = ( $this->graph->resolveExpression( $left, $context ) );

				$resolvedRight = ( $this->graph->resolveExpression( $right, $context ) );
				switch ( $op ) {
					case "notnull":
						$success &= ! empty( $resolvedLeft ) && $resolvedLeft != null;
						break;
					case "equals":

						if ( is_numeric( $resolvedRight ) && ! is_numeric( $resolvedLeft ) ) {
							$success &= $resolvedLeft->id === $resolvedRight;
						} else {

							$success &= $resolvedLeft === $resolvedRight;
						}
						break;
					case "notequals":

						if ( is_numeric( $resolvedRight ) && ! is_numeric( $resolvedLeft ) ) {
							$success &= $resolvedLeft->id != $resolvedRight;
						} else {
							$success &= $resolvedLeft !== $resolvedRight;
						}
						break;

					default:
						$success &= apply_filters( 'triggerhappy_operator_' . $op, false, $resolvedLeft, $resolvedRight );
						break;
				}

			}

			if ( $success ) {

				return true;
			}
		}

		return false;
	}

	public function getReturnData( $context ) {
		return $context->returnData;
	}

	/**
	 * @param $node CoreNode
	 * @param $context \TriggerHappyContext
	 * @param null $data
	 *
	 * @return null
	 */
	abstract public function runCallback( $node, $context, $data = null );

	public function setReturnData( $context, $data ) {
		$context->returnData = $data;
	}

	public function setData( $context, $data ) {
		if ( $data !== null ) {
			$context->setData( $this->id, $data );
		}

	}
}
