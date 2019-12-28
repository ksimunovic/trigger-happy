<?php


namespace HotSource\TriggerHappy\Nodes\Actions;


use HotSource\TriggerHappy\NodeField;
use HotSource\TriggerHappy\Nodes\CoreActionNode;
use HotSource\TriggerHappy\Nodes\CoreNode;

class Delay extends CoreActionNode {
	/**
	 * Delay constructor.
	 */
	public function __construct() {
		$this->name        = 'Delay';
		$this->description = 'Run action after x hours/days etc.';
		$this->cat         = 'WordPress';
		$this->nodeType    = $this->getNodeType();
		$this->fields      = $this->generateFields();
		$this->plugin      = 'wordpress';
		$this->callback    = 'triggerhappy_wc_delay';
	}

	/**
	 * @return NodeField[]
	 */
	public function generateFields() {
		return [
			new NodeField( 'time_interval', 'string', [
				'label'       => 'Time interval',
				'description' => 'Enter the time interval in which you want set your delay',
				'choices'     => triggerhappy_assoc_to_choices( triggerhappy_get_time_intervals() )
			] ),
			new NodeField( 'time_value', 'number', [
				'label'       => 'Time value',
				'description' => 'Hw many time intervals of your choice you want to delay the action.'
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
		$data['interval']   = triggerhappy_get_cron_interval_from_time_interval_and_value( $data['time_interval'], $data['time_value'] );

		wp_schedule_single_event( time() + $data['interval'], 'triggerhappy_single_event', [$node, $data, $context]);
	}
}