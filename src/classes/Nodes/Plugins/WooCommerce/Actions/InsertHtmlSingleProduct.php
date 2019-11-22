<?php

namespace HotSource\TriggerHappy\Nodes\Plugins\WooCommerce\Actions;

use HotSource\TriggerHappy\NodeField;
use HotSource\TriggerHappy\Nodes\CoreActionNode;
use HotSource\TriggerHappy\Nodes\CoreNode;

class InsertHtmlSingleProduct extends CoreActionNode {

	/**
	 * InsertHtmlSingleProduct constructor.
	 */
	public function __construct() {
		$this->name = 'Add HTML to Single Product template';
		$this->description = 'Add content to a single product page';
		$this->plugin = 'WooCommerce';
		$this->cat = 'WooCommerce';
		$this->actionType = 'product_render';
		$this->callback = 'triggerhappy_woocommerce_output_html_single_product';
		$this->nodeType = $this->getNodeType();
		$this->fields = $this->generateFields();
		$this->hook = '$section';
		$this->globals = [ 'product' => 'product' ];
	}

	/**
	 * @return NodeField[]
	 */
	public function generateFields() {
		return [
			new NodeField( 'product', 'wc_product', [ 'dir' => 'start' ] ),
			new NodeField( 'section', 'string', [
				'label'       => 'Section',
				'description' => 'Select the section of the page you attach this flow to',
				'dir'         => 'in',
				'choices'     => [
					[
						'id'   => 'woocommerce_before_main_content',
						'text' => 'Before the main content section',
					],
					[
						'id'   => 'woocommerce_after_main_content',
						'text' => 'After the main content section',
					],
					[
						'id'   => 'woocommerce_before_single_product',
						'text' => 'Top of the page (before product content)',
					],
					[
						'id'   => 'woocommerce_after_single_product',
						'text' => 'Bottom of the page (after product content)',
					],

					[
						'id'   => 'woocommerce_before_single_product_summary',
						'text' => 'Before the summary section',
					],
					[
						'id'   => 'woocommerce_single_product_summary:4',
						'text' => 'Before the product title',
					],

					[
						'id'   => 'woocommerce_single_product_summary:6',
						'text' => 'Before the product rating/price',
					],
					[
						'id'   => 'woocommerce_single_product_summary:15',
						'text' => 'Before the product excerpt',
					],
					[
						'id'   => 'woocommerce_single_product_summary:25',
						'text' => 'Before the add to cart buttons',
					],
					[
						'id'   => 'woocommerce_single_product_summary:35',
						'text' => 'After the add to cart buttons',
					],
					[
						'id'   => 'woocommerce_single_product_summary:45',
						'text' => 'Before the sharing buttons',
					],
					[
						'id'   => 'woocommerce_single_product_summary:55',
						'text' => 'After the sharing buttons',
					],
					[
						'id'   => 'woocommerce_after_single_product_summary:5',
						'text' => 'After the summary section - before tabs',
					],
					[
						'id'   => 'woocommerce_after_single_product_summary:25',
						'text' => 'After the summary section - after related products',
					],
				],
			] ),
			new NodeField( 'html', 'html', [
				'label'       => 'HTML',
				'description' => 'The HTML to be inserted',
				'dir'         => 'in',
			] ),
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
		$hook = isset( $this->hook ) ? $this->hook : $this->id;
		$priority = 10;
		if ( strpos( $hook, '$' ) === 0 ) {
			$hookField = substr( $hook, 1 );
			$inputData = $node->getInputData( $context );
			$hook = $inputData[ $hookField ];
			$hook_parts = explode( ':', $hook );
			$hook = $hook_parts[0];
			if ( count( $hook_parts ) > 1 ) {
				$priority = $hook_parts[1];
			}
		}

		add_action( $hook, function () use ( $hook, $node, $context ) {
			$data = $node->getInputData( $context );
			echo $data['html'];
		}, $priority );
	}
}