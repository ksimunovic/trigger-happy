<?php
require_once( dirname( __FILE__ ) . '/class-phpep.php' );
add_filter(
	'triggerhappy_generate_triggerhappy_action_hook',function( $arr, $node, $next ) {
		if ( ! isset( $node->def['fields']['start'] ) ) {
			$args = '';
		} else {
			$args = implode( $node->def['fields']['start'] );
		}
		$hook = isset( $node->def['hook'] ) ? $node->def['hook'] : $node->def['id'] ;
		array_push( $arr, "add_action('" . $hook . "', function(" . $args . ') { ' );

		$arr = call_user_func( $next,$arr );

		array_push( $arr, '});' );

		return $arr;
	},10,3
);

add_filter(
	'triggerhappy_generate_triggerhappy_set_arr_value',function( $arr, $node, $next ) {

		$expr = ($node->fields['array']->getExpression());

		$exprAsCode = $node->graph->generateExpression( $expr,null );
		$keyAsCode = $node->graph->generateExpression( $node->fields['key']->getExpression(),null );
		$valAsCode = $node->graph->generateExpression( $node->fields['value']->getExpression(),null );
		array_push( $arr, $exprAsCode . '[' . $keyAsCode . '] = ' . $valAsCode . ';' );

		return $arr;
	},10,3
);
add_filter(
	'triggerhappy_generate_triggerhappy_core_render_html',function( $arr, $node, $next ) {

		array_push( $arr, 'echo ' . $node->graph->generateExpression( $node->fields['html']->getExpression(), null ) . ';' );

		return $arr;
	},10,3
);


add_filter(
	'triggerhappy_generate_triggerhappy_filter_hook',function( $arr, $node, $next ) {
		$first = '';
		if ( ! isset( $node->def['fields']['start'] ) ) {
			$args = '';
		} else {
			$args = array();
			foreach ( $node->def['fields']['start'] as $key => $val ) {
				array_push( $args, '$' . $val['name'] );
				if ( $key == 0 ) {
					$first = '$' . $val['name'];
				}
			}
			$args = implode( $args );
		}
		$hook = isset( $node->def['hook'] ) ? $node->def['hook'] : $node->def['id'] ;
		array_push( $arr, "add_filter('" . $hook . "', function(" . $args . ') { ' );

		$arr = call_user_func( $next,$arr );

		array_push( $arr, 'return ' . $first . ';' );
			array_push( $arr, '});' );

		return $arr;
	},10,3
);
class TriggerHappyCodeGenerator {
	public function replaceParentContext( $code, $parentNode ) {
		$obj = preg_replace_callback(
			'/\$node0->([A-Za-z0-9_\-]*)/',function( $matches ) use ( $parentNode ) {
				$expr = ($parentNode->fields[ $matches[1] ]->getExpression());

				return  $parentNode->graph->generateExpression( $expr,$parentNode->fields[ $matches[1] ]->type );
			},$code
		);
		return $obj;
	}
	public function generateCodeForNode( $code, $node ) {
		if ( $node == null ) {
			return $code;
		}
		// echo "AND";print_r($node->def);
		$self = $this;
		if ( ! isset( $node->def['callback'] ) ) {
			// Maybe a grouped node
			$childGraphs = json_decode( $node->def['childGraphs'] );

			foreach ( $childGraphs as $i => $childGraph ) {
				$childFlow = new TriggerHappyFlow( $i,$childGraph );
				$childNode = $childFlow->getNode( 1 );

				$childCodeArr = $this->generateCodeForNode( array(), $childNode );
				foreach ( $childCodeArr as $i => $codeLine ) {
					array_push( $code, $this->replaceParentContext( $codeLine,$node ) );
				}
			}
			$next = $node->getNext();
			if ( count( $next ) > 0 ) {
				return $this->generateCodeForNode( $code,$node->graph->getNode( $next[0] ) );
			}
			return $code;

		}
		// die("triggerhappy_generate_{$node->def['callback']}");
		if ( has_filter( "triggerhappy_generate_{$node->def['callback']}" ) ) {
			$code = apply_filters(
				"triggerhappy_generate_{$node->def['callback']}",$code, $node, function( $code ) use ( $node ) {

					$next = $node->getNext();
					if ( count( $next ) > 0 ) {
						return $this->generateCodeForNode( $code,$node->graph->getNode( $next[0] ) );
					}
					return $code;

				}
			);
		} else {
			die( "triggerhappy_generate_{$node->def['callback']}" );
		}
		return $code;
	}
	public function generateCode( $node ) {
		error_reporting( E_ALL );
		ini_set( 'display_errors', '1' );
		$code = array();

		$n = $node->getNode( 1 );

		$code = $this->generateCodeForNode( $code,$n );
		$codeStr = '';
		$indent = 0;
		foreach ( $code as $i => $line ) {
			if ( strpos( $line,'}' ) !== false && strpos( $line,'}' ) >= 0 ) {
				$indent--;
			}

			for ( $i = 0; $i < $indent; $i++ ) {
				$codeStr .= "\t";
			}

			 $codeStr .= $line;
			if ( strpos( $line,'{' ) !== false && strpos( $line,'{' ) >= 0 ) {
				$indent++;
			}
			   $codeStr .= "\r\n";
		}
		return $codeStr;

	}
}
