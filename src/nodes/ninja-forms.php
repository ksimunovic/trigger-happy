<?php

function triggerhappy_load_ninja_forms_nodes( $nodes ) {

	$nodes['th_ninja_forms_after_submission'] = new HotSource\TriggerHappy\Nodes\Plugins\NinjaForms\Triggers\AfterSubmission();
	$nodes['th_ninja_forms_display_form'] = new HotSource\TriggerHappy\Nodes\Plugins\NinjaForms\Actions\DisplayForm();

	return $nodes;
}

function triggerhappy_load_ninja_forms_schema() {
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
	} );
}

add_filter( 'triggerhappy_nodes', 'triggerhappy_load_ninja_forms_nodes' );
add_action( 'triggerhappy_schema', 'triggerhappy_load_ninja_forms_schema' );
