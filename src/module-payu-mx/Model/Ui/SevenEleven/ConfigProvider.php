<?php
/**
* 
* PayU Mexico para Magento 2
* 
* @category     Ã©lOOm
* @package      Modulo PayUMx
* @copyright    Copyright (c) 2021 Ã©lOOm (https://www.eloom.com.br)
* @version      1.0.0
* @license      https://opensource.org/licenses/OSL-3.0
* @license      https://opensource.org/licenses/AFL-3.0
*
*/
declare(strict_types=1);

namespace Eloom\PayUMx\Model\Ui\SevenEleven;

use Eloom\PayUMx\Gateway\Config\SevenEleven\Config as SevenElevenConfig;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Session\SessionManagerInterface;

class ConfigProvider implements ConfigProviderInterface {
	
	const CODE = 'eloom_payments_payu_seveneleven';
	
	private $config;
	
	private $session;
	
	protected $escaper;
	
	public function __construct(SessionManagerInterface $session,
	                            \Magento\Framework\Escaper $escaper,
	                            SevenElevenConfig $sevenElevenConfig) {
		$this->session = $session;
		$this->escaper = $escaper;
		$this->config = $sevenElevenConfig;
	}
	
	public function getConfig() {
		$storeId = $this->session->getStoreId();
		
		$payment = [];
		$isActive = $this->config->isActive($storeId);
		if ($isActive) {
			$payment = [
				self::CODE => [
					'isActive' => $isActive,
					'instructions' => $this->getInstructions($storeId)
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