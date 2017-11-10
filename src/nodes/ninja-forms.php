<?php
triggerhappy_register_value_type(
	'nf_form_id','number',function() {
		$data = array();
		foreach ( Ninja_Forms()->form()->get_forms() as $form ) {
			array_push(
				$data,array(
					'id' => $form->get_id(),
					'text' => $form->get_setting( 'title' ),
				)
			);
		}
		return $data;
	}
);
triggerhappy_register_node(
	'ninja_forms_after_submission','ninja-forms',array(
		'description' => 'When a form is submitted',
		'name' => 'Form Submitted',
		'nodeType' => 'trigger',
		'plugin' => 'ninja-forms',
		'cat' => 'forms',
		'fields' => array(
			triggerhappy_field( 'create' ),
			triggerhappy_field(
				'form','nf_form_id',array(
					'dir' => 'filters',
				)
			),
			triggerhappy_field(
				'form_data', 'object', array(
					'dir' => 'out',
				)
			),
		),
	)
);



triggerhappy_register_node(
	'ninja_forms_display_form','ninja-forms',array(
		'description' => 'Display a form',
		'name' => 'Display Form',
		'nodeType' => 'action',
		'callback'=>'triggerhappy_nf_display_form',
		'plugin' => 'ninja-forms',
		'cat' => 'forms',
		'fields' => array(
			triggerhappy_field(
				'form','nf_form_id',array(
					'dir' => 'in',
				)
			),
		),
	)
);

function triggerhappy_nf_display_form( $node, $context) {
	$data = $node->getInputData($context);
	echo do_shortcode("[ninja_form id=" . $data['form']. "]");
}
