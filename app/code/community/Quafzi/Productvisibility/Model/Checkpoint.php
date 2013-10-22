<?php
/**
 * @category  Mage
 * @package   Quafzi_Productvisibility
 * @author    Thomas Birke <tbirke@netextreme.de>
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Quafzi_Productvisibility_Model_Checkpoint
 *
 * @category  Catalog
 * @package   Quafzi_Productvisibility
 * @author    Thomas Birke <tbirke@netextreme.de>
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @method Quafzi_Productvisibility_Model_Checkpoint setName()       setName(string $value)        Set the name of the checkpoint
 * @method string                                    getName()       getName()                     Get the name of the checkpoint
 * @method Quafzi_Productvisibility_Model_Checkpoint setHowto()      setHowto(string $value)       Set the description how to change the visibility
 * @method string                                    getHowto()      getHowto()                    Get the description how to change the visibility
 * @method Quafzi_Productvisibility_Model_Checkpoint setVisibility() setVisibility(boolean $value) Set the current visibility
 * @method boolean                                   getVisibility() getVisibility()               Get the current visibility
 */
class Quafzi_Productvisibility_Model_Checkpoint extends Mage_Core_Model_Abstract
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
