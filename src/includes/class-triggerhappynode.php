<?php
require_once( dirname( __FILE__ ) . '/../../vendor/autoload.php' );

class TriggerHappyNode {
	public $id;

	public $type;

	public $graph;

	public $def;

	public $filters;

	public $data = [];

	public $returnData = [];

	public $fields = [];

	public $returnFields = [];

	public $inputData;

	private $isExecuting = false;

	private $next = [];

	/**
	 * @deprecated
	 */
	public function __construct( $id, $type, $graph ) {
		$this->id = $id;
		$this->graph = $graph;
		$this->type = $type;
		$this->def = TriggerHappy::get_node( $type );
	}

	/**
	 * @deprecated
	 */
	public function setFilters( $filters ) {
		$this->filters = $filters;
	}

	/**
	 * @deprecated
	 */
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

	/**
	 * @deprecated
	 */
	public function getField( $fieldId ) {

		if ( isset( $this->fields[ $fieldId ] ) ) {
			return $this->fields[ $fieldId ];

		}

		return false;
	}

	/**
	 * @deprecated
	 */
	public function getReturnField( $fieldId ) {

		if ( isset( $this->returnFields[ $fieldId ] ) ) {
			return $this->returnFields[ $fieldId ];

		}

		return false;
	}

	/**
	 * @deprecated
	 */
	public function hasReturnData( $context ) {
		return isset( $context->returnData );
	}

	/**
	 * @deprecated
	 */
	public function getNext() {
		return $this->next;
	}

	/**
	 * @deprecated
	 */
	public function setNext( $nextIds ) {
		$this->next = $nextIds;
	}

	/**
	 * @deprecated
	 */
	public function getFieldsByType( $type ) {
		$fields = [];

		foreach ( $this->fields as $p => $field ) {
			if ( $field->type == $type ) {
				array_push( $fields, $field );
			}
		}

		return $fields;
	}

	/**
	 * @deprecated
	 */
	public function addField( $fieldId, $type ) {
		return $this->fields[ $fieldId ] = new TriggerHappyField( $fieldId, $type, $this );
	}

	/**
	 * @deprecated
	 */
	public function addReturnField( $fieldId, $type ) {
		return $this->returnFields[ $fieldId ] = new TriggerHappyField( $fieldId, $type, $this );
	}

	/**
	 * @deprecated
	 */
	public function execute( $context ) {
		if ( $this->isExecuting ) {
			return;
		}
		if ( isset( $this->def['callback'] ) ) {
			return call_user_func( $this->def['callback'], $this, $context );

		} elseif ( isset( $this->def['childGraphs'] ) ) {

			$child_graphs = json_decode( $this->def['childGraphs'] );
			foreach ( $child_graphs as $i => $graph ) {
				$data = $this->getInputData( $context );
				$childFlow = new TriggerHappyFlow( '', $graph, false );
				$newContext = new TriggerHappyContext();
				$context->setData( $this->id, $data );
				$newContext->parentContext = $context;
				$newContext->parentNodeId = $this->id;
				$childFlow->parentFlow = $this->graph;
				$childFlow->start( $newContext );

			}
			$this->next( $context );
		}
		$this->isExecuting = false;
	}

	/**
	 * @deprecated
	 */
	public function getInputData( $context ) {
		$data = [];

		$inPortList = $this->fields;
		if ( count( $this->fields ) > 0 ) {
			$in = $this->fields;

			foreach ( $in as $fieldId => $field ) {
				$data[ $fieldId ] = $field->resolveExpression( $context );
			}
		}


		return $data;
	}

	/**
	 * @deprecated
	 */
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

	/**
	 * @deprecated
	 */
	public function canExecute( $context ) {
		$data = $context->getData( $this->id );
		$success = true;
		if ( isset( $this->filters ) ) {
			$success = $success && $this->applyFilters( $context, $this->filters );

		}

		if ( is_object( $this->def ) ) { // new class-based implementation
			$nodeDefinition = $this->def->toArray();
			if ( isset( $nodeDefinition['nodeFilters'] ) ) {
				$success = $success && $this->applyFilters( $context, json_decode( json_encode( $nodeDefinition['nodeFilters'] ) ) );
			}
		} else {
			if ( isset( $this->def['nodeFilters'] ) ) {
				$success = $success && $this->applyFilters( $context, json_decode( json_encode( $this->def['nodeFilters'] ) ) );
			}
		}

		return $success;
	}

	/**
	 * @deprecated
	 */
	protected function applyFilters( $context, $filters ) {
		foreach ( $filters as $i => $orGroup ) {
			$success = true;
			foreach ( $orGroup as $i => $andFilter ) {

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

	/**
	 * @deprecated
	 */
	public function getReturnData( $context ) {
		return $context->returnData;
	}

	/**
	 * @deprecated
	 */
	public function setReturnData( $context, $data ) {
		$context->returnData = $data;
	}

	/**
	 * @deprecated
	 */
	public function setData( $context, $data ) {
		if ( $data !== null ) {
			$context->setData( $this->id, $data );
		}

	}
}
