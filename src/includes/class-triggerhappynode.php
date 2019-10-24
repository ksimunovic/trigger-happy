<?php
require_once( dirname( __FILE__ ) . '/../../vendor/autoload.php' );

use League\Pipeline\StageInterface;

class TriggerHappyNode implements StageInterface {
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

	public function __construct( $id, $type, $graph ) {
		$this->id = $id;
		$this->graph = $graph;
		$this->type = $type;
		$this->def = TriggerHappy::get_node( $type );
	}

	public function setFilters( $filters ) {
		$this->filters = $filters;
	}

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

		if ( isset( $this->fields[ $fieldId ] ) ) {
			return $this->fields[ $fieldId ];

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

	public function __invoke( $context ) {
		if ( $this->isExecuting ) {
			return;
		}
		if ( is_object( $this->def ) ) { // new class-based implementation

			// Adding inputData to flowNode so context can be removed from getInputData
			$this->inputData = $this->getInputData( new TriggerHappyContext() );

			$this->def->runCallback( $this, $context, $this->inputData );
		} else if ( isset( $this->def['callback'] ) ) {
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

	public function next( $context, $data = null ) {
		if ( $data !== null ) {
			$context->setData( $this->id, $data );
		}

		$result = null;
		if ( ! $this->canExecute( $context ) ) {
			return false;
		}

		foreach ( $this->next as $i => $id ) {
			$result = $this->graph->getNode( $id )->__invoke( $context );
		}

		return $this->getReturnData( $context );
	}

	public function canExecute( $context ) {
		$data = $context->getData( $this->id );
		$success = true;
		if ( isset( $this->filters ) ) {
			$success = $success && $this->applyFilters( $context, $this->filters );

		}

		if ( is_object( $this->def ) ) { // new class-based implementation
			//TODO: Implement nodeFilters with CoreTriggerNode
			if ( isset( $this->def->nodeFilters ) ) {
				$success = $success && $this->applyFilters( $context, json_decode( json_encode( $this->def->nodeFilters ) ) );
			}
		} else {
			if ( isset( $this->def['nodeFilters'] ) ) {
				$success = $success && $this->applyFilters( $context, json_decode( json_encode( $this->def['nodeFilters'] ) ) );
			}
		}

		return $success;
	}

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

	public function getReturnData( $context ) {
		return $context->returnData;
	}

	public function setReturnData( $context, $data ) {
		$context->returnData = $data;
	}

	public function setData( $context, $data ) {
		if ( $data !== null ) {
			$context->setData( $this->id, $data );
		}

	}
}
