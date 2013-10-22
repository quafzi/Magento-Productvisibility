<?php
/**
 * @category  Mage
 * @package   Mage_Catalog
 * @author    Thomas Birke <tbirke@netextreme.de>
 */

/**
 * Quafzi_Productvisibility Producttrigger
 *
 * @category  Mage
 * @package   Quafzi_Productvisibility
 * @author    Thomas Birke <tbirke@netextreme.de>
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class Quafzi_Productvisibility_Helper_Product extends Mage_Core_Helper_Abstract
{
    /** @var Mage_Catalog_Model_Product */
    protected $_product  = null;

    /** @var array */
    protected $_websites = array();

    /**
     * if product is enabled in any website
     * 
     * @param Mage_Catalog_Model_Product $product Product to get websites of
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
     * @param Mage_Catalog_Model_Product $product Product to get websites of
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

    /**
     * check if product is up-to-date in price index
     * 
     * @param Mage_Catalog_Model_Product $product Product to get websites of
     * 
     * @return boolean
     */
    public function isUpToDateInPriceIndex($product)
    {
        if (empty($this->_websites)) {
            return false;
        }
        $connection = Mage::getModel('core/resource')
            ->getConnection(Mage_Core_Model_Resource::DEFAULT_READ_RESOURCE);
        $select = $connection
            ->select()
            ->from(Mage::getSingleton('core/resource')->getTableName('catalog/product_index_price'))
            ->where('customer_group_id = ?', 0)
            ->where('entity_id = ?', $product->getId());

        $results = $connection->query($select)->fetchAll();

        if (empty($results)) {
            return false;
        }
        foreach ($results as $result) {
            if ($product->getTaxClassId() != $result['tax_class_id']) {
                return false;
            }
            if ($product->getPrice() != $result['price']) {
                return false;
            }
            if ($product->getFinalPrice() != $result['final_price']) {
                return false;
            }
            if (!is_null($product->getMinimalPrice())
                and $product->getMinimalPrice() != $result['min_price']
            ) {
                return false;
            }
            if ($product->getTierPrice() != $result['tier_price']) {
                return false;
            }
        }
        return true;
    }

    /**
     * check if product is up-to-date in price index
     *
     * @param Mage_Catalog_Model_Product $product Product to get websites of
     *
     * @return boolean
     */
    public function isUpToDateInStockIndex($product)
    {
        $connection = Mage::getModel('core/resource')
            ->getConnection(Mage_Core_Model_Resource::DEFAULT_READ_RESOURCE);
        $select = $connection
            ->select()
            ->from(Mage::getSingleton('core/resource')->getTableName('cataloginventory/stock_status'))
            ->where('product_id = ?', $product->getId());

        $results = $connection->query($select)->fetchAll();

        if (empty($results)) {
            return false;
        }
        foreach ($results as $result) {
            if ($product->getStockItem()->getQty() != $result['qty']) {
                return false;
            }
            if ($product->isInStock() != $result['stock_status']) {
                return false;
            }
        }
        return true;
    }

    /**
     * check if product is enabled
     *
     * @param Mage_Catalog_Model_Product $product Product to check status of
     *
     * @return boolean
     */
    public function isEnabled($product)
    {
        return Mage_Catalog_Model_Product_Status::STATUS_ENABLED == $product->getStatus();
    }
}
