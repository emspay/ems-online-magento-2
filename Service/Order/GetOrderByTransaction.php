<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace EMSPay\Payment\Service\Order;

use EMSPay\Payment\Api\Config\RepositoryInterface as ConfigRepository;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\OrderRepository;

/**
 * Get order by transaction service class
 */
class GetOrderByTransaction
{

    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var ConfigRepository
     */
    private $configRepository;

    /**
     * GetByTransaction constructor.
     *
     * @param OrderRepository $orderRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ConfigRepository $configRepository
     */
    public function __construct(
        OrderRepository $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ConfigRepository $configRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->configRepository = $configRepository;
    }

    /**
     * Get Order by EMS Transaction ID
     *
     * @param string $transactionId
     *
     * @return OrderInterface|null
     */
    public function execute(string $transactionId)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('emspay_transaction_id', $transactionId, 'eq')
            ->setPageSize(1)
            ->create();

        $orders = $this->orderRepository->getList($searchCriteria)->getItems();
        $order = reset($orders);

        if (!$order) {
            $this->configRepository->addTolog(
                'error', __('No order found for transaction id %1', $transactionId)
            );
        }

        return $order;
    }
}
