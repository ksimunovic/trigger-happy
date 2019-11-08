<?php
require_once( dirname( __FILE__ ) . '/class-phpep.php' );
require_once( dirname( __FILE__ ) . '/../../vendor/autoload.php' );

class TriggerHappyFlow {
	public $id = null;

	private $nodes = [];

	public function __construct( $id, $nodeGraph, $autoStart = true ) {
		$this->id = $id;
		$this->nodedata = $nodeGraph;
		if ( ! isset( $this->compiler ) || $this->compiler == null ) {
			$this->compiler = new TriggerHappyExpressionCompiler( [ $this, 'resolveNodeProperty' ] );
		}

		if ( $autoStart ) {

			$this->start();
		}
	}

	public function start( $context = null ) {
		$context = $context == null ? new TriggerHappyContext() : $context;

		$this->initialize();

		foreach ( $this->nodes as $nodeid => $nodeData ) {
			if ( is_object( $nodeData ) && $nodeData instanceof \HotSource\TriggerHappy\CoreNode ) { // new class-based implementation
				/** @var \HotSource\TriggerHappy\CoreNode $nodeData */
				if ( $nodeData->getNodeType() == 'trigger' ) {
					$nodeData->execute( $context );
				}
			} else {
				/** @var TriggerHappyNode $nodeData */
				if ( $nodeData->def['nodeType'] == 'trigger' ) {
					$nodeData->execute( $context );
				}
			}
		}
	}

	public function initialize() {
		if ( ! isset( $this->nodedata->nodes ) ) {
			return;
		}
		foreach ( $this->nodedata->nodes as $node ) {

			$object = TriggerHappy::get_node( $node->type );
			if ( ! empty( $object ) && $object instanceof \HotSource\TriggerHappy\CoreNode ) {
				$object->id = $node->nid;
				$object->graph = $this;

				// Replaces defined filters with updated one from db
				if ( isset( $node->filters ) && ! empty( $node->filters ) && ! empty( $node->filters[0] ) ) {
					$object->setFilters( $node->filters );
				}
				$this->addNode( $object );

				if ( isset( $node->next ) ) {
					$object->setNext( $node->next );
				}

				if ( ! empty( $node->expressions ) ) {
					foreach ( $node->expressions as $k => $v ) {
						$field = $object->getField( $k );
						if ( $field ) {
							$field->setExpression( $v );
						}
						$field = $object->getReturnField( $k );
						if ( $field ) {
							$field->setExpression( $v );
						}
					}
				}
			} else {
				$flowNode = new TriggerHappyNode( $node->nid, $node->type, $this );
				if ( isset( $node->filters ) && ! empty( $node->filters ) && ! empty( $node->filters[0] ) ) {
					$flowNode->setFilters( $node->filters );
				}
				$this->addNode( $flowNode );
				if ( isset( $node->next ) ) {
					$flowNode->setNext( $node->next );
				}
				if ( isset( $node->expressions ) ) {
					foreach ( $node->expressions as $k => $v ) {
						$field = $flowNode->getField( $k );
						if ( $field ) {
							$field->setExpression( $v );
						}
						$field = $flowNode->getReturnField( $k );
						if ( $field ) {
							$field->setExpression( $v );
						}
					}
				}
				$flowNode->data = [];
				if ( isset( $node->values ) && isset( $node->values->in ) ) {
					$vals = $node->values->in;
					foreach ( $vals as $i => $val ) {
						$flowNode->data[ $val->name ] = $val->value;
					}
				}
			}
		}

	}

	public function addNode( $node, $id = null ) {
		$id = $id == null ? $node->id : $id;
		$this->nodes[ $id ] = $node;

		if ( $node instanceof \HotSource\TriggerHappy\CoreNode ) { // new class-based implementation
			// No setting of fields needed as it's already in this object
			$node->addNodeToFields();
		} else {
			$def = TriggerHappy::get_node( $node->type );
			if ( isset( $def['fields'] ) ) {
				foreach ( $def['fields'] as $i => $portDef ) {
					$node->addField( $portDef['name'], $portDef['type'] );
				}
			}
		}

	}

