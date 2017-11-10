<?php

function triggerhappy_load_ultimate_member_nodes( $nodes ) {

	return $nodes;
}

function triggerhappy_get_ultimatemember_field() {

	return array(
		'membershipLevel' => function() {
			global $ultimatemember;
			if ($ultimatemember->user == null) return '';
			return $ultimatemember->user->get_role_name( $ultimatemember->user->get_role());
		}
	);
}
function triggerhappy_load_ultimate_member_schema() {
	triggerhappy_register_global_field(
        'ultimateMember',
        'um_user',
        'The Ultimate Member profile of the logged in user',
		'triggerhappy_get_ultimatemember_field'
    );

		triggerhappy_register_value_type(
			'um_user', 'number', function ( $search ) {
				$req = new WP_REST_Request( 'GET', '/wp/v2/posts' );
				$req->set_param( 'search', $search );
				$response = rest_do_request( $req );
				$data = ($response->get_data());
				return array_map(
					function ( $d ) {
						return array(
							'id' => $d['id'],
							'text' => $d['name'],
						);
					}, $data
				);
			}, true
		);


		triggerhappy_register_value_type(
			'um_role', 'number', function (  ) {
				global $ultimatemember;
				$roles = array();
				foreach( $ultimatemember->query->get_roles() as $key => $value ) {
					array_push($roles, array('id'=>$key,'text'=>$value));
				}
				return $roles;
			}, false
		);
		triggerhappy_register_json_schema(
			'um_user',
			array(
				'$schema' => 'http://json-schema.org/draft-04/schema#',
				'title' => 'ultimatemember',
				'type' => 'object',
				'properties' =>
				  array(
					  'membershipLevel' => array(
						 'description' => 'User Role',
						 'type' => 'um_role'
					 ),
					 'membershipFields' => array(
						 'description' => 'Fields',
						 'type' => 'array'
					 )
				 )
			 )
		 );

}

add_filter( 'triggerhappy_nodes', 'triggerhappy_load_ultimate_member_nodes' );
add_action( 'triggerhappy_schema', 'triggerhappy_load_ultimate_member_schema' );
