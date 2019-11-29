<?php

namespace HotSource\TriggerHappy\Nodes\Plugins\WooCommerce\Triggers;

use HotSource\TriggerHappy\Nodes\CoreNode;
use HotSource\TriggerHappy\Nodes\CoreTriggerNode;

class BeforeCheckoutForm extends CoreTriggerNode {

	/**
	 * BeforeCheckoutForm constructor.
	 */
	public function __construct() {
		$this->name = 'Before Checkout Form';
		$this->description = 'Before the checkout form is displayed';
		$this->cat = 'WooCommerce';
		$this->nodeType = $this->getNodeType();
		$this->plugin = 'WooCommerce';
		$this->callback = 'triggerhappy_filter_action';
		$this->hook = 'woocommerce_before_checkout_form';
	}

	/**
	 * @param CoreNode $node
	 * @param \TriggerHappyContext $context
	 * @param null $data
	 *
	 * @return void|null
	 */
	public function runCallback( $node, $context, $data = null ) {
		$this->actionHook( $node, $context );
	}
}
