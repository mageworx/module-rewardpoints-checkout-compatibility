/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
define(
    [
        'uiRegistry',
        'ko'
    ],
    function (registry, ko) {
        'use strict';

        return function (origComponent) {

            if (window.isMageWorxCheckout) {
                return origComponent.extend({

                    contentVisible: ko.observable(),
                    inputPlaceholder: ko.observable(),

                    /**
                     * Toggle collapsible class state
                     */
                    toggleCollapsible: function () {
                        this.contentVisible(!this.contentVisible());
                    }
                });
            }

            return origComponent;
        };
    }
);
