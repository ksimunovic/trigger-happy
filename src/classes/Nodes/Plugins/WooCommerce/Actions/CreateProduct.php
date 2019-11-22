<?php

namespace HotSource\TriggerHappy\Nodes\Plugins\WooCommerce\Actions;

use HotSource\TriggerHappy\NodeField;
use HotSource\TriggerHappy\Nodes\CoreActionNode;
use HotSource\TriggerHappy\Nodes\CoreNode;
use WP_REST_Request;

class CreateProduct extends CoreActionNode {

	/**
	 * CreateProduct constructor.
	 */
	public function __construct() {
		$this->name = 'Create a new product';
		$this->description = 'Creates (or updates) a product';
		$this->plugin = 'WooCommerce';
		$this->cat = 'Products';
		$this->actionType = 'render';
		$this->callback = 'triggerhappy_woocommerce_create_product';
		$this->nodeType = $this->getNodeType();
		$this->fields = $this->generateFields();
	}

	/**
	 * @return NodeField[]
	 */
	public function generateFields() {
		return [
			new NodeField( 'id', 'string', [
				'label'       => 'Product ID',
				'description' => 'Specify the product ID to update an existing product. Leave blank to create a new product',
				'dir'         => 'in',
			] ),
			new NodeField( 'name', 'string', [
				'label' => 'Product Name',
				'dir'   => 'in',
			] ),
			new NodeField( 'description', 'html', [
				'label' => 'Description',
				'dir'   => 'in',
			] ),
			new NodeField( 'regular_price', 'number', [
				'label' => 'Price',
				'dir'   => 'in',
			] ),
			new NodeField( 'stock_quantity', 'number', [
				'label' => 'Stock Quantity',
				'dir'   => 'in',
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
		$id = isset( $data['id'] ) ? $data['id'] : "";
		$data_to_save = [];
		foreach ( $data as $key => $value ) {
			if ( isset( $value ) && ! empty( $value ) ) {
				$data_to_save[ $key ] = $value;
			}
		}

		$req = new WP_REST_Request( 'POST', '/wc/v2/products' . ( ! empty( $id ) ? '/' . $id : '' ) );
		$req->set_header( 'Content-Type', 'application/json' );
		$req->set_body( json_encode( $data_to_save ) );

		add_filter( 'woocommerce_rest_check_permissions', '__return_true' );
		$response = rest_do_request( $req );
		remove_filter( 'woocommerce_rest_check_permissions', '__return_true' );

		$data = ( $response->get_data() );
		$node->next( $context, $data );
	}
}
