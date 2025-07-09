<?php
/**
 * Plugin Name: MyPlugin Payment Gateway for WooCommerce
 * Plugin URI: https://myplugin.com
 * Description: Accept payments through MyPlugin payment gateway in WooCommerce.
 * Version: 1.0.0
 * Author: MyPlugin
 * Author URI: https://myplugin.com
 * Text Domain: myplugin-payment-gateway
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * WC requires at least: 5.0
 * WC tested up to: 8.0
 *
 * @package MyPlugin_Payment_Gateway
 */

declare(strict_types=1);

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('MYPLUGIN_VERSION', '1.0.0');
define('MYPLUGIN_PLUGIN_FILE', __FILE__);
define('MYPLUGIN_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MYPLUGIN_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Main plugin class
 */
final class MyPlugin_Payment_Gateway_Plugin
{
    /**
     * Plugin instance
     */
    private static ?self $instance = null;

    /**
     * Get plugin instance
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
        $this->init_hooks();
    }

    /**
     * Initialize hooks
     */
    private function init_hooks(): void
    {
        add_action('plugins_loaded', [$this, 'init']);
        add_action('init', [$this, 'load_textdomain']);
    }

    /**
     * Initialize plugin
     */
    public function init(): void
    {
        // Check if WooCommerce is active
        if (!class_exists('WooCommerce')) {
            add_action('admin_notices', [$this, 'woocommerce_missing_notice']);
            return;
        }

        // Load required files
        $this->load_files();

        // Initialize gateway
        add_filter('woocommerce_payment_gateways', [$this, 'add_gateway']);

        // Initialize blocks support
        if (class_exists('Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType')) {
            add_action('woocommerce_blocks_loaded', [$this, 'init_blocks_support']);
        }
    }

    /**
     * Load required files
     */
    private function load_files(): void
    {
        // Load helper classes
        require_once MYPLUGIN_PLUGIN_DIR . 'includes/class-myplugin-logger.php';
        require_once MYPLUGIN_PLUGIN_DIR . 'includes/class-myplugin-validator.php';
        require_once MYPLUGIN_PLUGIN_DIR . 'includes/class-myplugin-config.php';
        require_once MYPLUGIN_PLUGIN_DIR . 'includes/class-myplugin-i18n.php';

        // Load main gateway class
        require_once MYPLUGIN_PLUGIN_DIR . 'includes/class-myplugin-gateway.php';

        // Load blocks support
        require_once MYPLUGIN_PLUGIN_DIR . 'includes/class-myplugin-blocks-support.php';
    }

    /**
     * Add gateway to WooCommerce
     */
    public function add_gateway(array $gateways): array
    {
        $gateways[] = 'MyPlugin_Gateway';
        return $gateways;
    }

    /**
     * Initialize blocks support
     */
    public function init_blocks_support(): void
    {
        if (class_exists('MyPlugin_Blocks_Support')) {
            new MyPlugin_Blocks_Support();
        }
    }

    /**
     * Load text domain
     */
    public function load_textdomain(): void
    {
        if (class_exists('MyPlugin_I18n')) {
            MyPlugin_I18n::load_textdomain();
        }
    }

    /**
     * WooCommerce missing notice
     */
    public function woocommerce_missing_notice(): void
    {
        echo '<div class="notice notice-error"><p>' .
             esc_html__('MyPlugin Payment Gateway requires WooCommerce to be installed and active.', 'myplugin-payment-gateway') .
             '</p></div>';
    }

    /**
     * Plugin activation hook
     */
    public static function activate(): void
    {
        // Check if WooCommerce is active
        if (!class_exists('WooCommerce')) {
            deactivate_plugins(plugin_basename(__FILE__));
            wp_die(
                esc_html__('MyPlugin Payment Gateway requires WooCommerce to be installed and active.', 'myplugin-payment-gateway'),
                'Plugin Activation Error',
                ['response' => 200, 'back_link' => true]
            );
        }

        // Flush rewrite rules for webhook endpoint
        flush_rewrite_rules();
    }

    /**
     * Plugin deactivation hook
     */
    public static function deactivate(): void
    {
        // Flush rewrite rules
        flush_rewrite_rules();
    }
}

// Initialize plugin
MyPlugin_Payment_Gateway_Plugin::get_instance();

// Register activation and deactivation hooks
register_activation_hook(__FILE__, [MyPlugin_Payment_Gateway_Plugin::class, 'activate']);
register_deactivation_hook(__FILE__, [MyPlugin_Payment_Gateway_Plugin::class, 'deactivate']);