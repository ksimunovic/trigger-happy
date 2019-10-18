<?php

namespace HotSource\TriggerHappy;

class CoreSendEmail extends CoreActionNode {

	public function __construct() {
		$this->name = 'Send an email';
		$this->description = 'Send a custom email';
		$this->plugin = 'wordpress';
		$this->cat = 'WordPress';
		$this->callback = 'triggerhappy_send_email';
		$this->nodeType = $this->getNodeType();
		$this->fields = $this->generateFields();
	}

	/**
	 * @return NodeField[]
	 */
	public function generateFields() {
		return [
			new NodeField( 'send_to', 'string', [
				'label'       => 'Send To',
				'description' => 'Enter the recipient email address',
			] ),
			new NodeField( 'subject', 'string', [
				'label'       => 'Subject',
				'description' => 'The email subject',
			] ),
			new NodeField( 'body', 'html', [
				'label'       => 'Body',
				'description' => 'The body of the email',
			] ),
		];
	}

	public function runCallback( $node, $context, $data = null ) {
		$headers = [ 'Content-Type: text/html; charset=UTF-8' ];
		wp_mail( $data['send_to'], $data['subject'], $data['body'], $headers );
		$node->next( $context );
	}

	/**
	 * Used in FlowHooksController::get_available_nodes() and
	 * FlowPluginsController::get_available_plugins()
	 * @return array
	 */
	public function toArray() {
		$fieldsArray = [];
		foreach ( $this->fields as $field ) {
			$fieldsArray[] = $field->createFieldDefinition();
		}

		return [
			'name'        => $this->name,
			'plugin'      => $this->plugin,
			'description' => $this->description,
			'cat'         => $this->cat,
			'callback'    => $this->callback,
			'nodeType'    => $this->nodeType,
			'fields'      => $fieldsArray,
		];
	}
}
