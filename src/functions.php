<?php


function triggerhappy_field( $name, $type = 'flow', $opts = array() ) {
	return TriggerHappy::get_instance()->create_field($name, $type, $opts);
}


function triggerhappy_register_node( $id, $ns, $options ) {
	return TriggerHappy::get_instance()->register_node( $id, $ns, $options );
}

function triggerhappy_register_json_schema( $id, $jsonSchema ) {
	return TriggerHappy::get_instance()->register_json_schema( $id, $jsonSchema );
}


function triggerhappy_register_value_type( $id, $parentType, $getOptions = null, $ajax = false ) {
	return TriggerHappy::get_instance()->register_value_type( $id, $parentType, $getOptions , $ajax  );
}


function triggerhappy_register_api_schema( $id, $apiRoute ) {
	return TriggerHappy::get_instance()->register_api_schema( $id, $apiRoute );
}

function triggerhappy_register_global_field(  $name, $type, $description, $callable = false  ) {
	return TriggerHappy::get_instance()->register_global_field(  $name, $type, $description, $callable   );
}
function triggerhappy_initialize() {
	return TriggerHappy::get_instance();
}
