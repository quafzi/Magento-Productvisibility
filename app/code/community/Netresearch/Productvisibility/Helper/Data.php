<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mage
 * @package   Mage_Catalog
 * @author    Thomas Kappel <thomas.kappel@netresearch.de>
 * @copyright 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Netresearch_Productvisibility
 * 
 * @category  Catalog
 * @package   Netresearch_Productvisibility
 * @author    Thomas Kappel <thomas.kappel@netresearch.de>
 * @copyright 2011 Netresearch GmbH & Co.KG <http://www.netresearch.de/>
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Netresearch_Productvisibility_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * if we're watching store view dependend product data
     * 
     * @param Mage_Catalog_Model_Product $product Product to check
     * 
     * @return boolean
     */
    public function isStoreView($product)
    {
        return Mage::app()->getStore(true) == $product->getStore();
    }
    
    /**
     * add default checkpoints of product visibility
     * 
     * @param Mage_Catalog_Model_Product $product Product to check
     * @param string                     $prefix  Prefix for checkpoint names
     * 
     * @return array Array of Netresearch_Productvisibility_Model_Checkpoint
     */
    public function getDefaultCheckpoints($product, $prefix='')
    {
        $checkpoints = array();
        if (strlen($prefix) and substr($prefix, -1) != ' ') {
            $prefix .= ' ';
        }
        $checkpoints[$prefix . 'is enabled'] = 
            $this->getIsEnabledCheckpoint($product, $prefix);
        $checkpoints[$prefix . 'is visible in catalog'] =  
            $this->getIsVisibleInCatalogCheckpoint($product, $prefix); 
        $checkpoints[$prefix . 'has website'] =
            $this->getHasWebsiteCheckpoint($product, $prefix); 
        $checkpoints[$prefix . 'has category'] =
            $this->getHasCategoryCheckpoint($product, $prefix); 
        $checkpoints[$prefix . 'is in stock'] = 
            $this->getIsInStockCheckpoint($product, $prefix);
        $checkpoints[$prefix . 'is up to date in price index'] =
            $this->getIsUpToDateInPriceIndexCheckpoint($product, $prefix);
        $checkpoints[$prefix . 'is up to date in stock index'] =
            $this->getIsUpToDateInStockIndexCheckpoint($product, $prefix);
        $checkpoints[$prefix . 'should be visible'] = 
            $this->getShouldBeVisibleCheckpoint($product, $prefix);
        return $checkpoints;
    }
    
    /**
     * Creates checkpoint to check if product is available or not
     *
     * @param Mage_Catalog_Model_Product $product
     * @param string $prefix
     * 
     * @return Netresearch_Productvisibility_Model_Checkpoint
     */
    protected function getIsEnabledCheckpoint($product, $prefix='')
    {
        return $this->createCheckpoint(
            $prefix . 'is enabled',
            Mage::helper('productvisibility/product')->isEnabled($product),
            'set status to enabled'
        );
    }

    /**
     * Creates checkpoint to check if product is visible or not
     *
     * @param Mage_Catalog_Model_Product $product
     * @param string $prefix
     * 
     * @return Netresearch_Productvisibility_Model_Checkpoint
     */
    protected function getIsVisibleInCatalogCheckpoint($product, $prefix='') 
    {
        $options =  Mage::getSingleton('catalog/product_visibility')->getOptionArray();
        return $this->createCheckpoint(
            $prefix . 'is visible in catalog',
            $product->isVisibleInSiteVisibility(),
            'set visibility to "Catalog" or "Catalog/Search"',
            $options[$product->getVisibility()]
        );
    }

    /**
     * Creates checkpoint to check if product is enabled for any website
     * It also shows for which website the product is enabled 
     *
     * @param Mage_Catalog_Model_Product $product
     * @param string $prefix
     * 
     * @return Netresearch_Productvisibility_Model_Checkpoint
     */
    protected function getHasWebsiteCheckpoint($product, $prefix='')
    {
        $websites = Mage::helper('productvisibility/product')
            ->getWebsites($product);
        return $this->createCheckpoint(
            $prefix . 'has website',
            0 < count($websites),
            'select an active website',
            Mage::helper('productvisibility/product')->__(
                'current websites: %s',
                implode(', ', $websites)
            )
        );
    }

    /**
     * Creates checkpoint to check if product is added to any category
     *
     * @param Mage_Catalog_Model_Product $product
     * @param string $prefix
     * 
     * @return Netresearch_Productvisibility_Model_Checkpoint
     */
    protected function getHasCategoryCheckpoint($product, $prefix='') 
    {
        $categories = array_diff(
            $product->getCategoryIds(), 
            array($product->getStore()->getRootCategoryId())
        );
        return $this->createCheckpoint(
            $prefix . 'has category',
            count($categories),
            'select a category',
            null,
            array($prefix . 'has website')
        );
    }
    
    /**
     * Creates checkpoint to check if product is in stock
     *
     * @param Mage_Catalog_Model_Product $product
     * @param string $prefix
     * 
     * @return Netresearch_Productvisibility_Model_Checkpoint
     */
    protected function getIsInStockCheckpoint($product, $prefix='')
    {
        return $this->createCheckpoint(
            $prefix . 'is in stock',
            $product->getStockItem()->getIsInStock(),
            'check inventory'
        );
    }

    /**
     * Creates checkpoint to check if product is up to date in price index
     *
     * @param Mage_Catalog_Model_Product $product
     * @param string $prefix
     * 
     * @return Netresearch_Productvisibility_Model_Checkpoint
     */
    protected function getIsUpToDateInPriceIndexCheckpoint($product, $prefix='') 
    {
        return $this->createCheckpoint(
            $prefix . 'is up to date in price index',
            Mage::helper('productvisibility/product')->isUpToDateInPriceIndex(
                $product
            ),
            'rebuild price index',
            null,
            array($prefix . 'is visible in catalog')
        );
    }    

    /**
     * Creates checkpoint to check if product is up to date in stock index
     *
     * @param Mage_Catalog_Model_Product $product
     * @param string $prefix
     * 
     * @return Netresearch_Productvisibility_Model_Checkpoint
     */
    protected function getIsUpToDateInStockIndexCheckpoint($product, $prefix='')
    {
        return $this->createCheckpoint(
            $prefix . 'is up to date in stock index',
            Mage::helper('productvisibility/product')->isUpToDateInStockIndex(
                $product
            ),
            'rebuild stock index',
            null,
            array($prefix . 'is in stock')
        );
    }

    /**
     * Creates checkpoint to check if product should be visible in frontend
     *
     * @param Mage_Catalog_Model_Product $product
     * @param string $prefix
     * 
     * @return Netresearch_Productvisibility_Model_Checkpoint
     */
    protected function getShouldBeVisibleCheckpoint($product, $prefix='')
    {
        $visible = Mage::helper('catalog/product')->canShow($product)
            and in_array($product->getStore()->getWebsite()->getId(), $product->getWebsiteIds());
        return $this->createCheckpoint(
            $prefix . 'should be visible',
            $visible,
            'check the other issues first',
            sprintf(
                '<a href="%s" onclick="window.open(this.href);return false;">%s</a>',
                $product->getProductUrl(),
                $product->getProductUrl()
            )
        );
    }
    
    /**
     * add overview checkpoints of product visibility
     * 
     * @param Mage_Catalog_Model_Product $product Product to check
     * 
     * @return array Array of Netresearch_Productvisibility_Model_Checkpoint
     */
    public function getOverviewCheckpoints($product)
    {
        $checkpoints = array();
        foreach (Mage::app()->getWebsites() as $website) {
            foreach ($website->getStores() as $store) {
                $storeProduct = $product->setStoreId($store->getId())->load($product->getId());
                $hasWebsite = in_array(
                    $storeProduct->getStore()->getWebsite()->getId(),
                    $storeProduct->getWebsiteIds()
                );
                $visibleInStore = Mage::helper('catalog/product')->canShow($storeProduct)
                    && Mage::helper('productvisibility/product')->isEnabled($storeProduct)
                    && $hasWebsite;
                $name = $website->getName() . ' - ' . $store->getName();
                $checkpoints[$name] = $this->createCheckpoint(
                    $name,
                    $visibleInStore,
                    Mage::helper('productvisibility')->__(
                        'select store view "%s" to view details', $name
                    )
                );
            }
        }
        
        return $checkpoints;
    }
    
    /**
     * add checkpoint for product visibility
     * 
     * @param string  $name         Name of the checkpoint
     * @param boolean $visible      Status
     * @param string  $howto        Explanation how to change this 
     * @param string  $details      Some details for the user
     * @param array   $dependencies Array of names of checkpoints this one depends on
     * 
     * @return Netresearch_Productvisibility_Model_Checkpoint
     */
    public function createCheckpoint($name, $visible, $howto, $details='',
        $dependencies=array()
    )
    {
        $checkpoint = Mage::getModel('productvisibility/checkpoint')
            ->setName($name)
            ->setHowto($howto)
            ->setVisibility($visible)
            ->setDetails($details)
            ->setDependencies($dependencies);
        
        return $checkpoint;
    }
}
