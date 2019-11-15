<?php

namespace HotSource\TriggerHappy\Nodes\Actions;

use HotSource\TriggerHappy\NodeField;
use HotSource\TriggerHappy\Nodes\CoreActionNode;
use HotSource\TriggerHappy\Nodes\CoreNode;

class AddNavMenuItem extends CoreActionNode {

	/**
	 * AddNavMenuItem constructor.
	 */
	public function __construct() {
		$this->name = 'Add Nav Menu Item';
		$this->description = 'Adds a link to a navigation menu';
		$this->plugin = '';
		$this->cat = 'Wordpress';
		$this->actionType = 'render';
		$this->nodeType = $this->getNodeType();
		$this->fields = $this->generateFields();
	}

	/**
	 * @return NodeField[]
	 */
	public function generateFields() {
		return [
			new NodeField( 'nav_menu',
				'wp_nav_menu',
				[ 'description' => 'The Nav Menu to be modified ' ]
			),
			new NodeField( 'text',
				'string',
				[ 'description' => 'The item text' ]
			),
			new NodeField( 'url',
				'string',
				[ 'description' => 'The item URL' ]
			),
		];
	}

	/**
	 * @param CoreNode $node
	 * @param \TriggerHappyContext $context
	 * @param null $data
	 *
	 * @return void|null
	 */
	public function runCallback( $node, $context, $data = null ) {
		if ( empty( $data['nav_menu'] ) || empty( $data['text'] ) || empty( esc_url( $data['url'] ) ) ) {
			$node->next( $context, [] );

			return;
		}

		$data['nav_menu_html'] = '<li><a title="' . esc_attr( $data['text'] ) . '" href="' . esc_url( $data['url'] ) . '">' . $data['text'] . '</a></li>';

		add_action( 'wp_nav_menu_items', function ( $items, $args ) use ( $data, $node ) {
			if ( $args->menu->term_id == $data['nav_menu'] ) {
				return $items . $data['nav_menu_html'];
			}

			return $items;
		}, null, 2 );
		$node->next( $context, [] );
	}
}
