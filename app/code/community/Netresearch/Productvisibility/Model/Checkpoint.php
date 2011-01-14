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
 * Netresearch_Productvisibility_Model_Checkpoint
 *
 * @category Netresearch_Productvisibility
 * @package  Netresearch_Productvisibility
 * @author   Thomas Kappel <thomas.kappel@netresearch.de>
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * 
 * @method Netresearch_Productvisibility_Model_Checkpoint setName()       setName(string $value)        Set the name of the checkpoint   
 * @method string                                         getName()       getName()                     Get the name of the checkpoint
 * @method Netresearch_Productvisibility_Model_Checkpoint setHowto()      setHowto(string $value)       Set the description how to change the visibility
 * @method string                                         getHowto()      getHowto()                    Get the description how to change the visibility
 * @method Netresearch_Productvisibility_Model_Checkpoint setVisibility() setVisibility(boolean $value) Set the current visibility
 * @method boolean                                        getVisibility() getVisibility()               Get the current visibility
 */
class Netresearch_Productvisibility_Model_Checkpoint extends Mage_Core_Model_Abstract
{
    /**
     * set the current status to visible
     * 
     * @return void
     */
    public function setVisible()
    {
        $this->setVisibility(true);
        return $this;
    }
    
    /**
     * set the current status to invisible
     * 
     * @return void
     */
    public function setInvisible()
    {
        $this->setVisibility(false);
        return $this;
    }
    
    /**
     * if the current status is visible
     * 
     * @return boolean
     */
    public function isVisible()
    {
        return $this->getVisibility();
    }
    
    /**
     * if the current status is invisible
     * 
     * @return boolean
     */
    public function isInvisible()
    {
        return false === $this->isVisible();
    }
    
    /**
     * if the current status is unknown
     * 
     * @return boolean
     */
    public function isUnknown()
    {
        return is_null($this->getVisibility());
    }
}
