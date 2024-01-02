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
		$this->instructions = $this->get_option( 'instructions' );
		$this->base_url = $this->get_option( 'base_url' );
		$this->api_key = $this->get_option( 'api_key' );
		$this->api_secret = $this->get_option( 'api_secret' );

        $this->apiRequest = new WC_Gateway_Tumeny_Api_Request($this->base_url, $this->api_key, $this->api_secret);

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'thankyou_page' ) );
		add_action( 'woocommerce_api_'. $this->id, array( $this, 'neo_wc_payment_callback' ) );
//		add_filter( 'woocommerce_payment_complete_order_status', array( $this, 'change_payment_complete_order_status' ), 10, 3 );

		// Customer Emails.
//		add_action( 'woocommerce_email_before_order_table', array( $this, 'email_instructions' ), 10, 3 );
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
			'instructions'       => array(
				'title'       => __( 'Instructions', 'wc-tumeny' ),
				'type'        => 'textarea',
				'description' => __( 'Instructions that will be added to the thank you page.', 'wc-tumeny' ),
				'default'     => __( 'Tumeny Mobile Money Payment before delivery.', 'wc-tumeny' ),
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

//        $api = new WC_Gateway_Tumeny_Api_Request();
//        $response = $api->get_token('https://0.0.0.0', '1a5c4775-2b61-430d-9029-9fa1775f7825', '159c9006efb086747987e449e80fa526299fe5c2');

//        $response = $response = wp_remote_post('https://0.0.0.0/api/token', array(
//            'method'      => 'POST',
//            'headers' => array(
//                'apiKey' => '1a5c4775-2b61-430d-9029-9fa1775f7825',
//                'apiSecret' => '159c9006efb086747987e449e80fa526299fe5c2',
//                'content-type' => 'application/json'
//            ),
//        ));


//        var_dump($order);
//        exit();

//		if ( $order->get_total() > 0 ) {
//			$this->neo_wc_gateway_process_tumeny_payment($order);
//		} else {
//			$order->payment_complete();
//		}
//
//		// Remove cart.
//		WC()->cart->empty_cart();

        $paymentId = $this->apiRequest->create_payment($order);

		// Return thankyou redirect.
		return array(
			'result'   => 'success',
			'redirect' => 'https://0.0.0.0/pay/'.$paymentId.'/new/payment?callback='.get_home_url().'/wc-api/tumeny?order_id='.$order_id,
//			'redirect' => $this->get_return_url( $order ),
		);
	}

	private function neo_wc_gateway_process_tumeny_payment($order) {

		
		// pending payment
		// $order->update_status( apply_filters( 'woocommerce_payleo_process_payment_order_status', $order->has_downloadable_item() ? 'wc-invoiced' : 'processing', $order ), __( 'Payments pending.', 'payleo-payments-woo' ) );
		
		// // If cleared
		// $order->payment_complete();
	}

	/**
	 * Output for the order received page.
	 */
	public function thankyou_page($order_id) {
//        $order = wc_get_order( $order_id );
//        $paymentId = $this->apiRequest->create_payment($order);

//        print_r(get_home_url());
//		if ( $this->instructions ) {
//			echo wp_kses_post( wpautop( wptexturize( $this->instructions ) ) );
//		}
	}

	/**
	 *
	 * @since  3.1.0
	 * @param  string         $status Current order status.
	 * @param  int            $order_id Order ID.
	 * @param  WC_Order|false $order Order object.
	 * @return string
	 */
	public function change_payment_complete_order_status( $status, $order_id = 0, $order = false ) {
		if ( $order && 'tumeny' === $order->get_payment_method() ) {
			$status = 'completed';
		}
		return $status;
	}

	/**
	 * Add content to the WC emails.
	 *
	 * @param WC_Order $order Order object.
	 * @param bool     $sent_to_admin  Sent to admin.
	 * @param bool     $plain_text Email format: plain text or HTML.
	 */
	public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {
		if ( $this->instructions && ! $sent_to_admin && $this->id === $order->get_payment_method() ) {
			echo wp_kses_post( wpautop( wptexturize( $this->instructions ) ) . PHP_EOL );
		}
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

            wp_redirect( $this->get_return_url( $order ) );
            exit();
        }
        if (paymentstatus::FAILED === $status) {
//            wp_redirect( $this->get_return_url( $order ) );
//            exit();
        }
    }
}