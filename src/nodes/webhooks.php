<?php


function triggerhappy_load_webhook_nodes( $nodes ) {


	$nodes['th_webhook_send'] = [
		'name'        => 'Send IFTTT Webhook',
		'plugin'      => '',
		'nodeType'    => 'action',
		'description' => 'Sends a webhook request to IFTTT',
		'cat'         => 'Webhooks',
		'callback'    => 'triggerhappy_webhook_send_ifttt',
		'fields'      => [
			triggerhappy_field(
				'event_name', 'string', [
					'label'       => 'Event Name',
					'description' => 'What is the name of the event you\'ve set up at IFTTT?',
					'dir'         => 'in',
				]
			),
			triggerhappy_field(
				'secret_key', 'string', [
					'label'       => 'Secret Key',
					'description' => 'What is your IFTTT secret key? (Found in Documentation at https://ifttt.com/maker_webhooks)',
					'dir'         => 'in',
				]
			),
			triggerhappy_field(
				'value1', 'array', [
					'label' => 'Value 1',
					'dir'   => 'in',
				]
			),
			triggerhappy_field(
				'value2', 'array', [
					'label' => 'Value 2',
					'dir'   => 'in',
				]
			),
			triggerhappy_field(
				'value3', 'array', [
					'label' => 'Value 3',
					'dir'   => 'in',
				]
			),
		],
	];

	$nodes['th_webhook_receive'] = [
		'name'        => 'When IFTTT Webhook Received',
		'plugin'      => '',
		'nodeType'    => 'trigger',
		'description' => 'When a webhook (IFTTT) is received',
		'cat'         => 'Webhooks',
		'callback'    => 'triggerhappy_webhook_receive_ifttt',
		'fields'      => [
			triggerhappy_field(
				'webhook_name', 'string', [
					'label'       => 'Event Name',
					'description' => 'Webhook will be available at ' . get_site_url() . "/?thwebhook=[eventname]",
					'dir'         => 'in',
				]
			),
			triggerhappy_field(
				'payload', 'array', [
					'label' => 'Payload (JSON)',
					'dir'   => 'start',
				]
			),
			triggerhappy_field(
				'payload_raw', 'array', [
					'label' => 'Payload (Plain Text)',
					'dir'   => 'start',
				]
			),

		],
	];

	$nodes['th_webhook_send_post'] = [
		'name'        => 'Send POST Webhook',
		'plugin'      => '',
		'nodeType'    => 'action',
		'description' => 'Sends a webhook request to a specified URL',
		'cat'         => 'Webhooks',
		'callback'    => 'triggerhappy_webhook_send',
		'fields'      => [

			triggerhappy_field(
				'url', 'string', [
					'label'       => 'Webhook URL',
					'description' => 'What is the URL of the Webhook?',
					'dir'         => 'in',
				]
			),
			triggerhappy_field(
				'payload', 'array', [
					'label' => 'The Webhook Payload to send',
					'dir'   => 'in',
				]
			),
		],
	];

	$nodes['th_webhook_receive_generic'] = [
		'name'        => 'When Webhook Received',
		'plugin'      => '',
		'nodeType'    => 'trigger',
		'description' => 'When a webhook is received',
		'cat'         => 'Webhooks',
		'callback'    => 'triggerhappy_webhook_receive',
		'fields'      => [
			triggerhappy_field(
				'webhook_name', 'string', [
					'label'       => 'Event Name',
					'description' => 'Webhook will be available at ' . get_site_url() . "/?thwebhook=[eventname]",
					'dir'         => 'in',
				]
			),
			triggerhappy_field(
				'payload', 'array', [
					'label' => 'Payload (JSON)',
					'dir'   => 'start',
				]
			),
			triggerhappy_field(
				'payload_post', 'array', [
					'label' => 'Payload (POST)',
					'dir'   => 'start',
				]
			),

		],
	];

	return $nodes;


}

add_filter( 'triggerhappy_nodes', 'triggerhappy_load_webhook_nodes' );


function triggerhappy_webhook_send_ifttt( $node, $context ) {
	$data = $node->getInputData( $context );
	$url = $data['webhook_url'];
	$value1 = $data['value1'];
	$value2 = $data['value2'];
	$value3 = $data['value3'];
	wp_remote_post( 'https://maker.ifttt.com/trigger/' . $data['event_name'] . "/with/key/" . $data['secret_key'], [
		'body' => [
			'value1' => $value1,
			'value2' => $value2,
			'value3' => $value3,
		],
	] );

}

function triggerhappy_webhook_receive_ifttt( $node, $context ) {
	$data = $node->getInputData( $context );
	add_action( 'wp', function () use ( $data, $node, $context ) {
		if ( isset( $_GET['thwebhook'] ) && $_GET['thwebhook'] == $data['webhook_name'] ) {
			$entityBody = file_get_contents( 'php://input' );
			$parsedBody = json_decode( $entityBody );
			$node->next( $context, [ 'payload' => $parsedBody, 'payload_raw' => $entityBody ] );
			exit;
		}
	} );


}


function triggerhappy_webhook_receive( $node, $context ) {
	$data = $node->getInputData( $context );
	add_action( 'wp', function () use ( $data, $node, $context ) {
		if ( isset( $_GET['thwebhook'] ) && $_GET['thwebhook'] == $data['webhook_name'] ) {
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


function triggerhappy_webhook_send( $node, $context ) {
	$data = $node->getInputData( $context );
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

add_action( 'wp', function () {

	if ( isset( $_GET['th_webhook_auth'] ) ) {
		file_put_contents( dirname( __FILE__ ) . "/test.txt", "WERWERWER" );
		header( "HTTP/1.1 404 Not Found - " . $_GET['th_webhook_auth'] );
		print_r( $_GET['th_webhook_auth'] );
		echo "DONE";
		exit;
	}
} );
