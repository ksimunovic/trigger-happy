<?php

function triggerhappy_load_forms_nodes( $nodes ) {

	$nodes['th_ninja_forms_after_submission'] = [
		'description' => 'When a Ninja Form is submitted',
		'name'        => 'Ninja Form Submitted',
		'nodeType'    => 'trigger',
		'plugin'      => 'ninja-forms',
		'callback'    => 'triggerhappy_filter_hook',
		'hook'        => 'ninja_forms_after_submission',
		'cat'         => 'Forms',
		'fields'      => [
			triggerhappy_field(
				'form_data', 'array', [
					'dir' => 'start',
				]
			),
			triggerhappy_field(
				'form', 'nf_form_id', [
					'dir' => 'in',
				]
			),
		],
	];

	$nodes['th_ninja_forms_display_form'] = [
		'description' => 'Display a Ninja Form',
		'name'        => 'Display Ninja Form',
		'nodeType'    => 'action',
		'actionType'  => 'render',
		'callback'    => 'triggerhappy_nf_display_form',
		'plugin'      => 'ninja-forms',
		'cat'         => 'Forms',
		'fields'      => [
			triggerhappy_field(
				'form', 'nf_form_id', [
					'dir' => 'in',
				]
			),
		],
	];

	return $nodes;
}

function triggerhappy_load_forms_schema() {
	triggerhappy_register_value_type(
		'nf_form_id', 'number', function () {
		$data = [];
		foreach ( Ninja_Forms()->form()->get_forms() as $form ) {
			array_push(
				$data, [
					'id'   => $form->get_id(),
					'text' => $form->get_setting( 'title' ),
				]
			);
		}

		return $data;
	}
	);

}

add_filter( 'triggerhappy_nodes', 'triggerhappy_load_forms_nodes' );
add_action( 'triggerhappy_schema', 'triggerhappy_load_forms_schema' );

function triggerhappy_nf_display_form( $node, $context ) {
	$data = $node->getInputData( $context );
	echo do_shortcode( "[ninja_form id=" . $data['form'] . "]" );
}