	public function resolveNodeProperty( $obj, $prop, $context ) {

		if ( is_string( $obj ) ) {

			$nodeId = str_replace( '_N', '', $obj );

			if ( is_numeric( $nodeId ) ) {
				$node = null;

				if ( $nodeId == 0 ) {
					$node = $this->parentFlow->getNode( $context->parentNodeId );
					$context = $context->parentContext;

				} else {
					$node = $this->getNode( $nodeId );
				}

				if ( ! empty( $node->def ) ) {
					$field_data = $this->findField( $node->def, $prop );
				} else {
					$field_data = $node->findField( $prop );
				}
				$type = $field_data != null ? $field_data['type'] : null;
				$context->fieldType = $type;

				$val = $context->getData( $node->id );

				if ( isset( $val[ $prop ] ) ) {
					$data = [
						'type' => $type,
						'val'  => $val[ $prop ],
					];

					return $data['val'];
				}

				return null;
			} else if ( $nodeId == '' ) {
				// Is a global variable
				$global = TriggerHappy::get_instance()->get_global( $prop );
				if ( $global !== null ) {
					$context->fieldType = $global['type'];

					return apply_filters( "triggerhappy_global_" . $prop, [] );
				}

				return null;

			}
		} else {

			$newValue = apply_filters( 'triggerhappy_resolve_field_' . $context->fieldType . '__' . $prop, null, $obj, $prop, $context );

			if ( $newValue != null ) {
				if ( isset( $newValue['type'] ) ) {
					$newType = $newValue['type'];
					$newValue = $newValue['value'];
					$context->fieldType = $newType;
				}

				return $newValue;
			} else {

				$newType = $this->getSchemaPropertyType( $context->fieldType, $prop );
			}
			if ( is_numeric( $obj ) ) {

			}

			$context->fieldType = $newType;
			$res = null;
			if ( is_object( $obj ) ) {

				if ( method_exists( $obj, 'get_' . $prop ) ) {
					$res = call_user_func( [ $obj, 'get_' . $prop ] );
				} elseif ( $newType == 'boolean' && method_exists( $obj, 'is_' . $prop ) ) {

					$res = call_user_func( [ $obj, 'is_' . $prop ] );
					if ( is_numeric( $res ) ) {
						$res = (bool) $res == 1 ? true : false;
					}

				} elseif ( property_exists( $obj, $prop ) ) {
					$res = $obj->{$prop};
				} elseif ( method_exists( $obj, $prop ) ) {

					$res = call_user_func( [ $obj, $prop ] );

				}
			} elseif ( is_array( $obj ) ) {

				if ( isset( $obj, $prop ) ) {
					$res = &$obj[ $prop ];
				}
			}
			if ( $res && is_callable( $res ) ) {
				$res = call_user_func( $res );
			}

			return $res;
		}

		return 'Unkonwn';
	}

	public function getNode( $id ) {
		if ( isset( $this->nodes[ $id ] ) ) {
			return $this->nodes[ $id ];
		}

		return null;
	}

	/**
	 * @deprecated Not used with class-based nodes, $node->findField() is used now
	 */
	public function findField( $def, $field ) {
		if ( $def instanceof \HotSource\TriggerHappy\CoreNode ) {
			foreach ( $def->generateFields() as $key => $field_data ) {
				$field_data = $field_data->createFieldDefinition();
				if ( isset( $field_data['name'] ) && $field == $field_data['name'] ) {
					return $field_data;
				}
			}
		} else {
			foreach ( $def['fields'] as $key => $field_data ) {
				if ( isset( $field_data['name'] ) && $field == $field_data['name'] ) {
					return $field_data;
				}
			}
		}

		return null;
	}

	public function getSchemaPropertyType( $fieldType, $prop ) {
		global $triggerhappy;
		if ( $fieldType == 'object' || $fieldType == 'string' || $fieldType == 'html' || $fieldType == 'number' || $fieldType == 'array' ) {
			return $fieldType;
		}
		$newSchema = null;
		if ( isset( $triggerhappy['types_schema'][ $fieldType ] ) ) {
			$newSchema = $triggerhappy['types_schema'][ $fieldType ];
			if ( is_callable( $newSchema ) ) {
				$newSchema = call_user_func( $newSchema );
			}
		}
		if ( $newSchema ) {
			$props = $newSchema['properties'];
			if ( isset( $props[ $prop ] ) ) {
				return $props[ $prop ]['type'];
			}
		}

		return $fieldType;
	}

	public function createContext() {
		return new TriggerHappyContext();
	}

	public function generateExpression( $expression, $context ) {
		return $this->compiler->generateCode( $expression, $context );
	}

	public function resolveExpression( $expression, $context ) {
		$compiler = $this->compiler;
		if ( $expression !== null ) {

			if ( is_string( $expression ) ) {

				$res = preg_replace_callback(
					'/\{\{(_N[^}]*)\}\}/', function ( $matches ) use ( $compiler, $context ) {
					$phpep = new PHPEP( $matches[1] );
					$ast = $phpep->exec();

					$res = $compiler->execute( $ast, $context );
					if ( is_object( $res ) ) {

						return $res->id;
					}

					return $res;
				}, $expression
				);

				return $res;
			} else {

				return $compiler->execute( $expression, $context );
			}
		}
	}

	public function setNode( $node, $id ) {
		$this->nodes[ $id ] = $node;
	}
}
