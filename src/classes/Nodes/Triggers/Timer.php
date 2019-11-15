<?php


namespace HotSource\TriggerHappy\Nodes\Triggers;


use HotSource\TriggerHappy\NodeField;
use HotSource\TriggerHappy\Nodes\CoreNode;
use HotSource\TriggerHappy\Nodes\CoreTriggerNode;

class Timer extends CoreTriggerNode {

	/**
	 * Timer constructor.
	 */
	public function __construct() {
		$this->name = 'On Timer';
		$this->description = 'Run every x hours/days etc.';
		$this->cat = 'WordPress';
		$this->nodeType = $this->getNodeType();
		$this->fields = $this->generateFields();
		$this->plugin = 'wordpress';
		$this->callback = 'triggerhappy_action_hook';
	}

	/**
	 * @return NodeField[]
	 */
	public function generateFields() {
		return [
			new NodeField( 'hours', 'number' ),
		];
	}

	/**
	 * @param CoreNode $node
	 * @param \TriggerHappyContext $context
	 * @param null $data
	 *
	 * @return void|null
	 */
	public function runCallback( $node, $context, $data = null ) {
		$data['hook'] = 'triggerhappy_timer_trigger__' . $node->graph->id;
		$data['recurrence'] = 'Every ' . $data['hours'] . ' hours (TriggerHappy)';
		$data['slug'] = sanitize_title( $data['recurrence'] );

		add_filter( 'cron_schedules', function ( $schedules ) use ( $data ) {
			$schedules[ $data['slug'] ] = [
				'interval' => $data['hours'] * 60 * 60,
				'display'  => __( $data['recurrence'] ),
			];

			return $schedules;
		}
		);
		add_action( $data['hook'], function () use ( $node, $data, $context ) {
			echo "";
			return $node->next( $context );
		}
		);

		if ( ! wp_next_scheduled( $data['hook'] ) ) {
			wp_schedule_event( time(), $data['slug'], $data['hook'] );
		}
	}
}
