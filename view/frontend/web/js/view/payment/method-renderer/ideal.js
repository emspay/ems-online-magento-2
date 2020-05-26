/*
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'ko',
        'jquery',
        'EMSPay_Payment/js/view/payment/method-renderer/default'
    ],
    function (ko, $, Component) {
        var checkoutConfig = window.checkoutConfig.payment;
        'use strict';
        return Component.extend({
            defaults: {
                template: 'EMSPay_Payment/payment/ideal',
                selectedIssuer: null
            },
            getIssuers: function () {
                return checkoutConfig[this.item.method].issuers;
            },
            getData: function () {
                return {
                    'method': this.item.method,
                    'additional_data': {
                        "issuer": this.selectedIssuer
                    }
                };
            }
        });
    }
);
