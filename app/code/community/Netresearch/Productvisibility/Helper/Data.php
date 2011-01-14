<?php
/**
 * Netresearch_Productvisibility
 * 
 * @category   Catalog
 * @package    Netresearch_Productvisibility
 * @author     Thomas Kappel <thomas.kappel@netresearch.de>
 * @copyright  Copyright (c) 2010 Netresearch GmbH & Co.KG <http://www.netresearch.de/>
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Netresearch_Productvisibility_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * add default checkpoints of product visibility
     * 
     * @param Mage_Catalog_Model_Product $product Product to check
     * @param string                     $prefix  Prefix for checkpoint names
     * 
     * @return array Array of instances of Netresearch_Productvisibility_Model_Checkpoint
     */
    public function getDefaultCheckpoints($product, $prefix='')
    {
        if (strlen($prefix) and substr($prefix, -1) != ' ') {
            $prefix .= ' ';
        }
        $checkpoints = array();
        $checkpoints[$prefix . 'is enabled'] = $this->createCheckpoint(
            $prefix . 'is enabled',
            1 == $product->getStatus(),
            'set status to enabled'
        );
        $visibility_options = Mage_Catalog_Model_Product_Visibility::getOptionArray();
        $checkpoints[$prefix . 'is visible in catalog'] = $this->createCheckpoint(
            $prefix . 'is visible in catalog',
            $product->isVisibleInSiteVisibility(),
            'set visibility to "Catalog" or "Catalog/Search"',
            $visibility_options[$product->getVisibility()]
        );
        $websites = Mage::helper('productvisibility/product')
            ->getWebsites($product);
        $checkpoints[$prefix . 'has website'] = $this->createCheckpoint(
            $prefix . 'has website',
            0 < count($websites),
            'select an active website',
            Mage::helper('productvisibility/product')
                ->__('current websites: %s', implode(', ', $websites))
        );
        $categories = $product->getAvailableInCategories();
        $checkpoints[$prefix . 'has category'] = $this->createCheckpoint(
            $prefix . 'has category',
            count($categories),
            'select a category'
        );
        $checkpoints[$prefix . 'is in stock'] = $this->createCheckpoint(
            $prefix . 'is in stock',
            $product->isInStock(),
            'check inventory'
        );
        $checkpoints[$prefix . 'is up to date in price index'] = $this->createCheckpoint(
            $prefix . 'is up to date in price index',
            $websites = Mage::helper('productvisibility/product')
                ->isUpToDateInPriceIndex($product),
            'rebuild price index',
            null,
            array($prefix . 'is visible in catalog')
        );
        $checkpoints[$prefix . 'is up to date in stock index'] = $this->createCheckpoint(
            $prefix . 'is up to date in stock index',
            $websites = Mage::helper('productvisibility/product')
                ->isUpToDateInStockIndex($product),
            'rebuild stock index',
            null,
            array($prefix . 'is in stock')
        );
        $visible = Mage::helper('catalog/product')->canShow($product)
            and in_array($product->getStore()->getWebsite()->getId(), $product->getWebsiteIds());
        $checkpoints[$prefix . 'should be visible'] = $this->createCheckpoint(
            $prefix . 'should be visible',
            $visible,
            'check the other issues first',
            sprintf(
                '<a href="%s" onclick="window.open(this.href);return false;">%s</a>',
                $product->getProductUrl(),
                $product->getProductUrl()
            )
        );
        
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
    public function createCheckpoint($name, $visible, $howto, $details='', $dependencies=array())
    {
        $checkpoint = Mage::getModel('productvisibility/checkpoint');
        $checkpoint
            ->setName($name)
            ->setHowto($howto)
            ->setVisibility($visible)
            ->setDetails($details)
            ->setDependencies($dependencies);
        
        return $checkpoint;
    }
}