<?php

class WC_Gateway_Tumeny extends WC_Payment_Gateway {

    private $apiRequest;
	public function __construct() {
		// Setup general properties.
		$this->setup_properties();

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Get settings.
		$this->title = $this->get_option( 'title' );
		$this->description = $this->get_option( 'description' );
		$this->base_url = $this->get_option( 'base_url' );
		$this->api_key = $this->get_option( 'api_key' );
		$this->api_secret = $this->get_option( 'api_secret' );

        $this->apiRequest = new WC_Gateway_Tumeny_Api_Request($this->base_url, $this->api_key, $this->api_secret);

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_api_'. $this->id, array( $this, 'neo_wc_payment_callback' ) );
	}

	/**
	 * Setup general properties for the gateway.
	 */
	protected function setup_properties() {
		$this->id                 = 'tumeny';
		$this->icon               = apply_filters( 'woocommerce_tumeny_icon', plugins_url('../assets/icon.png', __FILE__ ) );
		$this->method_title       = __( 'Mobile Money Payment by TuMeNy', 'wc-tumeny' );
		$this->method_description = __( 'Accepting Mobile Payment in Zambia.', 'wc-tumeny' );
		$this->has_fields         = false;
	}

	/**
	 * Initialise Gateway Settings Form Fields.
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled'            => array(
				'title'       => __( 'Enable/Disable', 'wc-tumeny' ),
				'label'       => __( 'Enable Tumeny Mobile Money Payment', 'wc-tumeny' ),
				'type'        => 'checkbox',
				'description' => '',
				'default'     => 'no',
			),
			'title'              => array(
				'title'       => __( 'Title', 'wc-tumeny' ),
				'type'        => 'text',
				'description' => __( 'Tumeny Mobile Money Payment method description that the customer will see on your checkout.', 'wc-tumeny' ),
				'default'     => __( 'Tumeny Mobile Money Payment', 'wc-tumeny' ),
				'desc_tip'    => true,
			),
			'description'        => array(
				'title'       => __( 'Description', 'wc-tumeny' ),
				'type'        => 'textarea',
				'description' => __( 'Tumeny Mobile Money Payment method description that the customer will see on your website.', 'wc-tumeny' ),
				'default'     => __( 'Tumeny Mobile Payments before delivery.', 'wc-tumeny' ),
				'desc_tip'    => true,
			),
            'base_url'         => array(
                'title'       => __( 'Base Url', 'wc-tumeny' ),
                'type'        => 'text',
                'description' => __( 'Tumeny Base Url', 'wc-tumeny' ),
                'desc_tip'    => true,
            ),
            'api_key'         => array(
                'title'       => __( 'API Key', 'wc-tumeny' ),
                'type'        => 'text',
                'description' => __( 'Tumeny API Key', 'wc-tumeny' ),
                'desc_tip'    => true,
            ),
            'api_secret'         => array(
                'title'       => __( 'API Secret', 'wc-tumeny' ),
                'type'        => 'text',
                'description' => __( 'Tumeny API Secret', 'wc-tumeny' ),
                'desc_tip'    => true,
            ),
		);
	}

	/**
	 * Process the payment and return the result.
	 *
	 * @param int $order_id Order ID.
	 * @return array
	 */
	public function process_payment( $order_id ) {
		$order = wc_get_order( $order_id );

		if ( $order->get_total() > 0 ) {
            $callback_url = get_home_url().'/wc-api/tumeny?order_id='.$order_id;
            $paymentId = $this->apiRequest->create_payment($order, $callback_url);
            $url = $this->base_url.'/pay/'.$paymentId.'/new/payment';
		} else {
			$order->payment_complete();
            WC()->cart->empty_cart();
            $url = $this->get_return_url( $order );
		}

		return array(
			'result'   => 'success',
			'redirect' => $url,
		);
	}

    public function neo_wc_payment_callback() {
        $order_id = isset($_GET['order_id']) ? $_GET['order_id'] : null;
        $payment_id = isset($_GET['paymentId']) ? $_GET['paymentId'] : null;

        $status = $this->apiRequest->get_payment_status($payment_id);

        if (paymentstatus::SUCCESS === $status) {
            $order = wc_get_order( $order_id );
            $order->payment_complete();
            wc_reduce_stock_levels($order_id);
            WC()->cart->empty_cart();

            wc_add_notice( 'Payment Successfully Completed' , 'success' );

            wp_redirect( $this->get_return_url( $order ) );
            exit();
        }
        if (paymentstatus::FAILED === $status) {
            wc_add_notice( 'Oops! Your Payment Failed - Please try again' , 'error' );
            wp_redirect( WC()->cart->get_checkout_url() );
            exit();
        }
    }
}