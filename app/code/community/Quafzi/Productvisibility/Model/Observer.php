<?php
/**
 * @category  Mage
 * @package   Mage_Catalog
 * @author    Thomas Birke <tbirke@netextreme.de>
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Quafzi_Productvisibility_Model_Observer
 *
 * @package   Quafzi_Productvisibility
 * @author    Thomas Birke <tbirke@netextreme.de>
 * @copyright 2011 Netresearch GmbH & Co.KG <http://www.netresearch.de/>
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Quafzi_Productvisibility_Model_Observer
{
    /**
     * inject visibility tab into product edit page
     *
     * @param Varien_Event_Observer $observer Observer
     *
     * @return void
     */
    public function injectProductEditTab(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        if ($block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs) {
            if ($this->_getRequest()->getActionName() == 'edit') {
                $product = $block->getProduct();
                $visibilityBlock = $block->getLayout()->createBlock(
                    'productvisibility/adminhtml_catalog_product_edit_tab_visibility',
                    'visibility-content',
                    array('template' => 'quafzi/productvisibility/tab.phtml')
                );
                $visibilityBlock->setProduct($product);
                $block->addTab(
                    'productvisibility',
                    array(
                        'label'   => Mage::helper('productvisibility')->__('Visibility Check'),
                        'content' => $visibilityBlock->toHtml(),
                    )
                );
            }
        }
    }

    /**
     * add checkpoints for configurable products
     *
     * @param Varien_Event_Observer $observer Observer
     *
     * @return void
     */
    public function addConfigurableCheckpoints(Varien_Event_Observer $observer)
    {
        /**
         * @var Quafzi_Productvisibility_Block_Adminhtml_Catalog_Product_Edit_Tab_Visibility
         */
        $block = $observer->getEvent()->getVisibilityBlock();
        if ($block->getProduct()->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
            $childrenLinks = array();
            $children = $block->getProduct()->getTypeInstance()
                ->getUsedProducts(null, $block->getProduct());
            foreach ($children as $id => $child) {
                $child->setStoreId($block->getProduct()->getStoreId());
                if (Mage::helper('catalog/product')->canShow($child)) {
                    $childrenLinks[$id] = sprintf(
                        '<a href="%s" onclick="window.open(this.href);return false;">%s</a>',
                        $child->getProductUrl(),
                        $child->getProductUrl()
                    );
                }
            }
            $block->addCheckpoint(
                Mage::helper('productvisibility')->createCheckpoint(
                    'associated products not visible individually',
                    0 < count($childrenLinks) ? null : true,
                    sprintf(
                        Mage::helper('productvisibility')
                        ->__('there are visible associated products:')
                        . '<ul><li>%s</li></ul>',
                        implode('</li><li>', $childrenLinks)
                    ),
                    Mage::helper('productvisibility')
                    ->__('there are no visible associated products')
                )
            );
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
