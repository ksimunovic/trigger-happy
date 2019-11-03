<?php

namespace HotSource\TriggerHappy;

class NodeField {

	/**
	 * @var string
	 */
	public $type;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var array
	 */
	private $options;

	private $expression = null;

	/**
	 * @var \TriggerHappyExpressionCompiler
	 */
	private $compiler = null;

	/**
	 * @var \TriggerHappyNode
	 */
	private $node = null;

	/**
	 * NodeField constructor.
	 *
	 * @param string $name
	 * @param string $type
	 * @param array $options
	 */
	public function __construct( $name, $type = 'flow', $options = [] ) {
		$this->name = $name;
		$this->type = $type;
		$this->options = $options;
	}

	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}

	/**
	 * Creates a field definition array - doesn't add it to the global field list, this needs to be done manually
	 *
	 * @return array
	 */
	public function createFieldDefinition() {
		$fieldDefinition = array_merge(
			[
				'dir'      => 'in',
				'desc'     => '',
				'label'    => '',
				'argIndex' => - 1,
			],
			$this->options
		);
		$fieldDefinition['name'] = $this->name;
		$fieldDefinition['type'] = $this->type;

		return $fieldDefinition;
	}

	/**
	 * This part of code is copied over from class-triggerhappyfield
	 */

	public function getExpression() {
		return $this->expression;
	}

	public function setExpression( $expr ) {
		$this->expression = $expr;
	}

	// Connected port has asked for our data
	public function getNode() {
		return $this->node;
	}

//	/**
//	 * @param \TriggerHappyNode $node
//	 */
	public function setNode( $node ) {
		$this->node = $node;
	}

	public function resolveExpression( $context ) {
		// Ensure executed
		$expression = $this->expression;

		return $this->node->graph->resolveExpression( $expression, $context );
	}

	public function resolveExpressionForOperation( $context ) {
		// Ensure executed
		$expression = $this->expression;

		$compiler = $this->getCompiler();
		$results = $compiler->resolveForOperation( $expression, $context );
		if ( is_string( $results['left'] ) && strpos( $results['left'], '_N' ) === 0 ) {

			$results['left'] = &$context->data->{substr( $results['left'], 2 )};

		}

		return $results;
	}

	public function getCompiler() {
		if ( $this->compiler == null ) {
			$this->compiler = $this->node->graph->compiler;
		}

		return $this->compiler;
	}
}
