<?php

namespace HotSource\TriggerHappy\Nodes\Actions;

use HotSource\TriggerHappy\NodeField;
use HotSource\TriggerHappy\Nodes\CoreActionNode;

class Login extends CoreActionNode {

	/**
	 * Login constructor.
	 */
	public function __construct() {
		$this->name = 'Log in as user';
		$this->description = 'Logs in as the specified user';
		$this->cat = 'WordPress';
		$this->nodeType = $this->getNodeType();
		$this->fields = $this->generateFields();
		$this->callback = 'triggerhappy_wp_login';
	}

	/**
	 * @return NodeField[]
	 */
	public function generateFields() {
		return [
			new NodeField( 'username', 'string', [
				'label'       => 'User Name',
				'description' => 'The user name of the user to log in as',
			] ),
			new NodeField( 'password', 'string', [
				'label'       => 'Password',
				'description' => 'The password to use when logging in ',
			] ),
		];
	}

	/**
	 * @param $node
	 * @param $context
	 * @param null $data
	 *
	 * @return void|null
	 */
	public function runCallback( $node, $context, $data = null ) {
		if ( is_user_logged_in() ) {
			wp_logout();
		}
		$data = $node->getInputData( $context );
		wp_signon( [
			'user_login'    => $data['username'],
			'user_password' => $data['password'],
		] );
		$node->next( $context );
	}
}
