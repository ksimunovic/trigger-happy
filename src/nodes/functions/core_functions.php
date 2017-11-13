<?php
function triggerhappy_wp_redirect( $node, $context ) {
	$data = $node->getInputData($context);
	wp_redirect($data['url']);
	exit;
}

function triggerhappy_send_email( $node, $context ) {
	$data = $node->getInputData( $context );

	$headers = array( 'Content-Type: text/html; charset=UTF-8' );
	wp_mail( $data[ 'send_to' ], $data[ 'subject' ], $data[ 'body' ], $headers );

	$node->next( $context );
}
function triggerhappy_wp_login( $node, $context ) {
	if ( is_user_logged_in() ) {
		wp_logout();
	}
	$data = $node->getInputData( $context );
	wp_signon( array(
		'user_login' => $data[ 'username' ],
		'user_password' => $data[ 'password' ]
	) );
}

function triggerhappy_wp_logout( $node, $context ) {
	if ( is_user_logged_in() ) {
		wp_logout();
	}
}

function triggerhappy_function_call( $node, $context ) {
	$function_to_call = $node->def['function'];
	$args = array();
	$data = $node->getInputData($context);
	foreach ($node->def['function_args'] as $i=>$id) {
		array_push($args, $data[$id]);
	}
	if (is_callable($function_to_call)) {
		call_user_func_array($function_to_call,$args);
	}
	$node->next( $context );
}
function triggerhappy_action_hook( $node, $context ) {

	$hook = isset( $node->def['hook'] ) ? $node->def['hook'] : $node->def['id'];
	$priority = 10;
	if (strpos($hook,'$') === 0) {
		$hookField = substr($hook,1);
		$inputData = $node->getInputData($context);
		$hook = $inputData[$hookField];
		$hook_parts = explode( ':', $hook );
		$hook = $hook_parts[0];
		if (count($hook_parts) > 1) {
			$priority = $hook_parts[1];
		}
	}
	add_action(
		$hook, function () use ( $hook, $node, $context ) {

			$args = array();
			$passed_args = new ArrayObject(func_get_args());

			if (isset($node->def['globals']))
			{
				foreach ($node->def['globals'] as $id=>$key) {
					$args[$id] = $GLOBALS[$key];
				}
			}

			$i = 0;
			foreach ($node->def['fields'] as $i=>$field) {
				if ($field['dir'] !== 'start' || isset($args[$field['name']]))
					continue;

				if (isset($passed_args[$i])) {
					$key = $field['name'];
					$args[$key] = $passed_args[$i];
				}
				$i++;
			}
			$node->setData($context,$args);

			if (!$node->canExecute($context)) {
				return;
			}

			return $node->next( $context, $args );
		}, $priority, 9999
	);
}
function triggerhappy_render_html_after_post_content( $node, $context ) {
	$data = $node->getInputData( $context );
	$position = $data['position'];
	$hook = 'the_content';
	if ( $position == 'before_title' || $position == 'after_title' ) {
		$hook = 'the_title';
	}
	add_filter( $hook, function($existing) use( $position, $data ) {
		if ( $position == 'before_title' || $position == 'before_content' ) {
			return $data['html'] . $existing;
		}
		return $existing . $data['html'];
	});
	$node->next( $context, array() );
}


function triggerhappy_render_html_on_position_action( $node, $context ) {
	$data = $node->getInputData( $context );
	$position = $data['position'];

	add_action( $position, function() use( $position, $data ) {
		echo $data['html'];
	});
	$node->next( $context, array() );

}
function triggerhappy_custom_hook( $node, $context ) {
	$data = $node->getInputData($context);

	if ($data['hookType'] == 'action') {
		add_action($data['hookName'], function() use($node,$context) {
			$passed_args = func_get_args();
			$node->next($context, array('args'=>$passed_args));
		},$data['priority']);
	} else if ($data['hookType'] == 'filter') {

		add_filter($data['hookName'], function() use($node,$context) {

			$passed_args = func_get_args();
			$args = new stdClass();
			$first_arg_name = '';
			foreach ($passed_args as $i=>$val) {
				$key = (string)$i;
				$args->{$key} = $val;
			}
			$args = new ArrayObject($passed_args);

			$node->next($context, array('args'=>$args));
			if (isset($args[0])) {

				return $args[0];

			}
			return $passed_args[0];
		},$data['priority']);
	}
}

