<?php


function triggerhappy_load_webhook_nodes( $nodes ) {


		$nodes['th_webhook_send'] = array(
			'name' => 'Send IFTTT Webhook',
			'plugin' => '',
			'nodeType' => 'action',
			'description' => 'Sends a webhook request to IFTTT',
			'cat' => 'Webhooks',
			'callback' => 'triggerhappy_webhook_send_ifttt',
			'fields' => array(
				triggerhappy_field(
					'event_name', 'string', array(
						'label' => 'Event Name',
						'description' => 'What is the name of the event you\'ve set up at IFTTT?',
						'dir' => 'in',
					)
				),
				triggerhappy_field(
					'secret_key', 'string', array(
						'label' => 'Secret Key',
						'description' => 'What is your IFTTT secret key? (Found in Documentation at https://ifttt.com/maker_webhooks)',
						'dir' => 'in',
					)
				),
				triggerhappy_field(
					'value1', 'array', array(
						'label' => 'Value 1',
						'dir' => 'in',
					)
				),
				triggerhappy_field(
					'value2', 'array', array(
						'label' => 'Value 2',
						'dir' => 'in',
					)
				),
				triggerhappy_field(
					'value3', 'array', array(
						'label' => 'Value 3',
						'dir' => 'in',
					)
				),
			)
		);

		$nodes['th_webhook_receive'] = array(
			'name' => 'When Webhook Received',
			'plugin' => '',
			'nodeType' => 'trigger',
			'description' => 'When a webhook (HTTP request) is received',
			'cat' => 'Webhooks',
			'callback' => 'triggerhappy_webhook_receive_ifttt',
			'fields' => array(
				triggerhappy_field(
					'webhook_name', 'string', array(
						'label' => 'Event Name',
						'description' => 'Webhook will be available at ' .get_site_url() . "/?thwebhook=[eventname]",
						'dir' => 'in',
					)
				),
				triggerhappy_field(
					'payload', 'array', array(
						'label' => 'Payload (JSON)',
						'dir' => 'start',
					)
				),
				triggerhappy_field(
					'payload_raw', 'array', array(
						'label' => 'Payload (Plain Text)',
						'dir' => 'start',
					)
				)

			)
		);
		return $nodes;


}

add_filter( 'triggerhappy_nodes', 'triggerhappy_load_webhook_nodes' );


function triggerhappy_webhook_send_ifttt( $node, $context ) {
	$data = $node->getInputData( $context );
	$url = $data['webhook_url'];
	$value1 = $data['value1'];
	$value2 = $data['value2'];
	$value3 = $data['value3'];
	wp_remote_post( 'https://maker.ifttt.com/trigger/' . $data['event_name'] . "/with/key/" . $data['secret_key'], array( 'body' => array(  'value1'=>$value1, 'value2'=>$value2, 'value3'=>$value3 ) ) );

}
function triggerhappy_webhook_receive_ifttt( $node, $context ) {
	$data = $node->getInputData( $context );
	add_action('wp', function() use($data, $node, $context) {
		if (isset($_GET['thwebhook']) && $_GET['thwebhook'] == $data['webhook_name']) {
			$entityBody = file_get_contents('php://input');
			$parsedBody = json_decode($entityBody);
			$node->next( $context, array( 'payload' => $parsedBody, 'payload_raw' => $entityBody ) );
			exit;
		}
	});


}
