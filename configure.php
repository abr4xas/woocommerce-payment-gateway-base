#!/usr/bin/env php
<?php

/**
 * MyPlugin Payment Gateway Configuration Script
 *
 * This script will help you configure the plugin with your custom information.
 * Based on Spatie's package-skeleton-php configure script.
 */

declare(strict_types=1);

function ask(string $question, string $default = ''): string
{
    $question = $default ? "{$question} [{$default}]" : $question;
    $question .= ': ';

    $handle = fopen('php://stdin', 'r');
    $answer = trim(fgets($handle));
    fclose($handle);

    return $answer ?: $default;
}

function confirm(string $question, bool $default = false): bool
{
    $answer = ask($question.' ('.($default ? 'Y/n' : 'y/N').')');
    if (!$answer) {
        return $default;
    }
    return strtolower($answer) === 'y';
}

function writeln(string $line): void
{
    echo $line.PHP_EOL;
}

function run(string $command): string
{
    return trim(shell_exec($command) ?? '');
}

function slugify(string $subject): string
{
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $subject), '-'));
}

function title_case(string $subject): string
{
    return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $subject)));
}

function replace_in_file(string $file, array $replacements): void
{
    $contents = file_get_contents($file);
    file_put_contents(
        $file,
        str_replace(
            array_keys($replacements),
            array_values($replacements),
            $contents
        )
    );
}

function remove_readme_paragraphs(string $file): void
{
    $contents = file_get_contents($file);
    file_put_contents(
        $file,
        preg_replace('/<!--.*?-->/s', '', $contents) ?: $contents
    );
}

function determine_separator(string $path): string
{
    return str_replace('/', DIRECTORY_SEPARATOR, $path);
}

function replace_for_windows(): array
{
    return preg_split('/\r\n|\r|\n/', run('dir /S /B * | findstr /v /i .git\\ | findstr /v /i '.basename(__FILE__).' | findstr /r /i /M /F:/ ":author :plugin_name :gateway_name MyPlugin myplugin author@domain.com myplugin_gateway myplugin_webhook myplugin-blocks myplugin-checkout mypluginBlocksData MYPLUGIN_ Contributors: myplugin === MyPlugin Payment Gateway ==="'));
}

function replace_for_all_other_oses(): array
{
    return explode(PHP_EOL, run('grep -E -r -l -i ":author|:plugin_name|:gateway_name|MyPlugin|myplugin|author@domain.com|myplugin_gateway|myplugin_webhook|myplugin-blocks|myplugin-checkout|mypluginBlocksData|MYPLUGIN_|Contributors: myplugin|=== MyPlugin Payment Gateway ===" --exclude-dir=.git ./* ./.github/* | grep -v '.basename(__FILE__)));
}

function setup_plugin_structure(string $pluginName, string $gatewayName): void
{
    // Rename main plugin file
    $mainFile = __DIR__.'/myplugin-payment-gateway.php';
    $newMainFile = __DIR__.'/'.$pluginName.'-payment-gateway.php';

    if (file_exists($mainFile)) {
        rename($mainFile, $newMainFile);
    }

    // Rename includes directory files
    $includesDir = __DIR__.'/includes/';
    if (is_dir($includesDir)) {
        $files = [
            'class-myplugin-gateway.php' => 'class-'.$gatewayName.'-gateway.php',
            'class-myplugin-blocks-support.php' => 'class-'.$gatewayName.'-blocks-support.php',
            'class-myplugin-logger.php' => 'class-'.$gatewayName.'-logger.php',
            'class-myplugin-validator.php' => 'class-'.$gatewayName.'-validator.php',
            'class-myplugin-config.php' => 'class-'.$gatewayName.'-config.php',
            'class-myplugin-i18n.php' => 'class-'.$gatewayName.'-i18n.php',
        ];

        foreach ($files as $oldFile => $newFile) {
            $oldPath = $includesDir.$oldFile;
            $newPath = $includesDir.$newFile;
            if (file_exists($oldPath)) {
                rename($oldPath, $newPath);
            }
        }
    }

    // Rename assets directory files
    $assetsDir = __DIR__.'/assets/';
    if (is_dir($assetsDir)) {
        // Rename JS files
        $jsDir = $assetsDir.'js/';
        if (is_dir($jsDir)) {
            $oldJsFile = $jsDir.'blocks.js';
            $newJsFile = $jsDir.$gatewayName.'-blocks.js';
            if (file_exists($oldJsFile)) {
                rename($oldJsFile, $newJsFile);
            }
        }

        // Rename CSS files
        $cssDir = $assetsDir.'css/';
        if (is_dir($cssDir)) {
            $oldCssFile = $cssDir.'checkout.css';
            $newCssFile = $cssDir.$gatewayName.'-checkout.css';
            if (file_exists($oldCssFile)) {
                rename($oldCssFile, $newCssFile);
            }
        }
    }


}

// Get user information
$gitName = run('git config user.name');
$authorName = ask('Author name', $gitName);

$gitEmail = run('git config user.email');
$authorEmail = ask('Author email', $gitEmail);

$usernameGuess = explode(':', run('git config remote.origin.url'))[1] ?? '';
$usernameGuess = dirname($usernameGuess);
$usernameGuess = basename($usernameGuess);
$authorUsername = ask('Author username', $usernameGuess);

$currentDirectory = getcwd();
$folderName = basename($currentDirectory);
$pluginName = ask('Plugin name (without "payment-gateway" suffix)', $folderName);
$pluginSlug = slugify($pluginName);

