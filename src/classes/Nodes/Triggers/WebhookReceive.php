<?php

namespace HotSource\TriggerHappy\Nodes\Triggers;

use HotSource\TriggerHappy\NodeField;
use HotSource\TriggerHappy\Nodes\CoreNode;
use HotSource\TriggerHappy\Nodes\CoreTriggerNode;

class WebhookReceive extends CoreTriggerNode {

	/**
	 * WebhookReceived constructor.
	 */
	public function __construct() {
		$this->name = 'When Webhook Received';
		$this->description = 'When a webhook is received';
		$this->cat = 'Webhooks';
		$this->nodeType = $this->getNodeType();
		$this->fields = $this->generateFields();
		$this->callback = 'triggerhappy_webhook_receive';
	}

	/**
	 * @return NodeField[]
	 */
	public function generateFields() {
		return [
			new NodeField(
				'webhook_name', 'string', [
					'label'       => 'Event Name',
					'description' => 'Webhook will be available at ' . get_site_url() . "/?thwebhook=[eventname]",
					'dir'         => 'in',
				]
			),
			new NodeField(
				'payload', 'array', [
					'label' => 'Payload (JSON)',
					'dir'   => 'start',
				]
			),
			new NodeField(
				'payload_post', 'array', [
					'label' => 'Payload (POST)',
					'dir'   => 'start',
				]
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
		add_action( 'wp', function () use ( $data, $node, $context ) {
			if ( isset( $_GET['thwebhook'] ) && isset( $data['webhook_name'] ) && $_GET['thwebhook'] == $data['webhook_name'] ) {
				$entityBody = file_get_contents( 'php://input' );
				$parsedBody = json_decode( $entityBody );
				$node->next( $context, [
					'payload'      => $parsedBody,
					'payload_raw'  => $entityBody,
					'payload_post' => $_POST,
				] );
				exit;
			}
		} );
	}
}
