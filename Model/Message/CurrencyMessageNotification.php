<?php
/**
 * All rights reserved.
 * See COPYING.txt for license details.
 */
namespace GingerPay\Payment\Model\Message;

use Magento\Framework\Notification\MessageInterface;
use Magento\Setup\Exception;
use Magento\Store\Model\StoreManagerInterface;
use GingerPay\Payment\Model\Cache\MulticurrencyCacheRepository;

class CurrencyMessageNotification implements MessageInterface {

    /**
     * Message identity
     */
    const MESSAGE_IDENTITY = 'currency_system_notification';

    /**
     * Store manager
     */
    protected $storeManager;
    /**
     * MulticurrencyCacheRepository
     */
    protected $multicurrencyRepositiry;
    /**
     * Missing currency
     */
    protected $missingCurrency = "EUR";

    public function __construct(
        StoreManagerInterface        $storeManager,
        MulticurrencyCacheRepository $multicurrencyRepositiry
    ) {
        $this->storeManager = $storeManager;
        $this->multicurrencyRepositiry = $multicurrencyRepositiry;
    }

    /**
     * Retrieve unique system message identity
     *
     * @return string
     */
    public function getIdentity()
    {
        return self::MESSAGE_IDENTITY;
    }

    /**
     * Check whether the system message should be shown
     *
     * @return bool
     */
    public function isDisplayed()
    {
        $currency = $this->storeManager->getStore()->getCurrentCurrencyCode();
        $currencyList = $this->multicurrencyRepositiry->get();

        if (!$currencyList) {
            return false;
        }

        foreach ($currencyList['currency_list']['payment_methods']as $payment) {
            if (!in_array($currency, $payment['currencies'])) {
                $this->missingCurrency = current($payment['currencies']);
                return true;
            }
        }
        return false;
    }

    /**
     * Retrieve system message text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getText()
    {
        return __('Some payments will be missing. To use them, set %1 in Stores -> Configuration -> General -> Currency setup', $this->missingCurrency);
    }

    /**
     * Retrieve system message severity
     * Possible default system message types:
     * - MessageInterface::SEVERITY_CRITICAL
     * - MessageInterface::SEVERITY_MAJOR
     * - MessageInterface::SEVERITY_MINOR
     * - MessageInterface::SEVERITY_NOTICE
     *
     * @return int
     */
    public function getSeverity()
    {
        return self::SEVERITY_MAJOR;
    }

}
