<?php

class TriggerHappyField {
	public $id;
	public $type;
	private $fieldList;
	private $expression = null;
	private $connectors = array();
	private $compiler = null;
	private $value = null;
	public function __construct( $id, $type, $node ) {
		$this->id = $id;
		$this->type = $type;
		$this->node = $node;

	}
	public function getCompiler() {
		if ( $this->compiler == null ) {
			$this->compiler = $this->getNode()->graph->compiler;
		}
		return $this->compiler;
	}
	public function getNode() {
		return $this->node;
	}

	// Connected port has asked for our data
	public function resolveExpression( $context ) {
		// Ensure executed
		$expression = $this->expression;

		$compiler = $this->getCompiler();

		return $this->getNode()->graph->resolveExpression( $expression, $context );
		// $this->getNode()->execute();
		return '';

	}

	public function resolveExpressionForOperation( $context ) {
		// Ensure executed
		$expression = $this->expression;

		$compiler = $this->getCompiler();
		$results = $compiler->resolveForOperation( $expression,$context );
		if ( is_string( $results['left'] ) && strpos( $results['left'],'_N' ) === 0 ) {

			$results['left'] = &$context->data->{substr( $results['left'],2 )};

		}
		return $results;
		// $this->getNode()->execute();
		return '';

	}
	public function setValue( $value ) {
		$this->value = $value;
	}
	public function setExpression( $expr ) {
		$this->expression = $expr;
	}
	public function getExpression() {
		return $this->expression;
	}
}
