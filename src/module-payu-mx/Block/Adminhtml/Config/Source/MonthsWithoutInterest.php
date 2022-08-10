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

namespace Eloom\PayUMx\Block\Adminhtml\Config\Source;

class MonthsWithoutInterest implements \Magento\Framework\Option\ArrayInterface {
	
	public function toOptionArray() {
		return [
			['value' => '3', 'label' => __('3 installments')],
			['value' => '6', 'label' => __('6 installments')],
			['value' => '9', 'label' => __('9 installments')],
			['value' => '12', 'label' => __('12 installments')]
		];
	}
}