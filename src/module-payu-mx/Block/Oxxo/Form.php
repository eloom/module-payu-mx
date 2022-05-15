<?php
/**
* 
* PayU Mexico para Magento 2
* 
* @category     elOOm
* @package      Modulo PayUMx
* @copyright    Copyright (c) 2022 elOOm (https://eloom.tech)
* @version      2.0.0
* @license      https://opensource.org/licenses/OSL-3.0
* @license      https://opensource.org/licenses/AFL-3.0
*
*/
declare(strict_types=1);

namespace Eloom\PayUMx\Block\Oxxo;

use Eloom\PayUMx\Gateway\Config\Oxxo\Config;
use Magento\Payment\Helper\Data;

class Form extends \Magento\Payment\Block\Form {

	protected $gatewayConfig;

	private $paymentDataHelper;

	public function __construct(Config $gatewayConfig,
	                            Data $paymentDataHelper) {
		$this->gatewayConfig = $gatewayConfig;
		$this->paymentDataHelper = $paymentDataHelper;
	}
}
