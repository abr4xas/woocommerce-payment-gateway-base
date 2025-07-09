<?php
/**
 * MyPlugin Payment Gateway
 *
 * @package MyPlugin_Payment_Gateway
 */

declare(strict_types=1);

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * MyPlugin Payment Gateway Class
 */
class MyPlugin_Gateway extends WC_Payment_Gateway
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->id = 'myplugin_gateway';
        $this->icon = '';
        $this->has_fields = true;
        $this->method_title = __('MyPlugin Payment Gateway', 'myplugin-payment-gateway');
        $this->method_description = __('Accept payments through MyPlugin payment gateway.', 'myplugin-payment-gateway');
        $this->supports = ['products', 'refunds', 'tokenization', 'block'];

        // Load settings
        $this->init_form_fields();
        $this->init_settings();

        // Define properties
        $this->title = $this->get_option('title');
        $this->description = $this->get_option('description');
        $this->enabled = $this->get_option('enabled');
        $this->sandbox_mode = 'yes' === $this->get_option('sandbox_mode');
        $this->api_key = $this->sandbox_mode ? $this->get_option('sandbox_api_key') : $this->get_option('api_key');
        $this->secret_key = $this->sandbox_mode ? $this->get_option('sandbox_secret_key') : $this->get_option('secret_key');

        // Hooks
        add_action('woocommerce_update_options_payment_gateways_' . $this->id, [$this, 'process_admin_options']);
        add_action('woocommerce_api_myplugin_webhook', [$this, 'handle_webhook']);
        add_action('admin_notices', [$this, 'admin_notices']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
    }

    /**
     * Initialize form fields
     */
    public function init_form_fields(): void
    {
        $this->form_fields = [
            'enabled' => [
                'title' => __('Enable/Disable', 'myplugin-payment-gateway'),
                'type' => 'checkbox',
                'label' => __('Enable MyPlugin Payment Gateway', 'myplugin-payment-gateway'),
                'default' => 'no',
            ],
            'title' => [
                'title' => __('Title', 'myplugin-payment-gateway'),
                'type' => 'text',
                'description' => __('Payment method title that the customer sees during checkout.', 'myplugin-payment-gateway'),
                'default' => __('MyPlugin Payment', 'myplugin-payment-gateway'),
                'desc_tip' => true,
            ],
            'description' => [
                'title' => __('Description', 'myplugin-payment-gateway'),
                'type' => 'textarea',
                'description' => __('Payment method description that the customer sees during checkout.', 'myplugin-payment-gateway'),
                'default' => __('Pay securely using MyPlugin payment gateway.', 'myplugin-payment-gateway'),
                'desc_tip' => true,
            ],
            'sandbox_mode' => [
                'title' => __('Sandbox Mode', 'myplugin-payment-gateway'),
                'type' => 'checkbox',
                'label' => __('Enable Sandbox Mode', 'myplugin-payment-gateway'),
                'description' => __('Place the payment gateway in sandbox mode for testing.', 'myplugin-payment-gateway'),
                'default' => 'yes',
                'desc_tip' => true,
            ],
            'api_key' => [
                'title' => __('Live API Key', 'myplugin-payment-gateway'),
                'type' => 'text',
                'description' => __('Enter your MyPlugin live API key.', 'myplugin-payment-gateway'),
                'default' => '',
                'desc_tip' => true,
            ],
            'secret_key' => [
                'title' => __('Live Secret Key', 'myplugin-payment-gateway'),
                'type' => 'password',
                'description' => __('Enter your MyPlugin live secret key.', 'myplugin-payment-gateway'),
                'default' => '',
                'desc_tip' => true,
            ],
            'sandbox_api_key' => [
                'title' => __('Sandbox API Key', 'myplugin-payment-gateway'),
                'type' => 'text',
                'description' => __('Enter your MyPlugin sandbox API key.', 'myplugin-payment-gateway'),
                'default' => '',
                'desc_tip' => true,
            ],
            'sandbox_secret_key' => [
                'title' => __('Sandbox Secret Key', 'myplugin-payment-gateway'),
                'type' => 'password',
                'description' => __('Enter your MyPlugin sandbox secret key.', 'myplugin-payment-gateway'),
                'default' => '',
                'desc_tip' => true,
            ],
        ];
    }

    /**
     * Validate settings
     */
    public function validate_settings(): array
    {
        $errors = [];

        if (empty($this->api_key)) {
            $errors[] = __('API Key is required', 'myplugin-payment-gateway');
        }

        if (empty($this->secret_key)) {
            $errors[] = __('Secret Key is required', 'myplugin-payment-gateway');
        }

        if ($this->sandbox_mode) {
            if (empty($this->get_option('sandbox_api_key'))) {
                $errors[] = __('Sandbox API Key is required when sandbox mode is enabled', 'myplugin-payment-gateway');
            }

            if (empty($this->get_option('sandbox_secret_key'))) {
                $errors[] = __('Sandbox Secret Key is required when sandbox mode is enabled', 'myplugin-payment-gateway');
            }
        }

        return $errors;
    }

    /**
     * Display admin notices
     */
    public function admin_notices(): void
    {
        // Only show notices on WooCommerce settings pages
        if (!isset($_GET['page']) || $_GET['page'] !== 'wc-settings') {
            return;
        }

        if (!isset($_GET['section']) || $_GET['section'] !== $this->id) {
            return;
        }

        $errors = $this->validate_settings();

        if (!empty($errors)) {
            echo '<div class="notice notice-error"><p><strong>' . esc_html__('MyPlugin Payment Gateway Configuration Error:', 'myplugin-payment-gateway') . '</strong></p><ul>';
            foreach ($errors as $error) {
                echo '<li>' . esc_html($error) . '</li>';
            }
            echo '</ul></div>';
        }

        // Show sandbox mode notice
        if ($this->sandbox_mode && $this->enabled === 'yes') {
            echo '<div class="notice notice-warning"><p>' . esc_html__('MyPlugin Payment Gateway is running in sandbox mode. No real payments will be processed.', 'myplugin-payment-gateway') . '</p></div>';
        }

        // Show webhook URL notice
        if ($this->enabled === 'yes' && !empty($this->api_key)) {
            $webhook_url = home_url('/wc-api/myplugin_webhook');
            echo '<div class="notice notice-info"><p><strong>' . esc_html__('Webhook URL:', 'myplugin-payment-gateway') . '</strong> ' . esc_url($webhook_url) . '</p><p>' . esc_html__('Configure this URL in your MyPlugin account webhook settings.', 'myplugin-payment-gateway') . '</p></div>';
        }
    }

    /**
     * Enqueue scripts and styles
     */
    public function enqueue_scripts(): void
    {
        if (!is_checkout()) {
            return;
        }

        wp_enqueue_style(
            'myplugin-checkout',
            MYPLUGIN_PLUGIN_URL . 'assets/css/checkout.css',
            [],
            MYPLUGIN_VERSION
        );
    }

    /**
     * Payment fields on checkout
     */
    public function payment_fields(): void
    {
        $description = $this->get_description();

        if ($description) {
            echo '<div class="myplugin-payment-description">' . wp_kses_post(wpautop($description)) . '</div>';
        }

        // Add card fields
        $this->credit_card_form();
    }

    /**
     * Credit card form fields
     */
    public function credit_card_form($args = [], $fields = []): void
    {
        wp_enqueue_script('wc-credit-card-form');

        $default_args = [
            'fields_have_names' => true,
        ];

        $args = wp_parse_args($args, apply_filters('woocommerce_credit_card_form_args', $default_args, $this->id));

        // Use WooCommerce's native method for generating credit card form fields
        $this->generate_credit_card_form_fields($args, $fields);
    }

    /**
     * Generate credit card form fields using WooCommerce's native method
     */
    private function generate_credit_card_form_fields($args, $fields): void
    {
        // Use WooCommerce's built-in field generation with proper structure
        $default_fields = [
            'card-number-field' => [
                'type' => 'text',
                'label' => __('Card Number', 'myplugin-payment-gateway'),
                'required' => true,
                'class' => ['form-row-wide'],
                'input_class' => ['input-text', 'wc-credit-card-form-card-number'],
                'maxlength' => 20,
                'autocomplete' => 'cc-number',
                'placeholder' => '•••• •••• •••• ••••',
            ],
            'card-expiry-field' => [
                'type' => 'text',
                'label' => __('Expiry (MM/YY)', 'myplugin-payment-gateway'),
                'required' => true,
                'class' => ['form-row-first'],
                'input_class' => ['input-text', 'wc-credit-card-form-card-expiry'],
                'autocomplete' => 'cc-exp',
                'placeholder' => 'MM / YY',
            ],
            'card-cvc-field' => [
                'type' => 'text',
                'label' => __('Card Code', 'myplugin-payment-gateway'),
                'required' => true,
                'class' => ['form-row-last'],
                'input_class' => ['input-text', 'wc-credit-card-form-card-cvc'],
                'maxlength' => 4,
                'autocomplete' => 'cc-csc',
                'placeholder' => 'CVC',
            ],
        ];

        $fields = wp_parse_args($fields, apply_filters('woocommerce_credit_card_form_fields', $default_fields, $this->id));

        // Generate the form using WooCommerce's native structure
        echo '<fieldset id="wc-' . esc_attr($this->id) . '-cc-form" class="wc-credit-card-form wc-payment-form">';
        do_action('woocommerce_credit_card_form_start', $this->id);

        foreach ($fields as $field_key => $field_config) {
            $field_name = $this->field_name(str_replace('-field', '', $field_key));
            echo woocommerce_form_field($field_name, $field_config);
        }

        do_action('woocommerce_credit_card_form_end', $this->id);
        echo '<div class="clear"></div></fieldset>';
    }

    /**
     * Get field name
     */
    public function field_name($name): string
    {
        return $this->supports('tokenization') ? $name : $this->id . '-' . $name;
    }

    /**
     * Check if gateway is available
     */
    public function is_available(): bool
    {
        if (!parent::is_available()) {
            return false;
        }

        if (empty($this->api_key) || empty($this->secret_key)) {
            return false;
        }

        return true;
    }

    /**
     * Process payment
     */
    public function process_payment($order_id): array
    {
        $order = wc_get_order($order_id);

        if (!$order) {
            return [
                'result' => 'failure',
                'redirect' => '',
            ];
        }

        try {
            // Validate card data
            $card_data = $this->get_card_data();

            if (!$card_data) {
                throw new Exception(__('Please enter your card details.', 'myplugin-payment-gateway'));
            }

            // Validate card fields
            $validation_errors = $this->validate_card_fields($card_data);

            if (!empty($validation_errors)) {
                throw new Exception(implode(' ', $validation_errors));
            }

            // Create payment request with card data
            $payment_data = $this->create_payment_request($order, $card_data);

            // Send request to payment gateway
            $response = $this->send_payment_request($payment_data);

            if ($response['success']) {
                // Payment successful
                $order->payment_complete($response['transaction_id']);
                $order->add_order_note(
                    sprintf(
                        __('Payment completed via MyPlugin. Transaction ID: %s', 'myplugin-payment-gateway'),
                        $response['transaction_id']
                    )
                );

                // Store transaction data
                update_post_meta($order_id, '_myplugin_transaction_id', $response['transaction_id']);

                // Clear cart
                WC()->cart->empty_cart();

                return [
                    'result' => 'success',
                    'redirect' => $this->get_return_url($order),
                ];
            } else {
                // Payment failed
                $order->update_status('failed', __('Payment failed: ' . $response['message'], 'myplugin-payment-gateway'));

                return [
                    'result' => 'failure',
                    'redirect' => '',
                ];
            }
        } catch (Exception $e) {
            $order->update_status('failed', __('Payment error: ' . $e->getMessage(), 'myplugin-payment-gateway'));

            return [
                'result' => 'failure',
                'redirect' => '',
            ];
        }
    }

    /**
     * Get card data from POST
     */
    private function get_card_data(): ?array
    {
        $card_number = sanitize_text_field($_POST[$this->field_name('card-number')] ?? '');
        $card_expiry = sanitize_text_field($_POST[$this->field_name('card-expiry')] ?? '');
        $card_cvc = sanitize_text_field($_POST[$this->field_name('card-cvc')] ?? '');

        if (empty($card_number) || empty($card_expiry) || empty($card_cvc)) {
            return null;
        }

        return [
            'number' => $card_number,
            'expiry' => $card_expiry,
            'cvc' => $card_cvc,
        ];
    }

    /**
     * Validate card fields
     */
    private function validate_card_fields(array $card_data): array
    {
        $errors = [];

        // Validate card number
        if (!$this->is_valid_card_number($card_data['number'])) {
            $errors[] = __('Please enter a valid card number.', 'myplugin-payment-gateway');
        }

        // Validate expiry date
        if (!$this->is_valid_expiry_date($card_data['expiry'])) {
            $errors[] = __('Please enter a valid expiry date (MM/YY).', 'myplugin-payment-gateway');
        }

        // Validate CVC
        if (!$this->is_valid_cvc($card_data['cvc'])) {
            $errors[] = __('Please enter a valid card security code.', 'myplugin-payment-gateway');
        }

        return $errors;
    }

    /**
     * Validate card number using Luhn algorithm
     */
    private function is_valid_card_number(string $number): bool
    {
        $number = preg_replace('/\s+/', '', $number);

        if (!preg_match('/^\d{13,19}$/', $number)) {
            return false;
        }

        $sum = 0;
        $length = strlen($number);
        $parity = $length % 2;

        for ($i = 0; $i < $length; $i++) {
            $digit = $number[$i];
            if ($i % 2 == $parity) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
            $sum += $digit;
        }

        return ($sum % 10) == 0;
    }

    /**
     * Validate expiry date
     */
    private function is_valid_expiry_date(string $expiry): bool
    {
        $expiry = preg_replace('/\s+/', '', $expiry);

        if (!preg_match('/^(\d{2})\/(\d{2})$/', $expiry, $matches)) {
            return false;
        }

        $month = (int) $matches[1];
        $year = (int) $matches[2];

        if ($month < 1 || $month > 12) {
            return false;
        }

        $current_year = (int) date('y');
        $current_month = (int) date('m');

        if ($year < $current_year || ($year == $current_year && $month < $current_month)) {
            return false;
        }

        return true;
    }

    /**
     * Validate CVC
     */
    private function is_valid_cvc(string $cvc): bool
    {
        return preg_match('/^\d{3,4}$/', $cvc);
    }

    /**
     * Create payment request data
     */
    private function create_payment_request(WC_Order $order, array $card_data): array
    {
        return [
            'amount' => $order->get_total() * 100, // Convert to cents
            'currency' => $order->get_currency(),
            'order_id' => $order->get_id(),
            'customer_email' => $order->get_billing_email(),
            'customer_name' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
            'card_number' => $card_data['number'],
            'card_expiry' => $card_data['expiry'],
            'card_cvc' => $card_data['cvc'],
            'return_url' => $this->get_return_url($order),
            'cancel_url' => $order->get_cancel_order_url(),
            'webhook_url' => home_url('/wc-api/myplugin_webhook'),
        ];
    }

    /**
     * Send payment request to gateway
     */
    private function send_payment_request(array $data): array
    {
        $api_url = $this->sandbox_mode
            ? 'https://api-sandbox.myplugin.com/payments'
            : 'https://api.myplugin.com/payments';

        $headers = [
            'Authorization' => 'Bearer ' . $this->api_key,
            'Content-Type' => 'application/json',
        ];

        $response = wp_remote_post($api_url, [
            'headers' => $headers,
            'body' => wp_json_encode($data),
            'timeout' => 30,
        ]);

        if (is_wp_error($response)) {
            throw new Exception($response->get_error_message());
        }

        $response_code = wp_remote_retrieve_response_code($response);
        $response_data = json_decode(wp_remote_retrieve_body($response), true);

        if ($response_code === 200 && $response_data) {
            return [
                'success' => true,
                'transaction_id' => $response_data['transaction_id'] ?? '',
                'message' => $response_data['message'] ?? 'Payment successful',
            ];
        } else {
            return [
                'success' => false,
                'message' => $response_data['message'] ?? 'Payment failed',
            ];
        }
    }

    /**
     * Handle webhook
     */
    public function handle_webhook(): void
    {
        $payload = file_get_contents('php://input');
        $data = json_decode($payload, true);

        if (!$data) {
            wp_die('Invalid webhook data', 'Webhook Error', ['response' => 400]);
        }

        // Verify webhook signature
        if (!$this->verify_webhook_signature($payload)) {
            wp_die('Invalid webhook signature', 'Webhook Error', ['response' => 401]);
        }

        $order_id = $data['order_id'] ?? 0;
        $order = wc_get_order($order_id);

        if (!$order) {
            wp_die('Order not found', 'Webhook Error', ['response' => 404]);
        }

        $status = $data['status'] ?? '';
        $transaction_id = $data['transaction_id'] ?? '';

        switch ($status) {
            case 'completed':
                $order->payment_complete($transaction_id);
                $order->add_order_note(
                    sprintf(
                        __('Payment confirmed via webhook. Transaction ID: %s', 'myplugin-payment-gateway'),
                        $transaction_id
                    )
                );
                break;

            case 'failed':
                $order->update_status('failed', __('Payment failed via webhook', 'myplugin-payment-gateway'));
                break;
        }

        wp_die('OK', 'Webhook Processed', ['response' => 200]);
    }

    /**
     * Verify webhook signature
     */
    private function verify_webhook_signature(string $payload): bool
    {
        $signature = $_SERVER['HTTP_X_MYPLUGIN_SIGNATURE'] ?? '';

        if (empty($signature)) {
            return false;
        }

        $expected_signature = hash_hmac('sha256', $payload, $this->secret_key);

        return hash_equals($expected_signature, $signature);
    }

    /**
     * Process refund
     */
    public function process_refund($order_id, $amount = null, $reason = ''): bool
    {
        $order = wc_get_order($order_id);

        if (!$order) {
            return false;
        }

        $transaction_id = get_post_meta($order_id, '_myplugin_transaction_id', true);

        if (empty($transaction_id)) {
            return false;
        }

        try {
            $refund_data = [
                'transaction_id' => $transaction_id,
                'amount' => $amount * 100, // Convert to cents
                'reason' => $reason,
            ];

            $api_url = $this->sandbox_mode
                ? 'https://api-sandbox.myplugin.com/refunds'
                : 'https://api.myplugin.com/refunds';

            $headers = [
                'Authorization' => 'Bearer ' . $this->api_key,
                'Content-Type' => 'application/json',
            ];

            $response = wp_remote_post($api_url, [
                'headers' => $headers,
                'body' => wp_json_encode($refund_data),
                'timeout' => 30,
            ]);

            if (is_wp_error($response)) {
                return false;
            }

            $response_code = wp_remote_retrieve_response_code($response);
            $response_data = json_decode(wp_remote_retrieve_body($response), true);

            if ($response_code === 200 && $response_data) {
                $order->add_order_note(
                    sprintf(
                        __('Refund processed via MyPlugin. Refund ID: %s', 'myplugin-payment-gateway'),
                        $response_data['refund_id'] ?? ''
                    )
                );
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}