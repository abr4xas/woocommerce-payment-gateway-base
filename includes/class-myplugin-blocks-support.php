<?php
/**
 * MyPlugin Blocks Support
 *
 * @package MyPlugin_Payment_Gateway
 */

declare(strict_types=1);

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * MyPlugin Blocks Support Class
 */
class MyPlugin_Blocks_Support extends Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType
{
    /**
     * Payment method name/id/slug.
     */
    protected $name = 'myplugin_gateway';

    /**
     * Initializes the payment method type.
     */
    public function initialize(): void
    {
        $this->settings = get_option('woocommerce_myplugin_gateway_settings', []);
    }

    /**
     * Returns if this payment method should be active. If false, the scripts will not be enqueued.
     */
    public function is_active(): bool
    {
        $payment_gateways = WC()->payment_gateways()->payment_gateways();
        return isset($payment_gateways['myplugin_gateway']) && $payment_gateways['myplugin_gateway']->is_available();
    }

    /**
     * Returns an array of scripts/handles to be registered for this payment method.
     */
    public function get_payment_method_script_handles(): array
    {
        $asset_path = MYPLUGIN_PLUGIN_DIR . 'assets/js/blocks.js';

        if (file_exists($asset_path)) {
            wp_register_script(
                'myplugin-blocks',
                MYPLUGIN_PLUGIN_URL . 'assets/js/blocks.js',
                ['wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-data', 'wp-hooks'],
                MYPLUGIN_VERSION,
                true
            );

            wp_localize_script(
                'myplugin-blocks',
                'mypluginBlocksData',
                [
                    'gatewayId' => $this->name,
                    'gatewayTitle' => $this->get_setting('title', __('MyPlugin Payment', 'myplugin-payment-gateway')),
                    'gatewayDescription' => $this->get_setting('description', __('Pay securely using MyPlugin payment gateway.', 'myplugin-payment-gateway')),
                    'supports' => $this->get_supported_features(),
                ]
            );

            return ['myplugin-blocks'];
        }

        return [];
    }

    /**
     * Returns an array of key=>value pairs of data made available to the payment methods script.
     */
    public function get_payment_method_data(): array
    {
        return [
            'title' => $this->get_setting('title', __('MyPlugin Payment', 'myplugin-payment-gateway')),
            'description' => $this->get_setting('description', __('Pay securely using MyPlugin payment gateway.', 'myplugin-payment-gateway')),
            'supports' => $this->get_supported_features(),
        ];
    }

    /**
     * Get supported features
     */
    private function get_supported_features(): array
    {
        return [
            'products' => true,
            'refunds' => true,
            'tokenization' => true,
            'block' => true,
        ];
    }
}