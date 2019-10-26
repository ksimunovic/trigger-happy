<?php

namespace HotSource\TriggerHappy\Nodes;

use HotSource\TriggerHappy\CoreTriggerNode;
use HotSource\TriggerHappy\NodeField;
use TH;

class CoreSinglePostQuery extends CoreTriggerNode {

	public function __construct() {
		$this->name = 'hen data for a Single Post is being loaded';
		$this->description = 'When single post data is being queried';
		$this->plugin = '';
		$this->cat = 'Queries';
		$this->triggerType = 'query';
		$this->nodeType = $this->getNodeType();
		$this->fields = $this->generateFields();
		$this->hook = 'template_redirect';
		$this->callback = 'triggerhappy_action_hook';
		$this->globals = [ 'post' => 'post' ];
		$this->filters = [
			[
				TH::Filter( TH::Expression( "_self.query.is_main_query" ), 'equals', true ),
			],
		];

		$this->filters = [
			[
				TH::Filter( TH::Expression( "_N1.query.is_single" ), 'equals', true ),
			],
		];
		$this->filters = [];
	}

	/**
	 * @return NodeField[]
	 */
	public function generateFields() {
		return [
			new NodeField( 'query', 'wp_query', [ 'dir' => 'start' ] ),
		];
	}

	/**
	 * @param \TriggerHappyNode $node
	 * @param \TriggerHappyContext $context
	 * @param null $data
	 *
	 * @return void|null
	 */
	public function runCallback( $node, $context, $data = null ) {

		$hook = isset( $node->def['hook'] ) ? $node->def['hook'] : $node->def['id'];
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
			$passed_args = new ArrayObject( func_get_args() );

			if ( isset( $node->def['globals'] ) ) {
				foreach ( $node->def['globals'] as $id => $key ) {
					$args[ $id ] = $GLOBALS[ $key ];
				}
			}

			$i = 0;
			foreach ( $node->def['fields'] as $i => $field ) {
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
}