function triggerhappy_filter_hook( $node, $context ) {
	$filter = isset( $node->def['hook'] ) ? $node->def['hook'] : $node->def['id'];
	$priority = 10;
	if (isset($node->def['priority']))
		$priority = $node->def['priority'];

	add_filter(
		$filter, function () use ( $filter,&$node, $context ) {
			$passed_args =  new ArrayObject(func_get_args());
			$start_fields = array_filter($node->def['fields'],function($arr_value) { return $arr_value['dir'] == 'start'; });
			$in_fields = array_filter($node->def['fields'],function($arr_value) { return $arr_value['dir'] == 'in'; });
			$args = array();
			foreach ($passed_args as $i=>$val) {
				if (isset($start_fields[$i])) {
					$key = $start_fields[$i]['name'];
					$args[$key] = $val;
				}
			}

			if (isset($node->def['globals']))
			{
				foreach ($node->def['globals'] as $id=>$key) {
					$args[$id] = $GLOBALS[$key];
				}
			}

			$node->setData($context,new ArrayObject($args));
			if (!$node->canExecute($context)) {

				return reset($passed_args);
			}

			$inputData = $node->getInputData( $context );

			$returnData = $node->next( $context, $args );

			if ($node->hasReturnData($context)) {

				return $node->getReturnData($context);
			}
			$first = reset($in_fields);
			if (isset($inputData[$first['name']]))
				return $inputData[$first['name']];
			return $passed_args[0];
		}, $priority, 9999
	);
}
function triggerhappy_core_set_value( $node, $context ) {
	$data = $node->getInputData( $context );
	$setField = $node->getField( 'set' );

	$operation_data = $setField->resolveExpressionForOperation( $context );
	$op = $operation_data[ 'left' ];

	if ( is_array( $op ) ) {
		$op[ $operation_data[ 'prop' ] ] = $data[ 'toValue' ];
	} elseif ( is_object( $op ) ) {
		$op->{ $operation_data[ 'prop' ] } = $data[ 'toValue' ];
	}
	$result = array(
		'updated' => $operation_data[ 'left' ],
	);
	$node->setReturnData( $context, $op );
	$node->next( $context, $result );
}

function triggerhappy_get_arr_value( $node, $context ) {
	$args = $node->getInputData( $context );

	return $node->next(
		$context, array(
			'result' => $args['obj'][ $args['key'] ],
		)
	);
}
function triggerhappy_timer_trigger( $node, $context ) {
	$args = $node->getInputData( $context );
	add_filter(
		'cron_schedules', function ( $schedules ) use ( $args ) {
			$time = 'Every ' . $args['hours'] . ' hours (TriggerHappy)';
			$timeslug = sanitize_title( $time );
			$schedules[ $timeslug ] = array(
				'interval' => $args['hours'] * 60 * 60,
				'display'  => __( $time ),
			);

			return $schedules;
		}
	);
	add_action(
		'triggerhappy_timer_trigger__' . $node->graph->id, function () use ( $node, $args, $context ) {
			return $node->next( $context );
		}
	);

	if ( ! wp_next_scheduled( 'triggerhappy_timer_trigger__' . $node->graph->id ) ) {
		$time = 'Every ' . $args['hours'] . ' hours (TriggerHappy)';
		$timeslug = sanitize_title( $time );
		wp_schedule_event( time(), $timeslug, 'triggerhappy_timer_trigger__' . $node->graph->id );
	}

}
function triggerhappy_set_arr_value( $node, $context ) {

	$args = $node->getInputData( $context );
	$key = $args['key'];
	$arr = $args['array'];
	if ( is_numeric( $key ) )
		$key = (string)$key;
	if ( is_array( $arr ) || is_a( $arr, 'ArrayObject' ) ) {
		$arr[$key] = $args['value'];
	} else {
		$arr->{$key} = $args['value'];
	}

	return $node->next( $context );
}

function triggerhappy_set_value( $node, $context ) {

	$args = $node->getInputData( $context );
	$key = $args['key'];
	$keyExpression = $node->getField( 'key' )->getExpression();
	$array = $node->resolveExpression( $keyExpression->left, $context );
	$prop =$node->resolveExpression( $keyExpression->right, $context );
	if ( is_numeric( $key ) ){
		$key = (string)$key;
	}
	if ( is_array( $arr ) || is_a( $arr, 'ArrayObject' ) ) {
		$arr[$key] = $args['value'];
	} else {
		$arr->{$key} = $args['value'];
	}

	return $node->next( $context );
}

function triggerhappy_set_meta_value( $node, $context ) {

	$args = $node->getInputData( $context );
	$key = $args['key'];
	$post = $args['post'];
	if ( ! is_numeric( $post ) ) {
		$post = $post->ID;
	}

	if ( is_numeric( $key ) ) {
		$key = (string)$key;
	}
	update_post_meta( $post, $key, $args['value'] );
	return $node->next( $context );
}
function triggerhappy_arr_remove( $node, $args ) {
	$arr = $args['data'];
	$key = $args['key'];
	if ( $key && is_string( $key ) ) {
		$key = array( $key );
	}
	foreach ( $key as $k ) {
		$keyparts = explode( '.', $k );
		$arrValue = &$arr;
		for ( $i = 0; $i < count( $keyparts ) - 1; $i++ ) {
			$arrValue = &$arrValue[ $keyparts[ $i ] ];
		}
		unset( $arrValue[ $keyparts[ count( $keyparts ) - 1 ] ] );
	}

	return $node->next(
		array(
			'result' => $arr,
		)
	);
}

