<?php

class TriggerHappyContext {
	public $data;
	public $returnData;

	public function __construct() {
		$this->data = new stdClass();
		$this->returnData = null;
	}

	public function getData( $nodeId ) {
		if ( isset( $this->data->{$nodeId} ) ) {
			return $this->data->{$nodeId};
		}

		return null;
	}

	public function setData( $nodeId, $data ) {
		$this->data->{$nodeId} = $data;
	}
}
