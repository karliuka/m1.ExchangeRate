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
class Faonni_ExchangeRate_Model_Currency_Import_Openexchangerate
	extends Mage_Directory_Model_Currency_Import_Abstract
{
    /**
     * Api url
     *
     * @var string
     */	
	protected $_url = 'https://openexchangerates.org/api/latest.json?app_id={{API_KEY}}&base={{CURRENCY_FROM}}';
    
	/**
     * Error messages
     *
     * @var array
     */    
	protected $_messages = array();

     /**
     * HTTP client
     *
     * @var Varien_Http_Client
     */
    protected $_httpClient;
	
    /**
     * Init http client
     *
     * @return  void
     */
    public function __construct()
    {
        $this->_httpClient = new Varien_Http_Client();
    }
	
    /**
     * Retrieve rate
     *
     * @param   string $currencyFrom
     * @param   string $currencyTo
     * @return  float
     */
    protected function _convert($currencyFrom, $currencyTo, $retry=0)
    {
		$url = str_replace('{{CURRENCY_FROM}}', $currencyFrom, $this->_url);
        $url = str_replace('{{API_KEY}}', Mage::getStoreConfig('currency/openexchangerate/api_key'), $url);

        try {
            $response = trim($this->_httpClient
                ->setUri($url)
                ->setConfig(array('timeout' => Mage::getStoreConfig('currency/openexchangerate/timeout')))
                ->request('GET')
                ->getBody()
			);
			
			if (!$response || false === ($json = json_decode($response))) {
                $this->_messages[] = Mage::helper('faonni_exchangerate')->__('Cannot retrieve rate from %s', $url);
				return null;
            }	
			
			if (!empty($json->error)) {
                $this->_messages[] = Mage::helper('faonni_exchangerate')->escapeHtml($json->description);
				return null;
            }
			
			if (!empty($json->rates->{$currencyTo})) {
                return (float) $json->rates->{$currencyTo};
            }
			$this->_messages[] = Mage::helper('faonni_exchangerate')->__('Cannot retrieve rate from %s', $url);
			return null;			
        }
        catch (Exception $e) {
            if($retry == 0) {
                $this->_convert($currencyFrom, $currencyTo, 1);
            } else {
                $this->_messages[] = Mage::helper('faonni_exchangerate')->__('Cannot retrieve rate from %s.', $url);
            }
        }
    }
}
