<?php
/**
 * MyPlugin Internationalization
 *
 * @package MyPlugin_Payment_Gateway
 */

declare(strict_types=1);

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * MyPlugin I18n Class
 */
class MyPlugin_I18n
{
    /**
     * Text domain
     */
    private const TEXT_DOMAIN = 'myplugin-payment-gateway';

    /**
     * Load text domain
     */
    public static function load_textdomain(): void
    {
        load_plugin_textdomain(
            self::TEXT_DOMAIN,
            false,
            dirname(plugin_basename(MYPLUGIN_PLUGIN_FILE)) . '/languages'
        );
    }

    /**
     * Get text domain
     */
    public static function get_text_domain(): string
    {
        return self::TEXT_DOMAIN;
    }

    /**
     * Translate string
     */
    public static function __(string $text): string
    {
        return __($text, self::TEXT_DOMAIN);
    }

    /**
     * Echo translated string
     */
    public static function _e(string $text): void
    {
        _e($text, self::TEXT_DOMAIN);
    }

    /**
     * Translate string with context
     */
    public static function _x(string $text, string $context): string
    {
        return _x($text, $context, self::TEXT_DOMAIN);
    }

    /**
     * Translate string with number
     */
    public static function _n(string $single, string $plural, int $number): string
    {
        return _n($single, $plural, $number, self::TEXT_DOMAIN);
    }

    /**
     * Get common payment gateway strings
     */
    public static function get_common_strings(): array
    {
        return [
            'gateway_title' => self::__('MyPlugin Payment'),
            'gateway_description' => self::__('Pay securely using MyPlugin payment gateway.'),
            'enable_disable' => self::__('Enable/Disable'),
            'enable_gateway' => self::__('Enable MyPlugin Payment Gateway'),
            'title' => self::__('Title'),
            'description' => self::__('Description'),
            'sandbox_mode' => self::__('Sandbox Mode'),
            'enable_sandbox' => self::__('Enable Sandbox Mode'),
            'api_key' => self::__('API Key'),
            'secret_key' => self::__('Secret Key'),
            'live_api_key' => self::__('Live API Key'),
            'live_secret_key' => self::__('Live Secret Key'),
            'sandbox_api_key' => self::__('Sandbox API Key'),
            'sandbox_secret_key' => self::__('Sandbox Secret Key'),
            'payment_completed' => self::__('Payment completed via MyPlugin.'),
            'payment_failed' => self::__('Payment failed'),
            'payment_error' => self::__('Payment error'),
            'webhook_error' => self::__('Webhook Error'),
            'invalid_webhook_data' => self::__('Invalid webhook data'),
            'invalid_signature' => self::__('Invalid webhook signature'),
            'order_not_found' => self::__('Order not found'),
            'refund_processed' => self::__('Refund processed via MyPlugin.'),
            'required_field' => self::__('This field is required'),
            'invalid_amount' => self::__('Invalid amount'),
            'invalid_currency' => self::__('Invalid currency'),
            'api_error' => self::__('API Error'),
            'network_error' => self::__('Network Error'),
            'timeout_error' => self::__('Request timeout'),
            'configuration_error' => self::__('Configuration Error'),
            'missing_api_key' => self::__('API Key is required'),
            'missing_secret_key' => self::__('Secret Key is required'),
            'sandbox_config_required' => self::__('Sandbox configuration is required when sandbox mode is enabled'),
        ];
    }

    /**
     * Get error messages
     */
    public static function get_error_messages(): array
    {
        return [
            'order_total_zero' => self::__('Order total must be greater than zero'),
            'billing_email_required' => self::__('Billing email is required'),
            'billing_country_required' => self::__('Billing country is required'),
            'billing_name_required' => self::__('Billing name is required'),
            'empty_api_response' => self::__('Empty response from API'),
            'missing_success_status' => self::__('Missing success status in API response'),
            'missing_transaction_id' => self::__('Missing transaction ID in successful API response'),
            'empty_webhook_data' => self::__('Empty webhook data'),
            'missing_order_id' => self::__('Missing order ID in webhook data'),
            'missing_status' => self::__('Missing status in webhook data'),
            'invalid_order_id' => self::__('Invalid order ID in webhook data'),
            'amount_exceeds_limit' => self::__('Amount exceeds maximum allowed value'),
            'invalid_currency_format' => self::__('Invalid currency format'),
        ];
    }

    /**
     * Get success messages
     */
    public static function get_success_messages(): array
    {
        return [
            'payment_successful' => self::__('Payment completed successfully'),
            'refund_successful' => self::__('Refund processed successfully'),
            'webhook_processed' => self::__('Webhook processed successfully'),
            'settings_saved' => self::__('Settings saved successfully'),
            'test_connection_success' => self::__('Test connection successful'),
        ];
    }

    /**
     * Get admin notices
     */
    public static function get_admin_notices(): array
    {
        return [
            'gateway_disabled' => self::__('MyPlugin Payment Gateway is disabled'),
            'configuration_incomplete' => self::__('MyPlugin Payment Gateway configuration is incomplete'),
            'sandbox_mode_active' => self::__('MyPlugin Payment Gateway is running in sandbox mode'),
            'webhook_not_configured' => self::__('Webhook URL not configured in payment provider'),
        ];
    }

    /**
     * Get help text
     */
    public static function get_help_text(): array
    {
        return [
            'api_key_help' => self::__('Enter your MyPlugin API key from your account dashboard'),
            'secret_key_help' => self::__('Enter your MyPlugin secret key from your account dashboard'),
            'sandbox_help' => self::__('Enable sandbox mode for testing payments'),
            'webhook_help' => self::__('Configure this URL in your MyPlugin account webhook settings'),
            'currency_help' => self::__('Select the currencies you want to accept'),
            'debug_help' => self::__('Enable debug logging for troubleshooting'),
        ];
    }

    /**
     * Get field descriptions
     */
    public static function get_field_descriptions(): array
    {
        return [
            'title_desc' => self::__('Payment method title that the customer sees during checkout'),
            'description_desc' => self::__('Payment method description that the customer sees during checkout'),
            'sandbox_desc' => self::__('Place the payment gateway in sandbox mode for testing'),
            'api_key_desc' => self::__('Enter your MyPlugin live API key'),
            'secret_key_desc' => self::__('Enter your MyPlugin live secret key'),
            'sandbox_api_key_desc' => self::__('Enter your MyPlugin sandbox API key'),
            'sandbox_secret_key_desc' => self::__('Enter your MyPlugin sandbox secret key'),
        ];
    }
}