<?php
/**
* 
* PayU Mexico para Magento 2
* 
* @category     elOOm
* @package      Modulo PayUMx
* @copyright    Copyright (c) 2022 elOOm (https://eloom.tech)
* @version      1.0.4
* @license      https://opensource.org/licenses/OSL-3.0
* @license      https://opensource.org/licenses/AFL-3.0
*
*/
declare(strict_types=1);

namespace Eloom\PayUMx\Model\Ui\Oxxo;

use Eloom\PayUMx\Gateway\Config\Oxxo\Config as OxxoConfig;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Escaper;
use Magento\Framework\View\Asset\Repository;
use Magento\Store\Model\StoreManagerInterface;

class ConfigProvider implements ConfigProviderInterface {

	const CODE = 'eloom_payments_payu_oxxo';

	protected $assetRepo;

	private $config;

	protected $escaper;

	protected $storeManager;

	public function __construct(Repository              $assetRepo,
	                            Escaper                 $escaper,
	                            OxxoConfig              $oxxoConfig,
	                            StoreManagerInterface $storeManager) {
		$this->assetRepo = $assetRepo;
		$this->escaper = $escaper;
		$this->config = $oxxoConfig;
		$this->storeManager = $storeManager;
	}

	public function getConfig() {
		$store = $this->storeManager->getStore();
		$payment = [];
		$storeId = $store->getStoreId();
		$isActive = $this->config->isActive($storeId);
		if ($isActive) {
			$currency = $store->getCurrentCurrencyCode();
			if ('MXN' != $currency) {
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
						'logo' => $this->assetRepo->getUrl('Eloom_PayUMx::images/oxxo.svg')
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