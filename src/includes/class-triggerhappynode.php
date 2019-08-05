<?php
class TriggerHappyNode {
	public $id;
	public $type;
	public $graph;
	public $def;
	public $filters;
	private $isExecuting = false;
	private $next = array();
	public $data = array();
	public $returnData = array();
	public $fields = array();
	public $returnFields = array();
	public function __construct( $id, $type, $graph ) {
		$this->id = $id;
		$this->graph = $graph;
		$this->type = $type;
		$this->def = TriggerHappy::get_node( $type );
	}
	public function setFilters( $filters ) {
		$this->filters = $filters;
	}
	public function getFieldDef($fieldId)  {
		if (isset($this->def['fields'])) {
			foreach ($this->def['fields'] as $i=>$fld) {
				if ($fld['name'] == $fieldId)
					return $fld;
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
	public function getInputData( $context ) {
		$data = array();

		$inPortList = $this->fields;
		if ( count( $this->fields ) > 0 ) {
			$in = $this->fields;

			foreach ( $in as $fieldId => $field ) {

				$data[ $fieldId ] = $field->resolveExpression( $context );

			}
		}


		return $data;
	}

	public function getReturnData( $context ) {
		return $context->returnData;
	}
	public function hasReturnData( $context ) {
		return isset($context->returnData);
	}
	public function setReturnData( $context,$data ) {
		 $context->returnData = $data;
	}
	public function setNext( $nextIds ) {
		$this->next = $nextIds;
	}
	public function getNext() {
		return $this->next;
	}
	public function getFieldsByType( $type ) {
		$fields = array();

		foreach ( $this->fields as $p => $field ) {
			if ( $field->type == $type ) {
				array_push( $fields,$field );
			}
		}

		return $fields;
	}
	public function addField( $fieldId, $type ) {
		return $this->fields[ $fieldId ] = new TriggerHappyField( $fieldId,$type,$this );
	}
	public function addReturnField( $fieldId, $type ) {
		return $this->returnFields[ $fieldId ] = new TriggerHappyField( $fieldId,$type,$this );
	}
	protected function applyFilters($context, $filters) {
		foreach ( $filters as $i => $orGroup ) {
			$success = true;
			foreach ( $orGroup as $i => $andFilter ) {

				$left = $andFilter->left;
				if (is_object($left) && isset($left->expr) )
					$left = $left->expr;
				$right = $andFilter->right->expr;
				if (is_object($right) && isset($right->expr) )
					$right = $right->expr;
				$op = $andFilter->op;

				$resolvedLeft = ($this->graph->resolveExpression( $left,$context ));

				$resolvedRight = ($this->graph->resolveExpression( $right,$context ));
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
						$success &= apply_filters('triggerhappy_operator_' . $op,false,$resolvedLeft, $resolvedRight);
					break;
				}

			}

			if ( $success ) {

				return true;
			}
		}
		return false;
	}
	public function canExecute( $context ) {
		$data = $context->getData( $this->id );
		$success = true;
		if ( isset( $this->filters ) ) {
			$success = $success && $this->applyFilters($context, $this->filters);

		}

		if ( isset( $this->def['nodeFilters'] ) ) {

			$success = $success && $this->applyFilters($context,  json_decode(json_encode($this->def['nodeFilters'])));

		}
		return $success;
	}
	public function execute( $context ) {
		if ( $this->isExecuting ) {
			return;
		}
		if ( isset( $this->def['callback'] ) ) {
			 return call_user_func( $this->def['callback'] , $this, $context );

		} elseif ( isset( $this->def['childGraphs'] ) ) {

			$child_graphs = json_decode( $this->def['childGraphs'] );
			foreach ( $child_graphs as $i => $graph ) {
				$data = $this->getInputData( $context );
				$childFlow = new TriggerHappyFlow( '',$graph,false );
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

	public function setData($context, $data) {
		if ( $data !== null ) {
			$context->setData( $this->id,$data );
		}

	}
	public function next( $context, $data = null ) {
		if ( $data !== null ) {
			$context->setData( $this->id,$data );
		}
		if ( isset( $this->graph->nodedata->mapFields ) && isset( $this->graph->nodedata->mapFields->{$this->id} ) ) {
			$fieldsToMapToParentContext = $this->graph->nodedata->mapFields->{$this->id};
			$parentData = $context->parentContext->getData( $context->parentNodeId,$data );
			foreach ( $fieldsToMapToParentContext as $from => $to ) {
				if ( isset( $data->{$from} ) ) {
					$parentData->{$to} = $data->{$from};
				}
			}
			global $pd;
			$pd = true;
			$newData = $this->graph->parentFlow->getNode( $context->parentNodeId )->getInputData( $context->parentContext );
			$context->parentContext->setData( $context->parentNodeId, $newData );
			$context->setData( 0,$newData );

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
}
