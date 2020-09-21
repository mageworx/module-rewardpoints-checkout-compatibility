<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPointsCheckout\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class CheckoutLayoutModifier
 */
class CheckoutLayoutModifier implements ObserverInterface
{
    /**
     * @var \MageWorx\RewardPoints\Helper\Data
     */
    protected $helperData;

    /**
     * LayoutProcessor constructor.
     *
     * @param \MageWorx\RewardPoints\Helper\Data $helperData
     */
    public function __construct(
        \MageWorx\RewardPoints\Helper\Data $helperData
    ) {
        $this->helperData = $helperData;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        /** @var \MageWorx\Checkout\Api\LayoutModifierAccessInterface $subject */
        $subject = $observer->getSubject();
        /** @var array $jsLayout */
        $jsLayout = &$subject->getJsLayout();

        $this->moveMessage($jsLayout);
        $this->moveInteractComponent($jsLayout);
    }

    /**
     * Move a regular reward-points message from the root checkout container to particular part in the sidebar
     *
     * @param array $jsLayout
     * @return array
     */
    private function moveMessage(array &$jsLayout): array
    {
        if (isset($jsLayout['components']['checkout']['children']['reward'])) {
            $copy = $jsLayout['components']['checkout']['children']['reward'];
            unset($jsLayout['components']['checkout']['children']['reward']);
            $copy['displayArea'] = \MageWorx\Checkout\Api\LayoutModifierAccessInterface::HANDLE_CUSTOM_MESSAGES;
            $copy['config']['template'] = 'MageWorx_RewardPointsCheckout/message';
            $jsLayout['components']['checkout']['children']['sidebar']['children']['rewardpointsMessage'] = $copy;
        }

        return $jsLayout;
    }

    /**
     * Move interact component from original place to particular place in (additional inputs) sidebar
     *
     * @param array $jsLayout
     * @return array
     */
    private function moveInteractComponent(array &$jsLayout): array
    {
        $nameInLayout = 'mageworx_rewardpoints';
        // Copy element
        $originalElement = $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
        ['children']['payment']['children']['afterMethods']['children'][$nameInLayout];

        // Remove original element from layout
        unset(
        $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
        ['payment']['children']['afterMethods']['children'][$nameInLayout]
        );

        $interactionComponentTemplate = $this->helperData->isAllowedCustomPointsAmount() ?
            'MageWorx_RewardPointsCheckout/summary/additional-inputs/mageworx-reward-points-with-custom-amount' :
            'MageWorx_RewardPointsCheckout/summary/additional-inputs/mageworx-reward-points';
        $originalElement['config']['template'] = $interactionComponentTemplate;

        // @TODO: Update child components here

        $jsLayout['components']['checkout']['children']['sidebar']['children']['additionalInputs']
        ['children'][$nameInLayout] = $originalElement;

        $totalsTemplate = 'MageWorx_RewardPointsCheckout/summary/totals/mageworx-reward-points';
        $jsLayout['components']['checkout']['children']['sidebar']['children']['summary']['children']['totals']
        ['children']['before_grandtotal']['children']['mageworx_rewardpoints']['config']['template'] = $totalsTemplate;

        return $jsLayout;
    }
}
