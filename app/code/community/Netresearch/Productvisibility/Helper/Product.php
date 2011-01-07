<?php
/**
 * Netresearch_Productvisibility Producttrigger
 * 
 * @category   Catalog
 * @package    Netresearch_Productvisibility
 * @author     Thomas Kappel <thomas.kappel@netresearch.de>
 * @copyright  Copyright (c) 2010 Netresearch GmbH & Co.KG <http://www.netresearch.de/>
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Netresearch_Productvisibility_Helper_Product extends Mage_Core_Helper_Abstract
{
    /** @var Mage_Catalog_Model_Product */
    protected $_product  = null;
    
    /** @var array */
    protected $_websites = array();
    
    /**
     * if product is enabled in any website
     * 
     * @param Mage_Catalog_Model_Product $product
     * 
     * @return boolean
     */
    public function getWebsites($product)
    {
        $this->initProduct($product);
        return $this->_websites;
    }
    
    /**
     * get websites where the product is enabled in
     * 
     * @param Mage_Catalog_Model_Product $product
     * 
     * @return array (int website_id => string website_name)
     */
    public function initProduct($product)
    {
        if (is_null($this->_product) or $this->_product != $product) {
            $this->_product = $product;
            if ($websiteIds = $product->getWebsiteIds()) {
                foreach ($websiteIds as $websiteId) {
                    $website = Mage::app()->getWebsite($websiteId);
                    $this->_websites[$websiteId] = $website->getName();
                }
            }
        }
    }
}
