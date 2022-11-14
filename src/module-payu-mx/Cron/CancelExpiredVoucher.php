<?php
/**
* 
* PayU Mexico para Magento 2
* 
* @category     elOOm
* @package      Modulo PayU Mexico
* @copyright    Copyright (c) 2022 elOOm (https://eloom.tech)
* @version      1.0.5
* @license      https://opensource.org/licenses/OSL-3.0
* @license      https://opensource.org/licenses/AFL-3.0
*
*/
declare(strict_types=1);

namespace Eloom\PayUMx\Cron;

use Eloom\Payment\Api\Data\OrderPaymentInterface;
use Eloom\PayU\Api\Data\OrderPaymentPayUInterface;
use Eloom\PayUMx\Gateway\Config\Oxxo\Config as OxxoConfig;
use Eloom\PayUMx\Gateway\Config\SevenEleven\Config as SevenElevenConfig;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\ObjectManager;
use Magento\Sales\Model\Order\Payment\Repository;
use Psr\Log\LoggerInterface;

class CancelExpiredVoucher {
	
	private $paymentRepository;
	
	private $searchCriteriaBuilder;
	
	private $logger;
	
	private $oxxoConfig;
	
	private $sevenElevenConfig;
	
	private $filterGroupBuilder;
	
	private $filterBuilder;
	
	public function __construct(LoggerInterface $logger,
	                            Repository $paymentRepository,
	                            SearchCriteriaBuilder $searchCriteriaBuilder,
	                            OxxoConfig $oxxoConfig,
	                            SevenElevenConfig $sevenElevenConfig,
	                            FilterBuilder $filterBuilder,
	                            FilterGroupBuilder $filterGroupBuilder) {
		$this->logger = $logger;
		$this->paymentRepository = $paymentRepository;
		$this->searchCriteriaBuilder = $searchCriteriaBuilder;
		$this->oxxoConfig = $oxxoConfig;
		$this->sevenElevenConfig = $sevenElevenConfig;
		$this->filterBuilder = $filterBuilder;
		$this->filterGroupBuilder = $filterGroupBuilder;
	}
	
	public function execute() {
		if ($this->oxxoConfig->isCancelable() || $this->sevenElevenConfig->isCancelable()) {
			$filter = null;
			if ($this->oxxoConfig->isCancelable()) {
				$filter = $this->filterBuilder->setField('method')
					->setValue(\Eloom\PayUMx\Model\Ui\Oxxo\ConfigProvider::CODE)
					->setConditionType('eq')
					->create();
			}
			
			$filter2 = null;
			if ($this->sevenElevenConfig->isCancelable()) {
				$filter2 = $this->filterBuilder->setField('method')
					->setValue(\Eloom\PayUMx\Model\Ui\SevenEleven\ConfigProvider::CODE)
					->setConditionType('eq')
					->create();
			}
			$filterGroup = $this->filterGroupBuilder->addFilter($filter)->addFilter($filter2)->create();
			
			// another
			$filter3 = $this->filterBuilder->setField(OrderPaymentPayUInterface::TRANSACTION_STATE)
				->setValue(\Eloom\PayU\Gateway\PayU\Enumeration\PayUTransactionState::PENDING()->key())
				->setConditionType('eq')
				->create();
			
			$filterGroup2 = $this->filterGroupBuilder->addFilter($filter3)->create();
			
			// another
			$filter4 = $this->filterBuilder->setField(OrderPaymentInterface::CANCEL_AT)
				->setValue(date('Y-m-d H:i:s', strtotime('now')))
				->setConditionType('lt')
				->create();
			
			$filterGroup3 = $this->filterGroupBuilder->addFilter($filter4)->create();
			
			$searchCriteria = $this->searchCriteriaBuilder->setFilterGroups([$filterGroup, $filterGroup2, $filterGroup3])->create();
			
			$paymentList = $this->paymentRepository->getList($searchCriteria)->getItems();
			if (count($paymentList)) {
				$processor = ObjectManager::getInstance()->get(\Eloom\PayU\Model\PaymentManagement\Processor::class);
				
				foreach ($paymentList as $payment) {
					try {
						$this->logger->info(sprintf("%s - Canceling voucher - Order %s", __METHOD__, $payment->getOrder()->getIncrementId()));
						$processor->cancelPayment($payment);
					} catch (\Exception $e) {
						$this->logger->critical(sprintf("%s - Exception: %s", __METHOD__, $e->getMessage()));
						//$this->logger->critical(sprintf("%s - Exception: %s", __METHOD__, $e->getTraceAsString()));
					}
				}
			}
		}
	}
}