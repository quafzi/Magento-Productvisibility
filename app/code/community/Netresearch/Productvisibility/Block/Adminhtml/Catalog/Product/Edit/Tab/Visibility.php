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
     * @param string  $name         Name of the checkpoint
     * @param boolean $visible      Status
     * @param string  $howto        Explanation how to change this 
     * @param string  $details      Some details for the user
     * @param array   $dependencies Array of names of checkpoints this one depends on
     * 
     * @return Netresearch_Productvisibility_Block_Adminhtml_Catalog_Product_Edit_Tab_Visibility
     */
    public function addCheckpoint($name, $visible, $howto, $details='', $dependencies=array())
    {
        $checkpoint = Mage::getModel('productvisibility/checkpoint');
        $checkpoint
            ->setName($name)
            ->setHowto($howto)
            ->setVisibility($visible)
            ->setDetails($details)
            ->setDependencies($dependencies);
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
            'is up to date in price index',
            $websites = Mage::helper('productvisibility/product')
                ->isUpToDateInPriceIndex($this->_product),
            'rebuild price index',
            null,
            array('is visible in catalog')
        );
        $this->addCheckpoint(
            'is up to date in stock index',
            $websites = Mage::helper('productvisibility/product')
                ->isUpToDateInStockIndex($this->_product),
            'rebuild stock index'
        );
        $this->addCheckpoint(
            'is salable',
            $this->_product->isSalable(),
            'please check the other problems first'
        );
        
        return $this;
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