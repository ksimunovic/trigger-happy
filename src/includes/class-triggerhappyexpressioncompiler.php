<?php

class TriggerHappyExpressionCompiler {
	private $resolveCallback;
	public function __construct( $resolveCallback ) {
		$this->resolveCallback = $resolveCallback;
	}
	protected function array_to_object( $array ) {
		$obj = new stdClass();
		foreach ( $array as $k => $v ) {
			if ( strlen( $k ) ) {
				if ( is_array( $v ) ) {
					$obj->{$k} = $this->array_to_object( $v ); // RECURSION
				} else {
					$obj->{$k} = $v;
				}
			}
		}
		return $obj;
	}
	public function Expression( &$expr, $context ) {
		if ( is_array( $expr ) ) {
			$expr = $this->array_to_object( $expr );
		}

		if ( !is_object( $expr ) ) {
			return $expr;
		}
		$call = array( $this, $expr->type );

		if ( is_callable( $call ) ) {

			$res = call_user_func( $call,$expr,$context );

			return $res;
		}
		if ( is_string( $expr ) || is_numeric( $expr )  || is_bool( $expr ) ) {

			return $expr;
		}


		die( 'unknown type' . $expr->type );
	}
	public function ExpressionCode( &$expr, $context ) {
		if ( is_array( $expr ) ) {
			$expr = $this->array_to_object( $expr );
		}
		if ( is_string( $expr ) ) {
			if ( $context == 'string' || $context == 'html' ) {
				return "'" . $expr . "'";
			}
			return $expr;
		}
		$call = array( $this, $expr->type . 'Code' );

		if ( is_callable( $call ) ) {

			$res = call_user_func( $call,$expr,$context );

			return $res;
		}
		if ( is_string( $expr ) || is_numeric( $expr ) ) {

			return $expr;
		}

		die( 'unknown type1' . $expr->type );
	}
	public function ThisExpression( $expr ) {
		return 'this';
	}
	public function ThisExpressionCode( $expr ) {
		return "$this->";
	}
	public function Compound( $expr, $context ) {
		$str = '';
		foreach ( $expr->body as $i => $bodyExpr ) {
			$str .= ' ' . self::Expression( $bodyExpr,$context );
		}
		return $str;
	}
	public function Identifier( $expr ) {
		return $expr->name;
	}

	public function IdentifierCode( $expr ) {
		return $expr->name;
	}
	public function Literal( &$expr ) {

		return $expr->value;
	}
	public function LiteralCode( &$expr ) {

		return $expr->value;
	}
	public function MemberExpression( &$expr, $context ) {

		$obj = $this->Expression( $expr->object,$context );
		if (is_array($obj) && isset($obj['__triggerhappy_call_functions'])) {
	   		$callable = $this->Expression( $expr->property,$context );

			return call_user_func($callable);
	   	}
		$prop = $this->Expression( $expr->property,$context );

		return call_user_func( $this->resolveCallback,$obj,$prop,$context );
	}
	public function MemberExpressionCode( &$expr, $context ) {

		 $obj = preg_replace_callback(
			 '/_N([0-9]*)/',function( $matches ) {
				return '$node' . $matches[1];
			 },$this->Expression( $expr->object,$context )
		 );
		if ( $obj == '$node1' ) {
			return '$' . $this->Expression( $expr->property,$context );
		} else if (is_array($obj) && isset($obj['__triggerhappy_type'])) {
			die('sa');
		}
		return  $obj . '->' . $this->Expression( $expr->property,$context );
	}
	public function CallExpression( &$expr, $context ) {
		$callingObject = $expr->callee->object;
		$callingObject = $this->Expression( $callingObject,$context );
		$callingMethod = $expr->callee->property;
		$callingMethod = $this->Expression( $callingMethod,$context );

		$args = array();
		foreach ( $expr->arguments as $i => $arg ) {
			array_push( $args,$this->Expression( $arg,$context ) );
		}
		$undefinedPlaceholder = new stdClass();
		$result = apply_filters( 'triggerhappy_expression_call_' . $this->Expression( $callingMethod,$context ), $undefinedPlaceholder , $callingObject, $callingMethod, $args );

		if ( $result != $undefinedPlaceholder ) {
			return $result;
		}

		return call_user_func_array( array( $callingObject, $callingMethod ), $args );

		// return call_user_func($this->resolveCallback,,$this->Expression($expr->property,$context),$context);
	}public function CallExpressionCode( &$expr, $context ) {
		$callingObject = $expr->callee->object;
		$callingObject = $this->Expression( $callingObject,$context );
		$callingMethod = $expr->callee->property;
		$callingMethod = $this->Expression( $callingMethod,$context );

		$args = array();
		foreach ( $expr->arguments as $i => $arg ) {
			array_push( $args,$this->Expression( $arg,$context ) );
		}
		$undefinedPlaceholder = new stdClass();
		return $callingObject . '->' . $this->Expression( $callingMethod,$context ) . '(' . implode( $args ) . ')';

		// return call_user_func($this->resolveCallback,,$this->Expression($expr->property,$context),$context);
	}
	public function BinaryExpressionCode( $expr, $context ) {
		return $this->ExpressionCode( $expr->left,$context ) . ' ' + $expr->operator + ' ' . $this->ExpressionCode( $expr->right,$context );
	}
	public function BinaryExpression( &$expr, $context ) {
		switch ( $expr->operator ) {
			case '*':
				return $this->Expression( $expr->left,$context ) * $this->Expression( $expr->right,$context );
			break;
			case '+':
				return $this->Expression( $expr->left,$context ) + $this->Expression( $expr->right,$context );
			break;
			case '/':
				return $this->Expression( $expr->left,$context ) / $this->Expression( $expr->right,$context );
			break;
			case '-':
				return $this->Expression( $expr->left,$context ) - $this->Expression( $expr->right,$context );
			break;
		}
	}
	public function execute( &$expression, $context ) {
		// print_r($expression);
		$result = $this->Expression( $expression,$context );

		return $result;
		die( 'express' );
	}
	public function generateCode( &$expression, $context ) {
		// print_r($expression);
		$result = $this->ExpressionCode( $expression,$context );

		return $result;
		die( 'express' );
	}
	public function resolveForOperation( &$expression, $context ) {

		if ( $expression->type == 'MemberExpression' ) {
			return array(
				'left' => $this->Expression( $expression->object,$context ),
				'prop' => $expression->property->name,
			);
		}
		print_r( $expression );

		die( 'express' );
	}
}
