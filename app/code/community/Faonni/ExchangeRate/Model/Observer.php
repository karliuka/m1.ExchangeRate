<?php
/**
 * Faonni
 *  
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade module to newer
 * versions in the future.
 * 
 * @package     Faonni_ExchangeRate
 * @copyright   Copyright (c) 2015 Karliuka Vitalii(karliuka.vitalii@gmail.com) 
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/
class Faonni_ExchangeRate_Model_Observer
{
    /**
     * Configuration pathes
     */
    const XML_PATH_SERVICE = 'currency/faonni_exchangerate_service';
	
    /**
     * Dispatch event before action
     *
     * @param   Varien_Event_Observer $observer
     * @return  Faonni_ExchangeRate_Model_Observer
     */
    public function preDispatch(Varien_Event_Observer $observer)
    {
		/** @var $action Mage_Core_Controller_Varien_Action */
        $action = $observer->getEvent()->getControllerAction();
		$service = $action->getRequest()->getParam('rate_services');
		
		Mage::getConfig()->saveConfig(self::XML_PATH_SERVICE, $service, 'default', 0);
		Mage::app()->getStore()->resetConfig();
	
        return $this;
    }
	
    /**
     * Prepare block
     *
     * @param   Varien_Event_Observer $observer
     * @return  Faonni_Compare_Model_Observer
     */
    public function toHtml(Varien_Event_Observer $observer)
    {
		/** @var $block Mage_Core_Block_Template */
        $block = $observer->getEvent()->getBlock();
		if ($block instanceof Mage_Adminhtml_Block_System_Currency_Rate_Services) {
			$block->getChild('import_services')->setValue(
				Mage::app()->getStore()->getConfig(self::XML_PATH_SERVICE)
			);
		}
        return $this;
    }	
}
