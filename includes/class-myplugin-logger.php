<?php
/**
 * MyPlugin Logger
 *
 * @package MyPlugin_Payment_Gateway
 */

declare(strict_types=1);

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * MyPlugin Logger Class
 */
class MyPlugin_Logger
{
    /**
     * Log source identifier
     */
    private const LOG_SOURCE = 'myplugin-gateway';

    /**
     * Log a message
     */
    public static function log(string $message, string $level = 'info', array $context = []): void
    {
        if (!wc_get_logger()) {
            return;
        }

        $context['source'] = self::LOG_SOURCE;
        $context['timestamp'] = current_time('mysql');

        wc_get_logger()->log($level, $message, $context);
    }

    /**
     * Log debug message
     */
    public static function debug(string $message, array $context = []): void
    {
        self::log($message, 'debug', $context);
    }

    /**
     * Log info message
     */
    public static function info(string $message, array $context = []): void
    {
        self::log($message, 'info', $context);
    }

    /**
     * Log warning message
     */
    public static function warning(string $message, array $context = []): void
    {
        self::log($message, 'warning', $context);
    }

    /**
     * Log error message
     */
    public static function error(string $message, array $context = []): void
    {
        self::log($message, 'error', $context);
    }

    /**
     * Log critical message
     */
    public static function critical(string $message, array $context = []): void
    {
        self::log($message, 'critical', $context);
    }

    /**
     * Log payment attempt
     */
    public static function log_payment_attempt(int $order_id, array $data = []): void
    {
        self::info("Payment attempt for order #{$order_id}", [
            'order_id' => $order_id,
            'amount' => $data['amount'] ?? 'unknown',
            'currency' => $data['currency'] ?? 'unknown',
        ]);
    }

    /**
     * Log payment success
     */
    public static function log_payment_success(int $order_id, string $transaction_id): void
    {
        self::info("Payment successful for order #{$order_id}", [
            'order_id' => $order_id,
            'transaction_id' => $transaction_id,
        ]);
    }

    /**
     * Log payment failure
     */
    public static function log_payment_failure(int $order_id, string $error_message): void
    {
        self::error("Payment failed for order #{$order_id}: {$error_message}", [
            'order_id' => $order_id,
            'error' => $error_message,
        ]);
    }

    /**
     * Log webhook received
     */
    public static function log_webhook_received(string $event_type, array $data = []): void
    {
        self::info("Webhook received: {$event_type}", [
            'event_type' => $event_type,
            'data' => $data,
        ]);
    }

    /**
     * Log API request
     */
    public static function log_api_request(string $endpoint, array $data = []): void
    {
        self::debug("API request to {$endpoint}", [
            'endpoint' => $endpoint,
            'data' => $data,
        ]);
    }

    /**
     * Log API response
     */
    public static function log_api_response(string $endpoint, int $status_code, array $response = []): void
    {
        $level = $status_code >= 200 && $status_code < 300 ? 'debug' : 'error';
        self::log("API response from {$endpoint}: {$status_code}", $level, [
            'endpoint' => $endpoint,
            'status_code' => $status_code,
            'response' => $response,
        ]);
    }
}