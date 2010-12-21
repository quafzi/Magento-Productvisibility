<?php 
/**
 * Netresearch_Productvisibility_Model_Observer
 *
 * @category   Netresearch_Productvisibility
 * @package    Netresearch_Productvisibility
 * @author     Thomas Kappel <thomas.kappel@netresearch.de>
 */
class Netresearch_Productvisibility_Model_Observer
{
    public function injectProductEditTab($observer)
    {
        $block = $observer->getEvent()->getBlock();
        if ($block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs) {
            if ($this->_getRequest()->getActionName() == 'edit' || $this->_getRequest()->getParam('type')) {
                $product = $block->getProduct();
                $visibility_block = $block->getLayout()->createBlock(
                    'productvisibility/adminhtml_catalog_product_edit_tab_visibility',
                    'visibility-content',
                    array('template' => 'netresearch/productvisibility/tab.phtml')
                );
                $visibility_block->setProduct($product);
                $block->addTab('productvisibility', array(
                    'label'   => 'Visibility Check',
                    'content' => $visibility_block->toHtml(),
                ));
            }
        }
    }
    
    /**
    * Shortcut to getRequest
    */
    protected function _getRequest()
    {
        return Mage::app()->getRequest();
    }
}