$gatewayName = ask('Gateway name (for class names)', $pluginSlug);
$gatewayClassName = title_case($gatewayName);

$description = ask('Plugin description', "Custom payment gateway for WooCommerce");

$pluginUrl = ask('Plugin URL', 'https://github.com/'.$authorUsername.'/'.$pluginSlug.'-payment-gateway');

$authorUrl = ask('Author URL', 'https://github.com/'.$authorUsername);

writeln('------');
writeln("Author : {$authorName} ({$authorUsername}, {$authorEmail})");
writeln("Plugin : {$pluginSlug} <{$description}>");
writeln("Gateway : {$gatewayName} ({$gatewayClassName})");
writeln("Plugin URL : {$pluginUrl}");
writeln("Author URL : {$authorUrl}");
writeln('------');

writeln('This script will replace the above values in all relevant files in the project directory.');
writeln('It will also rename files and directories to match your plugin name.');

if (!confirm('Modify files?', true)) {
    exit(1);
}

// Setup plugin structure first
setup_plugin_structure($pluginSlug, $gatewayName);

// Get files to replace
$files = (str_starts_with(strtoupper(PHP_OS), 'WIN') ? replace_for_windows() : replace_for_all_other_oses());

foreach ($files as $file) {
    if (empty($file)) {
        continue;
    }

    replace_in_file($file, [
        ':author_name' => $authorName,
        ':author_username' => $authorUsername,
        'author@domain.com' => $authorEmail,
        ':plugin_name' => $pluginName,
        ':plugin_slug' => $pluginSlug,
        ':gateway_name' => $gatewayName,
        ':gateway_class' => $gatewayClassName,
        'MyPlugin' => $gatewayClassName,
        'myplugin' => $gatewayName,
        'MYPLUGIN' => strtoupper($gatewayName),
        ':plugin_description' => $description,
        ':plugin_url' => $pluginUrl,
        ':author_url' => $authorUrl,
        'https://example.com/myplugin-payment-gateway' => $pluginUrl,
        'https://example.com' => $authorUrl,
        'Your Name' => $authorName,
        'https://myplugin.com' => $pluginUrl,
        'myplugin-payment-gateway' => $pluginSlug.'-payment-gateway',
        'myplugin_gateway' => $gatewayName.'_gateway',
        'myplugin_webhook' => $gatewayName.'_webhook',
        'myplugin-blocks' => $gatewayName.'-blocks',
        'myplugin-checkout' => $gatewayName.'-checkout',
        'mypluginBlocksData' => $gatewayName.'BlocksData',
        'myplugin-payment-gateway' => $pluginSlug.'-payment-gateway',
        'MYPLUGIN_VERSION' => strtoupper($gatewayName).'_VERSION',
        'MYPLUGIN_PLUGIN_FILE' => strtoupper($gatewayName).'_PLUGIN_FILE',
        'MYPLUGIN_PLUGIN_DIR' => strtoupper($gatewayName).'_PLUGIN_DIR',
        'MYPLUGIN_PLUGIN_URL' => strtoupper($gatewayName).'_PLUGIN_URL',
        'checkout.css' => $gatewayName.'-checkout.css',
        'blocks.js' => $gatewayName.'-blocks.js',
        // WordPress readme.txt placeholders
        'Contributors: myplugin' => 'Contributors: '.$authorUsername,
        'Tags: woocommerce, payment, gateway, credit card, checkout' => 'Tags: woocommerce, payment, gateway, credit card, checkout, '.$gatewayName,
        'A custom payment gateway for WooCommerce with advanced features including blocks support, webhooks, refunds, and comprehensive validation.' => $description,
        'MyPlugin Payment Gateway is a robust and secure payment gateway for WooCommerce that provides a complete solution for processing payments.' => $gatewayClassName.' Payment Gateway is a robust and secure payment gateway for WooCommerce that provides a complete solution for processing payments.',
        'myplugin-payment-gateway' => $pluginSlug.'-payment-gateway',
        'For support and customization, please contact the plugin author.' => 'For support and customization, please contact '.$authorName.' at '.$authorUrl,
        // Plugin title in readme.txt
        '=== MyPlugin Payment Gateway ===' => '=== '.$gatewayClassName.' Payment Gateway ===',
        // Plugin description in readme.txt
        'MyPlugin Payment Gateway is a robust and secure payment gateway' => $gatewayClassName.' Payment Gateway is a robust and secure payment gateway',
        // Initial release description
        '* Initial release' => '* Initial release of '.$gatewayClassName.' Payment Gateway',
        // Upgrade notice
        'Initial release of the MyPlugin Payment Gateway skeleton.' => 'Initial release of the '.$gatewayClassName.' Payment Gateway.',
    ]);

    // Handle specific file renames (only for files not handled by setup_plugin_structure)
    match (true) {
        str_contains($file, 'readme.txt') => remove_readme_paragraphs($file),
        default => [],
    };
}

writeln('------');
writeln('Plugin configuration completed!');
writeln('Next steps:');
writeln('1. Update your API endpoints in the gateway class');
writeln('2. Customize the payment logic for your provider');
writeln('3. Test the plugin in a WordPress environment');
writeln('4. Update the webhook URL in your payment provider');
writeln('5. Configure logging and validation settings');
writeln('6. Set up internationalization files in /languages/');
writeln('------');

if (confirm('Let this script delete itself?', true)) {
    unlink(__FILE__);
}