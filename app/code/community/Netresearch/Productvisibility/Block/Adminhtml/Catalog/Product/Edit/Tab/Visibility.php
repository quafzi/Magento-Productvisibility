<?php 
/**
 * Netresearch_Productvisibility_Model_Observer
 *
 * @category   Netresearch_Productvisibility
 * @package    Netresearch_Productvisibility
 * @author     Thomas Kappel <thomas.kappel@netresearch.de>
 */
class Netresearch_Productvisibility_Block_Adminhtml_Catalog_Product_Edit_Tab_Visibility extends Mage_Adminhtml_Block_Widget
{
    /**
     * @var Mage_Catalog_Model_Product
     */
    protected $_product;
    
    /**
     * @var array
     */
    protected $_checkpoints;
    
    /**
     * set product
     * 
     * @param Mage_Catalog_Model_Product $product
     */
    public function setProduct(Mage_Catalog_Model_Product $product)
    {
        $this->_product = $product;
    }
    
    /**
     * add checkpoint for product visibility
     * 
     * @param string  $name     Name of the checkpoint
     * @param boolean $visible  Status
     * @param string  $howto    Explanation how to change this 
     * @param string  $details  Some details for the user
     * 
     * @return Netresearch_Productvisibility_Block_Adminhtml_Catalog_Product_Edit_Tab_Visibility
     */
    public function addCheckpoint($name, $visible, $howto, $details='')
    {
        $checkpoint = Mage::getModel('productvisibility/checkpoint');
        $checkpoint
            ->setName($name)
            ->setHowto($howto)
            ->setVisibility($visible)
            ->setDetails($details);
        $this->_checkpoints[$name] = $checkpoint;
        
        return $this;
    }
    
    /**
     * get checkpoints of product visibility
     * 
     * @return array Array of Netresearch_Productvisibility_Model_Checkpoint
     */
    public function getCheckpoints()
    {
        $this->_checkpoints = array();
        $this->addDefaultCheckpoints();
        Mage::dispatchEvent('netresearch_product_visibility_checkpoints_load',
            array('visibility_block'=>$this));
        return $this->_checkpoints;
    }
    
    /**
     * add default checkpoints of product visibility
     * 
     * @return Netresearch_Productvisibility_Block_Adminhtml_Catalog_Product_Edit_Tab_Visibility
     */
    public function addDefaultCheckpoints()
    {
        $this->addCheckpoint(
            'is enabled',
            1 == $this->_product->getStatus(),
            'set status to enabled'
        );
        $visibility_options = Mage_Catalog_Model_Product_Visibility::getOptionArray();
        $this->addCheckpoint(
            'is visible in catalog',
            $this->_product->isVisibleInSiteVisibility(),
            'set visibility to "Catalog" or "Catalog/Search"',
            $visibility_options[$this->_product->getVisibility()]
        );
        $websites = Mage::helper('productvisibility/product')
            ->getWebsites($this->_product);
        $this->addCheckpoint(
            'has website',
            0 < count($websites),
            'select an active website',
            Mage::helper('productvisibility/product')
                ->__('current websites: %s', implode(', ', $websites))
        );
        $categories = $this->_product->getAvailableInCategories();
        $this->addCheckpoint(
            'has category',
            count($categories),
            'select a category'
        );
        $this->addCheckpoint(
            'is in stock',
            $this->_product->isInStock(),
            'check inventory'
        );
        $this->addCheckpoint(
            'price index is up to date',
            false,
            'not yet implemented'
        );
        $this->addCheckpoint(
            'stock index is up to date',
            false,
            'not yet implemented'
        );
        $this->addCheckpoint(
            'is salable',
            $this->_product->isSalable(),
            'please check the other problems first'
        );
        
        return $this;
    }
}