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
    
    public function addCheckpoint($checkpoint, $value)
    {
        $this->_checkpoints[$checkpoint] = $value;
    }
    
    public function getCheckpoints()
    {
        $this->_checkpoints = array(
            'is_enabled' => $this->_product->getStatus() == 1,
            'is_salable' => $this->_product->isSalable(),
        );
        Mage::dispatchEvent('netresearch_product_visibility_checkpoints_load', array('visibility_block'=>$this));
        return $this->_checkpoints;
    }
}