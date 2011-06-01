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
 * Netresearch_Productvisibility_Model_Observer
 *
 * @category  Netresearch_Productvisibility
 * @package   Netresearch_Productvisibility
 * @author    Thomas Kappel <thomas.kappel@netresearch.de>
 * @copyright 2011 Netresearch GmbH & Co.KG <http://www.netresearch.de/>
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Netresearch_Productvisibility_Block_Adminhtml_Catalog_Product_Edit_Tab_Visibility
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
        $this->setTemplate('netresearch/productvisibility/tab.phtml');
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
     * @return array Array of Netresearch_Productvisibility_Model_Checkpoint
     */
    public function getCheckpoints()
    {
        if (Mage::helper('productvisibility')->isStoreView($this->_product)) {
            $this->_checkpoints = Mage::helper('productvisibility')
                ->getOverviewCheckpoints($this->_product);
            Mage::dispatchEvent(
                'netresearch_product_visibility_checkpoints_load_overview',
                array('visibility_block'=>$this)
            );
        } else {
            $this->_checkpoints = Mage::helper('productvisibility')
                ->getDefaultCheckpoints($this->_product);
            Mage::dispatchEvent(
                'netresearch_product_visibility_checkpoints_load',
                array('visibility_block'=>$this)
            );
        }
        return $this->_checkpoints;
    }
    
    /**
     * add checkpoint
     * 
     * @param Netresearch_Productvisibility_Model_Checkpoint $checkpoint Checkpoint
     * 
     * @return void
     */
    public function addCheckpoint(
        Netresearch_Productvisibility_Model_Checkpoint $checkpoint
    )
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
