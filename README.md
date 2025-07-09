# MyPlugin Payment Gateway - Enhanced Skeleton

An optimized skeleton for creating custom payment gateways in WooCommerce. Includes essential features such as blocks support, webhooks, refunds, advanced logging, validation, configuration management, and internationalization, while maintaining a clean and reusable structure.

## Quick Configuration

Before using this skeleton, run the configuration script to customize it for your project:

```bash
php configure.php
```

This script will:
- Ask for your author information (name, email, username)
- Configure plugin and gateway names
- Rename all files to match your plugin name
- Replace all placeholder values in the code
- Update class names and namespaces
- Configure plugin URLs and descriptions

After running the script, you'll have a fully customized plugin ready for development.

## Structure

```
myplugin-payment-gateway/
├── myplugin-payment-gateway.php    # Main plugin file
├── includes/
│   ├── class-myplugin-gateway.php      # Main gateway class
│   ├── class-myplugin-blocks-support.php   # Blocks support
│   ├── class-myplugin-logger.php       # Advanced logging system
│   ├── class-myplugin-validator.php    # Data validation
│   ├── class-myplugin-config.php       # Configuration management
│   └── class-myplugin-i18n.php         # Internationalization
├── assets/
│   ├── js/
│   │   └── blocks.js                    # JavaScript for blocks
│   └── css/
│       └── checkout.css                 # CSS for checkout fields
├── languages/                      # Translations (optional)
├── configure.php                   # Configuration script
├── README.md                       # This file
├── readme.txt                      # WordPress plugin readme
└── CHANGELOG.md                    # Change log
```

## Skeleton Features

- ✅ **Blocks support** - Compatible with WooCommerce Blocks
- ✅ **JavaScript for blocks** - Payment method registration
- ✅ **Sandbox mode** - For testing and development
- ✅ **Secure webhooks** - With HMAC signature verification
- ✅ **Refunds support** - Automatic processing
- ✅ **Advanced logging** - Structured logging with context
- ✅ **Data validation** - Comprehensive input validation
- ✅ **Configuration management** - Centralized settings handling
- ✅ **Internationalization** - Complete translation support
- ✅ **Admin notices** - Configuration validation and warnings
- ✅ **Clean structure** - Modular and maintainable code
- ✅ **Customizable prefix** - Avoids conflicts
- ✅ **Complete configuration** - Essential fields
- ✅ **Auto-configuration script** - Easy setup and customization
- ✅ **Credit card fields** - Native WooCommerce forms
- ✅ **Card validation** - Luhn algorithm and complete validations
- ✅ **Custom CSS styles** - Modern and responsive interface

## Quick Start

1. **Configure**: Run `php configure.php` to customize the plugin
2. **Rename**: The script will automatically rename files and update code
3. **Implement**: Complete logic in `process_payment()`
4. **Customize**: Modify `send_payment_request()` for your API
5. **Configure**: Set up logging and validation settings
6. **Translate**: Add translation files in `/languages/`

## Main Files

### `myplugin-payment-gateway.php`
- Plugin initialization
- Gateway registration
- Blocks support
- Helper classes loading
- Basic constants

### `includes/class-myplugin-gateway.php`
- Main gateway class
- Field configuration (sandbox, API keys)
- Payment processing logic
- Webhook handling
- Refund processing
- Configuration validation
- Admin notices

### `includes/class-myplugin-blocks-support.php`
- WooCommerce blocks support
- JavaScript file registration
- Supported features configuration
- Localized data for JavaScript

### `includes/class-myplugin-logger.php`
- Advanced logging system
- Multiple log levels (debug, info, warning, error, critical)
- Context-aware logging
- Payment-specific logging methods
- API request/response logging
- Webhook logging

### `includes/class-myplugin-validator.php`
- Comprehensive data validation
- Order validation
- API response validation
- Webhook data validation
- Gateway settings validation
- Amount and currency validation
- Email validation
- Webhook signature verification
- Data sanitization

### `includes/class-myplugin-config.php`
- Centralized configuration management
- Singleton pattern for settings
- Sandbox/live mode handling
- API endpoint management
- Currency support
- Plugin path and URL management
- Gateway identification

### `includes/class-myplugin-i18n.php`
- Complete internationalization support
- Text domain management
- Translation helper methods
- Organized string collections
- Error messages
- Success messages
- Admin notices
- Help text
- Field descriptions

### `assets/js/blocks.js`
- Payment method registration for blocks
- Custom user interface
- Integrated CSS styles
- Integration with `@woocommerce/blocks-registry`

### `assets/css/checkout.css`
- Styles for credit card fields
- Modern and responsive interface
- Compatibility with classic and block checkout
- Custom styles for payment forms

### `configure.php`
- Interactive configuration script
- Automatic file renaming
- Placeholder replacement
- Git integration for author information
- Simplified for WordPress context

## Included Features

### Configuration
- Sandbox/live mode
- Separate API keys for sandbox and production
- Secret keys for webhook verification
- Customizable title and description
- Centralized configuration management

### Payment Processing
- Payment request creation
- API response handling
- Transaction ID storage
- Cart clearing
- Comprehensive logging

### Webhooks
- Endpoint: `/wc-api/myplugin_webhook`
- HMAC signature verification
- Status handling: completed, failed
- Automatic order notes
- Data validation

### Refunds
- Automatic refund processing
- Refund API integration
- Automatic order notes

