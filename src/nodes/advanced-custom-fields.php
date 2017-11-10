<?php


			triggerhappy_register_node(
				'acf_field_added','advanced-custom-fields',array(
					'description' => 'When an ACF Field is added',
					'name' => 'Field Added',
					'nodeType' => 'trigger',
					'plugin' => 'advanced-custom-fields',
					'cat' => 'security',
					'fields' => array(),
				)
			);
			triggerhappy_register_json_schema(
				'acf_global',
				array(
					'$schema' => 'http://json-schema.org/draft-04/schema#',
					'title' => 'acfglobal',
					'type' => 'object',
					'properties' =>
					  array(
						  'customField' => array(
							 'description' => 'Custom Fields',
							 'type' => 'array'
						 )
					 )
				 )
			 );
add_filter('triggerhappy_json_schema_wp_post', function($schema) {
	$schema['properties']['acf_field'] = array(
		'label'=>'ACF Fields',
		'description'=>'Access ACF custom fields for this post',
		'type'=>'array'
	);
	return $schema;
});
