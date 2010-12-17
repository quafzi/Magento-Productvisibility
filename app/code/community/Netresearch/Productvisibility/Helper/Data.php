<?php
/**
 * Netresearch_Productvisibility Producttrigger
 * 
 * @category   Catalog
 * @package    Netresearch_Productvisibility
 * @author     Thomas Kappel <thomas.kappel@netresearch.de>
 * @copyright  Copyright (c) 2010 Netresearch GmbH & Co.KG <http://www.netresearch.de/>
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Netresearch_Productvisibility_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getVisibility($product)
    {
        Mage::dispatchEvent('catalog_product_is_salable_before', array(
            'product'   => $this
        ));

        $salable = $this->getTypeInstance(true)->isSalable($this);

        $object = new Varien_Object(array(
            'product'    => $this,
            'is_salable' => $salable
        ));
        Mage::dispatchEvent('catalog_product_is_salable_after', array(
            'product'   => $this,
            'salable'   => $object
        ));
        return $object->getIsSalable();
    }
}
