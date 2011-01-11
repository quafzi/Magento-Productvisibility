<?php 
/**
 * Netresearch_Productvisibility_Model_Checkpoint
 *
 * @category   Netresearch_Productvisibility
 * @package    Netresearch_Productvisibility
 * @author     Thomas Kappel <thomas.kappel@netresearch.de>
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
        return false == $this->isVisible();
    }
}