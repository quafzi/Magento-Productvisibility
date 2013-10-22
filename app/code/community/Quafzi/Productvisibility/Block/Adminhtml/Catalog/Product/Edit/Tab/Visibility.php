<?php
/**
 * @category Mage
 * @package  Quafzi_Productvisibility
 * @author   Thomas Birke <tbirke@netextreme.de>
 */

/**
 * Quafzi_Productvisibility_Model_Observer
 *
 * @category Mage
 * @package  Quafzi_Productvisibility
 * @author   Thomas Birke <tbirke@netextreme.de>
 * @license  http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class Quafzi_Productvisibility_Block_Adminhtml_Catalog_Product_Edit_Tab_Visibility
    extends Mage_Adminhtml_Block_Widget implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * @var Mage_Catalog_Model_Product
     */
    protected $_product;

    /**
     * @var array
     */
    protected $_checkpoints;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('quafzi/productvisibility/tab.phtml');
        $this->setProduct(Mage::registry('product'));
    }

    public function getTabLabel()
    {
        return Mage::helper('productvisibility')->__('Visibility Check');
    }

    public function getTabTitle()
    {
        return Mage::helper('productvisibility')->__('Visibility Check');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

    /**
     * set product
     *
     * @param Mage_Catalog_Model_Product $product Product
     *
     * @return void
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
     * @return array Array of Quafzi_Productvisibility_Model_Checkpoint
     */
    public function getCheckpoints()
    {
        if (Mage::helper('productvisibility')->isStoreView($this->_product)) {
            $this->_checkpoints = Mage::helper('productvisibility')
                ->getOverviewCheckpoints($this->_product);
            Mage::dispatchEvent(
                'quafzi_product_visibility_checkpoints_load_overview',
                array('visibility_block'=>$this)
            );
        } else {
            $this->_checkpoints = Mage::helper('productvisibility')
                ->getDefaultCheckpoints($this->_product);
            Mage::dispatchEvent(
                'quafzi_product_visibility_checkpoints_load',
                array('visibility_block'=>$this)
            );
        }
        return $this->_checkpoints;
    }

    /**
     * add checkpoint
     *
     * @param Quafzi_Productvisibility_Model_Checkpoint $checkpoint Checkpoint
     *
     * @return void
     */
    public function addCheckpoint(Quafzi_Productvisibility_Model_Checkpoint $checkpoint)
    {
        $this->_checkpoints[$checkpoint->getName()] = $checkpoint;
    }

    /**
     * get a descriptions what to do if checkpoint fails
     *
     * @param string $checkpointName Name of the checkpoint
     *
     * @return array
     */
    public function getHowto($checkpointName)
    {
        $howto = array();
        $checkpoint = $this->_checkpoints[$checkpointName];
        if ($checkpoint->isInvisible()
            and 0 < count($checkpoint->getDependencies())
        ) {
            foreach ($checkpoint->getDependencies() as $dependency) {
                $dependendCheckpoint = $this->_checkpoints[$dependency];
                if ($dependendCheckpoint->isInvisible()) {
                    $howto[] = $dependendCheckpoint->getHowto();
                }
            }
        }
        $howto[] = $checkpoint->getHowto();
        return $howto;
    }
}
