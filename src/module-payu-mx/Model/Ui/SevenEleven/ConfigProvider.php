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

namespace Eloom\PayUMx\Model\Ui\SevenEleven;

use Eloom\PayUMx\Gateway\Config\SevenEleven\Config as SevenElevenConfig;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\Escaper;
use Magento\Store\Model\StoreManagerInterface;

class ConfigProvider implements ConfigProviderInterface {
	
	const CODE = 'eloom_payments_payu_seveneleven';

	protected $assetRepo;

	private $config;
	
	protected $escaper;

	protected $storeManager;

	private static $allowedCurrencies = ['MXN', 'USD'];

	public function __construct(Repository              $assetRepo,
	                            Escaper $escaper,
	                            SevenElevenConfig $sevenElevenConfig,
	                            StoreManagerInterface $storeManager) {
		$this->assetRepo = $assetRepo;
		$this->escaper = $escaper;
		$this->config = $sevenElevenConfig;
		$this->storeManager = $storeManager;
	}
	
	public function getConfig() {
		$store = $this->storeManager->getStore();
		$payment = [];
		$storeId = $store->getStoreId();
		$isActive = $this->config->isActive($storeId);
		if ($isActive) {
			$currency = $store->getCurrentCurrencyCode();
			if (!in_array($currency, self::$allowedCurrencies)) {
				return ['payment' => [
					self::CODE => [
						'message' =>  sprintf("Currency %s not supported.", $currency)
					]
				]];
			}

			$payment = [
				self::CODE => [
					'isActive' => $isActive,
					'instructions' => $this->getInstructions($storeId),
					'url' => [
						'logo' => $this->assetRepo->getUrl('Eloom_PayUMx::images/seven-eleven.svg')
					]
				]
			];
		}
		
		return [
			'payment' => $payment
		];
	}
	
	protected function getInstructions($storeId): string {
		return $this->escaper->escapeHtml($this->config->getInstructions($storeId));
	}
}
