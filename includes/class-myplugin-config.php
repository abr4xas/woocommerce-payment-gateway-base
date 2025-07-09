<?php
/**
 * MyPlugin Config
 *
 * @package MyPlugin_Payment_Gateway
 */

declare(strict_types=1);

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * MyPlugin Config Class
 */
class MyPlugin_Config
{
    /**
     * Config instance
     */
    private static ?self $instance = null;

    /**
     * Settings array
     */
    private array $settings = [];

    /**
     * Get config instance
     */
    public static function get_instance(): self
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct()
    {
        $this->load_settings();
    }

    /**
     * Load settings from WordPress options
     */
    private function load_settings(): void
    {
        $this->settings = get_option('woocommerce_myplugin_gateway_settings', []);
    }

    /**
     * Get a setting value
     */
    public function get(string $key, $default = null)
    {
        return $this->settings[$key] ?? $default;
    }

    /**
     * Set a setting value
     */
    public function set(string $key, $value): void
    {
        $this->settings[$key] = $value;
    }

    /**
     * Check if sandbox mode is enabled
     */
    public function is_sandbox(): bool
    {
        return 'yes' === $this->get('sandbox_mode', 'no');
    }

    /**
     * Get API key based on mode
     */
    public function get_api_key(): string
    {
        return $this->is_sandbox()
            ? $this->get('sandbox_api_key', '')
            : $this->get('api_key', '');
    }

    /**
     * Get secret key based on mode
     */
    public function get_secret_key(): string
    {
        return $this->is_sandbox()
            ? $this->get('sandbox_secret_key', '')
            : $this->get('secret_key', '');
    }

    /**
     * Get webhook URL
     */
    public function get_webhook_url(): string
    {
        return home_url('/wc-api/myplugin_webhook');
    }

    /**
     * Get API base URL
     */
    public function get_api_base_url(): string
    {
        return $this->is_sandbox()
            ? 'https://api-sandbox.myplugin.com'
            : 'https://api.myplugin.com';
    }

    /**
     * Get payment endpoint URL
     */
    public function get_payment_endpoint(): string
    {
        return $this->get_api_base_url() . '/payments';
    }

    /**
     * Get refund endpoint URL
     */
    public function get_refund_endpoint(): string
    {
        return $this->get_api_base_url() . '/refunds';
    }

    /**
     * Get gateway title
     */
    public function get_gateway_title(): string
    {
        return $this->get('title', __('MyPlugin Payment', 'myplugin-payment-gateway'));
    }

    /**
     * Get gateway description
     */
    public function get_gateway_description(): string
    {
        return $this->get('description', __('Pay securely using MyPlugin payment gateway.', 'myplugin-payment-gateway'));
    }

    /**
     * Check if gateway is enabled
     */
    public function is_enabled(): bool
    {
        return 'yes' === $this->get('enabled', 'no');
    }

    /**
     * Get all settings
     */
    public function get_all_settings(): array
    {
        return $this->settings;
    }

    /**
     * Check if gateway is properly configured
     */
    public function is_configured(): bool
    {
        return !empty($this->get_api_key()) && !empty($this->get_secret_key());
    }

    /**
     * Get supported currencies
     */
    public function get_supported_currencies(): array
    {
        return [
            'USD' => 'US Dollar',
            'EUR' => 'Euro',
            'GBP' => 'British Pound',
            'CAD' => 'Canadian Dollar',
            'AUD' => 'Australian Dollar',
            'JPY' => 'Japanese Yen',
        ];
    }

    /**
     * Get default currency
     */
    public function get_default_currency(): string
    {
        return get_woocommerce_currency();
    }

    /**
     * Get plugin version
     */
    public function get_version(): string
    {
        return MYPLUGIN_VERSION;
    }

    /**
     * Get plugin directory path
     */
    public function get_plugin_dir(): string
    {
        return MYPLUGIN_PLUGIN_DIR;
    }

    /**
     * Get plugin URL
     */
    public function get_plugin_url(): string
    {
        return MYPLUGIN_PLUGIN_URL;
    }

    /**
     * Get assets URL
     */
    public function get_assets_url(): string
    {
        return $this->get_plugin_url() . 'assets/';
    }

    /**
     * Get JavaScript URL
     */
    public function get_js_url(): string
    {
        return $this->get_assets_url() . 'js/';
    }

    /**
     * Get gateway ID
     */
    public function get_gateway_id(): string
    {
        return 'myplugin_gateway';
    }

    /**
     * Get gateway class name
     */
    public function get_gateway_class_name(): string
    {
        return 'MyPlugin_Gateway';
    }

    /**
     * Get blocks support class name
     */
    public function get_blocks_class_name(): string
    {
        return 'MyPlugin_Blocks_Support';
    }

    /**
     * Get text domain
     */
    public function get_text_domain(): string
    {
        return 'myplugin-payment-gateway';
    }
}