function triggerhappy_create_comment( $node, $args ) {
	$time = current_time( 'mysql' );

	$data = array(
		'comment_post_ID' => $args['post_id'],
		'comment_author' => $args['author'],
		'comment_author_email' => $args['author_email'],
		'comment_author_url' => $args['author_url'],
		'comment_content' => $args['comment_text'],
		'comment_author_IP' => '127.0.0.1',
		'comment_agent' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.10) Gecko/2009042316 Firefox/3.0.10 (.NET CLR 3.5.30729)',
		'comment_date' => $time,
		'comment_approved' => 1,
	);

	wp_insert_comment( $data );
}
function triggerhappy_create_post_type( $node, $context ) {
	$args = $node->getInputData( $context );
	$labels =  array(
		'name' => $args['label_plural'],
		'singular_name' => $args['label'],
	);
	$options = array();


	foreach ( $node->def['fields'] as $f => $field ) {
		if ( ! isset( $args[ $field['name'] ] ) || $args[ $field['name'] ] == '' )
			continue;
		if ( isset( $field['advanced'] ) && $field['advanced'] == 'Labels' ) {
			$labels[ $field['name'] ] = $args[ $field['name'] ];
		} else if ( isset($field['advanced']) && $field['advanced'] == 'Advanced Settings') {
			$options[ $field['name'] ] = $args[ $field['name'] ];
		}
	}
	$options['labels'] = $labels;
	if (isset( $args['public']) &&  $args['public'] != '') {
		$options['public'] = $args['public'];
	} else {
		$options['public'] = true;
	}
	if ( isset( $args['has_archive'] ) && $args['has_archive'] != '' ) {
		$options['has_archive'] = $args['has_archive'];
	} else {
		$options['has_archive'] = true;
	}

	register_post_type(
		$args['post_type'],
		$options
	);
}

function triggerhappy_insert_post_footer( $wf, $args ) {
	add_filter( 'the_content', function ( $c ) use ( $args ) {
		return $c . $args['content'];
	} );
}

function triggerhappy_wordpress_custom_shortcode( $node, $context ) {
	$data = $node->getInputData( $context );
	add_shortcode( $data['shortcode'], function ( $atts, $content = '' ) use( $node, $context ) {
		ob_start();
		$node->next( $context );
		return ob_get_clean();
	});
}

function triggerhappy_wordpress_custom_url( $node, $context ) {
	global $wp;
	if ( isset( $wp ) ) {
		$current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );
	}
	$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

	$args = array(
		'url' => $actual_link,
	);

	$node->next( $context, $args );
}

function triggerhappy_core_render_html( $node, $context ) {
	$data = $node->getInputData( $context );
	echo $data['html'];
	$node->next( $context, $data );
}

function triggerhappy_query_param( $node, $context ) {

	$args = $node->getInputData( $context );
	$query = $args['query'];
	$args = $node->getInputData( $context );
	$query->set( $args['query_param'], $args['value'] );
	$node->next( $context, $args );
}

function triggerhappy_add_nav_menu_item( $node, $context ) {
	$args = $node->getInputData( $context );
	$args['nav_menu'] = $args['nav_menu'] . '<li><a title="' . esc_attr( $args['text'] ) . '" href="'. esc_url( $args['url'] ) .'">' . $args['text'] . '</a></li>';
	$node->setReturnData( $context, $args['nav_menu'] );
	$result = $node->next( $context, $args );
}
function triggerhappy_custom_column( $node, $context ) {
	add_action( 'manage_posts_custom_column', function ( $post ) use ( $node, $context ) {
		$passed_args = func_get_args();
		$args = new stdClass();
		if ( isset( $node->def['fields'] ) && isset( $node->def['fields']['start'] ) ) {
			foreach ( $node->def['fields']['start'] as $i => $f ) {
				$argIndex = isset( $f['argIndex'] ) && $f['argIndex'] > -1 ? $f['argIndex'] : $i;

				if ( isset( $passed_args[ $argIndex ] ) ) {
					$value = $passed_args[ $argIndex ];
					$value = apply_filters( 'triggerhappy_args_' . $f['type'], $value, $node, $context );
					if ( $argIndex == 0 ) {
						$first_arg_type_is_array = is_array( $passed_args[ $argIndex ] );
						if ( $first_arg_type_is_array ) {
							$value = (object) $value;
						}
						$first_arg = $value;
						$first_arg_name = $f['name'];
					}

					$args->{$f['name']} = $value;
				}
			}
		}
		$node->next( $context, $args );
	} );
}

function triggerhappy_condition( $node, $context ) {
	$node->next( $context, array() );
}
