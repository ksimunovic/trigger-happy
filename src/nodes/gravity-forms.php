<?php

function triggerhappy_load_gravity_forms_nodes( $nodes ) {

	$nodes['th_gravity_forms_after_submission'] = new HotSource\TriggerHappy\Nodes\Plugins\GravityForms\Triggers\AfterSubmission();
	$nodes['th_gravity_forms_display_form'] = new HotSource\TriggerHappy\Nodes\Plugins\GravityForms\Actions\DisplayForm();

	return $nodes;
}

function triggerhappy_load_gravity_forms_schema() {
	triggerhappy_register_value_type(
		'gf_form_id', 'number', function () {
		$data = [];
		foreach ( GFAPI::get_forms() as $form ) {
			array_push(
				$data, [
					'id'   => $form['id'],
					'text' => $form['title'],
				]
			);
		}

		return $data;
	} );
}

add_filter( 'triggerhappy_nodes', 'triggerhappy_load_gravity_forms_nodes' );
add_action( 'triggerhappy_schema', 'triggerhappy_load_gravity_forms_schema' );
