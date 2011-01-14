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
     * get product
     * 
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return $this->_product;
    }
    
    /**
     * get checkpoints of product visibility
     * 
     * @return array Array of Netresearch_Productvisibility_Model_Checkpoint
     */
    public function getCheckpoints()
    {
        $this->_checkpoints = Mage::helper('productvisibility')
            ->getDefaultCheckpoints($this->_product);
        Mage::dispatchEvent('netresearch_product_visibility_checkpoints_load',
            array('visibility_block'=>$this));
        return $this->_checkpoints;
    }
    
    /**
     * add checkpoint
     * 
     * @param Netresearch_Productvisibility_Model_Checkpoint $checkpoint
     */
    public function addCheckpoint(Netresearch_Productvisibility_Model_Checkpoint $checkpoint)
    {
        $this->_checkpoints[$checkpoint->getName()] = $checkpoint;
    }
    
    /**
     * get a descriptions what to do if checkpoint fails
     * 
     * @param string $checkpoint_name Name of the checkpoint
     * 
     * @return array
     */
    public function getHowto($checkpoint_name)
    {
        $howto = array();
        $checkpoint = $this->_checkpoints[$checkpoint_name];
        if ($checkpoint->isInvisible()
            and 0 < count($checkpoint->getDependencies())
        ) {
            foreach ($checkpoint->getDependencies() as $dependency) {
                $dependend_checkpoint = $this->_checkpoints[$dependency];
                if ($dependend_checkpoint->isInvisible()) {
                    $howto[] = $dependend_checkpoint->getHowto();
                }
            }
        }
        $howto[] = $checkpoint->getHowto();
        return $howto;
    }
}