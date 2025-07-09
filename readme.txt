=== MyPlugin Payment Gateway ===
Contributors: myplugin
Tags: woocommerce, payment, gateway, credit card, checkout
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A custom payment gateway for WooCommerce with advanced features including blocks support, webhooks, refunds, and comprehensive validation.

== Description ==

MyPlugin Payment Gateway is a robust and secure payment gateway for WooCommerce that provides a complete solution for processing payments. Built with modern WordPress development practices, it includes essential features for production-ready payment processing.

= Key Features =

* **WooCommerce Blocks Support** - Fully compatible with the new block-based checkout
* **Credit Card Fields** - Native WooCommerce credit card form with validation
* **Sandbox Mode** - Complete testing environment for development
* **Secure Webhooks** - HMAC signature verification for secure callbacks
* **Refund Support** - Automatic refund processing capabilities
* **Advanced Logging** - Comprehensive logging system for debugging
* **Data Validation** - Complete input validation and sanitization
* **Internationalization** - Full translation support
* **Admin Notices** - Configuration validation and warnings
* **Clean Architecture** - Modular and maintainable code structure

= Technical Features =

* **Luhn Algorithm** - Credit card number validation
* **Expiry Date Validation** - MM/YY format validation
* **CVC Validation** - 3-4 digit security code validation
* **API Integration** - Ready for your payment provider API
* **Error Handling** - Comprehensive error management
* **Security** - Input sanitization and output escaping
* **Performance** - Optimized for production use

= Configuration =

The plugin includes an interactive configuration script that automatically:

* Renames all files to match your plugin name
* Updates class names and namespaces
* Replaces placeholder values throughout the code
* Configures author information and URLs
* Sets up proper file structure

= Development Ready =

This skeleton provides a solid foundation for building custom payment gateways. It follows WordPress coding standards and includes all necessary components for a production-ready payment gateway.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/myplugin-payment-gateway` directory, or install through WordPress admin
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Run the configuration script: `php configure.php`
4. Configure the gateway in WooCommerce > Settings > Payments
5. Add your API credentials and webhook URL

== Frequently Asked Questions ==

= Does this plugin work with WooCommerce Blocks? =

Yes, this plugin is fully compatible with WooCommerce Blocks and provides a custom checkout interface.

= Can I use this in sandbox mode for testing? =

Yes, the plugin includes a sandbox mode with separate API credentials for testing.

= Does it support refunds? =

Yes, the plugin includes automatic refund processing capabilities.

= Is it secure? =

Yes, the plugin includes comprehensive security measures including input validation, output escaping, and HMAC signature verification for webhooks.

= Can I customize the credit card fields? =

Yes, the plugin uses native WooCommerce credit card forms that can be customized through CSS and JavaScript.

== Screenshots ==

1. Payment gateway configuration page
2. Credit card fields in checkout
3. Admin settings interface
4. Webhook configuration

== Changelog ==

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.0.0 =
Initial release of the MyPlugin Payment Gateway skeleton.

== Configuration ==



== Development ==

To customize it for your specific payment provider:

1. Update the API endpoints in the gateway class
2. Modify the payment processing logic
3. Configure webhook handling for your provider
4. Add your specific validation rules
5. Customize the user interface as needed

== Support ==

For support and customization, please contact the plugin author.

== License ==

This plugin is licensed under the GPL v2 or later.

== Credits ==

Built with WordPress and WooCommerce development best practices.