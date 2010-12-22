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
    
    public function addCheckpoint($name, $visible, $howto)
    {
        $checkpoint = Mage::getModel('productvisibility/checkpoint');
        $checkpoint
            ->setName($name)
            ->setHowto($howto)
            ->setVisibility($visible);
        $this->_checkpoints[$name] = $checkpoint;
    }
    
    public function getCheckpoints()
    {
        $this->_checkpoints = array();
        $this->addDefaultCheckpoints();
        Mage::dispatchEvent('netresearch_product_visibility_checkpoints_load',
            array('visibility_block'=>$this));
        return $this->_checkpoints;
    }
    
    public function addDefaultCheckpoints()
    {
        $this->addCheckpoint(
            'is enabled',
            1 == $this->_product->getStatus(),
            'set status to enabled'
        );
        $this->addCheckpoint(
            'is visible in catalog',
            $this->_product->isVisibleInCatalog(),
            'set visibility to "Catalog" or "Catalog/Search"'
        );
        $this->addCheckpoint(
            'is salable',
            $this->_product->isSalable(),
            'please check the other problems first'
        );
    }
}