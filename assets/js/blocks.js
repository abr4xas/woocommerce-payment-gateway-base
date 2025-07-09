/**
 * MyPlugin Payment Gateway Blocks Support
 */

(function() {
    'use strict';

    // Check if WooCommerce Blocks is available
    if (typeof wc !== 'undefined' && wc.blocksRegistry) {
        const { registerPaymentMethod } = wc.blocksRegistry;
        const { createElement } = wp.element;
        const { __ } = wp.i18n;

        // Register payment method
        registerPaymentMethod({
            name: 'myplugin_gateway',
            label: mypluginBlocksData?.gatewayTitle || __('MyPlugin Payment', 'myplugin-payment-gateway'),
            content: createElement('div', {
                className: 'myplugin-payment-method',
            }, [
                createElement('div', {
                    key: 'description',
                    className: 'myplugin-payment-description',
                }, mypluginBlocksData?.gatewayDescription || __('Pay securely using MyPlugin payment gateway.', 'myplugin-payment-gateway')),
                createElement('div', {
                    key: 'card-fields',
                    className: 'myplugin-card-fields',
                }, [
                    createElement('div', {
                        key: 'card-number',
                        className: 'myplugin-card-number-field',
                    }, [
                        createElement('label', {
                            key: 'label',
                            htmlFor: 'myplugin-card-number',
                        }, __('Card Number', 'myplugin-payment-gateway') + ' *'),
                        createElement('input', {
                            key: 'input',
                            id: 'myplugin-card-number',
                            type: 'text',
                            name: 'myplugin-card-number',
                            placeholder: '•••• •••• •••• ••••',
                            maxLength: '20',
                            autoComplete: 'cc-number',
                        }),
                    ]),
                    createElement('div', {
                        key: 'card-details',
                        className: 'myplugin-card-details',
                    }, [
                        createElement('div', {
                            key: 'expiry',
                            className: 'myplugin-card-expiry-field',
                        }, [
                            createElement('label', {
                                key: 'label',
                                htmlFor: 'myplugin-card-expiry',
                            }, __('Expiry (MM/YY)', 'myplugin-payment-gateway') + ' *'),
                            createElement('input', {
                                key: 'input',
                                id: 'myplugin-card-expiry',
                                type: 'text',
                                name: 'myplugin-card-expiry',
                                placeholder: 'MM / YY',
                                autoComplete: 'cc-exp',
                            }),
                        ]),
                        createElement('div', {
                            key: 'cvc',
                            className: 'myplugin-card-cvc-field',
                        }, [
                            createElement('label', {
                                key: 'label',
                                htmlFor: 'myplugin-card-cvc',
                            }, __('Card Code', 'myplugin-payment-gateway') + ' *'),
                            createElement('input', {
                                key: 'input',
                                id: 'myplugin-card-cvc',
                                type: 'text',
                                name: 'myplugin-card-cvc',
                                placeholder: 'CVC',
                                maxLength: '4',
                                autoComplete: 'cc-csc',
                            }),
                        ]),
                    ]),
                ]),
            ]),
            edit: createElement('div', {
                className: 'myplugin-payment-method-editor',
            }, mypluginBlocksData?.gatewayTitle || __('MyPlugin Payment', 'myplugin-payment-gateway')),
            canMakePayment: () => true,
            ariaLabel: mypluginBlocksData?.gatewayTitle || __('MyPlugin Payment', 'myplugin-payment-gateway'),
            supports: {
                features: mypluginBlocksData?.supports || ['products', 'refunds', 'tokenization', 'block'],
            },
        });
    }

    // Add custom styles for the payment method
    const style = document.createElement('style');
    style.textContent = `
        .myplugin-payment-method {
            padding: 1rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #f9f9f9;
            margin-bottom: 1rem;
        }

        .myplugin-payment-description {
            margin-bottom: 1rem;
            color: #666;
        }

        .myplugin-card-fields {
            margin-top: 1rem;
        }

        .myplugin-card-number-field {
            margin-bottom: 1rem;
        }

        .myplugin-card-number-field label,
        .myplugin-card-expiry-field label,
        .myplugin-card-cvc-field label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #333;
        }

        .myplugin-card-number-field input,
        .myplugin-card-expiry-field input,
        .myplugin-card-cvc-field input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .myplugin-card-number-field input:focus,
        .myplugin-card-expiry-field input:focus,
        .myplugin-card-cvc-field input:focus {
            outline: none;
            border-color: #0073aa;
            box-shadow: 0 0 0 2px rgba(0, 115, 170, 0.2);
        }

        .myplugin-card-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .myplugin-payment-method-editor {
            padding: 1rem;
            border: 2px dashed #ccc;
            border-radius: 4px;
            text-align: center;
            color: #666;
        }

        .myplugin-payment-method .myplugin-logo {
            display: inline-block;
            width: 24px;
            height: 24px;
            background-color: #0073aa;
            border-radius: 4px;
            margin-right: 8px;
            vertical-align: middle;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .myplugin-card-details {
                grid-template-columns: 1fr;
            }
        }
    `;
    document.head.appendChild(style);

})();