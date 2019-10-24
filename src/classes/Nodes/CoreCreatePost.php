<?php

namespace HotSource\TriggerHappy\Nodes;

use HotSource\TriggerHappy\CoreActionNode;
use HotSource\TriggerHappy\NodeField;

class CoreCreatePost extends CoreActionNode {

	public function __construct() {
		$this->name = 'Create a new post';
		$this->description = 'Creates (or updates) a page or post';
		$this->plugin = 'WordPress';
		$this->cat = 'Posts';
		$this->callback = 'triggerhappy_create_post';
		$this->actionType = 'render';
		$this->nodeType = $this->getNodeType();
		$this->fields = $this->generateFields();
	}

	/**
	 * @return NodeField[]
	 */
	public function generateFields() {
		return [
			new NodeField( 'post_id', 'string', [
				'label'       => 'Post ID',
				'description' => 'Specify the post ID to update an existing post. Leave blank to create a new post',
				'dir'         => 'in',
			] ),
			new NodeField( 'post_type', 'wp_post_type', [
				'label'       => 'Post Type',
				'description' => 'Specify the type of post to create',
				'dir'         => 'in',
			] ),
			new NodeField( 'post_title', 'string', [
				'label' => 'Post Title',
				'dir'   => 'in',
			] ),
			new NodeField( 'post_content', 'string', [
				'label' => 'Content',
				'dir'   => 'in',
			] )
			,
			new NodeField( 'post_status', 'wp_post_status', [
				'label' => 'Post Status',
				'dir'   => 'in',
			] ),
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
		wp_insert_post( [
			'ID'           => $data['post_id'],
			'post_type'    => $data['post_type'],
			'post_title'   => $data['post_title'],
			'post_content' => $data['post_content'],
			'post_status'  => $data['post_status'],
		] );

		$node->next( $context, [] );
	}
}
