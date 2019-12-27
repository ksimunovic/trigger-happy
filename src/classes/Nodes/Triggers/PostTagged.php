<?php


namespace HotSource\TriggerHappy\Nodes\Triggers;


use HotSource\TriggerHappy\NodeField;
use HotSource\TriggerHappy\Nodes\CoreNode;
use HotSource\TriggerHappy\Nodes\CoreTriggerNode;

class PostTagged extends CoreTriggerNode {
	/**
	 * Timer PostSaved.
	 */
	public function __construct() {
		$this->name        = 'When a Post is tagged';
		$this->description = 'When a post is tagged with specific tag';
		$this->cat         = 'Posts';
		$this->nodeType    = $this->getNodeType();
		$this->fields      = $this->generateFields();
		$this->callback    = 'triggerhappy_action_hook';
	}

	/**
	 * @return NodeField[]
	 */
	public function generateFields() {
		return [
			new NodeField( 'tag_name', 'string', [
				'label'       => 'Tag name',
				'description' => 'Choose tag name that you want to track.',
				'choices'     => triggerhappy_get_tags_to_choices()
			] )
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
		$data['hook'] = 'rest_after_insert_post';

		add_action( $data['hook'], function ( $post_id ) use ( $node, $data, $context ) {
			$tags_array = [];

			$all_tags = get_the_tags($post_id);

			foreach ($all_tags as $tag) {
				$tags_array[] = $tag->name;
			}

			if (in_array($data['tag_name'], $tags_array)) {
				return $node->next( $context );
			}
		}, 10, 3);
	}
}