### WooCommerce Blocks
- Payment method registration
- Custom checkout interface
- Feature support: products, refunds, block
- Localized data from PHP

### Advanced Logging
- Structured logging with context
- Multiple log levels
- Payment-specific logging methods
- API request/response logging
- Webhook event logging
- Error tracking

### Data Validation
- Order data validation
- API response validation
- Webhook data validation
- Gateway settings validation
- Amount and currency validation
- Email validation
- Webhook signature verification
- Data sanitization

### Internationalization
- Complete translation support
- Organized string collections
- Error and success messages
- Admin notices and help text
- Field descriptions
- Context-aware translations

### Admin Interface
- Configuration validation
- Admin notices for errors
- Sandbox mode warnings
- Webhook URL display
- Settings validation

## Customization

### Using the Configuration Script
The easiest way to customize this skeleton is using the included configuration script:

```bash
# Run the configuration script
php configure.php

# Follow the interactive prompts
# The script will handle all renaming and replacements
```

### Manual Customization
If you prefer to customize manually:

#### Change Name
```php
// In all files, replace:
'myplugin' → 'tuplugin'
'MyPlugin' → 'TuPlugin'
'MYPLUGIN' → 'TUPLUGIN'
```

#### Using the Logger
```php
// Log payment attempts
MyPlugin_Logger::log_payment_attempt($order_id, [
    'amount' => $amount,
    'currency' => $currency
]);

// Log API requests
MyPlugin_Logger::log_api_request($endpoint, $data);

// Log errors
MyPlugin_Logger::error('Payment failed', ['order_id' => $order_id]);
```

#### Using the Validator
```php
// Validate order data
$errors = MyPlugin_Validator::validate_order($order);
if (!empty($errors)) {
    // Handle validation errors
}

// Validate API response
if (!MyPlugin_Validator::validate_api_response($response)) {
    // Handle invalid response
}

// Validate webhook data
$errors = MyPlugin_Validator::validate_webhook_data($data);
```

#### Using the Config
```php
// Get configuration instance
$config = MyPlugin_Config::get_instance();

// Get API key based on mode
$api_key = $config->get_api_key();

// Check if sandbox mode
if ($config->is_sandbox()) {
    // Sandbox logic
}

// Get webhook URL
$webhook_url = $config->get_webhook_url();
```

#### Using Internationalization
```php
// Translate strings
$message = MyPlugin_I18n::__('Payment completed successfully');

// Get error messages
$errors = MyPlugin_I18n::get_error_messages();

// Get help text
$help = MyPlugin_I18n::get_help_text();
```

#### Add Configuration Fields
```php
public function init_form_fields(): void
{
    $this->form_fields = [
        'enabled' => [
            'title' => __('Enable/Disable', 'text-domain'),
            'type' => 'checkbox',
            'label' => __('Enable Gateway', 'text-domain'),
            'default' => 'no',
        ],
        // Add your fields here
        'custom_field' => [
            'title' => __('Custom Field', 'text-domain'),
            'type' => 'text',
            'default' => '',
        ],
    ];
}
```

#### Implement Payment Logic
```php
private function send_payment_request(array $data): array
{
    // Log the request
    MyPlugin_Logger::log_api_request($this->get_payment_endpoint(), $data);

    // Replace with your real API
    $api_url = $this->sandbox_mode
        ? 'https://api-sandbox.yourprovider.com/payments'
        : 'https://api.yourprovider.com/payments';

    // Your API logic here
    $response = wp_remote_post($api_url, [
        'headers' => [
            'Authorization' => 'Bearer ' . $this->api_key,
            'Content-Type' => 'application/json',
        ],
        'body' => wp_json_encode($data),
        'timeout' => 30,
    ]);

    // Log the response
    $status_code = wp_remote_retrieve_response_code($response);
    MyPlugin_Logger::log_api_response($this->get_payment_endpoint(), $status_code, $response_data);

    // Process response
    if (is_wp_error($response)) {
        throw new Exception($response->get_error_message());
    }

    $response_data = json_decode(wp_remote_retrieve_body($response), true);

    // Validate response
    $errors = MyPlugin_Validator::validate_api_response_data($response_data);
    if (!empty($errors)) {
        throw new Exception(implode(', ', $errors));
    }

    return [
        'success' => true,
        'transaction_id' => $response_data['transaction_id'] ?? '',
        'message' => 'Payment successful',
    ];
}
```

#### Customize JavaScript for Blocks
```javascript
// In assets/js/blocks.js, customize the interface:
registerPaymentMethod({
    name: 'myplugin_gateway',
    label: 'Your Payment Method',
    content: createElement('div', {
        className: 'your-payment-method',
    }, 'Custom description'),
    // ... more options
});
```

## Standards Followed

- ✅ Customizable prefix to avoid conflicts
- ✅ `strict_types=1` in PHP files
- ✅ Class structure following WPCS
- ✅ WordPress hooks and filters
- ✅ No external dependencies (Composer)
- ✅ Complete blocks support
- ✅ JavaScript following WooCommerce standards
- ✅ Advanced logging and validation
- ✅ Complete internationalization
- ✅ Centralized configuration management

## Next Steps

1. **Configure** the plugin using `php configure.php`
2. **Implement** integration with your API
3. **Configure** webhooks in your provider
4. **Set up** logging and validation
5. **Add** translation files in `/languages/`
6. **Test** in sandbox mode
7. **Customize** according to specific needs

## License

GPL v2 or later