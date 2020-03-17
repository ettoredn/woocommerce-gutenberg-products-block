<?php
/**
 * Cart schema.
 *
 * @package WooCommerce/Blocks
 */

namespace Automattic\WooCommerce\Blocks\RestApi\StoreApi\Schemas;

use Automattic\WooCommerce\Blocks\RestApi\StoreApi\Utilities\CartController;

defined( 'ABSPATH' ) || exit;

/**
 * CartSchema class.
 *
 * @since 2.5.0
 */
class CartSchema extends AbstractSchema {
	/**
	 * The schema item name.
	 *
	 * @var string
	 */
	protected $title = 'cart';

	/**
	 * Cart item schema.
	 *
	 * @var AbstractSchema
	 */
	public $item_schema;

	/**
	 * Coupon schema.
	 *
	 * @var AbstractSchema
	 */
	public $coupon_schema;

	/**
	 * Shipping rate schema.
	 *
	 * @var AbstractSchema
	 */
	public $shipping_rate_schema;

	/**
	 * Shipping address schema.
	 *
	 * @var AbstractSchema
	 */
	public $shipping_address_schema;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->item_schema             = new CartItemSchema();
		$this->coupon_schema           = new CartCouponSchema();
		$this->shipping_rate_schema    = new CartShippingRateSchema();
		$this->shipping_address_schema = new ShippingAddressSchema();
	}

	/**
	 * Cart schema properties.
	 *
	 * @return array
	 */
	public function get_properties() {
		return [
			'order_id'         => [
				'description' => __( 'The draft order ID associated with this cart if one has been created. 0 if no draft exists.', 'woo-gutenberg-products-block' ),
				'type'        => 'integer',
				'context'     => [ 'view', 'edit' ],
				'readonly'    => true,
			],
			'coupons'          => [
				'description' => __( 'List of applied cart coupons.', 'woo-gutenberg-products-block' ),
				'type'        => 'array',
				'context'     => [ 'view', 'edit' ],
				'readonly'    => true,
				'items'       => [
					'type'       => 'object',
					'properties' => $this->force_schema_readonly( $this->coupon_schema->get_properties() ),
				],
			],
			'shipping_rates'   => [
				'description' => __( 'List of available shipping rates for the cart.', 'woo-gutenberg-products-block' ),
				'type'        => 'array',
				'context'     => [ 'view', 'edit' ],
				'readonly'    => true,
				'items'       => [
					'type'       => 'object',
					'properties' => $this->force_schema_readonly( $this->shipping_rate_schema->get_properties() ),
				],
			],
			'shipping_address' => [
				'description' => __( 'Current set shipping address for the customer.', 'woo-gutenberg-products-block' ),
				'type'        => 'object',
				'context'     => [ 'view', 'edit' ],
				'readonly'    => true,
				'items'       => [
					'type'       => 'object',
					'properties' => $this->force_schema_readonly( $this->shipping_address_schema->get_properties() ),
				],
			],
			'items'            => [
				'description' => __( 'List of cart items.', 'woo-gutenberg-products-block' ),
				'type'        => 'array',
				'context'     => [ 'view', 'edit' ],
				'readonly'    => true,
				'items'       => [
					'type'       => 'object',
					'properties' => $this->force_schema_readonly( $this->item_schema->get_properties() ),
				],
			],
			'items_count'      => [
				'description' => __( 'Number of items in the cart.', 'woo-gutenberg-products-block' ),
				'type'        => 'integer',
				'context'     => [ 'view', 'edit' ],
				'readonly'    => true,
			],
			'items_weight'     => [
				'description' => __( 'Total weight (in grams) of all products in the cart.', 'woo-gutenberg-products-block' ),
				'type'        => 'number',
				'context'     => [ 'view', 'edit' ],
				'readonly'    => true,
			],
			'needs_shipping'   => [
				'description' => __( 'True if the cart needs shipping. False for carts with only digital goods or stores with no shipping methods set-up.', 'woo-gutenberg-products-block' ),
				'type'        => 'boolean',
				'context'     => [ 'view', 'edit' ],
				'readonly'    => true,
			],
			'totals'           => [
				'description' => __( 'Cart total amounts provided using the smallest unit of the currency.', 'woo-gutenberg-products-block' ),
				'type'        => 'object',
				'context'     => [ 'view', 'edit' ],
				'readonly'    => true,
				'properties'  => array_merge(
					$this->get_store_currency_properties(),
					[
						'total_items'        => [
							'description' => __( 'Total price of items in the cart.', 'woo-gutenberg-products-block' ),
							'type'        => 'string',
							'context'     => [ 'view', 'edit' ],
							'readonly'    => true,
						],
						'total_items_tax'    => [
							'description' => __( 'Total tax on items in the cart.', 'woo-gutenberg-products-block' ),
							'type'        => 'string',
							'context'     => [ 'view', 'edit' ],
							'readonly'    => true,
						],
						'total_fees'         => [
							'description' => __( 'Total price of any applied fees.', 'woo-gutenberg-products-block' ),
							'type'        => 'string',
							'context'     => [ 'view', 'edit' ],
							'readonly'    => true,
						],
						'total_fees_tax'     => [
							'description' => __( 'Total tax on fees.', 'woo-gutenberg-products-block' ),
							'type'        => 'string',
							'context'     => [ 'view', 'edit' ],
							'readonly'    => true,
						],
						'total_discount'     => [
							'description' => __( 'Total discount from applied coupons.', 'woo-gutenberg-products-block' ),
							'type'        => 'string',
							'context'     => [ 'view', 'edit' ],
							'readonly'    => true,
						],
						'total_discount_tax' => [
							'description' => __( 'Total tax removed due to discount from applied coupons.', 'woo-gutenberg-products-block' ),
							'type'        => 'string',
							'context'     => [ 'view', 'edit' ],
							'readonly'    => true,
						],
						'total_shipping'     => [
							'description' => __( 'Total price of shipping.', 'woo-gutenberg-products-block' ),
							'type'        => 'string',
							'context'     => [ 'view', 'edit' ],
							'readonly'    => true,
						],
						'total_shipping_tax' => [
							'description' => __( 'Total tax on shipping.', 'woo-gutenberg-products-block' ),
							'type'        => 'string',
							'context'     => [ 'view', 'edit' ],
							'readonly'    => true,
						],
						'total_price'        => [
							'description' => __( 'Total price the customer will pay.', 'woo-gutenberg-products-block' ),
							'type'        => 'string',
							'context'     => [ 'view', 'edit' ],
							'readonly'    => true,
						],
						'total_tax'          => [
							'description' => __( 'Total tax applied to items and shipping.', 'woo-gutenberg-products-block' ),
							'type'        => 'string',
							'context'     => [ 'view', 'edit' ],
							'readonly'    => true,
						],
						'tax_lines'          => [
							'description' => __( 'Lines of taxes applied to items and shipping.', 'woo-gutenberg-products-block' ),
							'type'        => 'array',
							'context'     => [ 'view', 'edit' ],
							'readonly'    => true,
							'items'       => [
								'type'       => 'object',
								'properties' => [
									'name'  => [
										'description' => __( 'The name of the tax.', 'woo-gutenberg-products-block' ),
										'type'        => 'string',
										'context'     => [ 'view', 'edit' ],
										'readonly'    => true,
									],
									'price' => [
										'description' => __( 'The amount of tax charged.', 'woo-gutenberg-products-block' ),
										'type'        => 'string',
										'context'     => [ 'view', 'edit' ],
										'readonly'    => true,
									],
								],
							],
						],
					]
				),
			],
		];
	}

	/**
	 * Convert a woo cart into an object suitable for the response.
	 *
	 * @param \WC_Cart $cart Cart class instance.
	 * @return array
	 */
	public function get_item_response( $cart ) {
		$controller = new CartController();
		$context    = 'edit';

		return [
			'order_id'         => $this->get_order_id(),
			'coupons'          => array_values( array_map( [ $this->coupon_schema, 'get_item_response' ], array_filter( $cart->get_applied_coupons() ) ) ),
			'shipping_rates'   => array_values( array_map( [ $this->shipping_rate_schema, 'get_item_response' ], $controller->get_shipping_packages() ) ),
			'shipping_address' => $this->shipping_address_schema->get_item_response( WC()->customer ),
			'items'            => array_values( array_map( [ $this->item_schema, 'get_item_response' ], array_filter( $cart->get_cart() ) ) ),
			'items_count'      => $cart->get_cart_contents_count(),
			'items_weight'     => wc_get_weight( $cart->get_cart_contents_weight(), 'g' ),
			'needs_shipping'   => $cart->needs_shipping(),
			'totals'           => (object) array_merge(
				$this->get_store_currency_response(),
				[
					'total_items'        => $this->prepare_money_response( $cart->get_subtotal(), wc_get_price_decimals() ),
					'total_items_tax'    => $this->prepare_money_response( $cart->get_subtotal_tax(), wc_get_price_decimals() ),
					'total_fees'         => $this->prepare_money_response( $cart->get_fee_total(), wc_get_price_decimals() ),
					'total_fees_tax'     => $this->prepare_money_response( $cart->get_fee_tax(), wc_get_price_decimals() ),
					'total_discount'     => $this->prepare_money_response( $cart->get_discount_total(), wc_get_price_decimals() ),
					'total_discount_tax' => $this->prepare_money_response( $cart->get_discount_tax(), wc_get_price_decimals() ),
					'total_shipping'     => $this->prepare_money_response( $cart->get_shipping_total(), wc_get_price_decimals() ),
					'total_shipping_tax' => $this->prepare_money_response( $cart->get_shipping_tax(), wc_get_price_decimals() ),

					// Explicitly request context='edit'; default ('view') will render total as markup.
					'total_price'        => $this->prepare_money_response( $cart->get_total( $context ), wc_get_price_decimals() ),
					'total_tax'          => $this->prepare_money_response( $cart->get_total_tax(), wc_get_price_decimals() ),
					'tax_lines'          => $this->get_tax_lines( $cart ),
				]
			),
		];
	}

	/**
	 * Get a draft order ID from the session for current cart.
	 *
	 * @return int Draft order ID, or 0 if there isn't one yet.
	 */
	protected function get_order_id() {
		$draft_order_session = WC()->session->get( 'store_api_draft_order' );
		$draft_order_id      = isset( $draft_order_session['id'] ) ? absint( $draft_order_session['id'] ) : 0;
		$draft_order         = $draft_order_id ? wc_get_order( $draft_order_id ) : false;

		if ( $draft_order && $draft_order->has_status( 'checkout-draft' ) && 'store-api' === $draft_order->get_created_via() ) {
			return $draft_order_id;
		}

		return 0;
	}

	/**
	 * Get tax lines from the cart and format to match schema.
	 *
	 * @param \WC_Cart $cart Cart class instance.
	 * @return array
	 */
	protected function get_tax_lines( $cart ) {
		$cart_tax_totals = $cart->get_tax_totals();
		$tax_lines       = [];

		foreach ( $cart_tax_totals as $cart_tax_total ) {
			$tax_lines[] = array(
				'name'  => $cart_tax_total->label,
				'price' => $this->prepare_money_response( $cart_tax_total->amount, wc_get_price_decimals() ),
			);
		}

		return $tax_lines;
	}
}
