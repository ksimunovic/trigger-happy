<?php


namespace HotSource\TriggerHappy\Nodes\Triggers;


use HotSource\TriggerHappy\NodeField;
use HotSource\TriggerHappy\Nodes\CoreNode;
use HotSource\TriggerHappy\Nodes\CoreTriggerNode;

class Timer extends CoreTriggerNode {
	protected $creation_time;

	/**
	 * Timer constructor.
	 */
	public function __construct() {
		$this->name        = 'On Timer';
		$this->description = 'Run every x hours/days etc.';
		$this->cat         = 'WordPress';
		$this->nodeType    = $this->getNodeType();
		$this->fields      = $this->generateFields();
		$this->plugin      = 'wordpress';
		$this->callback    = 'triggerhappy_action_hook';
	}

	/**
	 * @return NodeField[]
	 */
	public function generateFields() {
		return [
			new NodeField( 'time_interval', 'string', [
				'label'       => 'Time interval',
				'description' => 'Enter the time interval in which you want set your timmer',
				'choices'     => triggerhappy_assoc_to_choices( triggerhappy_get_time_intervals() )
			] ),
			new NodeField( 'time_value', 'number', [
				'label'       => 'Time value',
				'description' => 'After how many time intervals of your choice you want to trigger the action.'
			] )
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
		$data['cron_hook']  = 'triggerhappy_timer_trigger__' . $node->graph->id;
		$data['recurrence'] = 'Every ' . $data['time_value'] . ' ' . $data['time_interval'] . ' (TriggerHappy)';
		$data['slug']       = sanitize_title( $data['recurrence'] );
		$data['interval'] = triggerhappy_get_cron_interval_from_time_interval_and_value( $data['time_interval'], $data['time_value'] );

		add_filter( 'cron_schedules', function ( $schedules ) use ( $data ) {
			$schedules[ $data['slug'] ] = [
				'interval' => $data['interval'],
				'display'  => __( $data['recurrence'] ),
			];

			return $schedules;
		}
		);

		add_action( $data['cron_hook'], function () use ( $node, $data, $context ) {
			return $node->next( $context );
		}
		);

		if ( ! wp_next_scheduled( $data['cron_hook'] ) ) {
			wp_schedule_event( time() + $data['interval'], $data['slug'], $data['cron_hook'] );
		}
	}
}
