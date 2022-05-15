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

class Info extends \Eloom\PayU\Block\Info {

	public function getPaymentLink() {
		return $this->getInfo()->getAdditionalInformation('paymentLink');
	}

	public function getPdfLink() {
		return $this->getInfo()->getAdditionalInformation('pdfLink');
	}
	
	public function getBarCode() {
		return vsprintf("%s%s%s%s-%s%s%s%s-%s%s%s%s-%s%s%s%s-%s%s%s%s-%s%s%s%s-%s%s%s%s-%s%s%s%s", str_split($this->getInfo()->getAdditionalInformation('barCode')));
	}
}
