<?php

namespace HotSource\TriggerHappy;

use TH;

abstract class CoreNode {

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $description;

	/**
	 * @var string
	 */
	protected $plugin;

	/**
	 * @var string
	 */
	protected $cat;

	/**
	 * @var string
	 */
	protected $nodeType;

	/**
	 * @var string
	 */
	protected $type;

	/**
	 * @var NodeField[]
	 */
	protected $fields;

	/**
	 * @var TH::Filter[]
	 */
	protected $filters;

	/**
	 * @return string
	 */
	abstract public function getNodeType(): string;

	/**
	 * @param $node \TriggerHappyNode
	 * @param $context \TriggerHappyContext
	 * @param null $data
	 *
	 * @return null
	 */
	abstract public function runCallback( $node, $context, $data = null );

	/**
	 *
	 * @return array
	 */
	abstract public function toArray();

	/**
	 * @param \TriggerHappyNode $node
	 *
	 * @return NodeField[]
	 */
	public function getFieldsWithNode( $node ): array {
		$fields = [];
		foreach ( $this->generateFields() as $field ) {
			$field->setNode( $node );
			$fields[ $field->getName() ] = $field;
		}

		return $fields;
	}

	/**
	 *
	 * @return NodeField[]
	 */
	abstract public function generateFields();

}
