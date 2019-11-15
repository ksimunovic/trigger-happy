<?php

namespace HotSource\TriggerHappy\Nodes\Actions;

use HotSource\TriggerHappy\Nodes\CoreActionNode;
use HotSource\TriggerHappy\NodeField;

class CoreSendEmail extends CoreActionNode {

	/**
	 * CoreSendEmail constructor.
	 */
	public function __construct() {
		$this->name = 'Send an email';
		$this->description = 'Send a custom email';
		$this->plugin = 'wordpress';
		$this->cat = 'WordPress';
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

	/**
	 * @param $node
	 * @param $context
	 * @param null $data
	 *
	 * @return void|null
	 */
	public function runCallback( $node, $context, $data = null ) {
		$headers = [ 'Content-Type: text/html; charset=UTF-8' ];
		wp_mail( $data['send_to'], $data['subject'], $data['body'], $headers );
		$node->next( $context );
	}
}
