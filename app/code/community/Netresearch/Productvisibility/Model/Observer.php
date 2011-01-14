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
    /**
     * inject visibility tab into product edit page
     * 
     * @param Varien_Event_Observer $observer
     */
    public function injectProductEditTab(Varien_Event_Observer $observer)
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
     * add checkpoints for configurable products
     * 
     * @param Varien_Event_Observer $observer
     */
    public function addConfigurableCheckpoints(Varien_Event_Observer $observer)
    {
        /**
         * @var Netresearch_Productvisibility_Block_Adminhtml_Catalog_Product_Edit_Tab_Visibility
         */
        $block = $observer->getEvent()->getVisibilityBlock();
        if ($block->getProduct()->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
            $children_links = array();
            foreach ($block->getProduct()->getTypeInstance()->getUsedProducts(null, $block->getProduct()) as $id => $child) {
                if (Mage::helper('catalog/product')->canShow($child)) {
                    $children_links[$id] = sprintf(
                        '<a href="%s" onclick="window.open(this.href);return false;">%s</a>',
                        $child->getProductUrl(),
                        $child->getProductUrl()
                    );
                }
            }
            $block->addCheckpoint(Mage::helper('productvisibility')->createCheckpoint(
                'associated products not visible individually',
                0 < count($children_links) ? null : true,
                sprintf(
                    'there are visible associated products: <ul><li>%s</li></ul>',
                    implode('</li><li>', $children_links)
                ),
                'there are no visible associated products'
            ));
        }
    }
    
    /**
     * Shortcut to getRequest
     * 
     * @return Mage_Core_Controller_Request_Http
     */
    protected function _getRequest()
    {
        return Mage::app()->getRequest();
    }
}