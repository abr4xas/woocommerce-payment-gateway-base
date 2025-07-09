<?php
/**
 * MyPlugin Validator
 *
 * @package MyPlugin_Payment_Gateway
 */

declare(strict_types=1);

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * MyPlugin Validator Class
 */
class MyPlugin_Validator
{
    /**
     * Validate order data
     */
    public static function validate_order(WC_Order $order): array
    {
        $errors = [];

        if (!$order->get_total() || $order->get_total() <= 0) {
            $errors[] = __('Order total must be greater than zero', 'myplugin-payment-gateway');
        }

        if (!$order->get_billing_email()) {
            $errors[] = __('Billing email is required', 'myplugin-payment-gateway');
        }

        if (!$order->get_billing_country()) {
            $errors[] = __('Billing country is required', 'myplugin-payment-gateway');
        }

        if (!$order->get_billing_first_name()) {
            $errors[] = __('Billing first name is required', 'myplugin-payment-gateway');
        }

        if (!$order->get_billing_last_name()) {
            $errors[] = __('Billing last name is required', 'myplugin-payment-gateway');
        }

        return $errors;
    }

    /**
     * Validate API response
     */
    public static function validate_api_response($response): bool
    {
        if (is_wp_error($response)) {
            return false;
        }

        $status_code = wp_remote_retrieve_response_code($response);
        return $status_code >= 200 && $status_code < 300;
    }

    /**
     * Validate API response data
     */
    public static function validate_api_response_data(array $data): array
    {
        $errors = [];

        if (empty($data)) {
            $errors[] = __('Empty response from API', 'myplugin-payment-gateway');
            return $errors;
        }

        if (!isset($data['success'])) {
            $errors[] = __('Missing success status in API response', 'myplugin-payment-gateway');
        }

        if (isset($data['success']) && $data['success'] && !isset($data['transaction_id'])) {
            $errors[] = __('Missing transaction ID in successful API response', 'myplugin-payment-gateway');
        }

        return $errors;
    }

    /**
     * Validate webhook data
     */
    public static function validate_webhook_data(array $data): array
    {
        $errors = [];

        if (empty($data)) {
            $errors[] = __('Empty webhook data', 'myplugin-payment-gateway');
            return $errors;
        }

        if (!isset($data['order_id'])) {
            $errors[] = __('Missing order ID in webhook data', 'myplugin-payment-gateway');
        }

        if (!isset($data['status'])) {
            $errors[] = __('Missing status in webhook data', 'myplugin-payment-gateway');
        }

        if (isset($data['order_id']) && !is_numeric($data['order_id'])) {
            $errors[] = __('Invalid order ID in webhook data', 'myplugin-payment-gateway');
        }

        return $errors;
    }

    /**
     * Validate gateway settings
     */
    public static function validate_gateway_settings(array $settings): array
    {
        $errors = [];

        if (empty($settings['api_key'])) {
            $errors[] = __('API Key is required', 'myplugin-payment-gateway');
        }

        if (empty($settings['secret_key'])) {
            $errors[] = __('Secret Key is required', 'myplugin-payment-gateway');
        }

        if (isset($settings['sandbox_mode']) && $settings['sandbox_mode'] === 'yes') {
            if (empty($settings['sandbox_api_key'])) {
                $errors[] = __('Sandbox API Key is required when sandbox mode is enabled', 'myplugin-payment-gateway');
            }

            if (empty($settings['sandbox_secret_key'])) {
                $errors[] = __('Sandbox Secret Key is required when sandbox mode is enabled', 'myplugin-payment-gateway');
            }
        }

        return $errors;
    }

    /**
     * Validate amount
     */
    public static function validate_amount(float $amount, string $currency = 'USD'): array
    {
        $errors = [];

        if ($amount <= 0) {
            $errors[] = __('Amount must be greater than zero', 'myplugin-payment-gateway');
        }

        if ($amount > 999999.99) {
            $errors[] = __('Amount exceeds maximum allowed value', 'myplugin-payment-gateway');
        }

        // Validate currency format
        if (!preg_match('/^[A-Z]{3}$/', $currency)) {
            $errors[] = __('Invalid currency format', 'myplugin-payment-gateway');
        }

        return $errors;
    }

    /**
     * Validate email
     */
    public static function validate_email(string $email): bool
    {
        return is_email($email) !== false;
    }

    /**
     * Validate webhook signature
     */
    public static function validate_webhook_signature(string $payload, string $signature, string $secret_key): bool
    {
        if (empty($signature) || empty($secret_key)) {
            return false;
        }

        $expected_signature = hash_hmac('sha256', $payload, $secret_key);
        return hash_equals($expected_signature, $signature);
    }

    /**
     * Sanitize and validate payment data
     */
    public static function sanitize_payment_data(array $data): array
    {
        $sanitized = [];

        if (isset($data['amount'])) {
            $sanitized['amount'] = (float) $data['amount'];
        }

        if (isset($data['currency'])) {
            $sanitized['currency'] = sanitize_text_field($data['currency']);
        }

        if (isset($data['order_id'])) {
            $sanitized['order_id'] = (int) $data['order_id'];
        }

        if (isset($data['customer_email'])) {
            $sanitized['customer_email'] = sanitize_email($data['customer_email']);
        }

        if (isset($data['customer_name'])) {
            $sanitized['customer_name'] = sanitize_text_field($data['customer_name']);
        }

        return $sanitized;
    }
}