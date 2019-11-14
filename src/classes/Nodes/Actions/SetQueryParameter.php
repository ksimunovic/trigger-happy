<?php

namespace HotSource\TriggerHappy\Nodes\Actions;

use HotSource\TriggerHappy\NodeField;
use HotSource\TriggerHappy\Nodes\CoreActionNode;
use HotSource\TriggerHappy\Nodes\CoreNode;

class SetQueryParameter extends CoreActionNode {

	public function __construct() {
		$this->name = 'Set Query Parameter';
		$this->description = 'Set a query parameter';
		$this->helpText = 'Sets a query parameter. Note: multiple queries are run on every page load, even the Admin dashboard. If you want to modify the main query, you\'ll need to check that is_main_query is set via the filters panel';
		$this->plugin = '';
		$this->cat = 'Queries';
		$this->actionType = 'query';
		$this->nodeType = $this->getNodeType();
		$this->fields = $this->generateFields();
	}

	/**
	 * @return NodeField[]
	 */
	public function generateFields() {
		return [
			new NodeField( 'query',
				'wp_query',
				[ 'description' => 'The Query to be updated' ]
			),
			new NodeField( 'query_param', 'string', [
				'description' => 'Query Parameter Name',
				'choices'     => triggerhappy_assoc_to_choices( [
					'author'         => 'Author ID',
					'author_name'    => 'Author Name',
					'cat'            => 'Category ID',
					'category_name'  => 'Category Name/Slug',
					'tag_id'         => 'Tag ID',
					'tag'            => 'Tag Slug',
					's'              => 'Match Keywords',
					'p'              => 'Single Post ID',
					'name'           => 'Single Post Name',
					'pagename'       => 'Single Page Slug',
					'post_parent'    => 'Parent Page ID',
					'post_type'      => 'Post Type',
					'post_status'    => 'Post Status',
					'posts_per_page' => 'Posts per page',
					'offset'         => 'Offset - number of Posts to skip',
					'orderby'        => 'Order By (ID, author, title, name, type, date, modified)',
					'order'          => 'Order Direction (ASC or DESC)',
				] ),
			] ),
			new NodeField( 'value',
				'string',
				[ 'description' => 'The value to set' ]
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
		$data['query']->set( $data['query_param'], $data['value'] );

		$node->next( $context, [] );
	}
}
