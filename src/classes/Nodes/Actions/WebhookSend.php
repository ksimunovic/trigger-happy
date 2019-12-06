<?php

namespace HotSource\TriggerHappy\Nodes\Actions;

use HotSource\TriggerHappy\NodeField;
use HotSource\TriggerHappy\Nodes\CoreActionNode;

class WebhookSend extends CoreActionNode {

	/**
	 * WebhookSend constructor.
	 */
	public function __construct() {
		$this->name = 'Send POST Webhook';
		$this->description = 'Sends a webhook request to a specified URL';
		$this->cat = 'Webhooks';
		$this->nodeType = $this->getNodeType();
		$this->fields = $this->generateFields();
		$this->callback = 'triggerhappy_webhook_send';

	}

	/**
	 * @return NodeField[]
	 */
	public function generateFields() {
		return [
			new NodeField(
				'url', 'string', [
					'label'       => 'Webhook URL',
					'description' => 'What is the URL of the Webhook?',
					'dir'         => 'in',
				]
			),
			new NodeField(
				'payload', 'array', [
					'label' => 'The Webhook Payload to send',
					'dir'   => 'in',
				]
			),
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
		$payload = $data['payload'];
		if ( is_object( $payload ) ) {
			$className = get_class( $payload );
			$payload = apply_filters( "triggerhappy_to_json__" . $className, $payload );
		}
		wp_remote_post( $data['url'], [
			'headers' => [ 'Content-Type' => 'application/json; charset=utf-8' ],
			'body'    => json_encode( $data['payload'] ),
		] );
	}
}
